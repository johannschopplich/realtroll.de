<?php

/** @var \Kirby\Cms\Block $block */

$file = $block->image()->toFile();
$src = $file?->url() ?? $block->src()->value();

if (!$src) {
  return;
}

?>
<figure>
  <img <?= attr([
    'class' => trim('mx-auto ' . ($block->pixelated()->or(true)->isTrue() ? 'pixelated' : '')),
    'src' => $src,
    'width' => $file?->width(),
    'height' => $file?->height(),
    'alt' => $block->alt()->escape(),
  ]) ?>>
  <?php if ($block->caption()->isNotEmpty()): ?>
    <figcaption><?= $block->caption() ?></figcaption>
  <?php endif ?>
</figure>
