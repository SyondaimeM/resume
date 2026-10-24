<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Mail\Mailable;

class ContactSubmitted extends Mailable
{
    public function __construct(public Message $messageRecord)
    {
    }

    public function build()
    {
        return $this->subject('New portfolio contact submission #'.$this->messageRecord->id)
            ->replyTo($this->messageRecord->email, $this->messageRecord->name)
            ->text('emails.contact-submitted');
    }
}
