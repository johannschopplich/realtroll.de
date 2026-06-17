<?php

/** @var \Kirby\Cms\Files $images */
/** @var bool $pixelated */
/** @var bool $intrinsic Render images at their native size (centered, never upscaled) instead of filling the cell. */

$pixelated ??= false;
$intrinsic ??= false;

if ($images->isEmpty()) return;

?>
<div class="<?= $intrinsic ? 'flex flex-wrap items-start justify-center gap-lg' : 'grid grid-cols-minmax-320px gap-lg' ?>">
  <?php foreach ($images as $file): ?>
    <figure class="text-center">
      <img <?= attr([
        'class' => trim(($pixelated ? 'pixelated ' : '') . ($intrinsic ? 'mx-auto max-w-full h-auto' : 'w-full h-auto')),
        'src' => $file->url(),
        'width' => $intrinsic ? $file->width() : null,
        'height' => $intrinsic ? $file->height() : null,
        'alt' => $file->caption()->or($file->alt()),
      ]) ?>>
      <?php if ($file->caption()->isNotEmpty()): ?>
        <figcaption class="<?= trim(($intrinsic ? 'mx-auto max-w-[16rem] ' : '') . 'mt-2 text-xs font-medium') ?>">
          <p><?= $file->caption() ?></p>
        </figcaption>
      <?php endif ?>
    </figure>
  <?php endforeach ?>
</div>
