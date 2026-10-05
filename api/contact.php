<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/zegel.php';
require_once __DIR__ . '/includes/smtp.php';

function abr_contact_line(string $value): string
{
    return trim(str_replace(["\r", "\n", "\0"], ' ', $value));
}

function abr_contact_header_word(string $value): string
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function abr_contact_store_backup(string $name, string $email, string $subject, string $message): void
{
    $dir = __DIR__ . '/berichten';
    if (!is_dir($dir) && !@mkdir($dir, 0750, true)) {
        return;
    }
    if (!is_writable($dir)) {
        return;
    }

    $body = "Bericht via Art by Renee\n"
        . 'Datum: ' . date('d-m-Y H:i:s') . "\n"
        . 'Naam: ' . $name . "\n"
        . 'E-mail: ' . $email . "\n"
        . 'Onderwerp: ' . $subject . "\n\n"
        . $message . "\n";

    @file_put_contents($dir . '/' . date('Y-m-d_His') . '_' . bin2hex(random_bytes(3)) . '.txt', $body);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    abr_json(['ok' => false, 'status' => 'method', 'message' => 'Alleen POST is toegestaan.'], 405);
}

abr_session();

$name = abr_contact_line((string) ($_POST['name'] ?? ''));
$email = abr_contact_line((string) ($_POST['email'] ?? ''));
$subject = abr_contact_line((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$zegel = (string) ($_POST['zegel'] ?? '');

// Honeypot: bots vullen dit; echte bezoekers niet.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    abr_json(['ok' => true, 'status' => 'sent', 'message' => 'Bedankt! Je bericht is verstuurd.']);
}

if (!abr_zegel_verify($zegel)) {
    abr_zegel_issue(true);
    abr_json([
        'ok' => false,
        'status' => 'zegel',
        'message' => 'Dat was niet het goede zegel. Klik het gouden zegel met de letters RB en probeer het opnieuw.',
        'seals' => abr_zegel_choices(),
        'letters' => ABR_ZEGEL_LETTERS,
        'hint' => 'Klik het gouden zegel met de letters RB.',
    ], 400);
}

$valid = $name !== ''
    && $subject !== ''
    && $message !== ''
    && strlen($name) <= 400
    && strlen($subject) <= 600
    && strlen($message) <= 20000
    && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

if (!$valid) {
    abr_zegel_issue(true);
    abr_json([
        'ok' => false,
        'status' => 'error',
        'message' => 'Het bericht kon niet worden verstuurd. Controleer of alle velden goed zijn ingevuld.',
        'seals' => abr_zegel_choices(),
        'letters' => ABR_ZEGEL_LETTERS,
        'hint' => 'Klik het gouden zegel met de letters RB.',
    ], 400);
}

// Altijd lokaal bewaren, zodat berichten niet verloren gaan als mail faalt.
abr_contact_store_backup($name, $email, $subject, $message);

$mailFrom = abr_mail_from();
$host = abr_mail_host();
$mailSubject = abr_contact_header_word('[Art by Renee] ' . $subject);
$plainBody =
    "Contactformulier Art by Renee\n"
    . 'Voor: ' . ABR_CONTACT_NAME . ' <' . ABR_CONTACT_EMAIL . ">\n\n"
    . "Naam: {$name}\n"
    . "E-mail: {$email}\n"
    . "Onderwerp: {$subject}\n\n"
    . "{$message}\n";
$mailBody = chunk_split(base64_encode($plainBody));

$replyTo = $name !== ''
    ? abr_contact_header_word($name) . ' <' . $email . '>'
    : $email;

$headerLines = [
    'Date: ' . date(DATE_RFC2822),
    'From: ' . abr_contact_header_word(ABR_MAIL_FROM_NAME) . ' <' . $mailFrom . '>',
    'Reply-To: ' . $replyTo,
    'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $host . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
];

$errors = [];
$sent = false;
$smtpMessage = implode("\r\n", array_merge(
    ['To: <' . ABR_CONTACT_EMAIL . '>', 'Subject: ' . $mailSubject],
    $headerLines
)) . "\r\n\r\n" . $mailBody;

foreach (abr_smtp_hosts(ABR_CONTACT_EMAIL) as $smtpHost) {
    $smtpError = '';
    if (abr_smtp_send($smtpHost, 25, $mailFrom, ABR_CONTACT_EMAIL, $smtpMessage, $smtpError)) {
        $sent = true;
        break;
    }
    $errors[] = $smtpError;
}

if (!$sent) {
    $headers = implode("\r\n", $headerLines);
    $sent = @mail(ABR_CONTACT_EMAIL, $mailSubject, $mailBody, $headers, '-f' . $mailFrom)
        || @mail(ABR_CONTACT_EMAIL, $mailSubject, $mailBody, $headers);
    if (!$sent) {
        $errors[] = 'mail(): ' . (error_get_last()['message'] ?? 'geen PHP-melding');
    }
}

if (!$sent) {
    error_log('[artbyrenee contact] versturen mislukt: ' . implode(' | ', $errors));
    abr_zegel_issue(true);
    abr_json([
        'ok' => false,
        'status' => 'mail',
        'message' => 'Er ging iets mis bij het versturen. Probeer het later nog eens.',
        'seals' => abr_zegel_choices(),
        'letters' => ABR_ZEGEL_LETTERS,
        'hint' => 'Klik het gouden zegel met de letters RB.',
    ], 500);
}

abr_json([
    'ok' => true,
    'status' => 'sent',
    'message' => 'Bedankt! Je bericht is verstuurd naar Renee Bult. Zij reageert zo snel mogelijk.',
]);
