<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeStaffMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function build()
    {
        // This links the logic to the beautiful HTML template we made
        return $this->subject('Welcome to the Team | ' . $this->details['company'])
                    ->view('emails.welcome_staff');
    }
}

