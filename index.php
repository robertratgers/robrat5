<?php
declare(strict_types=1);

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'robrat5.dreamhosters.com')) ?: 'robrat5.dreamhosters.com';
$canonical = $scheme . '://' . $host . '/';
$ogImage = $canonical . 'art/renee-collectie.webp';
$title = 'Art by Renee';
$description = 'Schilderijen in acryl en mixed media. Huisdierportretten op maat en een wisselende collectie ter overname.';
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8">
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no, email=no">
    <title><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="theme-color" content="#c0e8af">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="nl_NL">
    <meta property="og:url" content="<?= htmlspecialchars($canonical, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <script type="module" crossorigin src="/assets/index-Cf5AmWX3.js"></script>
    <link rel="stylesheet" crossorigin href="/assets/index-CaHdBUWd.css">
    <link rel="stylesheet" crossorigin href="/assets/gemini-built.css">
    <link rel="stylesheet" crossorigin href="/assets/gemini-bridge.css">
  </head>
  <body>
    <div id="root"></div>
    <noscript>
      <div style="font-family:var(--font-sans);background:var(--abr-bg);color:var(--abr-ink);padding:2rem">
        <h1 style="margin:0 0 0.5rem;font-family:var(--font-serif)"><?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h1>
        <p style="margin:0 0 1rem"><?= htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:900px">
          <div style="background:var(--abr-bg-soft);border:1px solid var(--abr-border);padding:1.25rem">
            <div style="letter-spacing:.12em;text-transform:uppercase;color:var(--abr-muted);font-size:.72rem;font-weight:500">E-mail</div>
            <div style="font-family:var(--font-serif);font-size:1.25rem;margin-top:0.5rem;font-weight:500"><?= htmlspecialchars(ABR_CONTACT_NAME, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
            <div style="color:var(--abr-muted);margin-top:0.5rem"><?= htmlspecialchars(ABR_CONTACT_EMAIL, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
          </div>
          <div style="background:var(--abr-bg-soft);border:1px solid var(--abr-border);padding:1.25rem">
            <div style="letter-spacing:.12em;text-transform:uppercase;color:var(--abr-muted);font-size:.72rem;font-weight:500">Telefoon</div>
            <div style="font-family:var(--font-serif);font-size:1.25rem;margin-top:0.5rem;font-weight:500"><?= htmlspecialchars(ABR_CONTACT_PHONE, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></div>
          </div>
        </div>
      </div>
    </noscript>
  </body>
</html>
