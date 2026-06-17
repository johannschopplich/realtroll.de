<?php

/** @var \Kirby\Cms\Block $block */

$pixelated = $block->pixelated()->isTrue();
$images = $block->images()->toFiles();

snippet('components/image-grid', [
  'images' => $images,
  'pixelated' => $pixelated,
  'layout' => $pixelated
    ? 'intrinsic'
    : ($images->count() <= 2 ? 'centered' : 'grid')
]);
