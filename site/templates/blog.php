<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', [
  'header' => [
    'image' => asset('assets/img/neues.gif'),
    'alt' => 'Neues',
    'width' => 120,
    'text' => 'Devlog &amp; Notizen aus der Werkstatt'
  ]
], slots: true);

$articles = $page->children()->listed()->sortBy('date', 'desc')->paginate(20);

?>

<h1 class="sr-only"><?= $page->title()->escape() ?></h1>

<div class="content-prose">
  <?php foreach ($articles as $article): ?>
    <article
      id="<?= \Kirby\Toolkit\Str::slug($article->date()->toDate('Y-m-d') . '-' . $article->slug()) ?>"
      class="relative p-3xl mb-5xl bg-white border-2 border-primary-700 md:p-5xl"
    >
      <?php snippet('components/corner-squares', ['size' => 3]) ?>

      <header class="mb-xl">
        <h2 class="mb-xs scroll-mt-8xl font-heading text-xl leading-none text-primary-700">
          <a href="<?= $article->url() ?>" class="link-default">
            <?= $article->title()->escape() ?>
          </a>
        </h2>
        <p class="text-sm text-contrast-medium">
          <time datetime="<?= $article->date()->toDate('c') ?>">
            <?= $article->date()->toDate('d.m.Y') ?>
          </time>
        </p>
      </header>

      <div class="prose text-sm">
        <?= $article->text()->toBlocks() ?>
      </div>
    </article>
  <?php endforeach ?>

  <?php if ($articles->pagination()->total() === 0): ?>
    <p class="text-center text-contrast-medium">Noch keine Einträge.</p>
  <?php endif ?>

  <?php if ($articles->pagination()->hasPages()): ?>
    <nav class="flex justify-between gap-lg mt-9xl text-sm" aria-label="Seitennavigation">
      <?php if ($prevUrl = $articles->pagination()->prevPageURL()): ?>
        <a href="<?= $prevUrl ?>" class="link-default text-primary-700" rel="prev">&larr; Neuere</a>
      <?php else: ?>
        <span></span>
      <?php endif ?>

      <span class="text-contrast-medium">
        Seite <?= $articles->pagination()->page() ?> / <?= $articles->pagination()->pages() ?>
      </span>

      <?php if ($nextUrl = $articles->pagination()->nextPageURL()): ?>
        <a href="<?= $nextUrl ?>" class="link-default text-primary-700" rel="next">Ältere &rarr;</a>
      <?php else: ?>
        <span></span>
      <?php endif ?>
    </nav>
  <?php endif ?>
</div>

<?php endsnippet() ?>
