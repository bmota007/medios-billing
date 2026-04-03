@extends('layouts.admin')

@section('content')
<style>
    /* Prevent clipping and allow horizontal swipe on mobile */
    .main-content, .container-fluid, .glass-card { overflow: visible !important; }
    
    .text-label { color: #94a3b8; font-size: 0.70rem; text-transform: uppercase; font-weight: 800; letter-spacing: 1.5px; }
    .glass-card { background: rgba(30, 41, 59, 0.60); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 1.5rem; }
    
    @media (max-width: 768px) {
        .container-fluid { padding-left: 5px !important; padding-right: 5px !important; }
        .glass-card { width: 100% !important; padding: 0 !important; border-radius: 12px !important; }
        .content-body-print { padding: 20px !important; }
    }

    @media print {
        nav, .no-print, .sidebar, .navbar, #sidebar-wrapper, .logout-btn, button, .btn, form { display: none !important; }
        body, .main-content, .container-fluid { background: white !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
        #printable-quote { background: white !important; border: none !important; box-shadow: none !important; width: 100% !important; position: absolute; top: 0; left: 0; }
        .text-white, .text-secondary, .text-sky-400, .text-dark-print, .text-secondary-print { color: black !important; }
        .bg-sky-600 { background-color: #0284c7 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .content-body-print { background: white !important; color: black !important; }
        .table-print th { background: #f3f4f6 !important; color: black !important; padding: 10px !important; }
        .table-print td { border-bottom: 1px solid #eee !important; padding: 10px !important; color: black !important; font-size: 12px !important; }
    }
</style>

<div class="container-fluid mb-5">
    {{-- Header & Action Row --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print flex-wrap gap-3">
        <div>
            <h2 class="text-white font-bold mb-1">Quote <span class="text-sky-400">Preview</span></h2>
            <p class="text-secondary small mb-0">Managing proposal for {{ $quote->customer->name ?? 'Customer' }}</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('quotes.index') }}" class="btn btn-outline-secondary text-white px-3 font-bold"><i class="fa-solid fa-arrow-left"></i></a>
            <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-warning px-3 font-bold text-dark"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
            <form action="{{ route('quotes.send', $quote->id) }}" method="POST" class="d-inline">@csrf<button type="submit" class="btn btn-success px-3 font-bold text-white"><i class="fa-solid fa-paper-plane"></i> Send</button></form>
            <a href="{{ route('quotes.download', $quote->id) }}" target="_blank" class="btn btn-primary px-3 font-bold"><i class="fa-solid fa-file-pdf"></i></a>
            <button onclick="window.print()" class="btn btn-info px-3 font-bold text-white"><i class="fa-solid fa-print"></i></button>
        </div>
    </div>

    {{-- The Quote Document --}}
    <div id="printable-quote" class="glass-card p-0 overflow-hidden border-0 shadow-lg">
        
        <div class="bg-sky-600 p-4 d-flex justify-content-between align-items-center quote-header-print">
            <div class="d-flex align-items-center">
                @if(optional($quote->company)->logo_path)
                    <img src="{{ asset('storage/' . $quote->company->logo_path) }}" alt="Logo" style="max-height: 50px;" class="me-3 p-1 bg-white rounded">
                @endif
                <div>
                    <h4 class="text-white font-bold mb-0">{{ $quote->company->name ?? 'Company' }}</h4>
                    <span class="text-white opacity-75 small">{{ $quote->title ?? 'Service Quote' }}</span>
                </div>
            </div>
            <div class="text-white text-end">
                <h3 class="mb-0 font-bold">PROPOSAL</h3>
                <span class="opacity-75 small">#{{ $quote->quote_number ?? $quote->id }}</span>
            </div>
        </div>

        <div class="p-4 p-md-5 content-body-print">
            <div class="row mb-5">
                <div class="col-md-6 col-12 mb-4 mb-md-0 text-start">
                    <p class="text-label mb-2">Prepared For</p>
                    <h4 class="text-white text-dark-print font-bold mb-1">{{ $quote->customer->name ?? 'Customer Name' }}</h4>
                    <p class="text-secondary-print small mb-0">{{ $quote->customer->email ?? '' }}</p>
                </div>
                <div class="col-md-6 col-12 text-md-end text-start">
                    <div class="d-inline-flex gap-4 text-start">
                        <div>
                            <span class="text-label d-block">Status</span>
                            <span class="badge bg-success bg-opacity-25 text-white border border-success px-3 py-1 rounded-pill uppercase small mt-1">{{ $quote->status }}</span>
                        </div>
                        <div>
                            <span class="text-label d-block">Created</span>
                            <span class="text-white text-dark-print font-bold d-block mt-1">{{ optional($quote->created_at)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- THE SWIPE FIX TABLE --}}
            <div style="width: 100%; overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; border-radius: 8px;" class="mb-4 shadow-sm">
                <table style="width: 100%; min-width: 600px; border-collapse: collapse; background: white;" class="table-print">
                    <thead>
                        <tr style="background: #f3f4f6; color: #111827;">
                            <th style="text-align: left; padding: 12px; font-size: 13px;">SERVICE DESCRIPTION</th>
                            <th style="text-align: center; padding: 12px; width: 80px; font-size: 13px;">QTY</th>
                            <th style="text-align: right; padding: 12px; width: 130px; font-size: 13px;">UNIT PRICE</th>
                            <th style="text-align: right; padding: 12px; width: 130px; font-size: 13px;">TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quote->items as $item)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 14px 12px; vertical-align: top;">
                                    {{-- Use service_name from DB --}}
                                    <div style="font-weight: 600; color: #111827;">{{ $item->service_name }}</div>
                                    @if(!empty($item->description))
                                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td style="padding: 14px 12px; text-align: center; color: #374151;">{{ $item->quantity }}</td>
                                <td style="padding: 14px 12px; text-align: right; color: #374151;">${{ number_format((float) $item->unit_price, 2) }}</td>
                                <td style="padding: 14px 12px; text-align: right; color: #111827; font-weight: 700;">${{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="padding: 20px; text-align: center; color: #9ca3af;">No items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row mt-5 g-3">
                <div class="col-md-7 col-12 order-2 order-md-1">
                    <div class="bg-dark bg-opacity-25 rounded-2xl p-4 border border-white border-opacity-10">
                        <h6 class="text-white font-bold mb-2 small uppercase">Terms & Conditions</h6>
                        <p class="text-secondary small mb-2">Valid for 30 days. Acceptance constitutes a binding agreement.</p>
                        @if($quote->deposit_amount > 0)
                            <div class="text-sky-400 font-bold small p-2 bg-sky-400 bg-opacity-10 rounded border border-sky-400 border-opacity-20">
                                Deposit required: ${{ number_format($quote->deposit_amount, 2) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-5 col-12 order-1 order-md-2">
                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 text-dark">
                        <div class="d-flex justify-content-between text-secondary mb-2 small uppercase font-bold"><span>Subtotal</span><span>${{ number_format((float) $quote->subtotal, 2) }}</span></div>
                        <div class="d-flex justify-content-between text-dark font-bold text-xl border-top pt-2"><span>Total</span><span class="text-sky-600">${{ number_format((float) $quote->total, 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
