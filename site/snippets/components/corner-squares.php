<?php

/**
 * @var array $corners e.g. ['top-left','top-right','bottom-left','bottom-right']
 * @var int $size edge length in spacing units (1 unit = 0.25rem)
 */

$corners ??= ['top-left', 'top-right', 'bottom-left', 'bottom-right'];
$size ??= 3;
$edge = ($size * 0.25) . 'rem';
$positions = [
  'top-left' => '-top-px left-0 -translate-x-1/2 -translate-y-1/2',
  'top-right' => '-top-px right-0 translate-x-1/2 -translate-y-1/2',
  'bottom-left' => '-bottom-px left-0 -translate-x-1/2 translate-y-1/2',
  'bottom-right' => '-bottom-px right-0 translate-x-1/2 translate-y-1/2',
];

?>
<?php foreach ($corners as $corner): ?>
  <?php if (isset($positions[$corner])): ?>
    <div class="corner-square z-20 <?= $positions[$corner] ?>" style="width: <?= $edge ?>; height: <?= $edge ?>" aria-hidden="true"></div>
  <?php endif ?>
<?php endforeach ?>
