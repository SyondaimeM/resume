<?php

namespace App\Jobs;

use App\Mail\ContactSubmitted;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SendContactNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(public int $messageId)
    {
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(): void
    {
        $recipient = config('portfolio_notifications.recipient');
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Configure PORTFOLIO_NOTIFICATION_EMAIL before sending contact notifications.');
        }

        Mail::to($recipient)->send(new ContactSubmitted(Message::findOrFail($this->messageId)));
    }
}
