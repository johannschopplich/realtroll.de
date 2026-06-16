<?php

/** @var string|null $spacing */

?>
<div class="flex justify-center <?= $spacing ?? 'my-8xl' ?>" aria-hidden="true">
  <div class="relative w-48 border-t-2 border-primary-700">
    <?php snippet('components/corner-squares', [
      'corners' => ['top-left', 'top-right'],
      'size' => 2,
    ]) ?>
  </div>
</div>
