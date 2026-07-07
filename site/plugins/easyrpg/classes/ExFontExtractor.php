<?php

declare(strict_types = 1);

namespace JohannSchopplich\EasyRpg;

use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;

/**
 * Extracts the ExFont bitmap embedded in a game's `RPG_RT.exe` and writes
 * it as `ExFont.bmp` into the game directory.
 *
 * Desktop builds of the EasyRPG Player read the ExFont from `RPG_RT.exe`
 * at runtime, but the Emscripten (web) build cannot - it requires a
 * dedicated ExFont file in the game directory and falls back to its
 * built-in glyph set otherwise, silently dropping per-game custom glyphs.
 */
final class ExFontExtractor
{
    public const string EXFONT_FILENAME = 'ExFont.bmp';

    private const string RUNTIME_EXECUTABLE = 'rpg_rt.exe';
    private const int BITMAP_WIDTH = 156;
    private const int BITMAP_HEIGHT = 48;
    private const int INFO_HEADER_SIZE = 40;
    private const int PALETTE_SIZE = 1024;
    private const int FILE_HEADER_SIZE = 14;

    /**
     * Writes the ExFont file for the given game root if the game ships an
     * `RPG_RT.exe` and no ExFont file exists yet. Returns whether the game
     * directory contains an ExFont file afterwards.
     */
    public static function ensure(string $gameRoot): bool
    {
        $executablePath = null;

        foreach (Dir::read($gameRoot) as $name) {
            $lowercaseName = Str::lower($name);

            if (self::isExFontName($lowercaseName)) {
                return true;
            }

            if ($lowercaseName === self::RUNTIME_EXECUTABLE) {
                $executablePath = $gameRoot . '/' . $name;
            }
        }

        if ($executablePath === null) {
            return false;
        }

        $bitmap = self::extract(F::read($executablePath) ?: '');

        if ($bitmap === null) {
            return false;
        }

        return F::write($gameRoot . '/' . self::EXFONT_FILENAME, $bitmap);
    }

    /**
     * Locates the ExFont inside the executable and returns it as a
     * standalone BMP file. Stock `RPG_RT.exe` builds embed exactly one
     * 156x48 8-bit bitmap resource - the ExFont - so matching its
     * `BITMAPINFOHEADER` is sufficient; a full PE resource walk is not
     * needed for this corpus.
     */
    public static function extract(string $executable): string|null
    {
        $infoHeader = pack(
            'VVVvv',
            self::INFO_HEADER_SIZE,
            self::BITMAP_WIDTH,
            self::BITMAP_HEIGHT,
            1, // color planes
            8  // bits per pixel
        );

        $offset = strpos($executable, $infoHeader);

        if ($offset === false) {
            return null;
        }

        // 156 bytes per row are already a multiple of 4, so no row padding applies
        $dibSize = self::INFO_HEADER_SIZE + self::PALETTE_SIZE + self::BITMAP_WIDTH * self::BITMAP_HEIGHT;
        $deviceIndependentBitmap = substr($executable, $offset, $dibSize);

        if (strlen($deviceIndependentBitmap) !== $dibSize) {
            return null;
        }

        $fileHeader = 'BM' . pack(
            'VvvV',
            self::FILE_HEADER_SIZE + $dibSize,
            0,
            0,
            self::FILE_HEADER_SIZE + self::INFO_HEADER_SIZE + self::PALETTE_SIZE
        );

        return $fileHeader . $deviceIndependentBitmap;
    }

    /**
     * Mirrors the lookup-key semantics of {@see GameIndex}: any game-root
     * file whose extension-stripped name is `exfont` acts as the ExFont.
     */
    private static function isExFontName(string $lowercaseName): bool
    {
        $lastDotPosition = strrpos($lowercaseName, '.');
        $baseName = $lastDotPosition === false ? $lowercaseName : substr($lowercaseName, 0, $lastDotPosition);

        return $baseName === 'exfont';
    }
}
