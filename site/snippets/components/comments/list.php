<?php

/** @var \Kirby\Cms\Pages $comments Visible comments, oldest-first */
/** @var IntlDateFormatter $dateFormatter */
/** @var bool $withReply Reply buttons off when the article no longer accepts comments */

$topLevel = [];
$repliesByParent = [];

foreach ($comments as $comment) {
  $parent = $comment->topLevelParent($comments);

  if ($parent === null) {
    $topLevel[] = $comment;
  } else {
    $repliesByParent[$parent->id()][] = $comment;
  }
}

?>
<?php if ($topLevel === []): ?>
  <p class="mb-5xl text-contrast-medium">Noch nichts hier. Schreib den ersten Kommentar.</p>
<?php else: ?>
  <ol class="flex flex-col gap-4xl mb-7xl list-none">
    <?php foreach ($topLevel as $comment): ?>
      <li>
        <?php snippet('components/comments/item', [
          'comment' => $comment,
          'dateFormatter' => $dateFormatter,
          'isReply' => false,
          'withReply' => $withReply ?? true
        ]) ?>

        <?php $replies = $repliesByParent[$comment->id()] ?? [] ?>
        <?php if ($replies !== []): ?>
          <?php $lastReplyIndex = count($replies) - 1 ?>
          <ul class="flex flex-col gap-2xl mt-2xl ml-2xl list-none md:ml-4xl">
            <?php foreach ($replies as $replyIndex => $reply): ?>
              <?php
                $isFirstReply = $replyIndex === 0;
                $isLastReply = $replyIndex === $lastReplyIndex;

                $trunkExtentClass = match (true) {
                  $isLastReply === false => '-bottom-6',
                  $isFirstReply === true => 'h-8',
                  default => 'h-2.5'
                };
              ?>
              <li class="relative">
                <span
                  class="
                    absolute <?= $isFirstReply ? 'top-[calc((var(--spacing-2xl)*-1)+4px)]' : 'top-0' ?> -left-3 <?= $trunkExtentClass ?>
                    w-[2px] bg-contrast-lower md:-left-5
                  "
                  aria-hidden="true"
                ></span>
                <span class="absolute -left-3 top-2.5 h-[2px] w-2 bg-contrast-lower md:-left-5 md:w-4" aria-hidden="true"></span>
                <?php snippet('components/comments/item', [
                  'comment' => $reply,
                  'dateFormatter' => $dateFormatter,
                  'isReply' => true,
                  'withReply' => $withReply ?? true
                ]) ?>
              </li>
            <?php endforeach ?>
          </ul>
        <?php endif ?>
      </li>
    <?php endforeach ?>
  </ol>
<?php endif ?>
