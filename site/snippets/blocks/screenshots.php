<?php

/** @var \Kirby\Cms\Block $block */

/** @var \Kirby\Cms\Page */
$parent = $block->parent();
$screenshots = $parent->screenshots()->toFiles();

if ($screenshots->isEmpty()) {
  return;
}

?>
<div class="grid grid-cols-minmax-320px gap-lg">
  <?php foreach ($screenshots as $file): ?>
    <figure class="text-center">
      <img class="pixelated w-full h-auto" src="<?= $file->url() ?>" alt="<?= $file->caption()->or($file->alt()) ?>">
      <?php if ($file->caption()->isNotEmpty()): ?>
        <figcaption class="my-2 text-xs font-medium">
          <p><?= $file->caption() ?></p>
        </figcaption>
      <?php endif ?>
    </figure>
  <?php endforeach ?>
</div>
