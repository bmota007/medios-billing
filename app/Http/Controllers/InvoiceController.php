<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Charge;

class InvoiceController extends Controller
{
    public function history()
    {
        $invoices = Invoice::where(
            'company_id',
            auth()->user()->company_id
        )->latest()->get();

        return view('invoices.index', compact('invoices'));
    }

    public function showPaymentPage($invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)
            ->orWhere('invoice_number', $invoice_no)
            ->firstOrFail();

        return view('invoices.pay', [
            'invoice' => $invoice,
            'company' => $invoice->company
        ]);
    }

    public function pay($id)
    {
        $invoice = Invoice::findOrFail($id);

        return redirect()->route(
            'invoice.pay',
            $invoice->invoice_no
        );
    }

    // 🔥 DIRECT VIEW FIX (APPEND ONLY)
    public function payDirect($id)
    {
        $invoice = Invoice::where('id', $id)
            ->with('company', 'customer')
            ->firstOrFail();

        return view('invoices.pay', compact('invoice'));
    }

    public function stripePost(Request $request, $id)
    {
        $invoice = Invoice::where('id', $id)
            ->with('customer', 'company')
            ->firstOrFail();

        // 🔥 STORE PAYMENT METHOD
        $invoice->payment_method =
            $request->payment_method ?? 'card';

        $invoice->save();

        $company = $invoice->company;

        $isTest = (
            $company->billing_mode === 'test' ||
            env('APP_BILLING_MODE') === 'test'
        );

        Stripe::setApiKey(
            $isTest
                ? (
                    $company->stripe_test_secret_key
                    ?? env('STRIPE_TEST_SECRET')
                )
                : (
                    $company->stripe_secret_key
                    ?? env('STRIPE_SECRET')
                )
        );

        try {

            Charge::create([
                "amount" => $invoice->total * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Invoice " . $invoice->invoice_no
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);

            if ($invoice->quote_id) {
                Quote::where('id', $invoice->quote_id)
                    ->update(['status' => 'paid']);
            }

            Mail::raw(
                "Payment received for Invoice #{$invoice->invoice_no}.",
                function ($m) use ($company) {
                    $m->to($company->email)
                        ->subject('Payment Received');
                }
            );

return back()->with(
    'success',
    'Payment successful!'
);

} catch (\Exception $e) {

    return back()->with(
        'error',
        $e->getMessage()
    );

}
}
    public function publicView($invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)
            ->orWhere('invoice_number', $invoice_no)
            ->firstOrFail();

        return view('invoices.show', compact('invoice'));
    }

    // 🔥 STRIPE CHECKOUT SESSION
    public function stripeCheckout($invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)
            ->with('company')
            ->firstOrFail();

        $company = $invoice->company;

        $isTest = ($company->stripe_mode ?? 'test') === 'test';

        \Stripe\Stripe::setApiKey(
            $isTest
                ? $company->stripe_test_secret_key
                : $company->stripe_secret_key
        );

        try {

            $session = \Stripe\Checkout\Session::create([

                'payment_method_types' => ['card'],

                'mode' => 'payment',

                'line_items' => [[

                    'price_data' => [

                        'currency' => 'usd',

                        'product_data' => [
                            'name' => 'Invoice #' . $invoice->invoice_no,
                        ],

                        'unit_amount' => intval(
                            $invoice->total * 100
                        ),

                    ],

                    'quantity' => 1,

                ]],

                'success_url' => url(
                    '/invoice/success/' . $invoice->invoice_no
                ),

                'cancel_url' => url(
                    '/invoice/pay/' . $invoice->invoice_no
                ),

            ]);

            return redirect($session->url);

        } catch (\Exception $e) {

            dd($e->getMessage());

        }
    }

    // 🔥 STRIPE SUCCESS HANDLER
    public function stripeSuccess($invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)
            ->orWhere('invoice_number', $invoice_no)
            ->first();

        if (!$invoice) {
            return redirect('/')
                ->with('error', 'Invoice not found.');
        }

        if ($invoice->status !== 'paid') {

            $invoice->status = 'paid';
            $invoice->paid_at = now();
            $invoice->payment_method = 'stripe';

            $invoice->save();
        }

        return view('invoices.success', compact('invoice'));
    }

    // ✅ INTERNAL INVOICE VIEW
    public function view($id)
    {
        $invoice = Invoice::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    // ✅ SHOW CREATE INVOICE FORM
    public function showForm()
    {
        $customers = \App\Models\Customer::where(
            'company_id',
            auth()->user()->company_id
        )->orderBy('name')->get();

        $company = auth()->user()->company;

        return view(
            'invoice.form',
            compact('customers', 'company')
        );
    }

// ✅ CREATE & SEND INVOICE
public function send(Request $request)
{
    $company = auth()->user()->company;

    // ✅ SAFE CUSTOMER CREATION
    $customer = null;

    if (!empty($request->customer_email)) {

$customer = \App\Models\Customer::firstOrCreate(
    [
        'email' => $request->customer_email,
    ],
    [
        'name' => $request->customer_name ?? 'Customer',

        'phone' => $request->customer_phone,

        'street_address' =>
            $request->street_address
            ?? 'Not Provided',

        'city_state_zip' =>
            $request->city_state_zip
            ?? 'Not Provided'
    ]
);
    }

    // ✅ Build invoice items
    $items = [];

    $subtotal = 0;

    foreach ($request->items as $item) {

        $qty = (float) ($item['qty'] ?? 0);

        $price = (float) ($item['price'] ?? 0);

        $lineTotal = $qty * $price;

        $subtotal += $lineTotal;

        $items[] = [
            'desc' => $item['description'] ?? '',
            'qty' => $qty,
            'price' => $price,
            'line_total' => $lineTotal
        ];
    }

    $taxPercent = (float) (
        $request->tax_percent ?? 0
    );

    $taxAmount = ($subtotal * $taxPercent) / 100;

    $grandTotal = $subtotal + $taxAmount;

    // ✅ Create invoice
    $invoice = new Invoice();

    $invoice->company_id = $company->id;

    // ✅ SAFE CUSTOMER ID
    $invoice->customer_id = $customer?->id;

    $invoice->invoice_no = 'INV-' . time();

    // ✅ LEGACY COLUMN SUPPORT
        $invoice->invoice_number = $invoice->invoice_no;

        $invoice->invoice_date = $request->invoice_date;

        $invoice->due_date = $request->due_date;

        $invoice->subtotal = $subtotal;

        $invoice->tax_percent = $taxPercent;

        $invoice->tax_amount = $taxAmount;

        $invoice->total = $grandTotal;

// ✅ SAVE CUSTOMER SNAPSHOT
$invoice->customer_name = $request->customer_name;
$invoice->customer_email = $request->customer_email;
$invoice->customer_phone = $request->customer_phone;

$invoice->street_address =
    $request->street_address;

$invoice->city_state_zip =
    $request->city_state_zip;

// ✅ SAVE BILLING VALUES
$invoice->subtotal_amount =
    $request->subtotal_amount ?? $subtotal;

$invoice->deposit_percent =
    $request->deposit_percent ?? 0;

$invoice->deposit_amount =
    $request->deposit_amount ?? 0;

$invoice->remaining_balance =
    $request->remaining_balance ?? 0;

$invoice->remaining_due_date =
    $request->remaining_due_date;

$invoice->auto_charge_enabled =
    $request->has('auto_charge_enabled');

        $invoice->items = json_encode($items);

        $invoice->status = 'unpaid';

        $invoice->save();

// ✅ SEND EMAIL TO CUSTOMER
Mail::send(
    'emails.invoice_created',
    ['invoice' => $invoice],
    function ($m) use ($invoice) {

        $m->to(
            $invoice->customer_email
                ?? $invoice->customer->email
        );

        $m->subject(
            'Invoice #' . $invoice->invoice_no
        );
    }
);

// ✅ MARK AS SENT
$invoice->status = 'sent';

$invoice->save();

return redirect()
    ->route('invoice.view', $invoice->id)
    ->with(
        'success',
        'Invoice sent successfully!'
    );
}

    // ✅ SEND EXISTING INVOICE
    public function sendExisting(Request $request)
    {
        $invoice = Invoice::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($request->invoice_id);

        Mail::send(
            'emails.invoice_created',
            ['invoice' => $invoice],
            function ($m) use ($invoice) {

                $m->to(
                    $invoice->customer_email
                        ?? optional($invoice->customer)->email
                );

                $m->subject(
                    'Invoice #' . $invoice->invoice_no
                );
            }
        );

        $invoice->status = 'sent';

        $invoice->save();

        return redirect()->back()
            ->with(
                'success',
                'Invoice sent successfully!'
            );
    }

// ✅ RESEND INVOICE
public function resend($invoice)
{
    $invoice = Invoice::where(
        'company_id',
        auth()->user()->company_id
    )->findOrFail($invoice);

    Mail::send(
        'emails.invoice_created',
        ['invoice' => $invoice],
        function ($m) use ($invoice) {

            $m->to(
                $invoice->customer_email
                    ?? $invoice->customer->email
            );

            $m->subject(
                'Invoice #' . $invoice->invoice_no
            );
        }
    );

    return redirect()->back()
        ->with(
            'success',
            'Invoice resent successfully!'
        );
}

// ✅ DOWNLOAD PDF
    public function pdf($id)
    {
        $invoice = Invoice::where(
            'company_id',
            auth()->user()->company_id
        )->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'pdf.invoice',
            compact('invoice')
        );

        return $pdf->download(
            'Invoice-' . $invoice->invoice_no . '.pdf'
        );
    }
}
