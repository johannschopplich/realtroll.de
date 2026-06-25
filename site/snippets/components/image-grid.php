<?php

/** @var \Kirby\Cms\Files $images */
/** @var bool $pixelated */
/** @var string $layout One of "grid" (fluid columns), "intrinsic" (native size, centered) or "centered" (capped width, centered). */

$pixelated ??= false;
$layout ??= 'grid';

if ($images->isEmpty()) return;

$isIntrinsic = $layout === 'intrinsic';
$isCentered = $layout === 'centered';
$isFlex = $isIntrinsic || $isCentered;

?>
<div class="<?php e($isFlex, 'flex flex-wrap items-start justify-center gap-lg', 'grid grid-cols-minmax-320px gap-lg') ?>">
  <?php foreach ($images as $file): ?>
    <figure class="<?= trim(($isCentered ? 'basis-[24rem] min-w-0 max-w-full ' : '') . 'text-center') ?>">
      <img <?= attr([
        'class' => trim(($pixelated ? 'pixelated ' : '') . ($isIntrinsic ? 'mx-auto max-w-full h-auto' : 'w-full h-auto')),
        'src' => $file->url(),
        'width' => $isIntrinsic ? $file->width() : null,
        'height' => $isIntrinsic ? $file->height() : null,
        'alt' => $file->caption()->or($file->alt())
      ]) ?>>
      <?php if ($file->caption()->isNotEmpty()): ?>
        <figcaption class="<?= trim(($isIntrinsic ? 'mx-auto max-w-[16rem] ' : '') . 'mt-2 text-xs font-medium') ?>">
          <p><?= $file->caption()->permalinksToUrls() ?></p>
        </figcaption>
      <?php endif ?>
    </figure>
  <?php endforeach ?>
</div>
