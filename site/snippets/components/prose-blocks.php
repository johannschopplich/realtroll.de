<?php

/** @var \Kirby\Cms\Blocks $blocks */

$breakoutTypes = ['screenshots', 'gallery'];

$sections = [];
$proseBlocks = [];
$characterBlocks = [];

foreach ($blocks as $block) {
  $type = $block->type();

  if ($type === 'character') {
    if ($proseBlocks) {
      $sections[] = ['type' => 'prose', 'blocks' => $proseBlocks];
      $proseBlocks = [];
    }
    $characterBlocks[] = $block;
    continue;
  }

  if ($characterBlocks) {
    $sections[] = ['type' => 'characters', 'blocks' => $characterBlocks];
    $characterBlocks = [];
  }

  if (in_array($type, $breakoutTypes, true)) {
    if ($proseBlocks) {
      $sections[] = ['type' => 'prose', 'blocks' => $proseBlocks];
      $proseBlocks = [];
    }
    $sections[] = ['type' => 'breakout', 'block' => $block];
  } else {
    $proseBlocks[] = $block;
  }
}

if ($characterBlocks) {
  $sections[] = ['type' => 'characters', 'blocks' => $characterBlocks];
}

if ($proseBlocks) {
  $sections[] = ['type' => 'prose', 'blocks' => $proseBlocks];
}

?>
<?php foreach ($sections as $section): ?>
  <?php if ($section['type'] === 'characters'): ?>
    <div class="content-prose my-3xl">
      <div class="<?= trim('grid gap-x-2xl gap-y-3xl' . (count($section['blocks']) > 1 ? ' sm:grid-cols-2' : '')) ?>">
        <?php foreach ($section['blocks'] as $block): ?>
          <?= $block ?>
        <?php endforeach ?>
      </div>
    </div>
  <?php elseif ($section['type'] === 'breakout'): ?>
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
