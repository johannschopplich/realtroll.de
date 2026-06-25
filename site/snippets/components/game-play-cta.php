<?php

/** @var \Kirby\Cms\Page $game */

$isPlayable = $game->gameFolder()->isNotEmpty();
$href = $isPlayable ? '/play/?game=' . $game->gameFolder() : $game->downloadLink();
$label = $isPlayable ? 'Spielen' : 'Download';
$icon = $isPlayable ? 'i-dinkie-icons-right-black-triangle-filled' : 'i-dinkie-icons-windows-alt';
$aria = $game->title()->escape() . ($isPlayable ? ' spielen' : ' herunterladen');

?>
<a
  href="<?= $href ?>"<?php e($isPlayable, ' target="_blank" rel="noopener"') ?>
  class="
    relative flex items-center justify-center
    -mx-3xl -mb-3xl mt-3xl gap-2 px-3xl py-3
    text-sm leading-none font-medium
    text-primary-700 border-t border-contrast-low
    hover:bg-primary-500 hover:text-white
    md:-mx-5xl md:-mb-5xl md:px-5xl
  "
  aria-label="<?= $aria ?>"
  data-play
>
  <span class="<?= $icon ?> translate-y-[-1px]" aria-hidden="true"></span>
  <?= $label ?>
</a>
