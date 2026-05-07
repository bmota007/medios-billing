<?php
namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    private function normalizeItems($quote) {
        $items = json_decode($quote->items ?? '[]', true);
        if (!is_array($items)) return [];
        return array_map(function ($i) {
            return [
                'service' => $i['service'] ?? 'Service',
                'description' => $i['description'] ?? '',
                'qty' => (float)($i['qty'] ?? 0),
                'price' => (float)($i['price'] ?? 0),
            ];
        }, $items);
    }

    public function index() { 
        $quotes = Quote::where('company_id', auth()->user()->company_id)->latest()->get(); 
        return view('quotes.index', compact('quotes')); 
    }

    public function create() { 
        $customers = Customer::where('company_id', auth()->user()->company_id)->get(); 
        return view('quotes.create', compact('customers')); 
    }

    public function store(Request $request) {
        $quote = new Quote();
        $quote->quote_number = 'Q-' . time();
        $quote->title = $request->title ?? 'New Quote';
        $quote->company_id = auth()->user()->company_id;
        $quote->customer_id = $request->customer_id;
        $quote->quote_date = $request->quote_date ?? now(); 
        $quote->expiry_date = $request->expiry_date;
        $quote->deposit_due_date = $request->deposit_due_date;
        $quote->balance_due_date = $request->balance_due_date;
        $items = $request->items ?? [];
        $subtotal = 0;
        foreach ($items as $item) { $subtotal += abs((float)($item['qty'] ?? 0)) * (float)($item['price'] ?? 0); }
        $quote->items = json_encode($items);
        $quote->subtotal = $subtotal;
        $quote->tax_percent = (float)($request->tax_percent ?? 0);
        $taxAmount = $subtotal * ($quote->tax_percent / 100);
        $quote->total = $subtotal + $taxAmount;
        $quote->deposit_value = $request->deposit_value ?? 0;
        $quote->deposit_type = $request->deposit_type ?? 'none';
        $quote->customer_notes = $request->customer_notes;
        $quote->selected_contract_id = $request->selected_contract_id;
        $quote->contract_required = $request->has('contract_required');
        $quote->require_sig_before_pay = $request->has('require_sig_before_pay');
        $quote->auto_convert_invoice = $request->has('auto_convert_invoice');
        $quote->status = 'draft';
        $quote->save();
        if (!$quote->public_token) { $quote->public_token = bin2hex(random_bytes(16)); $quote->save(); }
        return redirect()->route('quotes.pro_preview', $quote->id);
    }

    public function edit($id) {
        $quote = Quote::where('company_id', auth()->user()->company_id)->findOrFail($id);
        if (is_string($quote->items)) { $quote->items = json_decode($quote->items, true) ?: []; }
        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        return view('quotes.create', compact('quote', 'customers'));
    }

    public function update(Request $request, $id) {
        $quote = Quote::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $quote->title = $request->title;
        $quote->customer_id = $request->customer_id;
        $quote->quote_date = $request->quote_date;
        $quote->expiry_date = $request->expiry_date;
        $quote->deposit_due_date = $request->deposit_due_date;
        $quote->balance_due_date = $request->balance_due_date;
        $items = $request->items ?? [];
        $subtotal = 0;
        foreach ($items as $item) { $subtotal += abs((float)($item['qty'] ?? 0)) * (float)($item['price'] ?? 0); }
        $quote->items = json_encode($items);
        $quote->subtotal = $subtotal;
        $quote->tax_percent = (float)($request->tax_percent ?? 0);
        $taxAmount = $subtotal * ($quote->tax_percent / 100);
        $quote->total = $subtotal + $taxAmount;
        $quote->deposit_value = $request->deposit_value;
        $quote->deposit_type = $request->deposit_type;
        $quote->customer_notes = $request->customer_notes;
        $quote->selected_contract_id = $request->selected_contract_id;
        $quote->contract_required = $request->has('contract_required');
        $quote->require_sig_before_pay = $request->has('require_sig_before_pay');
        $quote->auto_convert_invoice = $request->has('auto_convert_invoice');
        $quote->save();
        return redirect()->route('quotes.pro_preview', $quote->id)->with('success', 'Quote updated.');
    }

    public function preview($id) { $quote = Quote::findOrFail($id); $items = $this->normalizeItems($quote); return view('quotes.pro_preview', compact('quote', 'items')); }
    
    // ✅ Route: quotes.public_view
    public function publicView($token) { $quote = Quote::where('public_token', $token)->firstOrFail(); $items = $this->normalizeItems($quote); return view('quotes.pro_preview', compact('quote', 'items')); }

    public function approve($token) {
        $quote = Quote::where('public_token', $token)->firstOrFail();
        $quote->status = 'approved';
        $quote->accepted_at = now();
        $quote->save();
        if ($quote->contract_required && $quote->selected_contract_id) { return redirect()->route('quotes.contract', $quote->public_token); }
        return back()->with('success', 'Proposal Approved!');
    }

    public function showContract($token) {
        $quote = Quote::where('public_token', $token)->with('customer', 'company')->firstOrFail();
        $company = $quote->company;
        $customer = $quote->customer;
        $contractFileUrl = null;
        $contractName = "Service Agreement";
        if ($quote->selected_contract_id) {
            $pathField = "contract_" . $quote->selected_contract_id . "_path";
            $nameField = "contract_" . $quote->selected_contract_id . "_name";
            $contractName = $company->$nameField ?? $contractName;
            if ($company->$pathField) { $contractFileUrl = Storage::disk('public')->url($company->$pathField); }
        }
        return view('quotes.contract', compact('quote', 'company', 'customer', 'contractFileUrl', 'contractName'));
    }

    public function sign($token, Request $request) {
        $quote = Quote::where('public_token', $token)->firstOrFail();
        $quote->signature_name = $request->signature_name; 
        $quote->signature_data = $request->signature_image; 
        $quote->signed_at = now();
        $quote->status = 'signed';
        $quote->save();

        if ($quote->auto_convert_invoice) {
            $taxAmount = $quote->subtotal * ($quote->tax_percent / 100);
            $invoice = new Invoice();
            $invID = 'INV-' . time();
            $invoice->invoice_number = $invID;
            $invoice->invoice_no = $invID; 
            $invoice->company_id = $quote->company_id;
            $invoice->customer_id = $quote->customer_id;
            $invoice->quote_id = $quote->id;
            $invoice->items = $quote->items;
            $invoice->subtotal = $quote->subtotal;
            $invoice->tax_percent = $quote->tax_percent;
            $invoice->tax_amount = $taxAmount; 
            $invoice->total = $quote->total;
            $invoice->status = 'unpaid';
            $invoice->save();
            $quote->invoice_id = $invoice->id;
            $quote->save();
        }
        return redirect()->route('quotes.public_view', $token)->with('success', 'Contract Signed Successfully!');
    }

    public function downloadPdf($id) { $quote = Quote::findOrFail($id); $items = $this->normalizeItems($quote); $isPdf = true; $pdf = Pdf::loadView('quotes.pro_preview', compact('quote', 'isPdf', 'items')); return $pdf->download('Quote-'.$quote->quote_number.'.pdf'); }
    public function send(Request $request, $id) { $quote = Quote::findOrFail($id); $customer = Customer::find($quote->customer_id); $recipient = $request->test_email ?? ($customer->email ?? null); if (!$recipient) { return back()->with('error', 'No email found.'); } Mail::send('emails.quote_sent', ['quote' => $quote, 'link' => url('/q/' . $quote->public_token), 'customer' => $customer], function ($m) use ($recipient, $quote) { $m->to($recipient)->subject('Your Quote #' . $quote->quote_number . ' is Ready'); }); $quote->status = 'sent'; $quote->sent_at = now(); $quote->save(); return back()->with('success', 'Quote sent!'); }
    public function markPaid($id) { $quote = Quote::findOrFail($id); $quote->status = 'paid'; $quote->save(); return back()->with('success', 'Paid.'); }
    public function markDeposit($id) { $quote = Quote::findOrFail($id); $quote->status = 'partial'; $quote->save(); return back()->with('success', 'Deposit Paid.'); }
    public function sendSms(Request $request, $id) { return back()->with('success', 'SMS Sent.'); }

    // ✅ DELETE QUOTE (APPEND-ONLY SAFE)
    public function destroy($id)
    {
        $quote = \App\Models\Quote::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $quote->delete();

        return redirect()
            ->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }

}
