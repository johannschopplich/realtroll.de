<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Normalizer;

/**
 * The comment text pipeline: the only genuinely security-critical boundary.
 *
 * - `render()` turns stored raw Markdown into sanitized display HTML.
 * - `clean()` is the Unicode hygiene applied to name and text before storing.
 */
final class CommentRenderer
{
    /**
     * Invisible characters stripped before storing: Bidi overrides
     * (U+202A–202E) and isolates (U+2066–2069), the zero-width attack
     * characters (U+200B, U+FEFF, U+2060), the Tags block (U+E0000–E007F,
     * invisible-ASCII smuggling) and the line/paragraph separators
     * (U+2028/U+2029). U+200C (ZWNJ) and U+200D (ZWJ) are deliberately
     * preserved – ZWJ joins emoji sequences, ZWNJ is orthographically
     * required in Persian and several Indic scripts.
     */
    private const STRIP_PATTERN = '/[\x{202A}-\x{202E}\x{2066}-\x{2069}\x{200B}\x{FEFF}\x{2060}\x{2028}\x{2029}\x{E0000}-\x{E007F}]/u';

    public static function render(string $raw): string
    {
        $html = (new CommentParsedown())->text($raw);

        return CommentHtml::sanitize($html);
    }

    public static function clean(string $value): string
    {
        // Replace invalid byte sequences first: otherwise `Normalizer::normalize`
        // fails and the /u `preg_replace` below returns null, silently passing
        // malformed input through with its Bidi/zero-width characters intact.
        $value = mb_scrub($value, 'UTF-8');

        $normalized = Normalizer::normalize($value, Normalizer::FORM_C);
        if ($normalized !== false) {
            $value = $normalized;
        }

        $value = preg_replace(self::STRIP_PATTERN, '', $value) ?? $value;

        // Trim after stripping: a name that was only whitespace (or whitespace
        // plus now-removed zero-width characters) collapses to empty and fails
        // the required check instead of storing a blank display name.
        return trim($value);
    }
}
