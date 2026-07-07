<?php

declare(strict_types = 1);

namespace RealTroll\Comments;

use Kirby\Http\Remote;
use Throwable;

/**
 * Cloudflare Turnstile siteverify adapter over `Remote::post`, fail-closed.
 *
 * The HTTP client is injectable (a callable returning the decoded JSON body,
 * throwing on transport failure) so tests never touch the network. Production
 * uses the default `Remote::post` client.
 */
final class Turnstile
{
    private const ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /** @var callable(string, array): array */
    private $client;

    public function __construct(
        private readonly string $secret,
        callable|null $client = null,
        private readonly string|null $expectedHostname = null,
        private readonly string|null $expectedAction = null
    ) {
        $this->client = $client ?? static fn (string $url, array $options): array =>
            Remote::post($url, $options)->json() ?? [];
    }

    public function verify(string|null $token, string|null $ip): bool
    {
        if ($token === null || $token === '') {
            return false;
        }

        try {
            $result = ($this->client)(self::ENDPOINT, [
                'data' => [
                    'secret'   => $this->secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ],
                'timeout' => 5,
            ]);
        } catch (Throwable) {
            // Remote throws on an unreachable host – treat as a failed check.
            return false;
        }

        if (($result['success'] ?? false) !== true) {
            return false;
        }

        // Cross-check the widget origin when configured, to block replaying a
        // token minted for another site/action.
        if ($this->expectedHostname !== null && ($result['hostname'] ?? null) !== $this->expectedHostname) {
            return false;
        }

        if ($this->expectedAction !== null && ($result['action'] ?? null) !== $this->expectedAction) {
            return false;
        }

        return true;
    }
}
