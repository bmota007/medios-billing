<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\InvoiceSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use App\Models\InvoiceEvent;

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
// ✅ CUSTOMER EMAIL FALLBACK

$customerEmail =
    $invoice->customer_email
    ?? optional($invoice->customer)->email;

// ✅ BACKUP CUSTOMER LOOKUP

if (
    !$customerEmail &&
    $invoice->customer_id
) {

    $customer =
        \App\Models\Customer::find(
            $invoice->customer_id
        );

    $customerEmail =
        $customer?->email;
}

// ✅ SEND PAID IN FULL RECEIPT

if ($customerEmail) {

    Mail::send(
        'emails.invoice_paid',
        ['invoice' => $invoice],
        function ($message) use (
            $invoice,
            $customerEmail
        ) {

            $message->to(
                $customerEmail
            )->subject(
                'Paid In Full Receipt - Invoice #' .
                $invoice->invoice_no
            );

        }
    );
}

// ✅ SEND COMPANY NOTIFICATION

if (
    $invoice->company &&
    $invoice->company->email
) {

    Mail::send(
        'emails.invoice_paid_admin',
        ['invoice' => $invoice],
        function ($message) use ($invoice) {

            $message->to(
                $invoice->company->email
            )->subject(
                'Invoice Paid In Full - #' .
                $invoice->invoice_no
            );

        }
    );
}
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

    $isTest = (
        $company->stripe_mode ?? 'test'
    ) === 'test';

    \Stripe\Stripe::setApiKey(
        $isTest
            ? $company->stripe_test_secret_key
            : $company->stripe_secret_key
    );

    // ✅ BLOCK DUPLICATE PAYMENTS

    if ($invoice->status === 'paid') {

        return redirect()
            ->back()
            ->with(
                'error',
                'This invoice has already been paid.'
            );
    }

    // ✅ BLOCK REPEATED DEPOSIT PAYMENTS

    if (
        $invoice->status === 'partial' &&
        $invoice->remaining_balance > 0
    ) {

        return redirect()
            ->back()
            ->with(
                'error',
                'Deposit already paid. Remaining balance still due.'
            );
    }

    try {

        $session = \Stripe\Checkout\Session::create([

'payment_method_types' => ['card'],

'mode' => 'payment',

'customer_creation' => 'always',

'payment_intent_data' => [

    'setup_future_usage' => 'off_session',

],

'line_items' => [[

    'price_data' => [

        'currency' => 'usd',

        'product_data' => [

            'name' =>
                'Invoice #' .
                $invoice->invoice_no,

        ],

'unit_amount' => (int) round(

    (
        $invoice->deposit_amount > 0
            ? $invoice->deposit_amount
            : $invoice->total

    ) * 100

),

    ],

    'quantity' => 1,


            ]],
'customer_email' =>
    $invoice->customer_email,
'metadata' => [

    'invoice_no' =>
        $invoice->invoice_no,

],
            'success_url' => url(
                '/invoice/success/' .
                $invoice->invoice_no
            ),

            'cancel_url' => url(
                '/invoice/pay/' .
                $invoice->invoice_no
            ),

        ]);

        return redirect($session->url);

    } catch (\Exception $e) {

        return back()->with(
            'error',
            $e->getMessage()
        );
    }
}

// 🔥 STRIPE SUCCESS HANDLER
public function stripeSuccess($invoice_no)
{
    $invoice = Invoice::where('invoice_no', $invoice_no)
        ->orWhere('invoice_number', $invoice_no)
        ->with('company', 'customer')
        ->first();

    if (!$invoice) {

        return redirect('/')
            ->with(
                'error',
                'Invoice not found.'
            );
    }

    // ✅ ONLY PROCESS ONCE

    if (
        $invoice->status !== 'paid' &&
        $invoice->status !== 'partial'
    ) {

// ✅ DETERMINE AMOUNT PAID

if (
    $invoice->deposit_amount > 0 &&
    $invoice->remaining_balance > 0
) {

    // ✅ DEPOSIT PAYMENT ONLY

    $invoice->amount_paid =
        round($invoice->deposit_amount, 2);

    $invoice->remaining_balance =
        round(
            $invoice->total -
            $invoice->deposit_amount,
            2
        );

    $invoice->status = 'partial';

} else {

    // ✅ FULL PAYMENT

    $invoice->amount_paid =
        round($invoice->total, 2);

    $invoice->remaining_balance = 0;

    $invoice->status = 'paid';
}

$invoice->paid_at = now();

$invoice->payment_method = 'stripe';
// ✅ STORE STRIPE CUSTOMER + PAYMENT METHOD
try {

    $company = $invoice->company;

    $isTest = (
        $company->stripe_mode ?? 'test'
    ) === 'test';

    \Stripe\Stripe::setApiKey(
        $isTest
            ? $company->stripe_test_secret_key
            : $company->stripe_secret_key
    );

    // Get latest checkout session
    $sessions = \Stripe\Checkout\Session::all([
        'limit' => 1,
    ]);

    if (
        isset($sessions->data[0])
    ) {

        $session = $sessions->data[0];

        $invoice->stripe_customer_id =
            $session->customer ?? null;

        // Pull payment intent
        if ($session->payment_intent) {

            $intent =
                \Stripe\PaymentIntent::retrieve(
                    $session->payment_intent
                );

            $invoice->stripe_payment_method_id =
                $intent->payment_method ?? null;
        }

        $invoice->save();
    }

} catch (\Exception $e) {

    \Log::error(
        'Stripe save error: ' .
        $e->getMessage()
    );
}

$invoice->save();
if ($invoice->status === 'partial') {

    InvoiceEvent::create([

        'invoice_id' => $invoice->id,

        'company_id' => $invoice->company_id,

        'user_id' => auth()->id(),

        'event_type' => 'deposit_paid',

        'title' => 'Deposit Payment Received',

        'description' => 'Customer paid invoice deposit.',

        'event_data' => [

            'amount_paid' => $invoice->amount_paid,

        ],

        'ip_address' => request()->ip(),

    ]);

}

// ✅ CREATE DEPOSIT RECEIPT SNAPSHOT

if ($invoice->status === 'partial') {

    InvoiceSnapshot::create([

        'invoice_id' =>
            $invoice->id,

        'invoice_no' =>
            $invoice->invoice_no,

        'snapshot_type' =>
            'deposit_receipt',

        'snapshot_data' => [

            'invoice' => $invoice
                ->load('company', 'customer')
                ->toArray(),

        ],

        'amount' =>
            $invoice->amount_paid,

        'payment_reference' =>
            $invoice->payment_reference,

        'snapshot_created_at' =>
            now(),

    ]);
}

// ✅ MARK RELATED QUOTE STATUS

if ($invoice->quote_id) {

    Quote::where(
        'id',
        $invoice->quote_id
    )->update([
        'status' => $invoice->status
    ]);
}

// ✅ CUSTOMER EMAIL FALLBACK

$customerEmail =
    $invoice->customer_email
    ?? optional($invoice->customer)->email;

// ✅ DEBUG FALLBACK SAFETY

if (
    !$customerEmail &&
    $invoice->customer_id
) {

    $customer =
        \App\Models\Customer::find(
            $invoice->customer_id
        );

    $customerEmail =
        $customer?->email;
}

// ✅ EMAIL CUSTOMER RECEIPT

if ($customerEmail) {

    Mail::send(
        'emails.invoice_paid',
        ['invoice' => $invoice],
        function ($message) use (
            $invoice,
            $customerEmail
        ) {

            $message->to(
                $customerEmail
            )->subject(
                'Payment Receipt - Invoice #' .
                $invoice->invoice_no
            );

        }
    );
}

// ✅ EMAIL TENANT / COMPANY

        if (
            $invoice->company &&
            $invoice->company->email
        ) {

            Mail::send(
                'emails.invoice_paid_admin',
                ['invoice' => $invoice],
                function ($message) use ($invoice) {

                    $message->to(
                        $invoice->company->email
                    )->subject(
                        'Invoice Paid - #' .
                        $invoice->invoice_no
                    );

                }
            );
        }
    }

    return view(
        'invoices.success',
        compact('invoice')
    );
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
InvoiceEvent::create([

    'invoice_id' => $invoice->id,

    'company_id' => auth()->user()->company_id ?? null,

    'user_id' => auth()->id(),

    'event_type' => 'invoice_created',

    'title' => 'Invoice Created',

    'description' => 'Invoice was successfully created.',

    'event_data' => [

        'invoice_no' => $invoice->invoice_no,

        'total' => $invoice->total,

    ],

    'ip_address' => request()->ip(),

]);

// ✅ CREATE ORIGINAL SNAPSHOT

InvoiceSnapshot::create([

    'invoice_id' =>
        $invoice->id,

    'invoice_no' =>
        $invoice->invoice_no,

    'snapshot_type' =>
        'original',

    'snapshot_data' => [

        'invoice' => $invoice->toArray(),

        'items' => $items,

    ],

    'amount' =>
        $invoice->total,

    'snapshot_created_at' =>
        now(),

]);

/*
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
*/

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
// ✅ DELETE INVOICE
public function destroy($invoice)
{
    $invoice = Invoice::where(
        'company_id',
        auth()->user()->company_id
    )->findOrFail($invoice);

    // ✅ OPTIONAL SAFETY:
    // Prevent deleting paid invoices

// ✅ OPTIONAL PROTECTION
// Allow deleting paid invoices for admins/testing

if (
    $invoice->status === 'paid' &&
    auth()->user()->role !== 'admin'
) {

    return redirect()
        ->back()
        ->with(
            'error',
            'Paid invoices cannot be deleted.'
        );
}

// ✅ DELETE INVOICE

$invoice->delete();

return redirect('/invoices')
    ->with(
        'success',
        'Invoice deleted successfully.'
    );
}

public function testEmail(Request $request, $invoice)
{
    $invoice = Invoice::where(
        'company_id',
        auth()->user()->company_id
    )->findOrFail($invoice);

    Mail::send(
        'emails.invoice_created',
        ['invoice' => $invoice],
        function ($m) use ($invoice, $request) {

            $m->to($request->test_email);

            $m->subject(
                'TEST Invoice #' . $invoice->invoice_no
            );
        }
    );

    return redirect()->back()
        ->with(
            'success',
            'Test email sent successfully!'
        );
}
public function chargeRemainingBalance($invoice)
{
    $invoice = Invoice::where(
        'company_id',
        auth()->user()->company_id
    )->findOrFail($invoice);

    // ✅ MUST HAVE STRIPE CUSTOMER

    if (
        !$invoice->stripe_customer_id ||
        !$invoice->stripe_payment_method_id
    ) {

        return redirect()
            ->back()
            ->with(
                'error',
                'Missing Stripe customer or payment method.'
            );
    }

    // ✅ MUST HAVE BALANCE

    if ($invoice->remaining_balance <= 0) {

        return redirect()
            ->back()
            ->with(
                'error',
                'No remaining balance due.'
            );
    }

    try {

        $company = $invoice->company;

        $isTest = (
            $company->stripe_mode ?? 'test'
        ) === 'test';

        \Stripe\Stripe::setApiKey(

            $isTest
                ? $company->stripe_test_secret_key
                : $company->stripe_secret_key

        );

        $intent = \Stripe\PaymentIntent::create([

            'amount' => intval(
                $invoice->remaining_balance * 100
            ),

            'currency' => 'usd',

            'customer' =>
                $invoice->stripe_customer_id,

            'payment_method' =>
                $invoice->stripe_payment_method_id,

            'off_session' => true,

            'confirm' => true,

            'description' =>
                'Remaining balance charge for invoice #' .
                $invoice->invoice_no,

        ]);

        // ✅ MARK FULLY PAID

        $invoice->amount_paid =
            $invoice->total;

        $invoice->remaining_balance = 0;

        $invoice->status = 'paid';

        $invoice->remaining_charged_at =
            now();

        $invoice->payment_reference =
            $intent->id;

        $invoice->paid_at = now();

        $invoice->save();
InvoiceEvent::create([

    'invoice_id' => $invoice->id,

    'company_id' => $invoice->company_id,

    'user_id' => auth()->id(),

    'event_type' => 'invoice_paid',

    'title' => 'Invoice Paid In Full',

    'description' => 'Invoice balance fully paid.',

    'event_data' => [

        'payment_reference' => $invoice->payment_reference,

        'amount_paid' => $invoice->amount_paid,

    ],

    'ip_address' => request()->ip(),

]);

// ✅ CREATE FINAL RECEIPT SNAPSHOT

InvoiceSnapshot::create([

    'invoice_id' =>
        $invoice->id,

    'invoice_no' =>
        $invoice->invoice_no,

    'snapshot_type' =>
        'final_receipt',

    'snapshot_data' => [

        'invoice' =>
            $invoice->toArray(),

    ],

    'amount' =>
        $invoice->total,

    'payment_reference' =>
        $intent->id ?? null,

    'snapshot_created_at' =>
        now(),

]);

// ✅ CUSTOMER EMAIL FALLBACK

$customerEmail =
    $invoice->customer_email
    ?? optional($invoice->customer)->email;

// ✅ BACKUP CUSTOMER LOOKUP

if (
    !$customerEmail &&
    $invoice->customer_id
) {

    $customer =
        \App\Models\Customer::find(
            $invoice->customer_id
        );

    $customerEmail =
        $customer?->email;
}

// ✅ SEND PAID IN FULL RECEIPT

if ($customerEmail) {

    Mail::send(
        'emails.invoice_paid',
        ['invoice' => $invoice],
        function ($message) use (
            $invoice,
            $customerEmail
        ) {

            $message->to(
                $customerEmail
            )->subject(
                'Paid In Full Receipt - Invoice #' .
                $invoice->invoice_no
            );

        }
    );
}

// ✅ SEND COMPANY NOTIFICATION

if (
    $invoice->company &&
    $invoice->company->email
) {

    Mail::send(
        'emails.invoice_paid_admin',
        ['invoice' => $invoice],
        function ($message) use ($invoice) {

            $message->to(
                $invoice->company->email
            )->subject(
                'Invoice Paid In Full - #' .
                $invoice->invoice_no
            );

        }
    );
}

        // ✅ UPDATE RELATED QUOTE

        if ($invoice->quote_id) {

            Quote::where(
                'id',
                $invoice->quote_id
            )->update([
                'status' => 'paid'
            ]);
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'Remaining balance charged successfully.'
            );

    } catch (\Exception $e) {

        \Log::error(
            'Remaining balance charge failed: ' .
            $e->getMessage()
        );

        return redirect()
            ->back()
            ->with(
                'error',
                'Stripe charge failed: ' .
                $e->getMessage()
            );
    }
}
}
