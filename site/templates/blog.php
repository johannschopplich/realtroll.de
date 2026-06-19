<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', [
  'header' => [
    'image' => asset('assets/img/neues.gif'),
    'alt' => 'Neues',
    'width' => 120,
    'text' => 'Devlog &amp; Notizen aus der Werkstatt'
  ],
  'hasFooter' => false
], slots: true);

$dateFormatter = new IntlDateFormatter('de_DE', IntlDateFormatter::LONG, IntlDateFormatter::NONE);

$perPage = 20;
$all = $page->children()->listed()->sortBy('date', 'desc');
$articles = $all->paginate($perPage);
$pagination = $articles->pagination();
$currentPage = $pagination->page();
$pages = $pagination->pages();

$yearPage = [];
$index = 0;
foreach ($all as $entry) {
  $year = (int) $entry->date()->toDate('Y');
  if (!isset($yearPage[$year])) {
    $yearPage[$year] = intdiv($index, $perPage) + 1;
  }
  $index++;
}
$years = array_keys($yearPage);
$maxYear = $years ? max($years) : (int) date('Y');
$minYear = $years ? min($years) : $maxYear;
$span = max(1, $maxYear - $minYear);

$currentYear = $articles->first() ? (int) $articles->first()->date()->toDate('Y') : $maxYear;
$playheadPos = ($maxYear - $currentYear) / $span * 100;

$window = array_values(array_filter(
  range(1, $pages),
  fn ($n) => $n === 1 || $n === $pages || abs($n - $currentPage) <= 1
));

?>

<h1 class="sr-only"><?= $page->title()->escape() ?></h1>

<?php if ($pagination->total() === 0): ?>
  <p class="content-prose text-center text-contrast-medium">Noch keine Einträge.</p>
<?php else: ?>
  <div class="bg-starfield pb-4xl">
    <div class="content-lg">
      <?php foreach ($articles as $article): ?>
        <article class="blog-card relative p-3xl mb-5xl max-w-[min(var(--container-prose),90%)] bg-white border-2 border-primary-700 [&:nth-child(odd)]:mr-auto [&:nth-child(even)]:ml-auto last:mb-0 md:p-5xl">
          <?php snippet('components/corner-squares', ['size' => 3]) ?>

          <header class="mb-xl">
            <p class="label-caps mb-1 text-sm text-contrast-medium">
              <time datetime="<?= $article->date()->toDate('c') ?>"><?= $article->date()->toDate($dateFormatter) ?></time>
            </p>
            <h2 class="font-heading text-xl leading-none text-primary-700">
              <a href="<?= $article->url() ?>" class="link-default"><?= $article->title()->escape() ?></a>
            </h2>
          </header>

          <div class="prose text-sm">
            <?= $article->text()->toBlocks() ?>
          </div>
        </article>
      <?php endforeach ?>
    </div>
  </div>

  <?php if ($pagination->hasPages()): ?>
    <div class="content-prose">
      <nav class="flex flex-wrap items-center justify-center gap-2 mt-4xl" aria-label="Seitennavigation">
        <?php $previous = null ?>
        <?php foreach ($window as $n): ?>
          <?php if ($previous !== null && $n - $previous > 1): ?>
            <span class="px-1 text-contrast-medium" aria-hidden="true">…</span>
          <?php endif ?>
          <?php if ($n === $currentPage): ?>
            <span
              class="inline-flex items-center justify-center min-w-9 h-9 px-2 font-heading leading-none text-white bg-primary-700 border-2 border-primary-700"
              aria-current="page"
            ><?= $n ?></span>
          <?php else: ?>
            <a <?= attr([
              'href' => $pagination->pageUrl($n),
              'class' => 'group inline-flex',
              'rel' => match (true) {
                $n === $currentPage - 1 => 'prev',
                $n === $currentPage + 1 => 'next',
                default => null
              }
            ]) ?>>
              <span class="inline-flex items-center justify-center min-w-9 h-9 px-2 font-heading leading-none text-primary-700 bg-white border-2 border-primary-700 transition-[transform,box-shadow] group-hover:-translate-x-0.5 group-hover:-translate-y-0.5 group-hover:shadow-solid group-focus-visible:-translate-x-0.5 group-focus-visible:-translate-y-0.5 group-focus-visible:shadow-solid motion-reduce:transition-none"><?= $n ?></span>
            </a>
          <?php endif ?>
          <?php $previous = $n ?>
        <?php endforeach ?>
      </nav>
    </div>

    <div class="absolute inset-x-0 -bottom-px z-1 h-12 pointer-events-none">
      <nav class="mx-auto h-full max-w-3xl px-2xl" aria-label="Zeitachse – zu Jahr springen">
        <div class="relative h-full">
          <?php foreach ($years as $year): ?>
            <?php
            $pos = ($maxYear - $year) / $span * 100;
            $isLustrum = $year % 5 === 0;
            $isEnd = $year === $maxYear || $year === $minYear;
            $isMajor = $isLustrum || $isEnd;
            $isCurrent = $year === $currentYear;
            $isMobileMark = $isEnd || ($isLustrum && abs($year - $maxYear) > 2 && abs($year - $minYear) > 2);
            ?>
            <a
              href="<?= $pagination->pageUrl($yearPage[$year]) ?>"
              class="group absolute bottom-0 block w-6 h-9 text-primary-700 -translate-x-1/2 pointer-events-auto max-md:w-11 <?= $isMobileMark ? '' : 'max-md:hidden' ?>"
              style="left: <?= round($pos, 2) ?>%"
              aria-label="Zu <?= $year ?>"
            >
              <span class="absolute bottom-0 left-1/2 bg-current -translate-x-1/2 translate-y-1/2 <?= $isLustrum ? 'w-2.5 h-2.5' : 'w-1.5 h-1.5' ?><?= $isCurrent ? ' hidden' : '' ?>"></span>
              <span class="absolute bottom-3 left-1/2 font-heading text-xs leading-none whitespace-nowrap -translate-x-1/2 <?= $isMajor ? 'opacity-100' : 'opacity-0 transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100' ?>"><?= $year ?></span>
            </a>
          <?php endforeach ?>
          <span class="absolute bottom-0 z-2 flex items-center px-xs text-primary-700 bg-theme-background -translate-x-1/2 translate-y-1/2" style="left: <?= round($playheadPos, 2) ?>%" aria-hidden="true">
            <?= svg('assets/img/diamond.svg') ?>
          </span>
        </div>
      </nav>
    </div>
  <?php endif ?>

<?php endif ?>

<?php endsnippet() ?>
