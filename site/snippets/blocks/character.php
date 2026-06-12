<?php

/** @var \Kirby\Cms\Block $block */

$file = $block->image()->toFile();
$scale = $file && $file->width() <= 48 ? 3 : 1;

?>
<div class="flex flex-col gap-lg p-lg bg-theme-background border-2 border-primary-700 shadow-[4px_4px_0_var(--un-color-primary-700)] sm:flex-row">
  <div class="flex flex-none items-center justify-center self-center size-28 bg-primary-100 border-2 border-t-primary-50 border-l-primary-50 border-b-primary-400 border-r-primary-400 sm:self-start">
    <?php if ($file): ?>
      <img
        class="pixelated"
        src="<?= $file->url() ?>"
        width="<?= $file->width() * $scale ?>"
        height="<?= $file->height() * $scale ?>"
        alt=""
      >
    <?php endif ?>
  </div>
  <div class="flex-1">
    <div class="flex flex-wrap items-center gap-2">
      <h3 class="my-0 font-heading text-lg text-primary-700"><?= $block->name()->escape() ?></h3>
      <?php if ($block->role()->isNotEmpty()): ?>
        <span class="game-chip-bevel-base"><?= $block->role()->escape() ?></span>
      <?php endif ?>
    </div>
    <div class="mt-sm space-y-4">
      <?= $block->text() ?>
    </div>
  </div>
</div>
