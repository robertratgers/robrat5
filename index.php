<?php
declare(strict_types=1);

require_once __DIR__ . '/api/includes/config.php';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = preg_replace('/[^a-z0-9.-]/i', '', (string) ($_SERVER['HTTP_HOST'] ?? 'robrat5.dreamhosters.com')) ?: 'robrat5.dreamhosters.com';
$canonical = $scheme . '://' . $host . '/';
$ogImage = $canonical . 'art/renee-collectie.webp';
$title = 'Art by Renee';
$description = 'Schilderijen in acryl en mixed media. Huisdierportretten op maat en een wisselende collectie ter overname.';

$site = require __DIR__ . '/includes/content.php';
$nl = $site['copy']['nl'];
$en = $site['copy']['en'];
$contact = $site['contact'];
$emailParts = explode('@', (string) $contact['email'], 2);
$emailLocal = $emailParts[0] ?? '';
$emailDomain = $emailParts[1] ?? '';
$phoneDigits = preg_replace('/\D+/', '', (string) $contact['phone']) ?: '';
$phoneTel = $phoneDigits;
if (str_starts_with($phoneDigits, '06')) {
    $phoneTel = '+31' . substr($phoneDigits, 1);
} elseif ($phoneDigits !== '' && !str_starts_with($phoneDigits, '31')) {
    $phoneTel = '+31' . ltrim($phoneDigits, '0');
} elseif ($phoneDigits !== '') {
    $phoneTel = '+' . $phoneDigits;
}

function abr_i18n(string $nlText, string $enText): string
{
    return 'data-i18n-nl="' . abr_h($nlText) . '" data-i18n-en="' . abr_h($enText) . '"';
}

function abr_art_card(array $work, array $nl, array $en): void
{
    $status = $work['status'] === 'sale' ? 'sale' : 'sold';
    $statusNl = $status === 'sale' ? $nl['for_sale'] : $nl['sold'];
    $statusEn = $status === 'sale' ? $en['for_sale'] : $en['sold'];
    $priceHtml = '';
    $priceAttr = '';
    if ($status === 'sale' && isset($work['price'])) {
        $priceNl = $nl['price_prefix'] . ' ' . number_format((int) $work['price'], 0, ',', '.');
        $priceEn = $en['price_prefix'] . ' ' . number_format((int) $work['price'], 0, ',', '.');
        $priceHtml = '<em ' . abr_i18n($priceNl, $priceEn) . '>' . abr_h($priceNl) . '</em>';
        $priceAttr = ' data-price-nl="' . abr_h($priceNl) . '" data-price-en="' . abr_h($priceEn) . '"';
    }
    ?>
    <button
      type="button"
      class="art-card reveal"
      data-lightbox
      data-image="<?= abr_h($work['image']) ?>"
      data-title-nl="<?= abr_h($work['title']['nl']) ?>"
      data-title-en="<?= abr_h($work['title']['en']) ?>"
      data-category-nl="<?= abr_h($work['category']['nl']) ?>"
      data-category-en="<?= abr_h($work['category']['en']) ?>"
      data-status-nl="<?= abr_h($statusNl) ?>"
      data-status-en="<?= abr_h($statusEn) ?>"
      <?= $priceAttr ?>
    >
      <img src="<?= abr_h($work['image']) ?>" alt="<?= abr_h($work['title']['nl']) ?>" loading="lazy" width="800" height="1000">
      <span class="status-badge <?= abr_h($status) ?>" <?= abr_i18n($statusNl, $statusEn) ?>><?= abr_h($statusNl) ?></span>
      <span class="art-card-meta">
        <small <?= abr_i18n($work['category']['nl'], $work['category']['en']) ?>><?= abr_h($work['category']['nl']) ?></small>
        <strong <?= abr_i18n($work['title']['nl'], $work['title']['en']) ?>><?= abr_h($work['title']['nl']) ?></strong>
        <?= $priceHtml ?>
      </span>
    </button>
    <?php
}
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8">
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no, email=no">
    <title><?= abr_h($title) ?></title>
    <meta name="description" content="<?= abr_h($description) ?>">
    <link rel="canonical" href="<?= abr_h($canonical) ?>">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="/favicon.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="/favicon.png">
    <meta name="theme-color" content="#c0e8af">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="nl_NL">
    <meta property="og:url" content="<?= abr_h($canonical) ?>">
    <meta property="og:title" content="<?= abr_h($title) ?>">
    <meta property="og:description" content="<?= abr_h($description) ?>">
    <meta property="og:image" content="<?= abr_h($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= abr_h($title) ?>">
    <meta name="twitter:description" content="<?= abr_h($description) ?>">
    <meta name="twitter:image" content="<?= abr_h($ogImage) ?>">
    <link rel="stylesheet" href="/assets/site.css">
    <link rel="stylesheet" href="/assets/abr-polish.css">
  </head>
  <body class="bg-abr-bg text-abr-ink overflow-x-hidden">
    <header class="site-header fixed inset-x-0 top-0 z-40">
      <div class="mx-auto flex max-w-[1200px] items-center justify-between px-5 py-6 sm:px-6 md:px-10 md:py-7">
        <a href="/" class="brand-mark" aria-label="Art by Renee">
          <strong>ArtbyRenee</strong>
          <svg class="brand-heart" viewBox="0 0 48 40" aria-hidden="true" focusable="false">
            <path fill="currentColor" d="M24 36C24 36 4 23.5 4 13.2C4 7.8 8.2 4 13.2 4C17 4 20.2 6.2 24 10.2C27.8 6.2 31 4 34.8 4C39.8 4 44 7.8 44 13.2C44 23.5 24 36 24 36Z"/>
          </svg>
        </a>

        <nav class="hidden items-center gap-8 md:flex" aria-label="Hoofdnavigatie">
          <a class="nav-link" href="#huisdieren" <?= abr_i18n($nl['nav']['pets'], $en['nav']['pets']) ?>><?= abr_h($nl['nav']['pets']) ?></a>
          <a class="nav-link" href="#collectie" <?= abr_i18n($nl['nav']['collection'], $en['nav']['collection']) ?>><?= abr_h($nl['nav']['collection']) ?></a>
          <a class="nav-link" href="#over" <?= abr_i18n($nl['nav']['about'], $en['nav']['about']) ?>><?= abr_h($nl['nav']['about']) ?></a>
          <a class="nav-link" href="#contact" <?= abr_i18n($nl['nav']['contact'], $en['nav']['contact']) ?>><?= abr_h($nl['nav']['contact']) ?></a>
          <button type="button" class="lang-toggle" data-lang-toggle aria-label="Switch language"><?= abr_h($nl['lang_label']) ?></button>
        </nav>

        <div class="flex items-center gap-3 md:hidden">
          <button type="button" class="lang-toggle" data-lang-toggle aria-label="Switch language"><?= abr_h($nl['lang_label']) ?></button>
          <button type="button" class="menu-button" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" aria-label="Menu">
            <span></span><span></span>
          </button>
        </div>
      </div>
    </header>

    <div id="mobile-menu" class="mobile-menu" hidden>
      <a href="#huisdieren" <?= abr_i18n($nl['nav']['pets'], $en['nav']['pets']) ?>><?= abr_h($nl['nav']['pets']) ?></a>
      <a href="#collectie" <?= abr_i18n($nl['nav']['collection'], $en['nav']['collection']) ?>><?= abr_h($nl['nav']['collection']) ?></a>
      <a href="#over" <?= abr_i18n($nl['nav']['about'], $en['nav']['about']) ?>><?= abr_h($nl['nav']['about']) ?></a>
      <a href="#contact" <?= abr_i18n($nl['nav']['contact'], $en['nav']['contact']) ?>><?= abr_h($nl['nav']['contact']) ?></a>
    </div>

    <main>
      <section class="hero-section px-5 pt-32 pb-16 sm:px-6 sm:pt-36 sm:pb-20 md:px-10 md:pt-40 md:pb-28">
        <div class="mx-auto grid max-w-[1200px] items-center gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:gap-20">
          <div class="min-w-0 reveal">
            <p class="eyebrow" <?= abr_i18n($nl['eyebrow'], $en['eyebrow']) ?>><?= abr_h($nl['eyebrow']) ?></p>
            <h1 class="mt-5 font-serif text-[clamp(2.75rem,9vw,6.2rem)] leading-[0.92] tracking-[-0.04em] font-medium" <?= abr_i18n($nl['hero_title'], $en['hero_title']) ?>><?= abr_h($nl['hero_title']) ?></h1>
            <p class="mt-6 max-w-xl text-base leading-relaxed text-abr-muted md:text-lg" <?= abr_i18n($nl['hero_text'], $en['hero_text']) ?>><?= abr_h($nl['hero_text']) ?></p>
            <div class="mt-9 flex flex-wrap gap-3">
              <a class="primary-button" href="#collectie">
                <span <?= abr_i18n($nl['hero_cta'], $en['hero_cta']) ?>><?= abr_h($nl['hero_cta']) ?></span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14M14 7l5 5-5 5" stroke="currentColor" stroke-width="1.8"/></svg>
              </a>
              <a class="ghost-button" href="#contact" <?= abr_i18n($nl['commission_cta'], $en['commission_cta']) ?>><?= abr_h($nl['commission_cta']) ?></a>
            </div>
          </div>
          <div class="hero-art w-full min-w-0 reveal reveal-delay">
            <div class="hero-main-image">
              <img src="/art/bloemenweide.webp" alt="<?= abr_h($site['collection'][0]['title']['nl']) ?>" width="840" height="1050">
            </div>
            <div class="hero-small-image">
              <img src="/art/kattenportret.webp" alt="<?= abr_h($site['pets'][2]['title']['nl']) ?>" width="480" height="640">
            </div>
          </div>
        </div>
      </section>

      <section id="huisdieren" class="border-t border-abr-border/80 px-5 py-16 sm:px-6 sm:py-20 md:px-10 md:py-28">
        <div class="mx-auto max-w-[1200px]">
          <div class="mb-10 max-w-2xl sm:mb-12 reveal">
            <p class="eyebrow" <?= abr_i18n($nl['pets_eyebrow'], $en['pets_eyebrow']) ?>><?= abr_h($nl['pets_eyebrow']) ?></p>
            <h2 class="section-title mt-4" <?= abr_i18n($nl['pets_title'], $en['pets_title']) ?>><?= abr_h($nl['pets_title']) ?></h2>
            <p class="mt-5 text-base leading-relaxed text-abr-muted md:text-lg" <?= abr_i18n($nl['pets_text'], $en['pets_text']) ?>><?= abr_h($nl['pets_text']) ?></p>
            <p class="mt-3 text-sm tracking-wide text-abr-muted-soft" <?= abr_i18n($nl['pets_note'], $en['pets_note']) ?>><?= abr_h($nl['pets_note']) ?></p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            <?php foreach ($site['pets'] as $work) {
                abr_art_card($work, $nl, $en);
            } ?>
          </div>
        </div>
      </section>

      <section id="collectie" class="border-t border-abr-border/80 px-5 py-16 sm:px-6 sm:py-20 md:px-10 md:py-28">
        <div class="mx-auto max-w-[1200px]">
          <div class="mb-10 max-w-2xl sm:mb-12 reveal">
            <p class="eyebrow" <?= abr_i18n($nl['collection_eyebrow'], $en['collection_eyebrow']) ?>><?= abr_h($nl['collection_eyebrow']) ?></p>
            <h2 class="section-title mt-4" <?= abr_i18n($nl['collection_title'], $en['collection_title']) ?>><?= abr_h($nl['collection_title']) ?></h2>
            <p class="mt-5 text-base leading-relaxed text-abr-muted md:text-lg" <?= abr_i18n($nl['collection_text'], $en['collection_text']) ?>><?= abr_h($nl['collection_text']) ?></p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            <?php foreach ($site['collection'] as $work) {
                abr_art_card($work, $nl, $en);
            } ?>
          </div>
        </div>
      </section>

      <section id="over" class="border-t border-abr-border/80 px-5 py-16 sm:px-6 sm:py-20 md:px-10 md:py-28">
        <div class="mx-auto grid max-w-[1200px] items-center gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-20">
          <div class="about-image-wrap reveal">
            <img src="/art/renee-collectie.webp" alt="Renee" width="920" height="1150">
          </div>
          <div class="reveal reveal-delay">
            <p class="eyebrow" <?= abr_i18n($nl['about_eyebrow'], $en['about_eyebrow']) ?>><?= abr_h($nl['about_eyebrow']) ?></p>
            <h2 class="section-title mt-4" <?= abr_i18n($nl['about_title'], $en['about_title']) ?>><?= abr_h($nl['about_title']) ?></h2>
            <p class="mt-6 font-serif text-[1.35rem] leading-7 italic text-abr-ink-soft md:text-[1.7rem]" <?= abr_i18n($nl['about_quote'], $en['about_quote']) ?>><?= abr_h($nl['about_quote']) ?></p>
            <p class="mt-6 max-w-xl text-base leading-relaxed text-abr-muted md:text-lg" <?= abr_i18n($nl['about_body'], $en['about_body']) ?>><?= abr_h($nl['about_body']) ?></p>
            <div class="about-how mt-8">
              <h3 <?= abr_i18n($nl['about_how_title'], $en['about_how_title']) ?>><?= abr_h($nl['about_how_title']) ?></h3>
              <p <?= abr_i18n($nl['about_how'], $en['about_how']) ?>><?= abr_h($nl['about_how']) ?></p>
            </div>
          </div>
        </div>
      </section>

      <section id="contact" class="border-t border-abr-border/80 px-5 py-16 sm:px-6 sm:py-20 md:px-10 md:py-28">
        <div class="mx-auto max-w-[1200px]">
          <div class="max-w-2xl reveal">
            <p class="eyebrow" <?= abr_i18n($nl['contact_eyebrow'], $en['contact_eyebrow']) ?>><?= abr_h($nl['contact_eyebrow']) ?></p>
            <h2 class="section-title mt-4" <?= abr_i18n($nl['contact_title'], $en['contact_title']) ?>><?= abr_h($nl['contact_title']) ?></h2>
            <p class="mt-5 text-base leading-relaxed text-abr-muted md:text-lg" <?= abr_i18n($nl['contact_text'], $en['contact_text']) ?>><?= abr_h($nl['contact_text']) ?></p>
          </div>

          <div class="abr-inlichtingen reveal">
            <p class="abr-inlichtingen-label" <?= abr_i18n($nl['inlichtingen'], $en['inlichtingen']) ?>><?= abr_h($nl['inlichtingen']) ?></p>
            <div class="abr-inlichtingen-body">
              <p class="abr-inlichtingen-name"><?= abr_h($contact['name_short']) ?></p>
              <a class="abr-tel-button" href="tel:<?= abr_h($phoneTel) ?>"><?= abr_h($contact['phone']) ?></a>
              <p
                class="abr-mail-at"
                data-mail-local="<?= abr_h($emailLocal) ?>"
                data-mail-domain="<?= abr_h($emailDomain) ?>"
                role="link"
                tabindex="0"
                title="<?= abr_h($nl['email']) ?>"
              >
                <span class="abr-mail-local"><?= abr_h($emailLocal) ?></span><span class="abr-mail-sep"> [at] </span><span class="abr-mail-domain"><?= abr_h($emailDomain) ?></span>
              </p>
              <a class="abr-mail-at abr-instagram-link" href="<?= abr_h($contact['instagram_url']) ?>" target="_blank" rel="noopener noreferrer">
                <span class="abr-mail-local">instagram</span><span class="abr-mail-sep"> [at] </span><span class="abr-mail-domain">renee_bult</span>
              </a>
            </div>
          </div>

          <div class="contact-form-wrap mt-14 reveal">
            <p class="eyebrow" <?= abr_i18n($nl['form_eyebrow'], $en['form_eyebrow']) ?>><?= abr_h($nl['form_eyebrow']) ?></p>
            <h3 class="mt-3 font-serif text-3xl font-medium tracking-[-0.02em] md:text-4xl" <?= abr_i18n($nl['form_title'], $en['form_title']) ?>><?= abr_h($nl['form_title']) ?></h3>

            <form id="contact-form" class="contact-form mt-8" action="/api/contact" method="post" novalidate>
              <div id="contact-alert" class="contact-alert" hidden></div>
              <div class="contact-form-grid">
                <label class="contact-field">
                  <span <?= abr_i18n($nl['form']['name'], $en['form']['name']) ?>><?= abr_h($nl['form']['name']) ?></span>
                  <input type="text" name="name" autocomplete="name" required maxlength="400">
                </label>
                <label class="contact-field">
                  <span <?= abr_i18n($nl['form']['email'], $en['form']['email']) ?>><?= abr_h($nl['form']['email']) ?></span>
                  <input type="email" name="email" autocomplete="email" required>
                </label>
                <label class="contact-field contact-field--full">
                  <span <?= abr_i18n($nl['form']['subject'], $en['form']['subject']) ?>><?= abr_h($nl['form']['subject']) ?></span>
                  <input type="text" name="subject" required maxlength="600">
                </label>
                <label class="contact-field contact-field--full">
                  <span <?= abr_i18n($nl['form']['message'], $en['form']['message']) ?>><?= abr_h($nl['form']['message']) ?></span>
                  <textarea name="message" required maxlength="20000"></textarea>
                </label>
                <div class="contact-hp" aria-hidden="true">
                  <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <fieldset class="contact-captcha contact-field--full">
                  <legend <?= abr_i18n($nl['form']['captcha_legend'], $en['form']['captcha_legend']) ?>><?= abr_h($nl['form']['captcha_legend']) ?></legend>
                  <p class="contact-captcha-loading" data-captcha-loading>…</p>
                  <div class="contact-zegels" data-zegels></div>
                </fieldset>
                <div class="contact-actions contact-field--full">
                  <button type="submit" class="contact-button" data-submit <?= abr_i18n($nl['form']['submit'], $en['form']['submit']) ?>><?= abr_h($nl['form']['submit']) ?></button>
                  <p class="contact-privacy" <?= abr_i18n($nl['form']['privacy'], $en['form']['privacy']) ?>><?= abr_h($nl['form']['privacy']) ?></p>
                </div>
              </div>
            </form>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t border-abr-border/80 px-5 py-10 sm:px-6 md:px-10">
      <div class="site-footer mx-auto max-w-[1200px]">
        <a href="/" class="brand-mark brand-mark-small" aria-label="Art by Renee">
          <strong>ArtbyRenee</strong>
          <svg class="brand-heart" viewBox="0 0 48 40" aria-hidden="true" focusable="false">
            <path fill="currentColor" d="M24 36C24 36 4 23.5 4 13.2C4 7.8 8.2 4 13.2 4C17 4 20.2 6.2 24 10.2C27.8 6.2 31 4 34.8 4C39.8 4 44 7.8 44 13.2C44 23.5 24 36 24 36Z"/>
          </svg>
        </a>
        <div class="site-footer-copy">
          <p class="site-footer-copyright">© <?= date('Y') ?> <?= abr_h($contact['name']) ?></p>
          <p class="site-footer-tagline" <?= abr_i18n($nl['footer_line'], $en['footer_line']) ?>><?= abr_h($nl['footer_line']) ?></p>
        </div>
        <p class="site-footer-credit">
          <span <?= abr_i18n($nl['footer_credit'], $en['footer_credit']) ?>><?= abr_h($nl['footer_credit']) ?></span>
          <a href="<?= abr_h($contact['designer_url']) ?>" target="_blank" rel="noopener noreferrer"><?= abr_h($contact['designer_name']) ?></a>
        </p>
      </div>
    </footer>

    <div id="lightbox" class="lightbox" hidden>
      <button type="button" class="lightbox-close" data-lightbox-close aria-label="Sluiten"><span></span><span></span></button>
      <div class="lightbox-content">
        <img src="/favicon.svg" alt="" width="32" height="32" data-lightbox-img>
        <div>
          <p data-lightbox-category>&nbsp;</p>
          <p class="lightbox-title" data-lightbox-title>&nbsp;</p>
          <strong data-lightbox-meta>&nbsp;</strong>
        </div>
      </div>
    </div>

    <noscript>
      <div style="font-family:Outfit,sans-serif;background:#f3faef;color:#3d3a36;padding:2rem">
        <p style="margin:0 0 0.5rem;font-family:Cormorant Garamond,serif;font-size:1.75rem;font-weight:500"><?= abr_h($title) ?></p>
        <p style="margin:0 0 1rem"><?= abr_h($description) ?></p>
        <div style="display:flex;gap:2rem;max-width:720px;font-family:Consolas,Monaco,monospace;font-size:14px;color:#1a3a6b">
          <div style="color:#4a6fa5;font-family:Outfit,sans-serif">Inlichtingen</div>
          <div>
            <div style="margin:0 0 .65rem;font-family:Outfit,sans-serif;font-size:15px"><?= abr_h($contact['name_short']) ?></div>
            <div style="margin:0 0 .65rem"><a href="tel:<?= abr_h($phoneTel) ?>" style="display:inline-block;padding:.35rem .75rem;border:1px solid #6b7280;border-radius:4px;background:linear-gradient(#f8f8f8,#ddd);color:#1a3a6b;text-decoration:none;font-weight:700"><?= abr_h(ABR_CONTACT_PHONE) ?></a></div>
            <div style="margin:0 0 .35rem"><?= abr_h($emailLocal) ?> [at] <?= abr_h($emailDomain) ?></div>
            <div><a href="<?= abr_h($contact['instagram_url']) ?>" style="color:#1a3a6b">instagram [at] renee_bult</a></div>
          </div>
        </div>
      </div>
    </noscript>

    <script>
      window.ABR_I18N = {
        form: {
          nl: <?= json_encode($nl['form'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
          en: <?= json_encode($en['form'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
        }
      };
    </script>
    <script src="/assets/site.js" defer></script>
  </body>
</html>
