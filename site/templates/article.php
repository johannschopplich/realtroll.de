<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true)

?>

<article class="content-prose">
  <header class="mb-7xl text-center">
    <p class="mb-sm text-sm text-contrast-medium">
      <time datetime="<?= $page->date()->toDate('c') ?>">
        <?= $page->date()->toDate('d.m.Y') ?>
      </time>
    </p>
    <h1 class="editorial-title hyphenate"><?= $page->title()->escape() ?></h1>
  </header>

  <?php if ($cover = $page->cover()->toFile()): ?>
    <figure class="mb-5xl">
      <img
        class="w-full <?= $cover->width() <= 640 ? 'pixelated' : '' ?>"
        src="<?= $cover->url() ?>"
        alt="<?= $cover->alt()->or($page->title()) ?>"
      >
    </figure>
  <?php endif ?>

  <div class="prose">
    <?= $page->text()->toBlocks() ?>
  </div>
</article>

<nav class="content-prose flex justify-between gap-lg mt-9xl text-sm" aria-label="Artikel-Navigation">
  <?php if ($prev = $page->prevListed()): ?>
    <a href="<?= $prev->url() ?>" class="decoration-[length:var(--un-decoration-thickness)] text-primary-700 hover:underline">
      &larr; <?= $prev->title()->escape() ?>
    </a>
  <?php else: ?>
    <span></span>
  <?php endif ?>

  <?php if ($next = $page->nextListed()): ?>
    <a href="<?= $next->url() ?>" class="decoration-[length:var(--un-decoration-thickness)] text-primary-700 hover:underline">
      <?= $next->title()->escape() ?> &rarr;
    </a>
  <?php endif ?>
</nav>

<?php endsnippet() ?>
