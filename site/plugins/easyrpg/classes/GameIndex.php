<?php

declare(strict_types = 1);

namespace JohannSchopplich\EasyRpg;

use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;
use Normalizer;

/**
 * PHP port of the EasyRPG `gencache` tool, which generates the
 * `index.json` file map the EasyRPG Player web port requires.
 *
 * @see https://github.com/EasyRPG/Tools/tree/master/gencache
 */
final class GameIndex
{
    public const int METADATA_VERSION = 2;
    public const int DEFAULT_RECURSION_DEPTH = 4;

    /** File extensions that are kept in lookup keys below the game root. */
    private const array KEPT_EXTENSIONS = ['.ini', '.po'];

    public static function generate(string $gameRoot, int $recursionDepth = self::DEFAULT_RECURSION_DEPTH): array
    {
        return [
            'metadata' => [
                'version' => self::METADATA_VERSION,
                'date' => date('Y-m-d')
            ],
            'cache' => self::scanDirectory($gameRoot, $recursionDepth)
        ];
    }

    private static function scanDirectory(string $path, int $remainingDepth, string|null $directoryName = null): array
    {
        if ($remainingDepth === 0) {
            return [];
        }

        $isGameRoot = $directoryName === null;
        $entries = [];

        if ($isGameRoot === false) {
            $entries['_dirname'] = $directoryName;
        }

        foreach (Dir::read($path) as $name) {
            // `_dirname` is a reserved keyword
            if ($name === '_dirname') {
                continue;
            }

            $key = self::normalizeName($name);
            $absolutePath = $path . '/' . $name;

            if (is_dir($absolutePath)) {
                $subdirectoryEntries = self::scanDirectory($absolutePath, $remainingDepth - 1, $name);

                if ($subdirectoryEntries !== []) {
                    $entries[$key] = $subdirectoryEntries;
                }

                continue;
            }

            if ($isGameRoot === true || self::hasKeptExtension($key)) {
                if (self::stripExtension($key) === 'exfont') {
                    $key = 'exfont';
                }

                $entries[$key] = $name;
            } else {
                $entries[self::stripExtension($key)] = $name;
            }
        }

        return $entries;
    }

    /**
     * The player looks up files case-insensitively via NFKC-normalized
     * lowercase keys, matching ICU's behavior in the original tool.
     */
    private static function normalizeName(string $name): string
    {
        $lowercaseName = Str::lower($name);

        return Normalizer::normalize($lowercaseName, Normalizer::FORM_KC) ?: $lowercaseName;
    }

    private static function hasKeptExtension(string $name): bool
    {
        foreach (self::KEPT_EXTENSIONS as $extension) {
            if (str_ends_with($name, $extension)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Byte-exact port of the original tool's `strip_ext`; `pathinfo()`
     * is locale-sensitive for multibyte names and not safe here.
     */
    private static function stripExtension(string $name): string
    {
        $lastDotPosition = strrpos($name, '.');

        return $lastDotPosition === false ? $name : substr($name, 0, $lastDotPosition);
    }
}
