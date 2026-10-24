<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        $migration = require database_path('migrations/2022_10_24_221735_create_messages_table.php');
        $migration->up();
    }

    public function test_valid_message_is_saved_and_acknowledged(): void
    {
        $this->withSession($this->captchaSession())->postJson('/message', [
            'name' => 'Portfolio Visitor',
            'email' => 'visitor@example.com',
            'message' => 'I would like to discuss a project.',
            'status' => 0,
            'captcha' => 'abc23',
        ])->assertStatus(201)->assertJson([
            'status' => 'success',
            'msg' => 'Thank you! Your message has been received.',
        ]);

        $this->assertDatabaseHas('messages', [
            'name' => 'Portfolio Visitor',
            'email' => 'visitor@example.com',
            'description' => 'I would like to discuss a project.',
            'status' => 1,
        ]);
    }

    public function test_missing_fields_are_rejected(): void
    {
        $this->postJson('/message', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message', 'captcha']);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_invalid_and_oversized_fields_are_rejected(): void
    {
        $this->postJson('/message', [
            'name' => str_repeat('a', 256),
            'email' => 'not-an-email',
            'message' => str_repeat('a', 10001),
        ])->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_database_failure_returns_a_safe_error(): void
    {
        Schema::drop('messages');

        $response = $this->withSession($this->captchaSession())->postJson('/message', [
            'name' => 'Portfolio Visitor',
            'email' => 'visitor@example.com',
            'message' => 'Please contact me.',
            'captcha' => 'ABC23',
        ]);

        $response->assertStatus(503)->assertExactJson([
            'status' => 'error',
            'msg' => 'Your message could not be saved. Please try again later.',
        ]);
    }

    public function test_captcha_is_required_without_a_session_challenge(): void
    {
        $this->postJson('/message', $this->validMessage())
            ->assertStatus(422)->assertJsonValidationErrors('captcha');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_wrong_answer_consumes_the_challenge(): void
    {
        $this->withSession($this->captchaSession())
            ->postJson('/message', array_replace($this->validMessage(), ['captcha' => 'ZZZZZ']))
            ->assertStatus(422)->assertJsonValidationErrors('captcha');

        $this->postJson('/message', $this->validMessage())
            ->assertStatus(422)->assertJsonValidationErrors('captcha');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_expired_captcha_is_rejected(): void
    {
        $this->withSession($this->captchaSession(now()->subSecond()->timestamp))
            ->postJson('/message', $this->validMessage())
            ->assertStatus(422)->assertJsonValidationErrors('captcha');

        $this->assertDatabaseCount('messages', 0);
    }

    public function test_successful_captcha_cannot_be_replayed(): void
    {
        $this->withSession($this->captchaSession())
            ->postJson('/message', $this->validMessage())->assertStatus(201);
        $this->postJson('/message', $this->validMessage())
            ->assertStatus(422)->assertJsonValidationErrors('captcha');

        $this->assertDatabaseCount('messages', 1);
    }

    public function test_captcha_image_is_png_and_is_not_cached(): void
    {
        $response = $this->get('/contact/captcha');

        $response->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame("\x89PNG\r\n\x1a\n", substr($response->getContent(), 0, 8));
        $challenge = session('contact_captcha');
        $this->assertSame(64, strlen($challenge['digest']));
        $this->assertSame(now()->addMinutes(5)->timestamp, $challenge['expires_at']);
    }

    public function test_new_image_replaces_the_previous_challenge(): void
    {
        $this->get('/contact/captcha')->assertOk();
        $first = session('contact_captcha.digest');
        $this->get('/contact/captcha')->assertOk();

        $this->assertNotSame($first, session('contact_captcha.digest'));
    }

    public function test_message_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/message', [])->assertStatus(422);
        }

        $this->postJson('/message', [])->assertStatus(429)->assertHeader('Retry-After');
    }

    public function test_captcha_image_requests_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $this->get('/contact/captcha')->assertOk();
        }

        $this->get('/contact/captcha')->assertStatus(429);
    }

    private function captchaSession(?int $expiry = null): array
    {
        return ['contact_captcha' => [
            'digest' => hash('sha256', 'ABC23'),
            'expires_at' => $expiry ?? now()->addMinutes(5)->timestamp,
        ]];
    }

    private function validMessage(): array
    {
        return [
            'name' => 'Portfolio Visitor',
            'email' => 'visitor@example.com',
            'message' => 'Please contact me.',
            'captcha' => 'ABC23',
        ];
    }
}
