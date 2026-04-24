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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $brandName = auth()->user()->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();
        return view('quotes.index', compact('quotes', 'brandName', 'greeting'));
    }

    public function create()
    {
        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        $brandName = auth()->user()->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();
        return view('quotes.create', compact('customers', 'brandName', 'greeting'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required',
                'items' => 'required|array',
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $total += ((float)$item['qty'] * (float)$item['price']);
            }

            $depositAmount = 0;
            if ($request->deposit_type === 'percentage') {
                $depositAmount = ($total * (float)$request->deposit_value) / 100;
            } elseif ($request->deposit_type === 'fixed') {
                $depositAmount = (float)$request->deposit_value;
            }

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
                'selected_contract_id' => $request->selected_contract_id ?? 1,
                'customer_notes' => $request->customer_notes,
            ]);

            foreach ($request->items as $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'service_name' => $item['service'] ?? 'Service',
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['qty'] ?? 1,
                    'unit_price' => $item['price'] ?? 0,
                    'line_total' => ($item['qty'] ?? 1) * ($item['price'] ?? 0),
                ]);
            }

            return redirect()->route('quotes.show', $quote->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Store Error: ' . $e->getMessage());
        }
    }

    public function show(Quote $quote)
    {
        $quote->load(['customer', 'items', 'company']);
        $brandName = auth()->user()->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();
        return view('quotes.show', compact('quote', 'brandName', 'greeting'));
    }

    public function publicView($token)
    {
        $quote = Quote::with(['customer', 'items', 'company'])->where('public_token', $token)->firstOrFail();
        return view('quotes.public', compact('quote'));
    }

    public function showContract($token)
    {
        $quote = Quote::with(['customer', 'items', 'company'])->where('public_token', $token)->firstOrFail();

        [$selectedContractName, $selectedContractPath] = $this->resolveSelectedContract($quote);

        $selectedContractUrl = null;
        if ($selectedContractPath) {
            $selectedContractUrl = Storage::disk('public')->url($selectedContractPath);
        }

        return view('quotes.contract', compact(
            'quote',
            'selectedContractName',
            'selectedContractPath',
            'selectedContractUrl'
        ));
    }

    public function signContract(Request $request, $token)
    {
        try {
            $quote = Quote::with(['company', 'customer'])->where('public_token', $token)->firstOrFail();
            $typedName = $request->sign_name ?? 'Customer';

            $quote->update([
                'contract_status' => 'signed',
                'contract_signed_at' => now(),
                'signed_by' => $typedName,
                'status' => 'approved'
            ]);

            $invoice = $this->createInvoiceFromQuote($quote);

            try {
                Mail::send('emails.contract_signed', ['quote' => $quote, 'invoice' => $invoice], function ($m) use ($quote) {
                    $m->to($quote->company->email)->subject('🖋️ Contract Signed: ' . $quote->customer->name);
                });

                Mail::send('emails.quote_approved', ['quote' => $quote, 'invoice' => $invoice], function ($m) use ($quote) {
                    $m->to($quote->customer->email)->subject('Handshake Confirmed: Proposal #' . $quote->quote_number);
                });
            } catch (\Exception $e) {
                Log::error("Mail Fail: " . $e->getMessage());
            }

            if ($invoice) {
                return redirect()->route('invoice.public_view', ['invoice_no' => $invoice->invoice_no]);
            }

            return redirect()->route('quotes.public', $token)->with('success', 'Contract Signed!');
        } catch (\Exception $e) {
            Log::error("Signing Error: " . $e->getMessage());
            return "Server Error during signing.";
        }
    }

    public function approve($token)
    {
        $quote = Quote::where('public_token', $token)->firstOrFail();
        $quote->update(['status' => 'approved', 'accepted_at' => now()]);

        if ($quote->contract_required) {
            return redirect()->route('quotes.contract', ['token' => $token]);
        }

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
                    'description' => $item->service_name,
                    'qty'         => (float) $item->quantity,
                    'price'       => (float) $item->unit_price,
                    'line_total'  => (float) $item->line_total,
                ];
            }

            return Invoice::create([
                'company_id'      => $quote->company_id,
                'customer_name'   => $quote->customer->name ?? 'Customer',
                'customer_email'  => $quote->customer->email ?? '',
                'invoice_no'      => 'INV-' . strtoupper(Str::random(8)),
                'invoice_date'    => now(),
                'due_date'        => now()->addDays(7),
                'items'           => json_encode($items),
                'subtotal'        => (float) $quote->total,
                'total'           => (float) $quote->total,
                'deposit_amount'  => (float) ($quote->deposit_amount ?? 0),
                'status'          => 'unpaid',
                'public_token'    => Str::random(40),
            ]);
        } catch (\Exception $e) {
            Log::error("Invoice Fail: " . $e->getMessage());
            return null;
        }
    }

    public function edit($id)
    {
        $quote = Quote::with(['items', 'customer'])->findOrFail($id);
        $customers = Customer::where('company_id', auth()->user()->company_id)->get();
        $brandName = auth()->user()->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();
        return view('quotes.create', compact('quote', 'customers', 'brandName', 'greeting'));
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);

        $total = 0;
        foreach ($request->items as $item) {
            $total += ((float)$item['qty'] * (float)$item['price']);
        }

        $depositAmount = 0;
        if ($request->deposit_type === 'percentage') {
            $depositAmount = ($total * (float)$request->deposit_value) / 100;
        } elseif ($request->deposit_type === 'fixed') {
            $depositAmount = (float)$request->deposit_value;
        }

        $quote->update([
            'customer_id' => $request->customer_id,
            'subtotal' => $total,
            'total' => $total,
            'deposit_type' => $request->deposit_type,
            'deposit_value' => $request->deposit_value,
            'deposit_amount' => $depositAmount,
            'remaining_amount' => $total - $depositAmount,
            'contract_required' => $request->has('contract_required'),
            'selected_contract_id' => $request->selected_contract_id ?? 1,
            'customer_notes' => $request->customer_notes,
        ]);

        $quote->items()->delete();
        foreach ($request->items as $item) {
            QuoteItem::create([
                'quote_id' => $quote->id,
                'service_name' => $item['service'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'line_total' => $item['qty'] * $item['price'],
            ]);
        }

        return redirect()->route('quotes.show', $quote->id);
    }

    public function destroy($id)
    {
        Quote::findOrFail($id)->delete();
        return redirect()->route('quotes.index');
    }

    public function send($id)
    {
        $quote = Quote::with(['customer', 'items', 'company'])->findOrFail($id);
        $pdf = Pdf::loadView('quotes.pdf', compact('quote'));
        $link = url('/q/' . $quote->public_token);

        Mail::send('emails.quote_sent', ['quote' => $quote, 'link' => $link], function ($m) use ($quote, $pdf) {
            $m->to($quote->customer->email)
              ->subject('New Quote #' . $quote->quote_number)
              ->attachData($pdf->output(), "Quote_{$quote->quote_number}.pdf");
        });

        $quote->update(['status' => 'sent']);
        return back()->with('success', 'Quote sent!');
    }

    private function resolveSelectedContract(Quote $quote): array
    {
        $company = $quote->company;
        $selectedId = (int) ($quote->selected_contract_id ?? 1);

        $nameField = "contract_{$selectedId}_name";
        $pathField = "contract_{$selectedId}_path";

        $selectedName = $company->$nameField ?? null;
        $selectedPath = $company->$pathField ?? null;

        // fallback to old single-contract system
        if (!$selectedPath && !empty($company->contract_template_path)) {
            $selectedName = $selectedName ?: 'Default Contract';
            $selectedPath = $company->contract_template_path;
        }

        return [$selectedName, $selectedPath];
    }

    private function getGreeting()
    {
        $hour = date('H');
        if ($hour < 12) return 'Good Morning';
        if ($hour < 17) return 'Good Afternoon';
        return 'Good Evening';
    }
}
