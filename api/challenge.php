<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/zegel.php';

abr_zegel_issue(isset($_GET['refresh']));
$choices = abr_zegel_choices();

abr_json([
    'ok' => true,
    'letters' => ABR_ZEGEL_LETTERS,
    'hint' => 'Klik het gouden zegel met de letters RB.',
    'seals' => $choices,
]);
