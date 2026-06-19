<?php

/**
 * @var \Kirby\Filesystem\Asset|\Kirby\Cms\File $image
 * @var string $alt
 * @var int|null $width
 * @var string|null $text
 * @var bool $showFaces
 */

$width ??= $image->width();
$text ??= null;
$showFaces ??= false;

?>
<header
  class="<?= trim(implode(' ', [
    'pixelated relative flex items-center justify-center pt-6xl bg-starfield md:pt-7xl',
    $showFaces ? 'pb-8xl md:pb-[calc(var(--spacing-9xl)+var(--spacing-xl))]' : 'pb-6xl md:pb-7xl'
  ]), ' ') ?>"
>
  <div class="flex flex-col items-center">
    <img <?= attr([
      'class' => 'pixelated md:scale-[1.5]',
      'src' => $image->url(),
      'width' => $width,
      'height' => (int) round($width / $image->ratio()),
      'alt' => $alt
    ]) ?>>
    <?php if ($text): ?>
      <p class="mt-3xl px-3xl max-w-prose font-medium text-center text-balance tracking-tight md:mt-5xl"><?= $text ?></p>
    <?php endif ?>
  </div>
  <?php if ($showFaces): ?>
    <img
      class="pixelated absolute bottom-0 left-1/2 z-1 max-w-none origin-bottom -translate-x-1/2 -translate-y-[2px] scale-[1.5] md:scale-[2]"
      src="<?= asset('assets/img/editorial-gesichter.png')->url() ?>"
      width="207"
      height="42"
      alt=""
    >
  <?php endif ?>
</header>
