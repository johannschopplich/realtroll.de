<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true)

?>

<div class="content-lg mb-7xl text-center">
  <h1 class="display-title"><?= $page->title()->escape() ?></h1>
</div>

<div class="content-prose">
  <?php $articles = $page->children()->listed()->sortBy('date', 'desc') ?>
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
