<?php

declare(strict_types = 1);

namespace RealTroll\Website;

use Kirby\Email\Email;
use Kirby\Exception\Exception;
use Kirby\Http\Remote;

/**
 * Resend transport over the HTTPS API.
 *
 * The HTTP client is injectable so tests never touch the network; transport
 * failure surfaces as a throw, not as a status code.
 */
final class ResendEmail extends Email
{
    private const ENDPOINT = 'https://api.resend.com/emails';

    /** @var callable(string, array): array{code: int|null, body: array} */
    private $client;

    public function __construct(
        private readonly string $apiKey,
        array $props = [],
        bool $debug = false,
        callable|null $client = null
    ) {
        $this->client = $client ?? static function (string $url, array $options): array {
            $response = Remote::post($url, $options);

            return ['code' => $response->code(), 'body' => $response->json() ?? []];
        };

        // The parent constructor sends unless `debug` is set, so everything
        // `send` reaches for has to be in place before it runs.
        parent::__construct($props, $debug);
    }

    public function send(): bool
    {
        if ($this->apiKey === '') {
            throw new Exception(message: 'Missing Resend API key');
        }

        if ($this->attachments() !== []) {
            throw new Exception(message: 'The Resend transport does not support attachments');
        }

        $response = ($this->client)(self::ENDPOINT, [
            'data' => json_encode($this->payload(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ],
            'timeout' => 5
        ]);

        $code = $response['code'] ?? null;

        if ($code === null || $code < 200 || $code > 299) {
            throw new Exception(message: sprintf(
                'Resend rejected the message (HTTP %s): %s',
                $code ?? 'none',
                $response['body']['message'] ?? 'no reason given'
            ));
        }

        return $this->isSent = true;
    }

    private function payload(): array
    {
        $payload = [
            'from' => self::address($this->from(), $this->fromName()),
            'to' => self::recipients($this->to()),
            'subject' => $this->subject()
        ];

        if ($this->isHtml() === true) {
            $payload['html'] = $this->body()->html();
        }

        // `Body` fills a missing part with an empty string, which Resend would
        // send as an empty alternative part rather than as no part at all.
        if ($this->body()->text() !== '') {
            $payload['text'] = $this->body()->text();
        }

        if ($this->cc() !== []) {
            $payload['cc'] = self::recipients($this->cc());
        }

        if ($this->bcc() !== []) {
            $payload['bcc'] = self::recipients($this->bcc());
        }

        if ($replyTo = $this->replyTo()) {
            $payload['reply_to'] = [self::address($replyTo, $this->replyToName())];
        }

        return $payload;
    }

    /**
     * @param array<string, string|null> $addresses
     * @return list<string>
     */
    private static function recipients(array $addresses): array
    {
        $recipients = [];

        foreach ($addresses as $email => $name) {
            $recipients[] = self::address($email, $name);
        }

        return $recipients;
    }

    private static function address(string $email, string|null $name): string
    {
        if ($name === null || $name === '') {
            return $email;
        }

        // RFC 5322 requires quoting for a name carrying a period, and quoting
        // one that does not need it is harmless.
        return sprintf('"%s" <%s>', addcslashes($name, '"\\'), $email);
    }
}
