<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true)

?>

<?php
$logo = $page->logo()->toFile();
$heroScreenshot = $page->screenshots()->toFiles()->first();
?>

<div class="content-lg">
  <div class="relative border-2 border-primary-700 overflow-hidden">
    <?php if ($heroScreenshot): ?>
      <img class="absolute inset-0 size-full object-cover pixelated" src="<?= $heroScreenshot->url() ?>" alt="" aria-hidden="true">
    <?php endif ?>
    <div class="absolute inset-0 bg-primary-950/75" aria-hidden="true"></div>
    <div class="relative py-8xl md:py-9xl px-3xl text-center">
      <h1 class="<?= $logo ? 'sr-only' : 'editorial-title hyphenate' ?>"><?= $page->title()->escape() ?></h1>
      <?php if ($logo): ?>
        <img
          class="pixelated mx-auto max-w-full h-auto"
          src="<?= $logo->url() ?>"
          width="<?= $logo->width() * 2 ?>"
          height="<?= $logo->height() * 2 ?>"
          alt=""
        >
      <?php endif ?>
      <div class="mt-xl">
        <?php snippet('components/game-chips', ['game' => $page, 'classes' => 'justify-center', 'appearance' => 'glass', 'size' => 'lg']) ?>
      </div>
    </div>
  </div>
</div>

<div class="content-lg text-center mt-5xl">
  <div class="columns gap-lg items-center justify-center">
    <?php if ($page->gameFolder()->isNotEmpty()): ?>
      <div class="column-narrow">
        <a href="/play/?game=<?= $page->gameFolder() ?>" class="button-primary" target="_blank">
          Online spielen!
        </a>
      </div>
    <?php endif ?>
    <div class="column-narrow">
      <a href="<?= $page->downloadLink() ?>" class="<?= $page->gameFolder()->isNotEmpty() ? 'button-primary-outlined' : 'button-primary' ?>">
        Download (Windows)
      </a>
    </div>
  </div>
</div>

<div class="content max-w-prose mt-8xl">
  <div class="prose">
    <?= $page->intro()->toBlocks() ?>
  </div>
</div>

<?php snippet('components/section-divider') ?>

<div id="screenshots" class="content-lg">
  <div class="grid grid-cols-minmax-320px gap-lg">
    <?php foreach ($page->screenshots()->toFiles() as $file): ?>
      <figure class="text-center">
        <img class="w-full h-auto pixelated" src="<?= $file->url() ?>" alt="<?= $file->caption()->or($file->alt()) ?>">
        <?php if ($file->caption()->isNotEmpty()): ?>
          <figcaption class="my-2 text-xs">
            <p><?= $file->caption() ?></p>
          </figcaption>
        <?php endif ?>
      </figure>
    <?php endforeach ?>
  </div>
</div>

<?php $text = $page->text()->toBlocks() ?>
<?php if ($text->isNotEmpty()): ?>
  <?php snippet('components/section-divider') ?>
  <div class="content max-w-prose">
    <div class="prose">
      <?= $text ?>
    </div>
  </div>
<?php endif ?>

<?php snippet('components/section-divider') ?>

<div class="columns gap-lg items-center justify-center text-center">
  <?php foreach ($page->children()->listed() as $subpage): ?>
    <div class="column-narrow">
      <a href="<?= $subpage->url() ?>" class="button-primary-outlined">
        <?= $subpage->title()->escape() ?>
      </a>
    </div>
  <?php endforeach ?>
  <div class="column-narrow">
    <a href="<?= url() ?>" class="button-primary">
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php endsnippet() ?>
