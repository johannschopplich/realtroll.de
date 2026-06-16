<?php

/** @var \Kirby\Cms\App $kirby */
/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */
/** @var array|null $header */
/** @var \Kirby\Template\Slot|null $slot */

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

    <?php if ($header ?? null): ?>
      <?php snippet('components/page-header', $header) ?>
    <?php endif ?>

    <main id="main" class="<?php e(!($header ?? null), 'mt-5xl') ?>">
      <?= $slot ?>
    </main>

    <div class="pt-7xl"></div>
    <div class="absolute bottom-0 left-1/2 flex items-center justify-center px-xs text-primary-700 bg-theme-background -translate-x-1/2 translate-y-1/2" aria-hidden="true">
      <svg width="14" height="14" viewBox="0 0 14 14" shape-rendering="crispEdges" fill="currentColor">
        <path d="M6 0h2v2h2v2h2v2h2v2h-2v2h-2v2h-2v2H6v-2H4v-2H2v-2H0V6h2V4h2V2h2z"/>
      </svg>
    </div>
    <?php snippet('components/corner-squares', [
      'corners' => ['bottom-left', 'bottom-right'],
      'size' => 2
    ]) ?>
  </div>

  <footer class="sticky bottom-0 z-0 bg-theme-background">
    <div class="relative pt-9xl pb-xl text-center bg-graph-paper">
      <div class="absolute inset-0 overflow-hidden pointer-events-none select-none" aria-hidden="true">
        <svg class="absolute inset-x-0 bottom-0 mx-auto w-full max-w-screen-lg opacity-5" viewBox="40 0 3880 760" focusable="false" aria-hidden="true">
          <text x="0" y="760" font-size="1000" text-anchor="start" class="font-heading font-bold fill-primary-700">real Troll</text>
        </svg>
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
