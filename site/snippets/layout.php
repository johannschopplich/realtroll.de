<!doctype html>
<html class="var-color-primary var-color-primary-100 var-color-primary-700" lang="de">
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
    'assets/fonts/Gridular.css',
    'assets/fonts/IosevkaSlab.css',
    'assets/css/main.css'
  ]) ?>

  <?= js('assets/js/main.js', ['type' => 'module']) ?>

  <script async defer data-domain="realtroll.de" src="https://plausible.io/js/plausible.js"></script>

</head>
<body data-template="<?= $page->intendedTemplate()->name() ?>">

  <div id="floating-screenshot" class="fixed top-[-120px] left-0 z-10 pointer-events-none hidden w-[320px] aspect-[4/3] translate-x-[var(--mouseX)] translate-y-[var(--mouseY)] items-center justify-center children:hidden">
    <img class="pixelated scale-[2]">
  </div>

  <header class="editorial flex items-center justify-center pixelated <?= $page->isHomePage() ? 'pb-[10rem] mb-5xl md:pb-[12rem] md:mb-7xl' : 'mb-sm' ?>" aria-hidden="true">
    <?php if ($page->isHomePage()): ?>
      <img class="pixelated md:scale-[2]" src="<?= asset('assets/img/willkommen.gif')->url() ?>" alt="Willkommen auf realtroll.de">
    <?php endif ?>
  </header>

  <main id="main">
    <?php if (!$page->isHomePage()): ?>
      <?php snippet('breadcrumb') ?>
    <?php endif ?>

    <?= $slot ?>
  </main>

  <footer class="py-5xl text-center">
    <img class="pixelated mx-auto mb-lg" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
    <nav class="flex gap-lg justify-center">
      <a href="https://realtroll.hpage.com">Blog</a>
      <a href="<?= url('impressum') ?>">Impressum</a>
      <a href="<?= url('datenschutzerklaerung') ?>">Datenschutzerklärung</a>
    </nav>
  </footer>

</body>
</html>
