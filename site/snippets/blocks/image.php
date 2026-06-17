<?php

/** @var \Kirby\Cms\Block $block */

$file = $block->image()->toFile();
if (!$file) return;

$isPixelated = $block->pixelated()->or(true)->isTrue();
$scale = $isPixelated ? max(1, $block->scale()->or(1)->toInt()) : 1;

?>
<figure>
  <?php snippet('components/pixel-image', [
    'file' => $file,
    'scale' => $scale,
    'class' => 'mx-auto',
    'alt' => $block->alt(),
    'pixelated' => $isPixelated
  ]) ?>
  <?php if ($block->caption()->isNotEmpty()): ?>
    <figcaption><?= $block->caption()->permalinksToUrls() ?></figcaption>
  <?php endif ?>
</figure>
