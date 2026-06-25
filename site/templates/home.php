<?php

/** @var \Kirby\Cms\Site $site */
/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', [
  'header' => [
    'image' => asset('assets/img/willkommen.gif'),
    'alt' => 'Willkommen auf realtroll.de',
    'text' => $page->text()->escape(),
    'showFaces' => true,
    'showDevlog' => true
  ]
], slots: true);

?>

<h1 class="sr-only"><?= $site->title()->escape() ?></h1>

<div class="content-lg">
  <?php snippet('components/games') ?>
</div>

<?php snippet('components/news') ?>

<figure
  id="screenshot-showcase"
  class="
    fixed bottom-[var(--spacing-lg)] right-[var(--spacing-lg)] z-50
    m-0 p-2
    bg-white border-2 border-primary-700
    shadow-float pointer-events-none invisible opacity-0 translate-y-4
    transition-[opacity,transform,visibility] duration-250 ease-out
    motion-reduce:transition-none
  "
  aria-hidden="true"
>
  <img class="pixelated block max-w-[calc(100vw-3rem)] h-auto" alt="">
  <figcaption class="flex items-center justify-between gap-lg mt-2 text-xs font-medium text-primary-700">
    <span data-showcase-title></span>
    <span class="flex gap-1" data-showcase-dots></span>
  </figcaption>
</figure>

<?php endsnippet() ?>
