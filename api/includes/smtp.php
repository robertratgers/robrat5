<?php
declare(strict_types=1);

/**
 * Minimale SMTP-bezorging zonder externe bibliotheek (zelfde aanpak als Van Zwol).
 */

/** @return list<string> */
function abr_smtp_hosts(string $recipient): array
{
    $domain = strtolower((string) substr(strrchr($recipient, '@') ?: '', 1));
    $hosts = ['localhost'];

    $mx = [];
    $weights = [];
    if ($domain !== '' && function_exists('getmxrr') && getmxrr($domain, $mx, $weights)) {
        array_multisort($weights, SORT_ASC, SORT_NUMERIC, $mx);
        foreach ($mx as $host) {
            $hosts[] = rtrim((string) $host, '.');
        }
    }
    if ($domain !== '') {
        $hosts[] = 'mail.' . $domain;
    }

    return array_values(array_unique($hosts));
}

/**
 * @param resource $socket
 * @return array{int, string}
 */
function abr_smtp_read($socket): array
{
    $text = '';
    while (($line = fgets($socket, 1024)) !== false) {
        $text .= $line;
        if (strlen($line) < 4 || $line[3] === ' ') {
            break;
        }
    }

    return [(int) substr($text, 0, 3), trim($text)];
}

/**
 * @param resource $socket
 * @return array{int, string}
 */
function abr_smtp_cmd($socket, string $command): array
{
    fwrite($socket, $command . "\r\n");

    return abr_smtp_read($socket);
}

/** Bezorgt een compleet bericht (headers + lege regel + body) via SMTP. */
function abr_smtp_send(string $host, int $port, string $from, string $to, string $message, string &$error): bool
{
    $context = stream_context_create(['ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
    ]]);
    $socket = @stream_socket_client('tcp://' . $host . ':' . $port, $errno, $errstr, 8, STREAM_CLIENT_CONNECT, $context);
    if ($socket === false) {
        $error = "{$host}:{$port} verbinding mislukt ({$errno} {$errstr})";
        return false;
    }
    stream_set_timeout($socket, 15);

    $helo = (string) ($_SERVER['SERVER_NAME'] ?? gethostname() ?: 'localhost');
    $fail = static function (string $step, string $reply) use ($socket, $host, &$error): bool {
        $error = "{$host}: {$step} geweigerd: {$reply}";
        @fwrite($socket, "QUIT\r\n");
        fclose($socket);
        return false;
    };

    [$code, $reply] = abr_smtp_read($socket);
    if ($code !== 220) {
        return $fail('welkom', $reply);
    }

    [$code, $reply] = abr_smtp_cmd($socket, 'EHLO ' . $helo);
    if ($code !== 250) {
        return $fail('EHLO', $reply);
    }

    if (stripos($reply, 'STARTTLS') !== false && extension_loaded('openssl')) {
        [$code] = abr_smtp_cmd($socket, 'STARTTLS');
        if ($code === 220 && @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            [$code, $reply] = abr_smtp_cmd($socket, 'EHLO ' . $helo);
            if ($code !== 250) {
                return $fail('EHLO na TLS', $reply);
            }
        }
    }

    [$code, $reply] = abr_smtp_cmd($socket, 'MAIL FROM:<' . $from . '>');
    if ($code !== 250) {
        return $fail('MAIL FROM', $reply);
    }
    [$code, $reply] = abr_smtp_cmd($socket, 'RCPT TO:<' . $to . '>');
    if ($code !== 250 && $code !== 251) {
        return $fail('RCPT TO', $reply);
    }
    [$code, $reply] = abr_smtp_cmd($socket, 'DATA');
    if ($code !== 354) {
        return $fail('DATA', $reply);
    }

    $data = preg_replace('/\r\n|\r|\n/', "\r\n", $message) ?? $message;
    $data = preg_replace('/^\./m', '..', $data) ?? $data;
    [$code, $reply] = abr_smtp_cmd($socket, rtrim($data, "\r\n") . "\r\n.");
    if ($code !== 250) {
        return $fail('bericht', $reply);
    }

    abr_smtp_cmd($socket, 'QUIT');
    fclose($socket);

    return true;
}
