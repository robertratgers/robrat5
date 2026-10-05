<?php
declare(strict_types=1);

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'robrat5.dreamhosters.com')) ?: 'robrat5.dreamhosters.com';
$loc = $scheme . '://' . $host . '/';
$lastmod = gmdate('Y-m-d');

header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=3600');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc><?= htmlspecialchars($loc, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8') ?></loc>
    <lastmod><?= htmlspecialchars($lastmod, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8') ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>
