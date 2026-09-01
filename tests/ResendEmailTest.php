<?php

declare(strict_types = 1);

use Kirby\Exception\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RealTroll\Website\ResendEmail;

#[CoversClass(ResendEmail::class)]
final class ResendEmailTest extends TestCase
{
    /** @var array{url: string, options: array}|null */
    private array|null $request = null;

    /** Sends a message through a spy client, since constructing a `ResendEmail` sends it. */
    private function send(
        array $props = [],
        string $apiKey = 're_test',
        array $response = ['code' => 200, 'body' => ['id' => 'a1b2c3']]
    ): void {
        new ResendEmail(
            $apiKey,
            array_replace([
                'from' => 'realtroll@kirby.tools',
                'fromName' => 'realtroll.de Kommentare',
                'to' => 'real_troll@example.com',
                'subject' => 'Neuer Kommentar von Anna zu Artikel A',
                'body' => ['html' => '<p>Hallo</p>', 'text' => 'Hallo']
            ], $props),
            false,
            function (string $url, array $options) use ($response): array {
                $this->request = ['url' => $url, 'options' => $options];

                return $response;
            }
        );
    }

    private function payload(): array
    {
        return json_decode($this->request['options']['data'], true);
    }

    #[Test]
    public function posts_the_subject_and_both_body_parts(): void
    {
        $this->send();

        $payload = $this->payload();

        $this->assertSame('Neuer Kommentar von Anna zu Artikel A', $payload['subject']);
        $this->assertSame('<p>Hallo</p>', $payload['html']);
        $this->assertSame('Hallo', $payload['text']);
    }

    #[Test]
    public function quotes_the_display_name_in_the_sender(): void
    {
        $this->send();

        $this->assertSame('"realtroll.de Kommentare" <realtroll@kirby.tools>', $this->payload()['from']);
    }

    #[Test]
    public function sends_a_bare_address_for_a_sender_without_a_name(): void
    {
        $this->send(['fromName' => null]);

        $this->assertSame('realtroll@kirby.tools', $this->payload()['from']);
    }

    #[Test]
    public function lists_every_to_address(): void
    {
        $this->send(['to' => ['first@example.com', 'second@example.com']]);

        $this->assertSame(['first@example.com', 'second@example.com'], $this->payload()['to']);
    }

    #[Test]
    public function sends_cc_and_bcc_as_lists(): void
    {
        $this->send(['cc' => 'kopie@example.com', 'bcc' => 'blind@example.com']);

        $payload = $this->payload();

        $this->assertSame(['kopie@example.com'], $payload['cc']);
        $this->assertSame(['blind@example.com'], $payload['bcc']);
    }

    #[Test]
    public function omits_html_for_a_plain_text_message(): void
    {
        $this->send(['body' => ['text' => 'Nur Text']]);

        $this->assertArrayNotHasKey('html', $this->payload());
    }

    #[Test]
    public function omits_the_text_part_for_an_html_only_message(): void
    {
        $this->send(['body' => ['html' => '<p>Hallo</p>']]);

        $this->assertArrayNotHasKey('text', $this->payload());
    }

    #[Test]
    public function sends_reply_to_as_a_list(): void
    {
        $this->send(['replyTo' => 'antwort@example.com', 'replyToName' => 'Antwort']);

        $this->assertSame(['"Antwort" <antwort@example.com>'], $this->payload()['reply_to']);
    }

    #[Test]
    public function posts_json_to_the_emails_endpoint_with_a_bearer_token(): void
    {
        $this->send();

        $headers = $this->request['options']['headers'];

        $this->assertSame('https://api.resend.com/emails', $this->request['url']);
        $this->assertSame('Bearer re_test', $headers['Authorization']);
        $this->assertSame('application/json', $headers['Content-Type']);
    }

    #[Test]
    public function bounds_the_request_with_a_5_second_timeout(): void
    {
        $this->send();

        $this->assertSame(5, $this->request['options']['timeout']);
    }

    #[Test]
    public function throws_for_a_message_with_an_attachment(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The Resend transport does not support attachments');

        $this->send(['attachments' => ['/tmp/beleg.pdf']]);
    }

    #[Test]
    public function throws_on_an_empty_api_key(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing Resend API key');

        $this->send(apiKey: '');
    }

    #[Test]
    public function throws_with_the_status_and_reason_resend_gives(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Resend rejected the message (HTTP 403): The kirby.tools domain is not verified');

        $this->send(response: [
            'code' => 403,
            'body' => ['message' => 'The kirby.tools domain is not verified']
        ]);
    }
}
