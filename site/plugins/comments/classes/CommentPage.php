<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\Page;
use Kirby\Cms\User;
use Kirby\Content\VersionId;

final class CommentPage extends Page
{
    /**
     * Comments are never viewable on their own.
     */
    public function render(
        array $data = [],
        $contentType = 'html',
        VersionId|string|null $versionId = null
    ): string {
        go($this->parent()->url() . '#kommentar-' . $this->slug());
    }

    /**
     * The stored developer reference, resolved session-free so the cached
     * article HTML stays cache-safe. Not named author() – that would shadow
     * the magic content-field method.
     */
    public function developer(): User|null
    {
        return $this->content()->get('author')->toUser();
    }

    /**
     * A developer reply prefers the live account name; a missing, deleted, or
     * nameless account falls back to the stored visitor name. Raw text –
     * escaping stays at the HTML boundary.
     */
    public function displayName(): string
    {
        $developerName = $this->developer()?->name()->value();

        return $developerName !== null && $developerName !== ''
            ? $developerName
            : (string)$this->name()->value();
    }
}
