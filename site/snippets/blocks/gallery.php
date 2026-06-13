<?php

/** @var \Kirby\Cms\Block $block */

$images = $block->images()->toFiles();

if ($images->isEmpty()) {
  return;
}

?>
<div class="grid grid-cols-minmax-320px gap-lg">
  <?php foreach ($images as $file): ?>
    <figure class="text-center">
      <img class="w-full h-auto" src="<?= $file->url() ?>" alt="<?= $file->caption()->or($file->alt()) ?>">
      <?php if ($file->caption()->isNotEmpty()): ?>
        <figcaption class="my-2 text-xs">
          <p><?= $file->caption() ?></p>
        </figcaption>
      <?php endif ?>
    </figure>
  <?php endforeach ?>
</div>
