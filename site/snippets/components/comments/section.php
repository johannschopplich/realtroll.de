<?php

/** @var ArticlePage $page */

$comments = $page->comments()->sortBy('date', 'asc');
$count = $comments->count();
$acceptsComments = $page->acceptsComments();

if ($acceptsComments === false && $count === 0) return;

$dateFormatter = new IntlDateFormatter('de_DE', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);

?>
<comment-section
  class="block"
  data-page-uuid="<?= esc($page->uuid()->toString()) ?>"
  data-token-url="<?= esc(url('kommentare/token')) ?>"
  data-submit-url="<?= esc(url('kommentare')) ?>"
>
<section id="kommentare" class="scroll-mt-8xl">
  <h2 class="flex items-center gap-2 mb-4xl font-heading text-lg text-primary-700">
    <span class="i-dinkie-icons-speech-balloon-small shrink-0" aria-hidden="true"></span>
    <span><?= $count ?> <?= $count === 1 ? 'Kommentar' : 'Kommentare' ?></span>
  </h2>

  <?php snippet('components/comments/list', [
    'comments' => $comments,
    'dateFormatter' => $dateFormatter,
    'withReply' => $acceptsComments
  ]) ?>
  <?php if ($acceptsComments === true): ?>
    <?php snippet('components/comments/form') ?>
  <?php else: ?>
    <p class="text-sm text-contrast-medium">Für diesen Artikel sind Kommentare derzeit geschlossen.</p>
  <?php endif ?>
</section>
</comment-section>
