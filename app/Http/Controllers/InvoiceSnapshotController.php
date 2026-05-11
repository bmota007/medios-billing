<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSnapshot;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceSnapshotController extends Controller
{
    public function show($snapshot)
    {
        $snapshot = InvoiceSnapshot::findOrFail($snapshot);

        $data = $snapshot->snapshot_data;

        return view(
            'invoices.snapshot',
            compact('snapshot', 'data')
        );
    }

    public function download($snapshot)
    {
        $snapshot = InvoiceSnapshot::findOrFail($snapshot);

        $data = $snapshot->snapshot_data;

        $invoice = (object) $data['invoice'];

        $pdf = Pdf::loadView(
            'pdf.snapshot',
            compact('snapshot', 'data', 'invoice')
        );

        return $pdf->download(
            'Receipt-' .
            $snapshot->invoice_no .
            '.pdf'
        );
    }

    public function email($snapshot)
    {
        $snapshot = InvoiceSnapshot::findOrFail($snapshot);

        $data = $snapshot->snapshot_data;

        $invoice = (object) $data['invoice'];

        $customerEmail =
            $invoice->customer_email ?? null;

        if (!$customerEmail) {

            return back()->with(
                'error',
                'Customer email not found.'
            );
        }

        Mail::send(
            'emails.invoice_paid',
            ['invoice' => $invoice],
            function ($message) use (
                $customerEmail,
                $invoice
            ) {

                $message->to(
                    $customerEmail
                )->subject(
                    'Receipt - Invoice #' .
                    $invoice->invoice_no
                );
            }
        );

        return back()->with(
            'success',
            'Receipt emailed successfully.'
        );
    }
public function publicReceipt($token)
{
    $snapshot = InvoiceSnapshot::where(
        'public_token',
        $token
    )->firstOrFail();

    $data = $snapshot->snapshot_data;

    $invoice = (object) $data['invoice'];

    return view(
        'invoices.snapshot',
        compact(
            'snapshot',
            'data',
            'invoice'
        )
    );
}
public function publicDownload($token)
{
    $snapshot = InvoiceSnapshot::where(
        'public_token',
        $token
    )->firstOrFail();

    $data = $snapshot->snapshot_data;

    $invoice = (object) $data['invoice'];

    $pdf = Pdf::loadView(
        'pdf.snapshot',
        compact('snapshot', 'data', 'invoice')
    );

    return $pdf->download(
        'Receipt-' .
        $snapshot->invoice_no .
        '.pdf'
    );
}

}
