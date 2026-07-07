<?php

/** @var \Kirby\Cms\Pages $comments */
/** @var IntlDateFormatter $dateFormatter */
/** @var bool $acceptsComments */

$count = $comments->count();

?>
<div data-comment-thread>
  <h2 class="flex items-center gap-2 mb-4xl font-heading text-lg text-primary-700">
    <span class="i-dinkie-icons-speech-balloon-small shrink-0" aria-hidden="true"></span>
    <span><?= $count ?> <?= $count === 1 ? 'Kommentar' : 'Kommentare' ?></span>
  </h2>

  <?php snippet('components/comments/list', [
    'comments' => $comments,
    'dateFormatter' => $dateFormatter,
    'withReply' => $acceptsComments
  ]) ?>
</div>
