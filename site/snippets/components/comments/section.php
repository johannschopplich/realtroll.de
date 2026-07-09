<?php

/** @var ArticlePage $page */

$comments = $page->comments()->sortBy('date', 'asc');
$acceptsComments = $page->acceptsComments();

if ($acceptsComments === false && $comments->count() === 0) return;

$dateFormatter = dateFormatter(IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

?>
<comment-section
  class="block"
  data-page-uuid="<?= esc($page->uuid()->toString()) ?>"
  data-token-url="<?= esc(url('kommentare/token')) ?>"
  data-submit-url="<?= esc(url('kommentare')) ?>"
>
<section id="kommentare" class="scroll-mt-8xl">
  <?php snippet('components/comments/thread', [
    'comments' => $comments,
    'dateFormatter' => $dateFormatter,
    'acceptsComments' => $acceptsComments
  ]) ?>
  <?php if ($acceptsComments === true): ?>
    <?php snippet('components/comments/form') ?>
  <?php else: ?>
    <p class="text-sm text-contrast-medium">Für diesen Artikel sind Kommentare derzeit geschlossen.</p>
  <?php endif ?>
</section>
</comment-section>
