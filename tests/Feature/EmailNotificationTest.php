<?php

namespace Tests\Feature;

use App\Jobs\SendContactNotification;
use App\Mail\ContactSubmitted;
use App\Mail\SystemError;
use App\Models\Message;
use App\Services\ErrorEmailReporter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class EmailNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        config(['cache.stores.file' => ['driver' => 'array']]);
        Mail::fake();
    }

    public function test_contact_job_sends_to_owner_with_visitor_reply_to(): void
    {
        $record = Message::create(['name' => 'Visitor', 'email' => 'visitor@example.test', 'description' => 'A new inquiry', 'status' => 1]);
        (new SendContactNotification($record->id))->handle();
        Mail::assertSent(ContactSubmitted::class, function ($mail) {
            $mail->build();
            return $mail->hasTo('owner@example.test') && $mail->hasReplyTo('visitor@example.test')
                && str_contains(view('emails.contact-submitted', ['messageRecord' => $mail->messageRecord])->render(), 'A new inquiry');
        });
    }

    public function test_reported_errors_send_redacted_throttled_email_without_hiding_logs(): void
    {
        config(['portfolio_notifications.errors_enabled' => true]);
        $error = new RuntimeException('secret-password-and-request-data');
        report($error);
        report(new RuntimeException('secret-password-and-request-data'));
        Mail::assertSent(SystemError::class, 2);
        Mail::assertSent(SystemError::class, function ($mail) {
            return $mail->hasTo('owner@example.test')
                && !str_contains(view('emails.system-error', ['details' => $mail->details])->render(), 'secret-password-and-request-data');
        });
        $reporter = app(ErrorEmailReporter::class);
        $reporter->report($error);
        $reporter->report($error);
        Mail::assertSent(SystemError::class, 2);
    }

    public function test_email_failure_is_logged_and_does_not_replace_original_exception(): void
    {
        config(['portfolio_notifications.errors_enabled' => true]);
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Transport unavailable'));
        Log::shouldReceive('error')->once()->with('System error email delivery failed.', ['exception_type' => RuntimeException::class]);
        app(ErrorEmailReporter::class)->report(new RuntimeException('Original error'));
        $this->assertTrue(true);
    }

    public function test_disabled_error_alerts_do_not_send_mail(): void
    {
        app(ErrorEmailReporter::class)->report(new RuntimeException('test'));
        Mail::assertNothingSent();
    }

    public function test_error_logs_and_browser_reports_send_safe_alerts(): void
    {
        config(['portfolio_notifications.errors_enabled' => true]);
        Log::error('A sensitive diagnostic that must not appear in email');
        Mail::assertSent(SystemError::class, 1);
        $this->postJson('/client-errors', ['kind' => 'script', 'source' => 'app.js', 'line' => 42])
            ->assertNoContent();
        Mail::assertSent(SystemError::class, 2);
        $this->postJson('/client-errors', ['kind' => 'script', 'source' => 'app.js', 'line' => 42])
            ->assertNoContent();
        Mail::assertSent(SystemError::class, 2);
        $this->postJson('/client-errors', ['kind' => 'script', 'source' => 'https://bad.test/private?secret=yes'])
            ->assertUnprocessable();
        $this->postJson('/client-errors', ['kind' => 'promise'])->assertStatus(429);
    }
}
