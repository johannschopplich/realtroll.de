<?php
/**
 * Plain-text alternative part (Kirby renders both templates multipart). No esc
 * (text/plain), but name/preview/parent excerpt arrive already single-lined.
 * Raw URLs stay tappable in every mail client.
 *
 * @var \Kirby\Cms\Page $comment
 * @var \Kirby\Cms\Page $article
 * @var string $name
 * @var string $preview
 * @var string $moderateUrl
 * @var string $viewUrl
 * @var string|null $parentName
 * @var string|null $parentExcerpt
 */
?>
Neuer Kommentar auf realtroll.de

Name: <?= $name ?>

Artikel: <?= $article->title()->value() ?>

Zeit: <?= (string)($comment->date()->toDate('d.m.Y H:i') ?? '') ?> Uhr
<?php if ($parentName !== null): ?>

Antwort auf: <?= $parentName ?>

<?= $parentExcerpt ?>
<?php endif ?>

Kommentar:
<?= $preview ?>


Im Artikel ansehen:
<?= $viewUrl ?>


Im Panel moderieren:
<?= $moderateUrl ?>
