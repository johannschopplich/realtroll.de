<?php

/** @var \Kirby\Cms\Block $block */

$file = $block->image()->toFile();
$scale = $file && $file->width() <= 48 ? 3 : 1;

?>
<article>
  <div class="relative flex items-end gap-xs border-b-2 border-primary-700">
    <?php snippet('components/corner-squares', [
      'corners' => ['bottom-right'],
      'size' => 2,
    ]) ?>
    <?php if ($file): ?>
      <div class="flex flex-none items-end justify-center mb-[-2px] size-24 bg-theme-background">
        <img
          class="pixelated"
          src="<?= $file->url() ?>"
          width="<?= $file->width() * $scale ?>"
          height="<?= $file->height() * $scale ?>"
          alt=""
        >
      </div>
    <?php endif ?>
    <div class="pb-2">
      <h3 class="my-0 font-heading text-lg leading-none text-primary-700"><?= $block->name()->escape() ?></h3>
      <?php if ($block->role()->isNotEmpty()): ?>
        <span class="label-caps text-xs text-primary-500"><?= $block->role()->escape() ?></span>
      <?php endif ?>
    </div>
  </div>
  <div class="prose hyphenate mt-lg text-sm">
    <?= $block->text() ?>
  </div>
</article>
