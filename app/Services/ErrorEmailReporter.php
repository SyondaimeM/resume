<?php

namespace App\Services;

use App\Mail\SystemError;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ErrorEmailReporter
{
    private bool $sending = false;

    public function report(Throwable $exception): void
    {
        if ($this->sending || !config('portfolio_notifications.errors_enabled')) {
            return;
        }
        $this->sending = true;
        try {
            $recipient = config('portfolio_notifications.recipient');
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                Log::error('Error email could not be sent: PORTFOLIO_NOTIFICATION_EMAIL is missing or invalid.');
                return;
            }
            $fingerprint = hash('sha256', get_class($exception).'|'.$exception->getFile().'|'.$exception->getLine());
            if (!Cache::store('file')->add('error-email:'.$fingerprint, true, config('portfolio_notifications.error_throttle_seconds'))) {
                return;
            }
            Mail::to($recipient)->send(new SystemError([
                'reference' => substr($fingerprint, 0, 12),
                'environment' => config('app.env'), 'time' => now()->toIso8601String(),
                'exception' => get_class($exception), 'file' => basename($exception->getFile()),
                'line' => $exception->getLine(),
            ]));
        } catch (Throwable $notificationFailure) {
            // Reporting must never replace the original error or recursively send more mail.
            Log::error('System error email delivery failed.', ['exception_type' => get_class($notificationFailure)]);
        } finally {
            $this->sending = false;
        }
    }
}
