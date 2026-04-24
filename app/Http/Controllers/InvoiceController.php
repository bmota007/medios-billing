<?php

namespace App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| IMPORTS
|--------------------------------------------------------------------------
*/
use App\Mail\InvoiceCreatedMail;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Services\TelnyxService; // ✅ Verified Import

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/
class InvoiceController extends Controller
{
    public function showPaymentPage($invoice_no)
    {
        $invoice = Invoice::with('company')
            ->where('invoice_no', $invoice_no)
            ->firstOrFail();

        if ($invoice->status === 'paid') {
            return redirect()->route('invoice.public_view', $invoice_no)
                ->with('error', 'This invoice has already been paid.');
        }

        $items = json_decode($invoice->items, true) ?? [];
        return view('invoice.payment', compact('invoice', 'items'));
    }

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

        if ($request->has('search') && $request->get('search') !== '') {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhere('invoice_no', 'LIKE', "%{$search}%");
            });
        }

        $invoices = $query->latest()->paginate(15);
        return view('invoice.history', compact('invoices'));
    }

    /**
     * ✅ FIX: Manual SMS trigger from History/View
     */
    public function sendInvoiceSms($id)
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $phone = $invoice->customer_phone ?? $invoice->customer->phone ?? null;

        if (!$phone) {
            return back()->with('error', 'Customer has no phone number.');
        }

        $message = "Hello {$invoice->customer_name}, your invoice #{$invoice->invoice_no} is ready. Total: $" . number_format($invoice->total, 2);
        $message .= "\nView here: " . route('invoice.public_view', $invoice->invoice_no);

        $sms = new TelnyxService();
        $sms->sendSms($phone, $message);

        return back()->with('success', 'SMS sent successfully!');
    }

    public function view($id_or_no)
    {
        $invoice = Invoice::with('company')
            ->where('invoice_no', $id_or_no)
            ->orWhere('id', $id_or_no)
            ->firstOrFail();

        if ((int)$invoice->company_id !== (int)auth()->user()->company_id) {
            abort(403);
        }

        $items = json_decode($invoice->items, true) ?? [];
        return view('invoice.public_view', compact('invoice', 'items'));
    }

    public function publicView($invoice_no)
    {
        $invoice = Invoice::with('company')
            ->where('invoice_no', $invoice_no)
            ->firstOrFail();

        $items = json_decode($invoice->items, true) ?? [];

        if ($invoice->status === 'sent') {
            $invoice->update(['status' => 'viewed']);
        }

        return view('invoice.public_view', compact('invoice', 'items'));
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with('company')->findOrFail($id);
        $items = json_decode($invoice->items, true) ?? [];
        $html = view('invoice.public_view', compact('invoice', 'items'))->render();

        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="invoice-'.$invoice->invoice_no.'.pdf"');
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        $user = auth()->user();
        $customers = Customer::where('company_id', $user->company_id)->orderBy('name')->get();
        $company = $user->company;

        return view('invoice.edit', compact('invoice', 'customers', 'company'));
    }

    public function send(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $invoice = new Invoice();
        $invoice->company_id = $company->id;
        $invoice->customer_name = $request->customer_name;
        $invoice->customer_email = $request->customer_email;
        $invoice->customer_phone = $request->customer_phone;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->due_date = $request->due_date;
        $invoice->remaining_due_date = $request->remaining_due_date;
        $invoice->invoice_no = 'INV-' . time();

        $items = is_array($request->items) ? $request->items : [];
        $invoice->items = json_encode($items);

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += ((float)$item['qty']) * ((float)$item['price']);
        }

        $taxPercent = (float)$request->tax_percent;
        $taxAmount = $subtotal * ($taxPercent / 100);
        $total = $subtotal + $taxAmount;

        $depositPercent = (float)$request->deposit_percent;
        $depositAmount = $total * ($depositPercent / 100);
        $remainingBalance = $total - $depositAmount;

        $invoice->subtotal_amount = $subtotal;
        $invoice->tax_percent = $taxPercent;
        $invoice->tax_amount = $taxAmount;
        $invoice->deposit_percent = $depositPercent;
        $invoice->deposit_amount = $depositAmount;
        $invoice->remaining_balance = $remainingBalance;
        $invoice->auto_charge_enabled = $request->has('auto_charge_enabled');
        $invoice->total = $total;
        $invoice->status = 'pending';

        $invoice->save();

        return redirect()->route('invoice.view', $invoice->invoice_no)
            ->with('success', 'Invoice created. Review before sending.');
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ((int)$invoice->company_id !== (int)auth()->user()->company_id) {
            abort(403);
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Cannot edit a fully paid invoice.');
        }

        $invoice->customer_name = $request->customer_name;
        $invoice->customer_email = $request->customer_email;
        $invoice->customer_phone = $request->customer_phone;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->due_date = $request->due_date;
        $invoice->remaining_due_date = $request->remaining_due_date;

        $items = is_array($request->items) ? $request->items : [];
        $invoice->items = json_encode($items);

        $subtotal = 0;
        foreach ($items as $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            $subtotal += $qty * $price;
        }

        $taxPercent = (float) ($request->tax_percent ?? 0);
        $taxAmount = $subtotal * ($taxPercent / 100);
        $total = $subtotal + $taxAmount;

        $depositPercent = (float) ($request->deposit_percent ?? 0);
        $depositAmount = $total * ($depositPercent / 100);

        $amountPaid = (float) ($invoice->amount_paid ?? 0);
        $remainingBalance = max($total - $amountPaid, 0);

        if ($amountPaid <= 0) {
            $invoice->status = 'pending';
        } elseif ($remainingBalance > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'paid';
        }

        $invoice->subtotal_amount = $subtotal;
        $invoice->tax_percent = $taxPercent;
        $invoice->tax_amount = $taxAmount;
        $invoice->deposit_percent = $depositPercent;
        $invoice->deposit_amount = $depositAmount;
        $invoice->remaining_balance = $remainingBalance;
        $invoice->total = $total;

        $invoice->save();

        return redirect()->route('invoice.view', $invoice->invoice_no)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * ✅ FIX: Main Dispatch (Email + SMS)
     */
    public function sendEmail($invoice_no)
    {
        $invoice = Invoice::with('company')->where('invoice_no', $invoice_no)->firstOrFail();

        try {
            if ($invoice->customer_email) {
                Mail::to($invoice->customer_email)->send(new \App\Mail\InvoiceCreatedMail($invoice));
            }

            if ($invoice->customer_phone) {
                $message = "Hi {$invoice->customer_name}, your invoice #{$invoice->invoice_no} is ready.\n";
                $message .= route('invoice.public_view', $invoice->invoice_no);
                
                // Switch from SmsService to TelnyxService
                $sms = new TelnyxService();
                $sms->sendSms($invoice->customer_phone, $message);
            }

            if ($invoice->status === 'pending') {
                $invoice->status = 'sent';
                $invoice->save();
            }

            return back()->with('success', 'Invoice sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * ✅ FIX: Direct SMS Send logic
     */
    public function sendSms(Request $request, $invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();
        
        $sms = new TelnyxService();
        $sms->sendSms($request->phone, $request->message);
        
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        if ((int)$invoice->company_id !== (int)auth()->user()->company_id) { abort(403); }
        $invoice->delete();
        return redirect()->route('invoice.history')->with('success', 'Invoice deleted successfully');
    }

    public function stripeCheckout($invoice_no)
    {
        $invoice = Invoice::with('company')->where('invoice_no', $invoice_no)->firstOrFail();
        \Stripe\Stripe::setApiKey($invoice->company->stripe_secret_key);

        $amountToCharge = ($invoice->deposit_amount > 0 && ($invoice->amount_paid ?? 0) == 0)
            ? $invoice->deposit_amount
            : ($invoice->remaining_balance ?? $invoice->total);

        $baseUrl = config('app.url');

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Invoice #' . $invoice->invoice_no],
                    'unit_amount' => (int) ($amountToCharge * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $baseUrl . '/invoice/success/' . $invoice->invoice_no,
            'cancel_url' => $baseUrl . '/invoice/view/' . $invoice->invoice_no,
        ]);

        return redirect($session->url);
    }

    public function stripeSuccess($invoice_no)
    {
        $invoice = Invoice::with('company')->where('invoice_no', $invoice_no)->firstOrFail();

        $amountPaidNow = ($invoice->deposit_amount > 0 && ($invoice->amount_paid ?? 0) == 0)
            ? $invoice->deposit_amount
            : ($invoice->remaining_balance ?? $invoice->total);

        $previousPaid = $invoice->amount_paid ?? 0;
        $newTotalPaid = $previousPaid + $amountPaidNow;

        $invoice->amount_paid = $newTotalPaid;
        $invoice->paid_at = now();

        $remaining = $invoice->total - $newTotalPaid;
        $invoice->remaining_balance = max($remaining, 0);

        if ($remaining <= 0) {
            $invoice->status = 'paid';
        } else {
            $invoice->status = 'partial';
        }

        $invoice->save();

        if ($invoice->customer_email) {
            Mail::to($invoice->customer_email)->send(new \App\Mail\InvoicePaidMail($invoice));
        }

        if ($invoice->company && $invoice->company->email) {
            Mail::to($invoice->company->email)->send(new \App\Mail\InvoicePaidAdminMail($invoice));
        }

        return redirect()->route('invoice.payment.success', $invoice_no);
    }

    public function submitManualPayment(Request $request, $invoice_no)
    {
        $invoice = Invoice::with('company')->where('invoice_no', $invoice_no)->firstOrFail();

        if ($invoice->status === 'paid') {
            return redirect()->route('invoice.public_view', $invoice_no)->with('error', 'Invoice already paid.');
        }

        $amountPaid = (float) $request->amount_paid;
        if ($amountPaid <= 0) { return back()->with('error', 'Invalid payment amount.'); }

        $invoice->payment_method = $request->payment_method;
        $invoice->payment_notes = $request->payment_notes ?? null;
        $invoice->check_number = $request->check_number ?? null;
        $invoice->paid_at = now();

        $previousPaid = $invoice->amount_paid ?? 0;
        $newTotalPaid = $previousPaid + $amountPaid;
        $invoice->amount_paid = $newTotalPaid;

        $remaining = $invoice->total - $newTotalPaid;
        $invoice->remaining_balance = max($remaining, 0);

        if ($remaining <= 0) {
            $invoice->status = 'paid';
        } elseif ($newTotalPaid > 0) {
            $invoice->status = 'partial';
        }

        $invoice->save();

        if ($invoice->customer_email) {
            Mail::to($invoice->customer_email)->send(new \App\Mail\InvoicePaidMail($invoice));
        }

        if ($invoice->company && $invoice->company->email) {
            Mail::to($invoice->company->email)->send(new \App\Mail\InvoicePaidAdminMail($invoice));
        }

        return redirect()->route('invoice.payment.success', $invoice_no);
    }

    public function paymentSuccess($invoice_no)
    {
        $invoice = Invoice::where('invoice_no', $invoice_no)->firstOrFail();
        return view('invoice.payment_success', compact('invoice'));
    }
}
