<?php

/**
 * Fallback for a render path that bypasses `CommentPage::render()`, so a
 * standalone comment page is never emitted.
 * @var \Kirby\Cms\Page $page
 */

go($page->parent()->url() . '#kommentar-' . $page->slug());
