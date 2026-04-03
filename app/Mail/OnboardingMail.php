<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OnboardingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $token;

    /**
     * Create a new message instance for CUSTOMER onboarding.
     */
    public function __construct(Company $company, $token)
    {
        $this->company = $company;
        $this->token = $token;
    }

    /**
     * Build the message using the dedicated customer onboarding template.
     */
    public function build()
    {
        return $this->subject('Verify Your Business - ' . config('app.name'))
                    ->view('emails.onboarding');
    }
}
