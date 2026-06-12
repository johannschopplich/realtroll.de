<?php
/**
 * PROTOTYPE – floating switcher for toggleable live variants, render in
 * dev mode only (gate in the template). Arrow keys cycle variants,
 * internal links keep the active variant.
 *
 * @var string $variant active key
 * @var array<string, string> $variants key => label
 * @var string $param URL param name
 * @var string $position utility class for the vertical position (stack multiple switchers)
 * @var array{string, string} $arrowKeys keyboard keys for prev/next (differ per switcher)
 */
$param ??= 'variant';
$position ??= 'bottom-4';
$arrowKeys ??= ['ArrowLeft', 'ArrowRight'];
$arrowGlyphs = ['ArrowLeft' => '&larr;', 'ArrowRight' => '&rarr;', 'ArrowUp' => '&uarr;', 'ArrowDown' => '&darr;'];
$keys = array_keys($variants);
$index = array_search($variant, $keys, true);
$prevVariant = $keys[($index - 1 + count($keys)) % count($keys)];
$nextVariant = $keys[($index + 1) % count($keys)];
?>
<div class="fixed <?= $position ?> left-1/2 z-50 flex items-center gap-1 p-1 font-mono text-xs text-white bg-neutral-900 rounded-full -translate-x-1/2 shadow-lg select-none">
  <a href="?<?= $param ?>=<?= $prevVariant ?>" class="inline-flex items-center justify-center size-7 no-underline text-white rounded-full hover:bg-neutral-700" aria-label="Vorherige Variante"><?= $arrowGlyphs[$arrowKeys[0]] ?? '&larr;' ?></a>
  <span class="px-2 whitespace-nowrap"><?= strtoupper($variant) ?> &ndash; <?= $variants[$variant] ?></span>
  <a href="?<?= $param ?>=<?= $nextVariant ?>" class="inline-flex items-center justify-center size-7 no-underline text-white rounded-full hover:bg-neutral-700" aria-label="Nächste Variante"><?= $arrowGlyphs[$arrowKeys[1]] ?? '&rarr;' ?></a>
</div>
<script>
  (() => {
    const param = <?= json_encode($param) ?>;
    const variant = <?= json_encode($variant) ?>;
    const keys = <?= json_encode($keys) ?>;
    const arrowKeys = <?= json_encode($arrowKeys) ?>;

    const go = (offset) => {
      const url = new URL(location.href);
      const index = (keys.indexOf(variant) + offset + keys.length) % keys.length;
      url.searchParams.set(param, keys[index]);
      location.href = url.toString();
    };

    document.addEventListener("keydown", (event) => {
      const target = event.target;
      if (target instanceof HTMLElement && (target.matches("input, textarea, select") || target.isContentEditable)) return;
      if (!arrowKeys.includes(event.key)) return;
      event.preventDefault();
      go(event.key === arrowKeys[0] ? -1 : 1);
    });

    // Keep the active variant across internal links
    for (const link of document.querySelectorAll("a[href]")) {
      const url = new URL(link.href, location.href);
      if (url.origin === location.origin && !url.searchParams.has(param)) {
        url.searchParams.set(param, variant);
        link.href = url.toString();
      }
    }
  })();
</script>
