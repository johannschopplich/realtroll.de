<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Uuid\Uuid;

/**
 * Two-level threading over one article's loaded comment set.
 *
 * Membership in the set is the visibility check – no lookup ever widens past
 * it. Never resolve via `Uuid::for()->model()` or `Pages::find()`: both crawl
 * the whole site tree on a cache miss (exactly the orphan case), and the
 * write side runs on the unauthenticated pre-Turnstile path.
 */
final class CommentThread
{
    /** @var array<string, CommentPage> uuid => comment, in input order. */
    private readonly array $byUuid;

    /** @var array{topLevel: list<CommentPage>, replies: array<string, list<CommentPage>>}|null */
    private array|null $grouping = null;

    /**
     * @param Pages $comments Input order carries through to topLevel() and
     *                        repliesTo() – sorting is the caller's job.
     */
    public function __construct(Pages $comments)
    {
        $byUuid = [];

        foreach ($comments as $comment) {
            if ($comment instanceof CommentPage) {
                $byUuid[$comment->uuid()->toString()] = $comment;
            }
        }

        $this->byUuid = $byUuid;
    }

    /**
     * The `parentId` to store for a reply to the requested target, or null
     * for top-level. An unresolvable reference promotes instead of rejecting;
     * a resolvable one flattens onto its anchor.
     */
    public function storedParentId(string $requestedParentId): string|null
    {
        $target = $this->findByUuid($requestedParentId);

        return $target === null
            ? null
            : $this->anchorFor($target)->uuid()->toString();
    }

    /**
     * Thread openers, in input order.
     *
     * @return list<CommentPage>
     */
    public function topLevel(): array
    {
        return $this->group()['topLevel'];
    }

    /**
     * Replies rendered beneath $parent, in input order.
     *
     * @return list<CommentPage>
     */
    public function repliesTo(Page $parent): array
    {
        return $this->group()['replies'][$parent->uuid()->toString()] ?? [];
    }

    /**
     * The comment this one renders beneath, or null when it renders
     * top-level. Only an anchor holds replies, so a hand-edited deeper chain
     * promotes instead of nesting a third level.
     */
    public function parentOf(Page $comment): CommentPage|null
    {
        $parent = $this->findByUuid($this->storedParentIdField($comment));

        // A self-reference must promote, not vanish into its own reply group.
        if ($parent === null || $parent->is($comment)) {
            return null;
        }

        return $this->anchorFor($parent) === $parent ? $parent : null;
    }

    /**
     * Where a reply to $target renders: $target itself when its own reference
     * is unresolvable, otherwise its ancestor – the two-level cap.
     */
    private function anchorFor(CommentPage $target): CommentPage
    {
        return $this->findByUuid($this->storedParentIdField($target)) ?? $target;
    }

    private function findByUuid(string $uuid): CommentPage|null
    {
        if ($uuid === '' || !Uuid::is($uuid, 'page')) {
            return null;
        }

        return $this->byUuid[$uuid] ?? null;
    }

    private function storedParentIdField(Page $comment): string
    {
        // The threading reference lives in the `parentId` content field –
        // Kirby's native Page::parentId() is the storage parent id instead.
        return (string)$comment->content()->get('parentId');
    }

    /**
     * @return array{topLevel: list<CommentPage>, replies: array<string, list<CommentPage>>}
     */
    private function group(): array
    {
        if ($this->grouping !== null) {
            return $this->grouping;
        }

        $topLevel = [];
        $replies  = [];

        foreach ($this->byUuid as $comment) {
            $parent = $this->parentOf($comment);

            if ($parent === null) {
                $topLevel[] = $comment;
            } else {
                $replies[$parent->uuid()->toString()][] = $comment;
            }
        }

        return $this->grouping = ['topLevel' => $topLevel, 'replies' => $replies];
    }
}
