<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use DOMElement;
use Kirby\Sane\Html;

final class CommentHtml extends Html
{
    public static array $allowedAttrPrefixes = [];
    public static array $allowedAttrs = [];
    public static array $allowedTags = [
        'html' => true, 'body' => true,                     // mandatory wrappers, stripped on serialize
        'strong' => false, 'em' => false,
        'del' => false, 's' => false,                       // strikethrough (~~x~~)
        'a'  => ['href', 'rel'],
        'ul' => false, 'ol' => false, 'li' => false,
        'p'  => false, 'br' => false, 'hr' => false,      // hr must be listed, or the unwrap pass drops thematic breaks (---)
        'blockquote' => false, 'code' => false, 'pre' => false,
        // h1–h6 MUST be listed: the unwrap pass runs BEFORE the elementCallback;
        // unlisted, Dom would unwrap them and discard their text nodes before the
        // callback can rewrite them to <strong>.
        'h1' => false, 'h2' => false, 'h3' => false, 'h4' => false, 'h5' => false, 'h6' => false,
        // NO table tags: table parsing is already off in CommentParsedown, so no
        // table/tr/td arises that could unwrap (and swallow text) here.
    ];
    public static array $disallowedTags = ['script', 'style', 'iframe', 'object', 'meta'];
    public static array $urlAttrs = ['href'];
    public static array $allowedDataUris = [];

    public static function sanitizeElement(DOMElement $el, array $options): array
    {
        $name = strtolower($el->nodeName);

        // Demote rather than strip: no outline/SEO weight, no text loss
        if (in_array($name, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)) {
            $strong = $el->ownerDocument->createElement('strong');
            while ($el->firstChild !== null) {
                $strong->appendChild($el->firstChild);
            }
            $el->parentNode->replaceChild($strong, $el);
            return [];
        }

        if ($name === 'a') {
            $el->setAttribute('rel', 'nofollow ugc');
        }

        return [];
    }
}
