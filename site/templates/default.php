<?php

/** @var \Kirby\Cms\Page $page */

use Kirby\Toolkit\Str;

$blocks = $page->text()->toBlocks();
$showChapters = $page->parent() !== null && $page->chapterLayout()->isTrue();

$introBlocks = [];
$chapters = [];

if ($showChapters) {
  foreach ($blocks as $block) {
    if ($block->type() === 'heading' && $block->level()->value() === 'h2') {
      $title = trim(strip_tags($block->text()->value()));
      $chapters[] = ['id' => Str::slug($title), 'title' => $title, 'blocks' => []];
    } elseif ($chapters === []) {
      $introBlocks[] = $block;
    } else {
      $chapters[array_key_last($chapters)]['blocks'][] = $block;
    }
  }
}

snippet('layouts/default', slots: true);

?>

<div class="content-lg">
  <?php snippet('components/page-title', [
    'eyebrow' => $page->parent()?->title()?->escape(),
    'title' => $page->title()->value()
  ]) ?>
</div>

<?php if ($chapters === []): ?>
  <div class="mt-7xl">
    <?php snippet('components/prose-blocks', ['blocks' => $blocks]) ?>
  </div>
<?php else: ?>
  <?php if ($introBlocks !== []): ?>
    <div class="mt-7xl">
      <div class="content-prose">
        <div class="prose">
          <?php foreach ($introBlocks as $block): ?>
            <?= $block->toHtml() ?>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  <?php endif ?>

  <div class="content-prose mt-7xl">
    <ol class="ps-0 list-none space-y-xl">
      <?php foreach ($chapters as $index => $chapter): ?>
        <li>
          <details id="<?= $chapter['id'] ?>" class="group relative bg-theme-background border-2 border-primary-700 open:shadow-solid">
            <summary class="flex items-center gap-lg px-lg py-sm list-none cursor-pointer select-none [&::-webkit-details-marker]:hidden">
              <span class="chip-bevel-base font-heading"><?= $index + 1 ?></span>
              <h2 class="flex-1 my-0 font-heading font-normal text-base leading-heading text-primary-700"><?= esc($chapter['title']) ?></h2>
              <span class="font-heading text-xl text-primary-700 group-open:hidden" aria-hidden="true">+</span>
              <span class="hidden font-heading text-xl text-primary-700 group-open:inline" aria-hidden="true">−</span>
            </summary>
            <div class="px-lg pt-lg pb-xl border-t-2 border-primary-700">
              <div class="prose text-sm">
                <?php foreach ($chapter['blocks'] as $block): ?>
                  <?= $block->toHtml() ?>
                <?php endforeach ?>
              </div>
            </div>
          </details>
        </li>
      <?php endforeach ?>
    </ol>
  </div>
<?php endif ?>

<?php if ($parent = $page->parent()): ?>
  <?php snippet('components/section-divider') ?>
  <div class="text-center">
    <a href="<?= $parent->url() ?>" class="button-primary">
      Zurück zu <?= $parent->title()->escape() ?>
    </a>
  </div>
<?php endif ?>

<?php endsnippet() ?>
