<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\Page;

/**
 * Immutable accept/reject result of the admission policy. `accept` carries the
 * whole write payload (article, cleaned name/text, author, parentId) so the
 * route never re-resolves or re-cleans and drifts from what was validated;
 * `reject` carries the offending field, a stable machine `code` the client
 * branches on, and a human-readable message.
 */
final class Verdict
{
    private function __construct(
        public readonly bool $accepted,
        public readonly Page|null $article = null,
        public readonly string|null $name = null,
        public readonly string|null $text = null,
        public readonly string|null $author = null,
        public readonly string|null $parentId = null,
        public readonly string|null $field = null,
        public readonly string|null $code = null,
        public readonly string|null $message = null
    ) {
    }

    public static function accept(
        Page $article,
        string $name,
        string $text,
        string|null $author = null,
        string|null $parentId = null
    ): self {
        return new self(
            accepted: true,
            article: $article,
            name: $name,
            text: $text,
            author: $author,
            parentId: $parentId
        );
    }

    public static function reject(string $field, string $code, string $message): self
    {
        return new self(accepted: false, field: $field, code: $code, message: $message);
    }
}
