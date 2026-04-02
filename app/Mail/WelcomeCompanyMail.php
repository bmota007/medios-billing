<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $company;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $company, $password)
    {
        $this->user = $user;
        $this->company = $company;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name') . ' - Your Account is Ready')
                    ->view('emails.welcome_company');
    }
}
