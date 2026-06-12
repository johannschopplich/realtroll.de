<?php

/** @var \Kirby\Cms\Block $block */

$file = $block->image()->toFile();
$src = $file?->url() ?? $block->src()->value();

if (!$src) {
  return;
}

?>
<figure>
  <img
    class="pixelated mx-auto"
    src="<?= $src ?>"
    <?php if ($file): ?>
      width="<?= $file->width() ?>"
      height="<?= $file->height() ?>"
    <?php endif ?>
    alt="<?= $block->alt()->escape() ?>"
  >
  <?php if ($block->caption()->isNotEmpty()): ?>
    <figcaption><?= $block->caption() ?></figcaption>
  <?php endif ?>
</figure>
