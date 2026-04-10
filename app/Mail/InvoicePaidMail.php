<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $items = json_decode($this->invoice->items, true) ?? [];

        // Generate PDF
$pdf = Pdf::loadView('invoice.pdf', [
    'invoice' => $this->invoice,
    'items' => json_decode($this->invoice->items, true) ?? []
]);


        return $this->subject('Payment Received - Invoice #' . $this->invoice->invoice_no)
            ->view('emails.invoice_paid')
            ->attachData(
                $pdf->output(),
                'Invoice-' . $this->invoice->invoice_no . '.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
