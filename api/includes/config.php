<?php
declare(strict_types=1);

/**
 * Art by Renee — contactconfiguratie.
 */

const ABR_CONTACT_EMAIL = 'Reneeb2409@gmail.com';
const ABR_MAIL_FROM_NAME = 'Art by Renee website';
const ABR_CONTACT_NAME = 'Renee Bult';
const ABR_CONTACT_PHONE = '06-23125951';

function abr_h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function abr_mail_host(): string
{
    $host = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['SERVER_NAME'] ?? '')) ?: '';

    return $host !== '' ? strtolower($host) : 'rrcreaties.org';
}

/** From-adres van de website (nooit gelijk aan ABR_CONTACT_EMAIL). */
function abr_mail_from(): string
{
    return 'noreply@' . abr_mail_host();
}

function abr_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function abr_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
