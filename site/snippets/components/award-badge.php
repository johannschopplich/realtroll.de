<?php

/**
 * @var \Kirby\Cms\File|null $file
 * @var string $alt
 * @var string $class
 */

$class ??= '';
$alt ??= '';

?>
<?php if (!$file): ?>
  <span class="inline-flex items-center px-2 py-1 font-mono text-xs leading-tight text-contrast-medium border border-dashed border-primary-700/40"><?= esc($alt ?: 'Bild fehlt') ?></span>
<?php else: ?>
  <img
    class="pixelated <?= $class ?>"
    src="<?= $file->url() ?>"
    width="<?= $file->width() ?>"
    height="<?= $file->height() ?>"
    alt="<?= esc($alt) ?>"
    loading="lazy"
  >
<?php endif ?>
