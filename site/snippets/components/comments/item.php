<?php

/** @var \Kirby\Cms\Page $comment */
/** @var IntlDateFormatter $dateFormatter */
/** @var bool $isReply */
/** @var bool $withReply */

// A nameless Panel user (empty account name) falls back to the stored visitor name.
$developer = $comment->developer();
$developerName = $developer?->name()->value();
$displayName = $developerName !== null && $developerName !== ''
  ? $developerName
  : $comment->name()->value();

$hasReplyButton = ($withReply ?? true) === true;

?>
<article id="kommentar-<?= $comment->slug() ?>" class="scroll-mt-8xl">
  <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1 mb-2">
    <?php if ($developer !== null): ?>
      <span
        class="
          inline-flex items-center gap-1.5
          px-2 py-1 font-medium text-sm leading-none
          text-white bg-primary-700 border-2 border-primary-700
        "
      ><?= esc($displayName) ?><span class="i-dinkie-icons-checkmark-circled shrink-0 translate-y-[-1px]" aria-hidden="true"></span><span class="sr-only"> (Entwickler)</span></span>
    <?php else: ?>
      <span class="font-medium text-sm text-primary-700"><?= esc($displayName) ?></span>
    <?php endif ?>
    <time
      datetime="<?= $comment->date()->toDate('c') ?>"
      class="label-caps shrink-0 ml-auto text-xs text-contrast-medium"
    ><?= $comment->date()->toDate($dateFormatter) ?></time>
  </div>

  <div class="p-lg bg-white border-2 border-primary-700<?= $isReply ? '' : ' shadow-solid md:p-xl' ?>">
    <div class="prose text-sm">
      <?= \RealTroll\Comments\CommentRenderer::render($comment->text()->value()) ?>
    </div>

    <?php if ($hasReplyButton): ?>
      <button
        type="button"
        class="group link-primary mt-sm min-h-6 py-1 text-sm"
        data-reply-to="<?= esc($comment->uuid()->toString()) ?>"
        data-reply-name="<?= esc($displayName) ?>"
        <?= $isReply === true ? 'data-reply-nested' : '' ?>
      >
        <span class="i-dinkie-icons-left-hook-arrow shrink-0" aria-hidden="true"></span>
        <span
          class="
            link-default [--un-decoration-color:transparent]
            group-hover:decoration-current group-focus-visible:decoration-current
          "
        >Antworten</span>
      </button>
    <?php endif ?>
  </div>
</article>
