<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Cms\User;
use Kirby\Content\VersionId;
use Kirby\Uuid\Uuid;

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
     * Resolves the stored developer reference to a user, session-free: reads the
     * users collection, never the session, so the cached article HTML stays
     * cache-safe. Named for the role it expresses (a developer reply), and to
     * avoid shadowing the magic `author` content-field method.
     */
    public function developer(): User|null
    {
        return $this->content()->get('author')->toUser();
    }

    public function isTopLevel(): bool
    {
        return $this->content()->get('parentId')->isEmpty();
    }

    /**
     * The visible comment this reply nests under, or null when it must render
     * as top-level (orphan promotion). Null unifies every non-nestable case:
     * parent deleted, hidden (draft), on another article, or not a comment.
     *
     * Pass the comment's own sibling set (its article's loaded comments):
     * membership there already means "non-draft comment on this article", so an
     * absent parent is exactly the orphan verdict.
     */
    public function topLevelParent(Pages $siblings): self|null
    {
        $parent = $this->parentComment($siblings);

        if ($parent === null) {
            return null;
        }

        // Nest only under a comment that itself renders as top-level: either a
        // genuine thread opener or a promoted orphan (the guard's orphan branch
        // deliberately stores a level-2 target whose own ancestor is gone, so
        // that relation must render as nesting). One extra hop, no recursion –
        // guard-written references never chain deeper.
        if ($parent->isTopLevel() || $parent->parentComment($siblings) === null) {
            return $parent;
        }

        return null;
    }

    private function parentComment(Pages $siblings): self|null
    {
        $parentId = $this->content()->get('parentId')->value();

        if (empty($parentId) || !Uuid::is($parentId, 'page')) {
            return null;
        }

        // Scan by hand rather than `$siblings->find($parentId)`: find() delegates
        // to `Uuid::for()->model()`, which crawls the whole site tree on a cache
        // miss – exactly when the parent is absent (the orphan case).
        foreach ($siblings as $sibling) {
            if ($sibling instanceof self && $sibling->uuid()->toString() === $parentId) {
                return $sibling;
            }
        }

        return null;
    }
}
