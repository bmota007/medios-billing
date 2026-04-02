<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InvoicePaidMail; // Corrected Mailable name

class InvoiceController extends Controller
{
    /**
     * 1. ADMINISTRATIVE VIEWS
     */

    public function showForm()
    {
        $user = auth()->user();
        $customers = Customer::where('company_id', $user->company_id)->orderBy('name')->get();
        $company = $user->company;
        return view('invoice.form', compact('customers', 'company'));
    }

    public function history(Request $request)
    {
        $query = Invoice::where('company_id', auth()->user()->company_id);
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhere('invoice_no', 'LIKE', "%{$search}%");
            });
        }
        $invoices = $query->latest()->paginate(15);
        return view('invoice.history', compact('invoices'));
    }

    public function view($id_or_no)
    {
        $invoice = Invoice::with('company')
            ->where('invoice_no', $id_or_no)
            ->orWhere('id', $id_or_no) 
            ->firstOrFail();

        if ((int)$invoice->company_id !== (int)auth()->user()->company_id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $items = json_decode($invoice->items, true) ?? [];
        return view('invoice.public_view', compact('invoice', 'items'));
    }

    public function edit($id_or_no)
    {
        $invoice = Invoice::where('invoice_no', $id_or_no)->orWhere('id', $id_or_no)->firstOrFail();
        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        return view('invoice.form', compact('invoice', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->customer_name = $request->customer_name;
        $invoice->customer_email = $request->customer_email;
        $invoice->items = json_encode($request->items ?? []);
        
        $total = 0;
        foreach ($request->items as $item) {
            $total += ($item['qty'] ?? 1) * ($item['price'] ?? 0);
        }
        $invoice->total = $total;
        $invoice->save();

        return redirect()->route('invoice.view', $invoice->invoice_no)->with('success', 'Invoice Updated Successfully!');
    }

    /**
     * 2. CORE ACTIONS
     */

    public function send(Request $request)
    {
        $company = auth()->user()->company;
        $invoice = new Invoice();
        $invoice->company_id = $company->id;
        $invoice->customer_name = $request->customer_name;
        $invoice->customer_email = $request->customer_email;
        $invoice->street_address = $request->street_address;
        $invoice->city_state_zip = $request->city_state_zip;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->due_date = $request->due_date;
        $invoice->invoice_no = 'INV-' . time();
        
        $items = $request->items ?? [];
        $invoice->items = json_encode($items);

        $total = 0;
        foreach ($items as $item) {
            $total += ($item['qty'] ?? 1) * ($item['price'] ?? 0);
        }
        
        $invoice->total = $total;
        $invoice->status = 'pending';
        $invoice->save();

        return redirect()->route('invoice.view', $invoice->invoice_no)->with('success', 'Invoice generated successfully!');
    }

    public function sendEmail($invoice_no)
    {
        $invoice = Invoice::with('company')
            ->where('invoice_no', $invoice_no)
            ->orWhere('id', $invoice_no)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);

        Mail::send('emails.invoice_notification', ['invoice' => $invoice], function ($message) use ($invoice, $pdf) {
            $message->to($invoice->customer_email)
                    ->subject('Invoice #' . $invoice->invoice_no . ' from ' . $invoice->company->name)
                    ->attachData($pdf->output(), $invoice->invoice_no . '.pdf');
        });

        return redirect()->back()->with('success', 'Invoice sent to customer successfully!');
    }

    public function resend($id)
    {
        return $this->sendEmail($id);
    }

    public function downloadPdf($id_or_no)
    {
        $invoice = Invoice::with('company')
            ->where('invoice_no', $id_or_no)
            ->orWhere('id', $id_or_no)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice]);
        return $pdf->download($invoice->invoice_no . '.pdf');
    }

    public function destroy(Invoice $invoice)
    {
        if ((int)$invoice->company_id !== (int)auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
        $invoice->delete();
        return redirect()->route('invoice.history')->with('success', 'Invoice deleted successfully.');
    }

public function markPaid($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        // Send Professional Receipt
        try {
            Mail::to($invoice->customer_email)->send(new \App\Mail\InvoicePaidMail($invoice));
        } catch (\Exception $e) {
            Log::error("Manual Mark Paid Email Error: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Invoice marked as paid.');
    }

    /**
     * 3. PUBLIC & PAYMENT METHODS
     */

    public function publicView($invoice_no)
    {
        try {
            $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();
            return view('invoice.public', compact('invoice'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'INVOICE LOAD FAILED'], 500);
        }
    }

    public function showPaymentPage($invoice_no)
    {
        $invoice = Invoice::with('company')->where('invoice_no', $invoice_no)->firstOrFail();
        return view('invoice.payment', compact('invoice'));
    }

    public function stripeCheckout($invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)->with('company')->firstOrFail();
        $company = $invoice->company;

        $stripeSecret = ($company->stripe_mode === 'live') 
            ? $company->stripe_secret_key 
            : $company->stripe_test_secret_key;

        \Stripe\Stripe::setApiKey($stripeSecret ?: config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Payment for Invoice #{$invoice->invoice_no}",
                    ],
                    'unit_amount' => (int)($invoice->total * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', $invoice->invoice_no),
            'cancel_url' => route('invoice.public_view', $invoice->invoice_no),
            'metadata' => [
                'invoice_no' => $invoice->invoice_no,
                'company_id' => $company->id
            ],
        ]);

        return redirect($session->url);
    }

    public function stripeSuccess($invoice_no)
    {
        $invoice = Invoice::with('company')->where('invoice_no', $invoice_no)->firstOrFail();
        if ($invoice->status !== 'paid') {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);
        }
        return view('invoice.success', compact('invoice'));
    }

public function submitManualPayment(Request $request, $invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $request->payment_method
        ]);

        // Send Professional Receipt
        try {
            Mail::to($invoice->customer_email)->send(new \App\Mail\InvoicePaidMail($invoice));
        } catch (\Exception $e) {
            Log::error("Manual Payment Email Error: " . $e->getMessage());
        }

        return redirect()->route('stripe.success', $invoice->invoice_no);
    }

}
