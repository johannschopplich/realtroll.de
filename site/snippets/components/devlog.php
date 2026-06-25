<?php

$blog = page('blog');
$post = $blog?->children()->listed()->sortBy('date', 'desc')->first();
if (!$post) return;

$href = $blog->url() . '#' . $post->slug();

?>
<p class="group relative flex flex-wrap items-center justify-center mt-2xl gap-x-3 gap-y-2 max-w-full text-sm">
  <span class="label-caps inline-flex shrink-0 items-center gap-2 px-2 py-1 text-xs leading-none text-primary-800 border-2 border-primary-700">
    <span class="inline-block size-2 bg-primary-700 motion-safe:animate-devlog-blink" aria-hidden="true"></span>
    Neues
  </span>
  <a href="<?= $href ?>" class="link-primary static min-w-0 text-sm after:absolute after:inset-0 after:content-['']">
    <span class="link-default [--un-decoration-color:transparent] min-w-0 max-w-[28rem] truncate group-hover:decoration-current"><?= $post->title()->escape() ?></span>
  </a>
</p>
