<?php

/** @var \Kirby\Cms\Blocks $blocks */

$breakoutTypes = ['screenshots', 'gallery'];

$sections = [];
$proseBlocks = [];

foreach ($blocks as $block) {
  if (in_array($block->type(), $breakoutTypes, true)) {
    if ($proseBlocks) {
      $sections[] = ['type' => 'prose', 'blocks' => $proseBlocks];
      $proseBlocks = [];
    }
    $sections[] = ['type' => 'breakout', 'block' => $block];
  } else {
    $proseBlocks[] = $block;
  }
}

if ($proseBlocks) {
  $sections[] = ['type' => 'prose', 'blocks' => $proseBlocks];
}

?>
<?php foreach ($sections as $section): ?>
  <?php if ($section['type'] === 'breakout'): ?>
    <div class="content-lg my-3xl">
      <?= $section['block'] ?>
    </div>
  <?php else: ?>
    <div class="content-prose">
      <div class="prose">
        <?php foreach ($section['blocks'] as $block): ?>
          <?= $block ?>
        <?php endforeach ?>
      </div>
    </div>
  <?php endif ?>
<?php endforeach ?>
