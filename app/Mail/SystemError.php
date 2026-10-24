<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SystemError extends Mailable
{
    public function __construct(public array $details)
    {
    }

    public function build()
    {
        return $this->subject('Portfolio system error ['.$this->details['reference'].']')
            ->text('emails.system-error');
    }
}
