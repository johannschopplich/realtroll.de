<?php

declare(strict_types = 1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\CommentRenderer;

#[CoversClass(CommentRenderer::class)]
final class CommentRendererTest extends TestCase
{
    #[Test]
    public function renders_basic_markdown_emphasis(): void
    {
        $html = CommentRenderer::render('This is **bold** and *italic*.');

        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<em>italic</em>', $html);
    }

    #[Test]
    public function demotes_headings_to_strong_preserving_text(): void
    {
        $html = CommentRenderer::render("# Große Überschrift\n\n## Kleiner");

        $this->assertStringNotContainsString('<h1', $html);
        $this->assertStringNotContainsString('<h2', $html);
        $this->assertStringContainsString('<strong>Große Überschrift</strong>', $html);
        $this->assertStringContainsString('<strong>Kleiner</strong>', $html);
    }

    #[Test]
    public function preserves_strikethrough_as_del(): void
    {
        $html = CommentRenderer::render('This is ~~wrong~~ actually.');

        $this->assertStringContainsString('<del>wrong</del>', $html);
    }

    #[Test]
    public function keeps_pipe_table_literal(): void
    {
        // Parsedown needs the divider row to build a table; the two-line form
        // would become a <table> if the Table block were still registered.
        $html = CommentRenderer::render("a | b\n--|--\nc | d");

        $this->assertStringNotContainsString('<table', $html);
        $this->assertStringNotContainsString('<td', $html);
        $this->assertStringContainsString('a | b', $html);
    }

    #[Test]
    public function renders_image_markdown_without_dropping_alt_text(): void
    {
        // Images are unsupported, but the alt text must survive: `![alt](url)`
        // becomes a link, never an <img> the sanitizer would unwrap and empty.
        $html = CommentRenderer::render('![Screenshot der Meldung](http://example.com/x.png)');

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringContainsString('Screenshot der Meldung', $html);
    }

    #[Test]
    public function preserves_thematic_break(): void
    {
        $html = CommentRenderer::render("oben\n\n---\n\nunten");

        $this->assertStringContainsString('<hr', $html);
    }

    #[Test]
    public function renders_unordered_list_items(): void
    {
        $html = CommentRenderer::render("- eins\n- zwei");

        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li>eins</li>', $html);
        $this->assertStringContainsString('<li>zwei</li>', $html);
    }

    #[Test]
    public function renders_blockquote(): void
    {
        $html = CommentRenderer::render('> zitiert');

        $this->assertStringContainsString('<blockquote>', $html);
        $this->assertStringContainsString('zitiert', $html);
    }

    #[Test]
    public function strips_data_uri_images_and_links(): void
    {
        $html = CommentRenderer::render(
            "![x](data:image/png;base64,iVBORw0KGgo=)\n\n"
            . '[klick](data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==)'
        );

        $this->assertStringNotContainsString('data:', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    #[Test]
    public function drops_niche_scheme_links(): void
    {
        $html = CommentRenderer::render(
            "[steam](steam://run/570)\n\n[irc](irc://irc.example.com)\n\n[ftp](ftp://files.example.com)"
        );

        $this->assertStringNotContainsString('steam:', $html);
        $this->assertStringNotContainsString('irc:', $html);
        $this->assertStringNotContainsString('ftp:', $html);
    }

    #[Test]
    public function forces_nofollow_ugc_on_valid_links(): void
    {
        $html = CommentRenderer::render('[Seite](https://example.com/pfad)');

        $this->assertStringContainsString('href="https://example.com/pfad"', $html);
        $this->assertStringContainsString('rel="nofollow ugc"', $html);
    }

    #[Test]
    public function escapes_raw_html_to_inert_text(): void
    {
        // Safe mode escapes raw HTML – including malformed CDATA/comment-wrapped
        // payloads that some sanitizers reconstruct into live nodes on the
        // parse→serialize round-trip – so no live element is ever emitted.
        $html = CommentRenderer::render(
            '<img src=x onerror="alert(1)"> und <div onclick="evil()">x</div>'
            . '<![CDATA[<script>alert(1)</script>]]><!--><img src=x onerror=alert(1)-->'
        );

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<div', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString('&lt;img', $html);
    }

    #[Test]
    public function blocks_link_href_attribute_breakout(): void
    {
        // A quote inside the link URL must not escape the href attribute and
        // spawn a live element – this exercises the Sane DOM serialization, not
        // just Parsedown's safe mode.
        $html = CommentRenderer::render('[klick](http://a"><img src=x onerror=alert(1)>)');

        $this->assertStringNotContainsString('<img', $html);
    }

    #[Test]
    public function clean_strips_bidi_zero_width_tags_and_line_separators(): void
    {
        $dirty = "a\u{202E}b\u{2066}c\u{200B}d\u{FEFF}e\u{2060}f\u{2028}g\u{2029}h\u{E0001}i\u{E007F}j";

        $this->assertSame('abcdefghij', CommentRenderer::clean($dirty));
    }

    #[Test]
    public function clean_preserves_zwj_in_emoji_sequences(): void
    {
        // 👨‍👩‍👧‍👦 is four codepoints joined by three ZWJ (U+200D); stripping
        // the joiner would split it into four separate glyphs.
        $family = "\u{1F468}\u{200D}\u{1F469}\u{200D}\u{1F467}\u{200D}\u{1F466}";

        $this->assertSame($family, CommentRenderer::clean($family));
    }

    #[Test]
    public function clean_preserves_zwnj(): void
    {
        $persian = "\u{0645}\u{200C}\u{06CC}";

        $this->assertSame($persian, CommentRenderer::clean($persian));
    }

    #[Test]
    public function clean_nfc_normalizes_decomposed_input(): void
    {
        // "e" + combining acute accent should compose to precomposed "é".
        $this->assertSame("\u{00E9}", CommentRenderer::clean("e\u{0301}"));
    }

    #[Test]
    public function clean_strips_invisibles_from_invalid_utf8_input(): void
    {
        // Invalid bytes must not bypass the strip: without scrubbing them first,
        // the /u regex returns null and the Bidi override would pass through.
        $result = CommentRenderer::clean("\xFF\u{202E}abc\xFE");

        $this->assertStringNotContainsString("\u{202E}", $result);
        $this->assertStringContainsString('abc', $result);
    }

    #[Test]
    public function clean_trims_surrounding_whitespace(): void
    {
        $this->assertSame('hallo', CommentRenderer::clean('  hallo  '));
        $this->assertSame('', CommentRenderer::clean('   '));
    }
}
