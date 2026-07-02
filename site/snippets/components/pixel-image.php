<?php

/**
 * Renders an image at its intrinsic size, optionally scaled up in whole steps.
 * The width/height attributes reserve the correct box so the layout never
 * shifts; `pixelated` keeps small pixel art crisp when scaled.
 *
 * Pass `alt` raw (unescaped) – attr() escapes attribute values itself.
 *
 * @var \Kirby\Cms\File $file
 * @var int $scale
 * @var string $class
 * @var \Kirby\Content\Field|string $alt
 * @var bool $pixelated
 */

$scale ??= 1;
$class ??= '';
$alt ??= '';
$pixelated ??= true;

if (!$file) return;

?>
<img <?= attr([
  'class' => trim(($pixelated ? 'pixelated ' : '') . $class),
  // Keeps pixel art crisp in contexts without the site's CSS, e.g. feed readers
  'style' => $pixelated ? 'image-rendering: pixelated' : null,
  'src' => $file->url(),
  'width' => $file->width() ? $file->width() * $scale : null,
  'height' => $file->height() ? $file->height() * $scale : null,
  'alt' => $alt
]) ?>>
