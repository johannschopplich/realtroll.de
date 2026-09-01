<?php

use Kirby\Cms\App;
use RealTroll\Website\ResendEmail;

load([
    'RealTroll\\Website\\ResendEmail' => 'classes/ResendEmail.php',
], __DIR__);

if (!function_exists('dateFormatter')) {
    function dateFormatter(
        int $dateType = IntlDateFormatter::LONG,
        int $timeType = IntlDateFormatter::NONE
    ): IntlDateFormatter {
        static $formatters = [];
        $locale = App::instance()->languageCode() ?? 'de';
        $key = "{$locale}:{$dateType}:{$timeType}";
        return $formatters[$key] ??= IntlDateFormatter::create($locale, $dateType, $timeType);
    }
}

App::plugin('realtroll/website', [
    'components' => [
        'email' => fn (App $kirby, array $props, bool $debug = false): ResendEmail => new ResendEmail(
            (string)$kirby->option('realtroll.website.resend.apiKey', ''),
            $props,
            $debug
        )
    ],
    'siteMethods' => [
        'realTroll' => function (): array {
            $sameAs = ['https://realtroll.de', 'https://realtroll.hpage.com'];
            foreach ($this->social()->toStructure() as $entry) {
                if ($entry->url()->isNotEmpty()) {
                    $sameAs[] = $entry->url()->value();
                }
            }

            return [
                '@type' => 'Person',
                'name' => 'real Troll',
                'url' => 'https://realtroll.de',
                'sameAs' => $sameAs
            ];
        }
    ],
    'pageMethods' => [
        'breadcrumbList' => function (): array {
            $crumbs = [$this->site()->homePage(), ...$this->parents()->flip()->values(), $this];

            $items = [];
            $position = 1;
            foreach ($crumbs as $crumb) {
                $items[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $crumb->title()->value(),
                    'item' => $crumb->url()
                ];
            }

            return ['itemListElement' => $items];
        }
    ]
]);
