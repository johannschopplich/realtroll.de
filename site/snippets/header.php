<!doctype html>
<html class="var-color-primary var-color-primary-100" lang="de">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title><?= $page->customTitle()->or($page->title() . ' – ' . $site->title()) ?></title>

  <?php $meta = $page->meta() ?>
  <?= $meta->robots() ?>
  <?= $meta->jsonld() ?>
  <?= $meta->social() ?>

  <link rel="icon" href="/assets/img/icons/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="/assets/img/icons/favicon-16x16.png" sizes="16x16" type="image/png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <?= css([
    'https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap',
    'assets/css/main.css'
  ]) ?>

  <?= js('assets/js/main.js', ['type' => 'module']) ?>

  <script async defer data-domain="realtroll.de" src="https://plausible.io/js/plausible.js"></script>

</head>
<body data-template="<?= $page->intendedTemplate()->name() ?>">

  <div id="floating-screenshot" class="fixed top-[-120px] left-0 w-[320px] aspect-[4/3] flex justify-center items-center translate-x-[var(--mouseX)] translate-y-[var(--mouseY)] pointer-events-none z-10 hidden children:hidden">
    <img class="pixelated scale-200">
  </div>

  <header class="editorial pixelated flex items-center justify-center <?= $page->isHomePage() ? 'pb-48 mb-7xl' : 'mb-sm' ?>" aria-hidden="true">
    <?php if ($page->isHomePage()): ?>
      <img class="pixelated scale-200" src="<?= asset('assets/img/willkommen.gif')->url() ?>" alt="Willkommen auf realtroll.de">
    <?php endif ?>
  </header>

  <main id="main">
    <?php if (!$page->isHomePage()): ?>
      <?php snippet('breadcrumb') ?>
    <?php endif ?>
