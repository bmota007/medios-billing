<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeOnboardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $plan = $this->company->plan_name ?? $this->company->plan ?? 'Starter';

        return $this->subject('Welcome to Medios Billing – Your ' . $plan . ' Plan Is Active')
                    ->view('emails.welcome-onboard');
    }
}
