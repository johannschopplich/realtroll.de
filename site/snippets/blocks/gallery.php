<?php

/** @var \Kirby\Cms\Block $block */

$pixelated = $block->pixelated()->isTrue();

snippet('components/image-grid', [
  'images' => $block->images()->toFiles(),
  'pixelated' => $pixelated,
  'intrinsic' => $pixelated
]);
