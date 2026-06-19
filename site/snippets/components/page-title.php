<?php

/**
 * @var string $title
 * @var string|null $eyebrow
 * @var string|null $intro
 * @var string $align
 * @var string|null $titleClass
 * @var string|null $class
 */

$eyebrow ??= null;
$intro ??= null;
$align ??= 'center';
$titleClass ??= null;
$class ??= null;

?>
<header <?= attr([
  'class' => trim(($align === 'center' ? 'text-center' : '') . ' ' . ($class ?? ''))
]) ?>>
  <?php if ($eyebrow): ?>
    <p class="label-caps mb-lg text-sm text-contrast-medium"><?= $eyebrow ?></p>
  <?php endif ?>
  <h1 class="<?= trim('display-title ' . ($titleClass ?? '')) ?>"><?= esc($title) ?></h1>
  <?php if ($intro): ?>
    <p class="mt-3xl text-balance"><?= $intro ?></p>
  <?php endif ?>
</header>
