<?php

/** @var \Kirby\Cms\App $kirby */
/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */
/** @var mixed $slot */

?>
<!doctype html>
<html lang="de">
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

  <?php if (vite()->isDev()): ?>
    <?= css('assets/dev/index.css?v=' . time(), ['id' => 'vite-dev-css']) ?>
  <?php endif ?>

  <?= vite()->js('main.ts') ?>
  <?= vite()->css('main.ts') ?>

  <?= css([
    'assets/fonts/PPMondwest.css',
    'assets/fonts/IosevkaSlab.css'
  ]) ?>

</head>
<body class="overflow-x-clip min-h-dvh" data-template="<?= $page->intendedTemplate()->name() ?>">

  <div class="relative z-1 min-h-dvh bg-polka-grid border-b-2 border-primary-700">
    <?php snippet('components/navbar') ?>

    <?php if ($page->isHomePage()): ?>
      <header
        class="pixelated relative flex items-center justify-center pt-6xl pb-8xl md:pt-7xl md:pb-[calc(var(--spacing-9xl)+var(--spacing-xl))]"
        style="background: url('<?= asset('assets/img/bg-pattern.svg')->url() ?>') center repeat fixed"
      >
        <div class="flex flex-col items-center">
          <img class="pixelated md:scale-[1.5]" src="<?= asset('assets/img/willkommen.gif')->url() ?>" alt="Willkommen auf realtroll.de">
          <p class="mt-3xl px-3xl max-w-prose font-medium text-center text-balance tracking-tight md:mt-5xl"><?= $page->text()->kti() ?></p>
        </div>
        <img
          class="pixelated absolute bottom-0 left-1/2 z-1 max-w-none -translate-x-1/2 -translate-y-[2px] origin-bottom scale-[1.5] md:scale-[2]"
          src="<?= asset('assets/img/editorial-gesichter.png')->url() ?>"
          width="207"
          height="42"
          alt=""
        >
      </header>
    <?php endif ?>

    <main id="main" class="<?php e(!$page->isHomePage(), 'mt-5xl') ?>">
      <?= $slot ?>
    </main>

    <div class="pt-9xl"></div>
    <?php snippet('components/corner-squares', ['corners' => ['bottom-left', 'bottom-right'], 'size' => 2]) ?>
  </div>

  <footer class="sticky bottom-0 z-0 bg-theme-background">
    <div class="relative pt-9xl pb-xl text-center bg-graph-paper">
      <div class="absolute inset-0 overflow-hidden pointer-events-none select-none" aria-hidden="true">
        <p class="absolute inset-x-0 bottom-0 font-heading text-[18vw] leading-none whitespace-nowrap text-center text-primary-700 translate-y-1/5 opacity-5">real Troll</p>
      </div>
      <img class="pixelated mx-auto mb-lg" src="<?= asset('assets/img/real-troll-avatar.gif')->url() ?>" alt="Avatar von real Troll">
      <nav class="flex gap-lg justify-center flex-wrap text-sm">
        <a href="<?= url('impressum') ?>">Impressum</a>
        <a href="<?= url('datenschutzerklaerung') ?>">Datenschutzerklärung</a>
      </nav>
    </div>
  </footer>

</body>
</html>
