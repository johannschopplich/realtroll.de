<?php

/** @var \Kirby\Cms\Block $block */

$file = $block->image()->toFile();
$src = $file?->url() ?? $block->src()->value();

if (!$src) {
  return;
}

$isPixelated = $block->pixelated()->or(true)->isTrue();
$scale = $isPixelated ? max(1, $block->scale()->or(1)->toInt()) : 1;
$width = $file?->width();
$height = $file?->height();

?>
<figure>
  <img <?= attr([
    'class' => trim('mx-auto ' . ($isPixelated ? 'pixelated' : '')),
    'src' => $src,
    'width' => $width ? $width * $scale : null,
    'height' => $height ? $height * $scale : null,
    'alt' => $block->alt()->escape(),
  ]) ?>>
  <?php if ($block->caption()->isNotEmpty()): ?>
    <figcaption><?= $block->caption() ?></figcaption>
  <?php endif ?>
</figure>
