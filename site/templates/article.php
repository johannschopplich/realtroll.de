<?php

/** @var \Kirby\Cms\Page $page */

snippet('layouts/default', slots: true);

$dateFormatter = dateFormatter();

?>

<article class="content-prose">
  <?php snippet('components/page-title', [
    'align' => 'left',
    'class' => 'mb-7xl',
    'titleClass' => 'hyphenate',
    'eyebrow' => '<time datetime="' . $page->date()->toDate('c') . '">' . $page->date()->toDate($dateFormatter) . '</time>',
    'title' => $page->title()->value()
  ]) ?>

  <div class="prose article-dropcap">
    <?= $page->text()->toBlocks() ?>
  </div>
</article>

<?php snippet('components/section-divider') ?>

<div class="content-prose">
  <?php snippet('components/comments/section') ?>
</div>

<?php if ($page->nextListed()): ?>
  <a
    href="<?= $page->nextListed()->url() ?>"
    class="
      group fixed top-1/2 left-[--spacing-lg] z-20 hidden items-center text-primary-700 -translate-y-1/2
      lg:flex
    "
    aria-label="Neuer: <?= $page->nextListed()->title()->escape() ?>"
  >
    <span
      class="
        i-dinkie-icons-left-arrow-circled
        shrink-0 text-[2.75rem]
        -translate-y-[2px] transition-transform
        group-hover:-translate-x-1 group-focus-visible:-translate-x-1 motion-reduce:transition-none
      "
      aria-hidden="true"
    ></span>
    <span
      class="
        ml-2 w-max max-w-[14rem]
        opacity-0 -translate-x-1 transition-[opacity,transform]
        group-hover:translate-x-0 group-hover:opacity-100
        group-focus-visible:translate-x-0 group-focus-visible:opacity-100 motion-reduce:transition-none
      "
    >
      <span class="label-caps block mb-0.5 text-xs leading-none text-theme-base">Neuer</span>
      <span class="line-clamp-2 w-full text-sm font-medium leading-tight text-balance"><?= $page->nextListed()->title()->escape() ?></span>
    </span>
  </a>
<?php endif ?>
<?php if ($page->prevListed()): ?>
  <a
    href="<?= $page->prevListed()->url() ?>"
    class="
      group fixed top-1/2 right-[--spacing-lg] z-20 hidden items-center text-primary-700 -translate-y-1/2
      lg:flex
    "
    aria-label="Älter: <?= $page->prevListed()->title()->escape() ?>"
  >
    <span
      class="
        mr-2 w-max max-w-[14rem]
        text-right opacity-0 translate-x-1 transition-[opacity,transform]
        group-hover:translate-x-0 group-hover:opacity-100
        group-focus-visible:translate-x-0 group-focus-visible:opacity-100 motion-reduce:transition-none
      "
    >
      <span class="label-caps block mb-0.5 text-xs leading-none text-theme-base">Älter</span>
      <span class="line-clamp-2 w-full text-sm font-medium leading-tight text-balance"><?= $page->prevListed()->title()->escape() ?></span>
    </span>
    <span
      class="
        i-dinkie-icons-right-arrow-circled
        shrink-0 text-[2.75rem]
        -translate-y-[2px] transition-transform
        group-hover:translate-x-1 group-focus-visible:translate-x-1 motion-reduce:transition-none
      "
      aria-hidden="true"
    ></span>
  </a>
<?php endif ?>

<nav class="content-prose flex justify-between gap-lg mt-8xl text-sm lg:hidden" aria-label="Artikel-Navigation">
  <?php if ($page->nextListed()): ?>
    <a href="<?= $page->nextListed()->url() ?>" class="link-primary group">
      <span
        class="
          i-dinkie-icons-left-arrow-circled
          shrink-0 text-lg translate-y-[-1px] transition-transform
          group-hover:-translate-x-1 group-focus-visible:-translate-x-1 motion-reduce:transition-none
        "
        aria-hidden="true"
      ></span>
      <span
        class="
          label-caps link-default [--un-decoration-color:transparent]
          group-hover:decoration-current group-focus-visible:decoration-current
        "
      >Neuer</span>
    </a>
  <?php else: ?><span></span><?php endif ?>
  <?php if ($page->prevListed()): ?>
    <a href="<?= $page->prevListed()->url() ?>" class="link-primary group">
      <span
        class="
          label-caps link-default [--un-decoration-color:transparent]
          group-hover:decoration-current group-focus-visible:decoration-current
        "
      >Älter</span>
      <span
        class="
          i-dinkie-icons-right-arrow-circled
          shrink-0 text-lg translate-y-[-1px] transition-transform
          group-hover:translate-x-1 group-focus-visible:translate-x-1 motion-reduce:transition-none
        "
        aria-hidden="true"
      ></span>
    </a>
  <?php endif ?>
</nav>

<?php endsnippet() ?>
