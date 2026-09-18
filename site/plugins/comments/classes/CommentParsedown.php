<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Parsedown;

/**
 * Parsedown in safe mode, without tables or images.
 */
final class CommentParsedown extends Parsedown
{
    public function __construct()
    {
        $this->setSafeMode(true);

        // Table is registered under the `-`, `:` and `|` markers; drop only the
        // Table entries so the other block types on those markers survive.
        foreach (['-', ':', '|'] as $marker) {
            if (isset($this->BlockTypes[$marker])) {
                $this->BlockTypes[$marker] = array_values(array_filter(
                    $this->BlockTypes[$marker],
                    static fn (string $blockType): bool => $blockType !== 'Table'
                ));
            }
        }

        // Images are unsupported (comments never load external image URLs).
        if (isset($this->InlineTypes['!'])) {
            $this->InlineTypes['!'] = array_values(array_filter(
                $this->InlineTypes['!'],
                static fn (string $inlineType): bool => $inlineType !== 'Image'
            ));
        }
    }
}
