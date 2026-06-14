<?php

use Kirby\Cms\Page;

class GamePage extends Page
{
    public function metadata(): array
    {
        $site = $this->site();

        $videoGame = [
            'name' => $this->title()->value(),
            'url' => $this->url(),
            'inLanguage' => 'de',
            'applicationCategory' => 'GameApplication',
            'operatingSystem' => $this->gameFolder()->isNotEmpty()
                ? ['Windows', 'Web browser']
                : 'Windows',
            'author' => [
                '@type' => 'Person',
                '@id' => $site->url() . '/#person-realtroll',
                'name' => 'real Troll',
                'url' => 'https://realtroll.hpage.com',
                'sameAs' => [
                    'https://realtroll.hpage.com',
                    'https://www.instagram.com/der_real_troll/',
                    'https://www.youtube.com/user/realtroll'
                ]
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR',
                'availability' => 'https://schema.org/InStock',
                'url' => $this->url()
            ],
        ];

        if ($this->description()->isNotEmpty()) {
            $videoGame['description'] = $this->description()->value();
        }

        if ($this->published()->isNotEmpty()) {
            $videoGame['datePublished'] = $this->published()->toDate('Y-m-d');
        }

        $screenshots = $this->screenshots()->toFiles()->map(fn ($file) => $file->url())->values();
        if (!empty($screenshots)) {
            $videoGame['image'] = $screenshots;
        }

        if ($this->engine()->isNotEmpty()) {
            $videoGame['additionalProperty'] = [
                '@type' => 'PropertyValue',
                'name' => 'Engine',
                'value' => $this->engine()->value()
            ];
        }

        return [
            'jsonld' => [
                'VideoGame' => $videoGame,
            ]
        ];
    }
}
