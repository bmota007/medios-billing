@extends('layouts.admin')

@section('content')
<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-5 no-print flex-wrap gap-3">
        <div>
            <h2 class="text-white font-bold mb-1">
                Quote <span class="text-sky-400">Preview</span>
            </h2>
            <p class="text-secondary small mb-0">
                Review and manage proposal for {{ $quote->customer->name ?? 'Customer' }}
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            {{-- Back Button --}}
            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary text-white px-3 font-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Back
            </a>

            {{-- Edit Button --}}
            <a href="{{ route('quotes.edit', $quote->id) }}" 
               style="background:#f59e0b;color:white;padding:10px 15px;border-radius:6px;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;">
               <i class="fa-solid fa-pen-to-square me-2"></i> Edit Quote
            </a>

            {{-- Resend Button (POST Form for Security) --}}
            <form action="{{ route('quotes.send', $quote->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" 
                        style="background:#10b981;color:white;padding:10px 15px;border:none;border-radius:6px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;">
                    <i class="fa-solid fa-rotate-right me-2"></i> Resend Quote
                </button>
            </form>

            {{-- Download PDF --}}
            <a href="{{ route('quotes.download', $quote->id) }}" target="_blank" class="btn btn-primary px-3 font-bold shadow-lg">
                <i class="fa-solid fa-file-pdf me-2"></i> Download PDF
            </a>

            {{-- Print Button --}}
            <button onclick="window.print()" class="btn btn-info px-3 font-bold text-white shadow-lg">
                <i class="fa-solid fa-print me-2"></i> Print
            </button>

            {{-- Initial Send Button (POST Form) --}}
            <form action="{{ route('quotes.send', $quote->id) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-warning px-3 font-bold text-dark shadow-lg">
                    <i class="fa-solid fa-paper-plane me-2"></i> Send Quote
                </button>
            </form>
        </div>
    </div>

    {{-- Printable Quote Card --}}
    <div id="printable-quote" class="glass-card p-0 overflow-hidden border-0 shadow-lg">
        <div class="bg-sky-600 p-4 d-flex justify-content-between align-items-center quote-header-print">
            <div class="d-flex align-items-center">
                @if(optional($quote->company)->logo_path)
                    <img
                        src="{{ asset('storage/' . $quote->company->logo_path) }}"
                        alt="Company Logo"
                        style="max-height: 50px;"
                        class="me-3 p-1 bg-white rounded logo-print"
                    >
                @endif

                <div>
                    <h4 class="text-white font-bold mb-0 company-name-print">
                        {{ $quote->company->name ?? 'Company Name' }}
                    </h4>
                    <span class="text-white opacity-75 small title-print">
                        {{ $quote->title ?? 'Service Quote' }}
                    </span>
                </div>
            </div>

            <div class="text-white text-end">
                <h3 class="mb-0 font-bold doc-type-print">PROPOSAL</h3>
                <span class="opacity-75 small num-print">
                    #{{ $quote->quote_number ?? $quote->id }}
                </span>
            </div>
        </div>

        <div class="p-5 content-body-print">
            <div class="row mb-5">
                <div class="col-md-6 col-12 mb-4 mb-md-0">
                    <p class="text-label mb-2">Prepared For</p>
                    <h4 class="text-dark-print font-bold mb-1">
                        {{ $quote->customer->name ?? 'Customer Name' }}
                    </h4>
                    <p class="text-secondary-print small mb-0">
                        {{ $quote->customer->email ?? '' }}
                    </p>
                    <p class="text-secondary-print small mb-0">
                        {{ $quote->customer->phone ?? '' }}
                    </p>
                </div>

                <div class="col-md-6 col-12 text-md-end">
                    <div class="d-inline-flex gap-4 text-start stats-box-print">
                        <div>
                            <span class="text-label d-block">Status</span>
                            <span class="badge-print uppercase mt-1">
                                {{ $quote->status }}
                            </span>
                        </div>
                        <div>
                            <span class="text-label d-block">Created</span>
                            <span class="text-dark-print font-bold d-block mt-1">
                                {{ optional($quote->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-top: 30px;" class="table-print">
                <thead>
                    <tr style="background: #f3f4f6; color: #111827;">
                        <th style="text-align: left; padding: 12px;">Service Description</th>
                        <th style="text-align: center; padding: 12px; width: 100px;">Qty</th>
                        <th style="text-align: right; padding: 12px; width: 140px;">Price</th>
                        <th style="text-align: right; padding: 12px; width: 140px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quote->items as $item)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <td style="padding: 14px 12px; vertical-align: top;">
                                <div style="font-weight: 600; color: #ffffff;">
                                    {{ $item->service_name }}
                                </div>
                                @if(!empty($item->description))
                                    <div style="font-size: 13px; color: #9ca3af; margin-top: 4px;">
                                        {{ $item->description }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 12px; text-align: center; color: #ffffff;">
                                {{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}
                            </td>
                            <td style="padding: 14px 12px; text-align: right; color: #ffffff;">
                                ${{ number_format((float) $item->unit_price, 2) }}
                            </td>
                            <td style="padding: 14px 12px; text-align: right; color: #ffffff; font-weight: 600;">
                                ${{ number_format((float) $item->line_total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 18px 12px; text-align: center; color: #9ca3af;">
                                No quote items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="row mt-5">
                <div class="col-md-7 col-12 mb-4 mb-md-0">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 shadow-md">
                        <h6 class="text-white font-bold mb-2 small uppercase">Terms & Conditions</h6>
                        <p class="text-slate-300 small mb-1 italic">
                            Valid for 30 days. Acceptance constitutes a binding agreement.
                        </p>

                        @if($quote->deposit_type === 'percentage')
                            <p class="text-slate-300 small mb-1">
                                Deposit required: {{ number_format((float) $quote->deposit_value, 2) }}%
                                (${{ number_format((float) $quote->deposit_amount, 2) }})
                            </p>
                        @elseif($quote->deposit_type === 'fixed')
                            <p class="text-slate-300 small mb-1">
                                Deposit required: ${{ number_format((float) $quote->deposit_amount, 2) }}
                            </p>
                        @endif

                        @if(!empty($quote->remaining_due_date))
                            <p class="text-slate-300 small mb-0">
                                Remaining due date: {{ optional($quote->remaining_due_date)->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-md-5 col-12">
                    <div class="bg-slate-800 rounded-2xl p-6 border border-slate-700 shadow-md">
                        <div class="flex justify-between text-slate-300 text-sm mb-2">
                            <span>Subtotal</span>
                            <span>${{ number_format((float) $quote->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-white text-lg font-bold mb-3">
                            <span>Total</span>
                            <span>${{ number_format((float) $quote->total, 2) }}</span>
                        </div>

                        @if($quote->deposit_amount > 0)
                            <div class="flex justify-between text-yellow-400 font-semibold text-sm">
                                <span>Deposit Due Now</span>
                                <span>${{ number_format($quote->deposit_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-blue-400 font-semibold text-sm mt-1">
                                <span>Remaining Balance</span>
                                <span>${{ number_format($quote->remaining_amount, 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-label { color: #94a3b8; font-size: 0.70rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; }
    .font-bold { font-weight: 700; }
    .glass-card { background: rgba(30, 41, 59, 0.60); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 1.5rem; }
    .text-dark-print { color: #ffffff; }
    .text-secondary-print { color: #cbd5e1; }
    .badge-print { display: inline-block; padding: 6px 12px; border: 1px solid rgba(255,255,255,0.25); border-radius: 999px; color: #fff; font-size: 12px; font-weight: 700; }

    @media print {
        nav, .no-print, .sidebar, .navbar, #sidebar-wrapper, .logout-btn, button, .btn { display: none !important; }
        body, .main-content, .container-fluid { background: white !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        #printable-quote { background: white !important; border: none !important; box-shadow: none !important; width: 100% !important; position: absolute; top: 0; left: 0; }
        .text-white, .text-secondary, .text-sky-400 { color: black !important; }
        .bg-sky-600 { background-color: #0284c7 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .content-body-print { background: white !important; }
        .text-dark-print { color: #000 !important; }
        .text-secondary-print { color: #444 !important; }
        .table-print th { background: #f3f4f6 !important; color: black !important; padding: 10px !important; }
        .table-print td { border-bottom: 1px solid #eee !important; padding: 10px !important; color: black !important; }
    }
</style>
@endsection
