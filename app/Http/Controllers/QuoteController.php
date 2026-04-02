<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\QuoteItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with(['customer'])->where('company_id', auth()->user()->company_id)->latest()->get();
        foreach ($quotes as $quote) {
            if (!$quote->customer) {
                $quote->customer = (object)['name' => 'Unknown Customer', 'email' => '', 'phone' => ''];
            }
        }
        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        return view('quotes.create', compact('customers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate(['customer_id' => 'required', 'items' => 'required|array']);
            $total = 0;
            foreach ($request->items as $item) { $total += ((float)$item['qty'] * (float)$item['price']); }

            $depositAmount = 0;
            if ($request->deposit_type === 'percentage') { $depositAmount = ($total * (float)$request->deposit_value) / 100; }
            elseif ($request->deposit_type === 'fixed') { $depositAmount = (float)$request->deposit_value; }

            $quote = Quote::create([
                'company_id' => auth()->user()->company_id,
                'customer_id' => $request->customer_id,
                'quote_number' => 'Q' . now()->format('ymdHis'),
                'public_token' => Str::random(40),
                'title' => 'Service Quote',
                'subtotal' => $total,
                'total' => $total,
                'deposit_type' => $request->deposit_type ?? 'none',
                'deposit_value' => $request->deposit_value ?? 0,
                'deposit_amount' => $depositAmount,
                'remaining_amount' => $total - $depositAmount,
                'status' => 'draft',
                'contract_required' => $request->boolean('contract_required'),
            ]);

            foreach ($request->items as $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'service_name' => $item['service'] ?? 'Service',
                    'quantity' => $item['qty'] ?? 1,
                    'unit_price' => $item['price'] ?? 0,
                    'line_total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                ]);
            }
            return redirect()->route('quotes.show', $quote->id);
        } catch (\Exception $e) { return back()->with('error', 'Store Error: ' . $e->getMessage()); }
    }

    public function show(Quote $quote) { $quote->load(['customer', 'items', 'company']); return view('quotes.show', compact('quote')); }

    public function publicView($token) { $quote = Quote::with(['customer', 'items', 'company'])->where('public_token', $token)->firstOrFail(); return view('quotes.public', compact('quote')); }

    public function showContract($token) { $quote = Quote::with(['customer', 'items', 'company'])->where('public_token', $token)->firstOrFail(); return view('quotes.contract', compact('quote')); }

public function signContract(Request $request, $token)
{
    try {
        $quote = Quote::with(['company', 'customer'])->where('public_token', $token)->firstOrFail();
        
        $quote->update([
            'contract_status' => 'signed',
            'contract_signed_at' => now(),
            'signed_by' => $request->sign_name ?? 'Customer',
            'status' => 'approved'
        ]);

        // Create the invoice first so we have the ID/No for the email
        $invoice = $this->createInvoiceFromQuote($quote);

        // ✅ 1. SEND NOTIFICATION TO COMPANY (Using your contract_signed template)
        try {
            Mail::send('emails.contract_signed', ['quote' => $quote, 'invoice' => $invoice], function ($m) use ($quote) {
                $m->to($quote->company->email)
                  ->subject('🖋️ Contract Signed: Quote #' . $quote->quote_number . ' - ' . $quote->customer->name);
            });
        } catch (\Exception $e) {
            Log::error("Company Sign Notification Fail: " . $e->getMessage());
        }

        // ✅ 2. SEND CONFIRMATION TO CUSTOMER (Using your quote_approved template)
        try {
            Mail::send('emails.quote_approved', ['quote' => $quote], function ($m) use ($quote) {
                $m->to($quote->customer->email)
                  ->subject('✅ Quote Approved & Contract Signed - ' . $quote->company->name);
            });
        } catch (\Exception $e) {
            Log::error("Customer Sign Notification Fail: " . $e->getMessage());
        }

        if ($invoice) {
            return redirect()->route('invoice.public_view', ['invoice_no' => $invoice->invoice_no]);
        }
        
        return redirect()->route('quotes.public', $token)->with('success', 'Contract Signed!');

    } catch (\Exception $e) {
        Log::error("Signing Error: " . $e->getMessage());
        return "Server Error during signing. Our team has been notified.";
    }
}

    public function approve($token)
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();
        $quote->update(['status' => 'approved', 'accepted_at' => now()]);
        
        if ($quote->contract_required) { return redirect()->route('quotes.contract', ['token' => $token]); }

        $invoice = $this->createInvoiceFromQuote($quote);
        return redirect()->route('invoice.public_view', ['invoice_no' => $invoice->invoice_no]);
    }

    protected function createInvoiceFromQuote(Quote $quote)
    {
        try {
            $quote->load(['customer', 'items', 'company']);
            $items = [];
            foreach ($quote->items as $item) {
                $items[] = [
                    'service_name' => $item->service_name,
                    'quantity' => (float) $item->quantity,
                    'price' => (float) $item->unit_price,
                    'total' => (float) $item->line_total,
                ];
            }

            return Invoice::create([
                'company_id' => $quote->company_id,
                'customer_name' => $quote->customer->name ?? 'Customer',
                'customer_email' => $quote->customer->email ?? '',
                'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'items' => json_encode($items),
                'subtotal' => (float) $quote->total,
                'total' => ($quote->deposit_amount > 0) ? (float) $quote->deposit_amount : (float) $quote->total,
                'status' => 'unpaid',
            ]);
        } catch (\Exception $e) {
            Log::error("Invoice Generation Fail: " . $e->getMessage());
            return null;
        }
    }

    public function edit($id)
    {
        $quote = Quote::with(['items', 'customer'])->findOrFail($id);
        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        return view('quotes.create', compact('quote', 'customers'));
    }

public function update(Request $request, $id)
{
    $quote = Quote::findOrFail($id);
    
    // 1. Calculate the new total
    $total = 0;
    foreach ($request->items as $item) { 
        $total += ((float)$item['qty'] * (float)$item['price']); 
    }

    // 2. RECALCULATE DEPOSIT MATH (This was the missing piece)
    $depositAmount = 0;
    if ($request->deposit_type === 'percentage') { 
        $depositAmount = ($total * (float)$request->deposit_value) / 100; 
    } elseif ($request->deposit_type === 'fixed') { 
        $depositAmount = (float)$request->deposit_value; 
    }

    // 3. Update the Quote
    $quote->update([
        'customer_id' => $request->customer_id,
        'subtotal' => $total,
        'total' => $total,
        'deposit_type' => $request->deposit_type,
        'deposit_value' => $request->deposit_value,
        'deposit_amount' => $depositAmount, // Update the actual dollars
        'remaining_amount' => $total - $depositAmount, // Update the remainder
        'contract_required' => $request->has('contract_required'),
    ]);

    // Update items as you were doing before...
    $quote->items()->delete();
    foreach ($request->items as $item) {
        QuoteItem::create([
            'quote_id' => $quote->id,
            'service_name' => $item['service'],
            'quantity' => $item['qty'],
            'unit_price' => $item['price'],
            'line_total' => $item['qty'] * $item['price'],
        ]);
    }

    return redirect()->route('quotes.show', $quote->id);
}

    public function destroy($id) { Quote::findOrFail($id)->delete(); return redirect()->route('quotes.index'); }

    public function send($id)
    {
        $quote = Quote::with(['customer', 'items', 'company'])->findOrFail($id);
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'));
        $link = url('/q/' . $quote->public_token);
        Mail::send('emails.quote_sent', ['quote' => $quote, 'link' => $link], function ($m) use ($quote, $pdf) {
            $m->to($quote->customer->email)->subject('New Quote #' . $quote->quote_number)->attachData($pdf->output(), "Quote_{$quote->quote_number}.pdf");
        });
        $quote->update(['status' => 'sent']);
        return back()->with('success', 'Quote sent!');
    }

    public function stripeCheckout($token)
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();
        $company = $quote->company;
        \Stripe\Stripe::setApiKey($company->stripe_mode === 'live' ? $company->stripe_secret_key : $company->stripe_test_secret_key);
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [['price_data' => ['currency' => 'usd', 'product_data' => ['name' => 'Quote #'.$quote->quote_number], 'unit_amount' => (int)(($quote->deposit_amount > 0 ? $quote->deposit_amount : $quote->total) * 100)], 'quantity' => 1]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', ['invoice_no' => $quote->quote_number]),
            'cancel_url' => route('quotes.public', $token),
            'metadata' => ['quote_id' => $quote->id, 'company_id' => $company->id],
        ]);
        return redirect($session->url);
    }
}
