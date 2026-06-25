<?php

/** @var \Kirby\Cms\Page $game */

?>
<a
  href="<?= $game->url() ?>"
  class="link-primary not-prose"
  aria-label="Steckbrief von <?= $game->title()->escape() ?>"
>
  <span class="link-default [--un-decoration-color:transparent] group-hover:decoration-current group-has-[[data-play]:hover]:decoration-transparent">Steckbrief</span>
  <span
    class="
      i-dinkie-icons-white-right-backhand-index
      absolute left-full top-1/2
      ml-1
      opacity-0 -translate-y-1/2 transition-[opacity,margin]
      group-hover:opacity-100 group-hover:ml-2 group-has-[[data-play]:hover]:opacity-[0]
      max-md:opacity-100 max-md:ml-2 [@media(hover:none)]:opacity-100 [@media(hover:none)]:ml-2
      motion-reduce:transition-none
    "
    aria-hidden="true"
  ></span>
</a>
