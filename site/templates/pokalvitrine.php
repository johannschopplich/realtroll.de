<?php

/** @var \Kirby\Cms\Page $page */

$entries = $page->awards()->toStructure()->sortBy('year', 'desc');

// Clearly-landscape banners bind to the column width; everything else binds to
// height. A height-only rule sized each banner by its ratio, so narrow ones
// shrank below the wide ones and the widest overflowed the column.
$badgeClass = function ($file) {
  $ratio = $file ? max(0.01, $file->ratio()) : 1;
  if ($ratio <= 0.85) {
    return 'w-auto h-28 md:h-32'; // Portrait trophies
  }
  if ($ratio >= 1.7) {
    return 'h-auto w-32 md:w-40'; // Horizontal banners fill the column
  }
  return 'w-auto h-14 md:h-16'; // Square-ish icons & near-square badges
};

snippet('layouts/default', slots: true);

?>
<div class="content-prose">
  <?php snippet('components/page-title', [
    'eyebrow' => 'Auszeichnungen &amp; Presse',
    'title' => $page->title()->value(),
    'intro' => $page->intro()->isNotEmpty() ? $page->intro()->value() : null
  ]) ?>
</div>

<div class="content-lg mt-7xl">
  <?php $lastYear = null ?>
  <?php foreach ($entries as $entry): ?>
    <?php $year = $entry->year()->value() ?>
    <?php if ($year !== $lastYear): $lastYear = $year ?>
      <div class="sticky top-14 z-2 grid grid-cols-[2rem_1fr] items-center gap-x-lg bg-theme-background md:grid-cols-[3rem_1fr]">
        <span class="flex justify-center text-primary-700">
          <?= svg('assets/img/diamond.svg') ?>
        </span>
        <h2 class="py-sm font-heading text-3xl leading-none text-primary-700"><?= $year ?></h2>
      </div>
    <?php endif ?>

    <div class="grid grid-cols-[2rem_1fr] gap-x-lg md:grid-cols-[3rem_1fr]">
      <div class="relative">
        <span class="absolute left-1/2 top-0 bottom-0 w-0.5 bg-contrast-lower -translate-x-1/2" aria-hidden="true"></span>
      </div>

      <div class="grid items-start gap-x-4xl gap-y-lg py-3xl md:grid-cols-[10rem_1fr]">
        <div class="flex flex-wrap items-end gap-3 md:justify-center">
          <?php foreach ($entry->badges()->toFiles() as $badge): ?>
            <?php snippet('components/award-badge', [
              'file' => $badge,
              'alt' => $entry->label()->value(),
              'class' => $badgeClass($badge)
            ]) ?>
          <?php endforeach ?>
        </div>

        <div>
          <?php $games = $entry->games()->toPages() ?>
          <?php if ($games->isNotEmpty()): ?>
            <p class="mb-1 text-sm text-contrast-medium">
              <?php foreach ($games->values() as $index => $game): ?>
                <?php if ($index > 0): ?><span class="text-primary-700/40" aria-hidden="true"> &middot; </span><?php endif ?>
                <a href="<?= $game->url() ?>" class="link-default"><?= $game->title()->escape() ?></a>
              <?php endforeach ?>
            </p>
          <?php endif ?>
          <h3 class="font-heading text-xl leading-none text-primary-700"><?= $entry->label()->escape() ?></h3>
          <div class="prose mt-2 text-sm <?php e($entry->type()->value() === 'press', '[&_p]:italic') ?>"><?= $entry->caption() ?></div>
        </div>
      </div>
    </div>
  <?php endforeach ?>
</div>

<?php endsnippet() ?>
