<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /* =========================
       SHOW FORM
    ========================= */
    public function showForm(Request $request)
    {
$customers = Customer::where('company_id', auth()->user()->company_id)
                     ->orderBy('name')
                     ->get();

        $selectedCustomer = null;

        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::find($request->customer_id);
        }

        return view('invoice.form', compact('customers', 'selectedCustomer'));
    }

    /* =========================
       PREVIEW BEFORE SAVE
    ========================= */
    public function preview(Request $request)
    {
        $data = $this->buildInvoiceData($request);

        $data['invoice_date'] = Carbon::parse($data['invoice_date'])->format('m/d/Y');
        $data['due_date']     = Carbon::parse($data['due_date'])->format('m/d/Y');
        $data['notes']        = $this->limitNotes($data['notes'] ?? '');
        $data['is_receipt']   = false;

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $data['pdf_base64'] = base64_encode($pdf->output());

        return view('invoice.preview', $data);
    }

    /* =========================
       SAVE + SEND INVOICE
    ========================= */
    public function send(Request $request)
    {
        $data = $this->buildInvoiceData($request);

        $invoice = Invoice::create([
            'invoice_no'      => $data['invoice_no'],
            'customer_name'   => $data['customer_name'],
            'customer_email'  => $data['customer_email'],
            'street_address'  => $data['street_address'],
            'city_state_zip'  => $data['city_state_zip'],
            'invoice_date'    => $data['invoice_date'],
            'due_date'        => $data['due_date'],
            'total'           => $data['grand_total'],
            'notes'           => $data['notes'],
            'items'           => $data['items'],
            'sent_at'         => now(),
            'status'          => 'unpaid',
        ]);

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $this->emailInvoice($invoice, $pdf);

        return redirect()->route('invoice.history')
            ->with('success', 'Invoice sent successfully.');
    }

    /* =========================
       VIEW INVOICE
    ========================= */
    public function view(Invoice $invoice)
    {
        $sub_total = $this->calculateSubTotal($invoice);

        $data = $this->buildSavedInvoiceData($invoice, $sub_total, false);

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $pdf_base64 = base64_encode($pdf->output());

        return view('invoice.preview', array_merge($data, [
            'pdf_base64' => $pdf_base64,
            'status'     => $invoice->status,
            'invoiceId'  => $invoice->id,
        ]));
    }

    /* =========================
       VIEW RECEIPT
    ========================= */
    public function receipt(Invoice $invoice)
    {
        if ($invoice->status !== 'paid') {
            return redirect()->route('invoice.view', $invoice->id);
        }

        $sub_total = $this->calculateSubTotal($invoice);

        $data = $this->buildSavedInvoiceData($invoice, $sub_total, true);

        $pdf = Pdf::loadView('pdf.invoice', $data);
        $pdf_base64 = base64_encode($pdf->output());

        return view('invoice.preview', array_merge($data, [
            'pdf_base64' => $pdf_base64,
            'status'     => 'paid',
            'invoiceId'  => $invoice->id,
        ]));
    }

    /* =========================
       DELETE INVOICE
    ========================= */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('invoice.history')
            ->with('success', 'Invoice deleted successfully.');
    }

/* =========================
   RESEND RECEIPT
========================= */
public function resendReceipt(Invoice $invoice)
{
    if ($invoice->status !== 'paid') {
        return back()->withErrors([
            'error' => 'Receipt can only be resent for paid invoices.'
        ]);
    }

    $sub_total = $this->calculateSubTotal($invoice);

    $data = $this->buildSavedInvoiceData($invoice, $sub_total, true);

    // Generate receipt PDF
    $pdf = Pdf::loadView('pdf.invoice', $data);

    // Send receipt email again
    Mail::send('emails.invoice', [
        'invoice' => $invoice,
        'is_receipt' => true
    ], function ($message) use ($invoice, $pdf) {

        $message->to($invoice->customer_email)
            ->subject('Payment Received – Receipt #' . $invoice->invoice_no . ' | McIntosh Cleaning Services')
            ->attachData(
                $pdf->output(),
                'receipt-' . $invoice->invoice_no . '.pdf',
                ['mime' => 'application/pdf']
            );
    });

    return back()->with('success', 'Receipt resent successfully.');
}

    /* =========================
       HISTORY
    ========================= */
    public function history()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->paginate(15);
        return view('invoice.history', compact('invoices'));
    }

/* =========================
   MARK PAID
========================= */
public function markPaid(Request $request, Invoice $invoice)
{
    $invoice->update([
        'status'  => 'paid',
        'paid_at' => now(),
    ]);

    // Calculate subtotal
    $sub_total = 0;

    if (is_array($invoice->items)) {
        foreach ($invoice->items as $item) {
            $sub_total += $item['line_total'] ?? 0;
        }
    }

    // Build receipt data
    $data = [
        'customer_name'   => $invoice->customer_name,
        'customer_email'  => $invoice->customer_email,
        'street_address'  => $invoice->street_address,
        'city_state_zip'  => $invoice->city_state_zip,
        'invoice_date'    => $invoice->invoice_date,
        'due_date'        => $invoice->due_date,
        'notes'           => $invoice->notes,
        'items'           => $invoice->items,
        'invoice_no'      => $invoice->invoice_no,
        'sub_total'       => $sub_total,
        'grand_total'     => $invoice->total,
        'is_receipt'      => true,
        'paid_at'         => $invoice->paid_at,
    ];

    // Generate receipt PDF
    $pdf = Pdf::loadView('pdf.invoice', $data);

    // Email receipt automatically
    Mail::send('emails.invoice', [
        'invoice' => $invoice,
        'is_receipt' => true
    ], function ($message) use ($invoice, $pdf) {

        $message->to($invoice->customer_email)
            ->subject('Payment Received – Receipt #' . $invoice->invoice_no . ' | McIntosh Cleaning Services')
            ->attachData(
                $pdf->output(),
                'receipt-' . $invoice->invoice_no . '.pdf',
                ['mime' => 'application/pdf']
            );
    });

    return redirect()
        ->route('invoice.view', $invoice->id)
        ->with('success', 'Invoice marked as paid and receipt sent.');
}

/* =========================
   ADMIN DASHBOARD
========================= */
public function adminDashboard()
{
    $totalRevenue = Invoice::where('status', 'paid')->sum('total');

    $thisMonthRevenue = Invoice::where('status', 'paid')
        ->whereMonth('paid_at', now()->month)
        ->whereYear('paid_at', now()->year)
        ->sum('total');

    $paidCount = Invoice::where('status', 'paid')->count();
    $unpaidCount = Invoice::where('status', 'unpaid')->count();

    $outstandingBalance = Invoice::where('status', 'unpaid')->sum('total');

    $recentPayments = Invoice::where('status', 'paid')
        ->orderByDesc('paid_at')
        ->take(5)
        ->get();

    return view('invoice.dashboard', compact(
        'totalRevenue',
        'thisMonthRevenue',
        'paidCount',
        'unpaidCount',
        'outstandingBalance',
        'recentPayments'
    ));
}

/* =========================
   CUSTOMERS INDEX
========================= */
public function customersIndex(Request $request)
{
    $search = $request->query('search');

    $customers = Customer::where('company_id', auth()->user()->company_id)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('created_at')
        ->paginate(15);

    return view('customers.index', compact('customers'));
}

/* =========================
   RESEND INVOICE
========================= */
public function resend(Invoice $invoice)
{
    $sub_total = 0;

    if (is_array($invoice->items)) {
        foreach ($invoice->items as $item) {
            $sub_total += $item['line_total'] ?? 0;
        }
    }

    $data = [
        'customer_name'   => $invoice->customer_name,
        'customer_email'  => $invoice->customer_email,
        'street_address'  => $invoice->street_address,
        'city_state_zip'  => $invoice->city_state_zip,
        'invoice_date'    => $invoice->invoice_date,
        'due_date'        => $invoice->due_date,
        'notes'           => $invoice->notes,
        'items'           => $invoice->items,
        'invoice_no'      => $invoice->invoice_no,
        'sub_total'       => $sub_total,
        'grand_total'     => $invoice->total,
        'is_receipt'      => false,
        'paid_at'         => $invoice->paid_at,
    ];

    $pdf = Pdf::loadView('pdf.invoice', $data);

    Mail::send('emails.invoice', ['invoice' => $invoice], function ($message) use ($invoice, $pdf) {
        $message->to($invoice->customer_email)
            ->subject('New Invoice #' . $invoice->invoice_no . ' | McIntosh Cleaning Services')
            ->attachData(
                $pdf->output(),
                'invoice-' . $invoice->invoice_no . '.pdf',
                ['mime' => 'application/pdf']
            );
    });

    return back()->with('success', 'Invoice resent successfully.');
}

/* =========================
   SHOW CREATE CUSTOMER FORM
========================= */
public function customersCreate()
{
    return view('customers.create');
}

    /* =========================
       HELPERS
    ========================= */

    private function calculateSubTotal(Invoice $invoice)
    {
        $sub_total = 0;

        if (is_array($invoice->items)) {
            foreach ($invoice->items as $item) {
                $sub_total += $item['line_total'] ?? 0;
            }
        }

        return $sub_total;
    }

    private function buildSavedInvoiceData($invoice, $sub_total, $is_receipt)
    {
        return [
            'customer_name'   => $invoice->customer_name,
            'customer_email'  => $invoice->customer_email,
            'street_address'  => $invoice->street_address,
            'city_state_zip'  => $invoice->city_state_zip,
            'invoice_date'    => $invoice->invoice_date,
            'due_date'        => $invoice->due_date,
            'notes'           => $invoice->notes,
            'items'           => $invoice->items,
            'invoice_no'      => $invoice->invoice_no,
            'sub_total'       => $sub_total,
            'grand_total'     => $invoice->total,
            'is_receipt'      => $is_receipt,
            'paid_at'         => $invoice->paid_at,
        ];
    }

    private function buildInvoiceData(Request $request): array
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'street_address' => 'required|string|max:255',
            'city_state_zip' => 'required|string|max:255',
            'invoice_date'   => 'required|date',
            'due_date'       => 'required|date',
            'items'          => 'required|array|min:1',
            'items.*.desc'   => 'required|string',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.price'  => 'required|numeric',
        ]);

        $items = [];
        $subTotal = 0;

        foreach ($request->items as $it) {
            $qty = (int) $it['qty'];
            $price = (float) $it['price'];
            $line = $qty * $price;

            $items[] = [
                'desc'       => $it['desc'],
                'qty'        => $qty,
                'price'      => $price,
                'line_total' => $line,
            ];

            $subTotal += $line;
        }

        return [
            'invoice_no'     => 'INV-' . now()->format('Ymd-His'),
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'street_address' => $request->street_address,
            'city_state_zip' => $request->city_state_zip,
            'invoice_date'   => $request->invoice_date,
            'due_date'       => $request->due_date,
            'items'          => $items,
            'sub_total'      => $subTotal,
            'grand_total'    => $subTotal,
            'notes'          => (string) $request->notes,
        ];
    }

    private function limitNotes(string $notes, int $maxCharacters = 320): string
    {
        return mb_strlen($notes) > $maxCharacters
            ? mb_substr($notes, 0, $maxCharacters) . '...'
            : $notes;
    }

    private function emailInvoice(Invoice $invoice, $pdf): void
    {
        Mail::send('emails.invoice', ['invoice' => $invoice], function ($message) use ($invoice, $pdf) {
            $message->to($invoice->customer_email)
                ->subject('Invoice #' . $invoice->invoice_no)
                ->attachData(
                    $pdf->output(),
                    'invoice-' . $invoice->invoice_no . '.pdf',
                    ['mime' => 'application/pdf']
                );
        });
    }
}
