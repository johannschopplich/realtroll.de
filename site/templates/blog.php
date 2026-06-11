<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true)

?>

<div class="content-lg text-center mb-7xl">
  <h1 class="editorial-title"><?= $page->title()->escape() ?></h1>
</div>

<div class="content-prose">
  <?php $articles = $page->children()->listed()->sortBy('date', 'desc') ?>
  <?php foreach ($articles as $article): ?>
    <article
      id="<?= \Kirby\Toolkit\Str::slug($article->date()->toDate('Y-m-d') . '-' . $article->slug()) ?>"
      class="relative border-2 border-primary-700 bg-white p-3xl md:p-5xl mb-5xl"
    >
      <?php snippet('components/corner-squares', ['size' => 3]) ?>

      <header class="mb-xl">
        <h2 class="font-heading text-xl text-primary-700 mb-xs leading-none scroll-mt-5xl">
          <a href="<?= $article->url() ?>" class="no-underline hover:underline decoration-[length:var(--un-decoration-thickness)]">
            <?= $article->title()->escape() ?>
          </a>
        </h2>
        <p class="text-sm text-contrast-medium">
          <time datetime="<?= $article->date()->toDate('c') ?>">
            <?= $article->date()->toDate('d.m.Y') ?>
          </time>
        </p>
      </header>

      <div class="prose">
        <?= $article->text()->toBlocks() ?>
      </div>
    </article>
  <?php endforeach ?>

  <?php if ($articles->count() === 0): ?>
    <p class="text-center text-contrast-medium">Noch keine Einträge.</p>
  <?php endif ?>
</div>

<?php endsnippet() ?>
