<?php

declare(strict_types = 1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Comments\Turnstile;

#[CoversClass(Turnstile::class)]
final class TurnstileTest extends TestCase
{
    #[Test]
    public function accepts_a_successful_verification(): void
    {
        $turnstile = new Turnstile('secret', static fn (): array => ['success' => true]);

        $this->assertTrue($turnstile->verify('token', '203.0.113.1'));
    }

    #[Test]
    public function rejects_an_unsuccessful_verification(): void
    {
        $turnstile = new Turnstile('secret', static fn (): array => ['success' => false]);

        $this->assertFalse($turnstile->verify('token', '203.0.113.1'));
    }

    #[Test]
    public function fails_closed_when_the_client_throws(): void
    {
        $turnstile = new Turnstile('secret', static function (): array {
            throw new RuntimeException('host unreachable');
        });

        $this->assertFalse($turnstile->verify('token', '203.0.113.1'));
    }

    #[Test]
    public function rejects_a_missing_token(): void
    {
        $turnstile = new Turnstile('secret', static fn (): array => ['success' => true]);

        $this->assertFalse($turnstile->verify(null, '203.0.113.1'));
        $this->assertFalse($turnstile->verify('', '203.0.113.1'));
    }

    #[Test]
    public function rejects_a_token_minted_for_another_hostname(): void
    {
        $turnstile = new Turnstile(
            'secret',
            static fn (): array => ['success' => true, 'hostname' => 'evil.example.com'],
            'realtroll.de'
        );

        $this->assertFalse($turnstile->verify('token', '203.0.113.1'));
    }

    #[Test]
    public function rejects_a_token_minted_for_another_action(): void
    {
        $turnstile = new Turnstile(
            'secret',
            static fn (): array => ['success' => true, 'hostname' => 'realtroll.de', 'action' => 'login'],
            'realtroll.de',
            'comment'
        );

        $this->assertFalse($turnstile->verify('token', '203.0.113.1'));
    }
}
