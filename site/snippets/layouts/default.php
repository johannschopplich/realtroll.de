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
    <?php if ($hasFooter ?? true): ?>
      <div class="absolute bottom-0 left-1/2 flex items-center justify-center px-xs text-primary-700 bg-theme-background -translate-x-1/2 translate-y-1/2" aria-hidden="true">
        <?= svg('assets/img/diamond.svg') ?>
      </div>
    <?php endif ?>
    <?php snippet('components/corner-squares', [
      'corners' => ['bottom-left', 'bottom-right'],
      'size' => 2
    ]) ?>
  </div>

  <?php snippet('components/footer') ?>

</body>
</html>
