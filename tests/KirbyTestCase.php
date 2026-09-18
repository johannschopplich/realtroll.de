<?php

declare(strict_types = 1);

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;
use PHPUnit\Framework\TestCase;

abstract class KirbyTestCase extends TestCase
{
    protected function tearDown(): void
    {
        App::destroy();
        Dir::remove(static::indexRoot());
    }

    protected static function bootApp(array $props = []): App
    {
        return new App(array_replace_recursive([
            'roots'      => ['index' => static::indexRoot()],
            'urls'       => ['index' => 'https://realtroll.de'],
            'pageModels' => ['article' => ArticlePage::class],
        ], $props));
    }

    protected static function indexRoot(): string
    {
        return __DIR__ . '/tmp/' . Str::kebab((new ReflectionClass(static::class))->getShortName());
    }
}
