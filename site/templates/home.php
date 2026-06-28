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
    fixed bottom-[var(--spacing-lg)] right-[var(--spacing-lg)] z-50 overflow-hidden
    m-0 w-[640px] max-w-[calc(100vw-2*var(--spacing-lg))] aspect-[4/3]
    bg-theme-background border-2 border-primary-700 shadow-float
    pointer-events-none invisible opacity-0 translate-y-4
    transition-[opacity,transform,visibility] duration-250 ease-out
    motion-reduce:transition-none hidpi:w-[480px]
  "
  aria-hidden="true"
>
  <img class="pixelated absolute inset-0 size-full object-cover" alt="">
  <span class="recess-overlay absolute inset-0" aria-hidden="true"></span>
  <figcaption
    class="
      absolute inset-x-0 bottom-0
      flex items-center justify-between gap-lg
      px-2.5 pt-5xl pb-2
      text-xs font-medium text-white
      bg-[linear-gradient(to_top,rgb(0_0_0_/_0.82),transparent)]
    "
  >
    <span class="min-w-0 truncate" data-showcase-title></span>
    <span class="flex gap-1 shrink-0" data-showcase-tiles></span>
  </figcaption>
</figure>

<?php endsnippet() ?>
