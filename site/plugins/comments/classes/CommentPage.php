<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\Page;
use Kirby\Cms\User;
use Kirby\Content\VersionId;
use Kirby\Toolkit\Str;

final class CommentPage extends Page
{
    public const SLUG_PREFIX = 'comment-';

    /**
     * Redirects to the comment's anchor in the parent article – comments are
     * never viewable on their own.
     */
    public function render(
        array $data = [],
        $contentType = 'html',
        VersionId|string|null $versionId = null
    ): string {
        go($this->parent()->url() . '#' . $this->anchor());
    }

    public function anchor(): string
    {
        return 'kommentar-' . Str::afterStart($this->slug(), self::SLUG_PREFIX);
    }

    /**
     * Resolves the stored developer reference session-free, so the cached
     * article HTML never depends on who is logged in.
     *
     * Not named `author()` – that would shadow the magic content-field method.
     */
    public function developer(): User|null
    {
        return $this->content()->get('author')->toUser();
    }

    /**
     * Prefers the live account name for a developer reply; a missing, deleted,
     * or nameless account falls back to the stored visitor name.
     *
     * Raw text – escaping stays at the HTML boundary.
     */
    public function displayName(): string
    {
        $developerName = $this->developer()?->name()->value();

        return $developerName !== null && $developerName !== ''
            ? $developerName
            : (string)$this->name()->value();
    }
}
