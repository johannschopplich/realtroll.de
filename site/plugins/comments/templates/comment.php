<?php

/**
 * Fallback for a render path that bypasses `CommentPage::render()`, so a
 * standalone comment page is never emitted.
 * @var \RealTroll\Comments\CommentPage $page
 */

go($page->parent()->url() . '#' . $page->anchor());
