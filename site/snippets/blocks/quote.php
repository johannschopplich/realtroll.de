<?php

/** @var \Kirby\Cms\Block $block */

$text = $block->text();
if ($text->isEmpty()) return;

$citation = $block->citation();

?>
<figure class="not-prose flex flex-col gap-2xl my-[calc(var(--un-prose-space-y)*2)] text-center text-primary-700 md:-mx-3xl">
  <?php snippet('components/diamond-divider') ?>

  <?php /* Zitat + Quelle als eine Flex-Einheit, damit die Quelle nicht im `gap-2xl` der Figure hängt, sondern eng am Zitat sitzt. */ ?>
  <div>
    <blockquote class="font-heading text-lg leading-heading text-primary-800 hyphenate md:text-xl">
      <?= $text ?>
    </blockquote>
    <?php if ($citation->isNotEmpty()): ?>
      <figcaption class="mt-1 text-sm text-contrast-medium">– <?= $citation ?></figcaption>
    <?php endif ?>
  </div>

  <?php snippet('components/diamond-divider') ?>
</figure>
