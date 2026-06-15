<?php

/** @var \Kirby\Cms\Files $images */
/** @var bool $pixelated */

$pixelated ??= false;

if ($images->isEmpty()) {
  return;
}

?>
<div class="grid grid-cols-minmax-320px gap-lg">
  <?php foreach ($images as $file): ?>
    <figure class="text-center">
      <img <?= attr([
        'class' => trim(($pixelated ? 'pixelated ' : '') . ' w-full h-auto'),
        'src' => $file->url(),
        'alt' => $file->caption()->or($file->alt()),
      ]) ?>>
      <?php if ($file->caption()->isNotEmpty()): ?>
        <figcaption class="my-2 text-xs font-medium">
          <p><?= $file->caption() ?></p>
        </figcaption>
      <?php endif ?>
    </figure>
  <?php endforeach ?>
</div>
