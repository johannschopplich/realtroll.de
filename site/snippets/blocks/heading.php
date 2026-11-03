<?php

/** @var \Kirby\Cms\Block $block */

$level = $block->level()->value();

if (!in_array($level, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
  $level = 'h2';
}

?>
<<?= $level ?>><?= $block->text()->permalinksToUrls() ?></<?= $level ?>>
