<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $failDays;

    /**
     * Create a new message instance.
     */
    public function __construct(Company $company, $failDays)
    {
        $this->company = $company;
        $this->failDays = $failDays;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Action Required: Payment for Medios Billing Failed')
                    ->view('emails.payment-failed');
    }
}
