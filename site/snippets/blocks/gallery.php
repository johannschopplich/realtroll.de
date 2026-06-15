<?php

/** @var \Kirby\Cms\Block $block */

snippet('components/image-grid', [
  'images' => $block->images()->toFiles(),
]);
