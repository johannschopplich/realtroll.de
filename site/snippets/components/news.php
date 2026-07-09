<?php

$blog = page('blog');
$posts = $blog?->children()->listed()->sortBy('date', 'desc')->limit(3);
if (!$posts || $posts->count() === 0) return;

$dateFormatter = dateFormatter();

?>
<section class="content-lg mt-8xl">
  <header class="flex items-center justify-between gap-lg mb-5xl">
    <h2 class="font-heading text-2xl leading-none text-primary-700">Aus der Werkstatt</h2>
    <a href="<?= $blog->url() ?>" class="group link-primary shrink-0 text-sm">
      <span class="link-default [--un-decoration-color:transparent] group-hover:decoration-current">Alle Neuigkeiten</span>
      <span
        class="
          i-dinkie-icons-right-arrow-circled transition-transform
          group-hover:translate-x-1 motion-reduce:transition-none
        "
        aria-hidden="true"
      ></span>
    </a>
  </header>

  <ul class="grid gap-lg md:grid-cols-3">
    <?php foreach ($posts as $post): ?>
      <?php
      $lead = '';
      foreach ($post->text()->toBlocks() as $block) {
        if ($block->type() === 'text') {
          $lead = \Kirby\Toolkit\Str::excerpt((string) $block->text(), 140);
          break;
        }
      }
      ?>
      <li class="group">
        <a
          href="<?= $blog->url() ?>#<?= $post->slug() ?>"
          class="
            flex flex-col h-full p-2xl
            bg-white border-2 border-primary-700
            transition-[transform,box-shadow]
            md:p-3xl md:group-hover:translate-[-4px] md:group-hover:shadow-solid md:group-active:translate-0
          "
        >
          <p class="label-caps mb-1 text-xs text-contrast-medium">
            <time datetime="<?= $post->date()->toDate('c') ?>"><?= $post->date()->toDate($dateFormatter) ?></time>
          </p>
          <h3 class="hyphenate mb-2 font-heading text-lg leading-tight text-primary-700"><?= $post->title()->escape() ?></h3>
          <?php if ($lead): ?>
            <p class="text-sm text-contrast-medium line-clamp-3"><?= esc($lead) ?></p>
          <?php endif ?>
          <span class="link-primary mt-auto pt-lg text-sm">
            <span class="link-default [--un-decoration-color:transparent] group-hover:decoration-current">Weiterlesen</span>
          </span>
        </a>
      </li>
    <?php endforeach ?>
  </ul>
</section>
