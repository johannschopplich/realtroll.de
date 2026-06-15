<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true);

$logo = $page->logo()->toFile();

?>
<div class="md:content-lg">
  <div class="relative overflow-hidden md:border-2 md:border-primary-700">
    <?php if ($heroScreenshot = ($page->heroScreenshot()->toFile() ?? $page->screenshots()->first())): ?>
      <img class="pixelated absolute inset-0 size-full object-cover" src="<?= $heroScreenshot->url() ?>" alt="" aria-hidden="true">
    <?php endif ?>
    <div class="absolute inset-0 bg-primary-950/75" aria-hidden="true"></div>
    <div class="relative py-8xl px-3xl text-center md:py-9xl">
      <h1 class="<?= $logo ? 'sr-only' : 'display-title hyphenate' ?>"><?= $page->title()->escape() ?></h1>
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

<div class="content-lg mt-5xl text-center">
  <div class="columns gap-lg items-center justify-center">
    <?php if ($page->gameFolder()->isNotEmpty()): ?>
      <div class="column-narrow">
        <a href="/play/?game=<?= $page->gameFolder() ?>" class="button-primary gap-2" target="_blank">
          <span class="i-dinkie-icons-video-game-filled translate-y-[-1px]" aria-hidden="true"></span>
          Im Browser starten
        </a>
      </div>
    <?php endif ?>
    <div class="column-narrow">
      <a href="<?= $page->downloadLink() ?>" class="<?= $page->gameFolder()->isNotEmpty() ? 'button-primary-outlined' : 'button-primary' ?> gap-2">
        <span class="i-dinkie-icons-windows-alt translate-y-[-1px]" aria-hidden="true"></span>
        Download
      </a>
    </div>
  </div>
  <?php if ($page->downloadNote()->isNotEmpty()): ?>
    <p class="mt-lg mx-auto max-w-prose text-sm text-balance"><?= $page->downloadNote() ?></p>
  <?php endif ?>
</div>

<div class="mt-8xl">
  <?php snippet('components/prose-blocks', ['blocks' => $page->text()->toBlocks()]) ?>
</div>

<?php snippet('components/section-divider') ?>

<div class="columns gap-lg items-center justify-center text-center">
  <div class="column-narrow">
    <a href="<?= url() ?>" class="button-primary gap-2">
      <span class="i-dinkie-icons-white-left-backhand-index translate-y-[-1px]" aria-hidden="true"></span>
      Zurück zur Spieleliste
    </a>
  </div>
</div>

<?php endsnippet() ?>
