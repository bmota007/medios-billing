@php
    // This tells the main layout to hide the sidebar
    $hideSidebar = true; 
@endphp

@extends('layouts.app')

@php
    $isInternal = auth()->check();
    $decodedItems = $items ?? (json_decode($invoice->items, true) ?: []);
    $publicUrl = route('invoice.public_view', $invoice->invoice_no);
@endphp

@push('page_styles')
<style>
    .mb-invoice-page { width: 100%; color: #f8fafc; }
    .mb-invoice-shell {
        position: relative;
        max-width: 1100px;
        width: 100%;
        margin: 0 auto;
        background: linear-gradient(180deg,rgba(2,6,23,.96),rgba(6,18,42,.96));
        border: 1px solid rgba(56,189,248,.18);
        border-radius: 28px;
        padding: 40px;
        box-shadow: 0 25px 60px rgba(0,0,0,.28);
    }
    
    .mb-invoice-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 10px; }
    .mb-top-link { color: #fff; text-decoration: none; font-weight: 700; margin-right: 15px; font-size: 14px; }
    .mb-action-btn { 
        display: inline-flex; align-items: center; gap: 8px; border: none; 
        border-radius: 12px; padding: 10px 16px; font-weight: 800; color: #fff; transition: .25s;
        text-decoration: none; font-size: 14px; cursor: pointer;
    }
    .mb-btn-send { background: #0ea5e9; }
    .mb-btn-resend { background: #f59e0b; }
    .mb-btn-sent { background: rgba(14, 165, 233, 0.2); color: #0ea5e9; border: 1px solid #0ea5e9; cursor: default; }
    .mb-btn-pdf { background: #4f46e5; }
    .mb-btn-print { background: #334155; }
    .mb-btn-link { background: #334155; }
    .mb-btn-sms { background: #14b8a6; }

    .mb-invoice-layout { display: grid; grid-template-columns: 2fr 1.2fr; gap: 24px; align-items: start; }
    .mb-left-panel, .mb-right-panel { display: flex; flex-direction: column; gap: 20px; }

    .mb-section-card { 
        background: rgba(15,23,42,.62); border: 1px solid rgba(255,255,255,.06); 
        border-radius: 22px; padding: 24px; 
    }
    .mb-summary-card { background: rgba(30,41,59,.4); border: 1px solid rgba(255,255,255,.06); border-radius: 22px; padding: 24px; }
    .mb-summary-label { font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 800; margin-bottom: 8px; letter-spacing: 0.05em; }
    .mb-summary-total { color: #38bdf8; font-weight: 900; font-size: 44px; }
    
    .mb-meta-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .mb-meta-box { background: rgba(15,23,42,.75); border: 1px solid rgba(255,255,255,.06); border-radius: 18px; padding: 18px; }
    
    .mb-items-table { width: 100%; }
    .mb-items-head, .mb-items-row { display: grid; grid-template-columns: 2fr 0.5fr 1fr 1fr; gap: 10px; padding: 12px 0; }
    .mb-items-head { border-bottom: 1px solid rgba(255,255,255,.1); color: #94a3b8; font-size: 12px; font-weight: 800; }
    .mb-items-row { border-bottom: 1px solid rgba(255,255,255,.05); color: #fff; }

    .mb-thanks-note {
        background: linear-gradient(135deg, rgba(56, 189, 248, 0.1), rgba(37, 99, 235, 0.05));
        border: 1px dashed rgba(56, 189, 248, 0.3);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        margin-top: 10px;
    }
    .mb-thanks-text { font-size: 20px; font-weight: 600; color: #f1f5f9; line-height: 1.4; }
    .mb-thanks-accent { color: #38bdf8; font-weight: 800; }

    .mb-deposit-highlight { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .mb-deposit-highlight > div { background: rgba(15,23,42,0.8); border: 1px solid rgba(255,255,255,.06); border-radius: 16px; padding: 18px; }
    .mb-pay-btn { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 16px; border-radius: 14px; font-weight: 900; color: #fff; text-decoration: none; border: none; width: 100%; cursor: pointer; }
    .mb-full-pay-btn { background: #3b82f6; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); }
    .mb-manual-btn-pay { background: rgba(30, 64, 175, 0.5); border: 1px solid #3b82f6; color: #dbeafe; }
    .mb-manual-btn-dep { background: rgba(146, 64, 14, 0.5); border: 1px solid #f59e0b; color: #fef3c7; }
    
    .mb-payment-mode-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #cbd5e1; font-size: 14px; cursor: pointer; }

    @media (max-width: 900px) { .mb-invoice-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="mb-invoice-page">

    @if($isInternal)
    <div class="mb-invoice-topbar" style="max-width:1100px; margin: 0 auto 24px;">
        <div class="mb-invoice-topbar-left">
            <a href="{{ route('invoice.history') }}" class="mb-top-link"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <a href="{{ route('invoice.edit', $invoice->id) }}" class="mb-top-link"><i class="fa-solid fa-pen-to-square"></i> Edit Invoice</a>
        </div>
        <div class="mb-invoice-topbar-right">
            @if(in_array($invoice->status, ['sent', 'viewed', 'partial', 'paid']))
                <button class="mb-action-btn mb-btn-sent" disabled><i class="fa-solid fa-check"></i> SENT</button>
                <form action="{{ route('invoice.resend', $invoice->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="mb-action-btn mb-btn-resend"><i class="fa-solid fa-rotate-right"></i> Resend</button>
                </form>
            @else
                <form action="{{ route('invoice.send_email', $invoice->invoice_no) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="mb-action-btn mb-btn-send"><i class="fa-solid fa-paper-plane"></i> Send Invoice</button>
                </form>
            @endif
            <a href="{{ route('invoice.download', $invoice->id) }}" class="mb-action-btn mb-btn-pdf"><i class="fa-solid fa-file-pdf"></i> Download PDF</a>
            <button class="mb-action-btn mb-btn-print" onclick="window.print()"><i class="fa-solid fa-print"></i> Print</button>
            <button class="mb-action-btn mb-btn-link" onclick="copyInvoiceLink()"><i class="fa-solid fa-link"></i> Copy Public Link</button>
            <button class="mb-action-btn mb-btn-sms" onclick="sendInvoiceSms()"><i class="fa-solid fa-comment-sms"></i> Send SMS</button>
        </div>
    </div>
    @endif

    <div class="mb-invoice-shell">
        <div style="position:absolute; top:15px; right:30px; font-size:10px; color:rgba(255,255,255,0.3);">Powered by MediosBilling</div>
        
        <div class="mb-invoice-head">
            <div>
                <div style="color:#7dd3fc; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em;">Professional Invoice</div>
                <h1 style="margin:5px 0; font-size:32px; font-weight:800;">Invoice #{{ $invoice->invoice_no }}</h1>
                <div style="margin-top:10px;">
                    @if($invoice->status == 'paid')
                        <span style="background:rgba(34,197,94,0.2); color:#4ade80; padding:6px 16px; border-radius:20px; font-size:12px; font-weight:800; border:1px solid rgba(34,197,94,0.3);">PAID</span>
                    @elseif($invoice->status == 'partial')
                        <span style="background:rgba(245,158,11,0.2); color:#fbbf24; padding:6px 16px; border-radius:20px; font-size:12px; font-weight:800; border:1px solid rgba(245,158,11,0.3);">PARTIAL</span>
                    @else
                        <span style="background:rgba(244,63,94,0.2); color:#fb7185; padding:6px 16px; border-radius:20px; font-size:12px; font-weight:800; border:1px solid rgba(244,63,94,0.3);">PENDING</span>
                    @endif
                </div>
            </div>
            <div style="text-align:right;">
                <h2 style="margin:0; font-size:22px; font-weight:800;">{{ $invoice->company->name ?? 'Our Company' }}</h2>
                <div style="color:#94a3b8; font-size:14px; margin-top:5px;">{{ $invoice->company->email ?? '' }}</div>
            </div>
        </div>

        <div class="mb-invoice-layout">
            <div class="mb-left-panel">
                <div class="mb-section-card">
                    <div class="mb-summary-label">Billed To</div>
                    <div style="font-size:20px; font-weight:800; color:#fff;">{{ $invoice->customer_name }}</div>
                    <div style="color:#cbd5e1; margin-top:4px;">{{ $invoice->customer_email }}</div>
                    @if($invoice->street_address) <div style="font-size:14px; color:#94a3b8; margin-top:8px;">{{ $invoice->street_address }}</div> @endif
                    @if($invoice->city_state_zip) <div style="font-size:14px; color:#94a3b8;">{{ $invoice->city_state_zip }}</div> @endif
                </div>

                <div class="mb-meta-grid">
                    <div class="mb-meta-box">
                        <span class="mb-summary-label">Invoice Date</span>
                        <div style="font-weight:700; color:#fff;">{{ $invoice->invoice_date }}</div>
                    </div>
                    <div class="mb-meta-box">
                        <span class="mb-summary-label">Deposit Due</span>
                        <div style="font-weight:700; color:#fff;">{{ $invoice->due_date }}</div>
                    </div>
                    <div class="mb-meta-box">
                        <span class="mb-summary-label">Remaining Due</span>
                        <div style="font-weight:700; color:#fff;">{{ $invoice->remaining_due_date }}</div>
                    </div>
                </div>

                <div class="mb-section-card">
                    <div style="font-size:18px; font-weight:800; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-list-check text-info"></i> Services & Items
                    </div>
                    <div class="mb-items-table">
                        <div class="mb-items-head">
                            <div>Description</div>
                            <div style="text-align:center;">Qty</div>
                            <div style="text-align:right;">Price</div>
                            <div style="text-align:right;">Total</div>
                        </div>
                        @forelse($decodedItems as $item)
                            @php
                                $q = (float)($item['qty'] ?? $item['quantity'] ?? 0);
                                $p = (float)($item['price'] ?? $item['unit_price'] ?? 0);
                                $d = $item['description'] ?? $item['service_name'] ?? 'Service';
                            @endphp
                            <div class="mb-items-row">
                                <div style="font-weight:500;">{{ $d }}</div>
                                <div style="text-align:center; color:#94a3b8;">{{ number_format($q, 2) }}</div>
                                <div style="text-align:right; color:#94a3b8;">${{ number_format($p, 2) }}</div>
                                <div style="text-align:right; font-weight:800; color:#38bdf8;">
                                    ${{ number_format($q * $p, 2) }}
                                </div>
                            </div>
                        @empty
                            <div style="padding:30px; text-align:center; color:#94a3b8;">No items found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="mb-thanks-note">
                    <div class="mb-thanks-text">
                        Thank you for choosing <span class="mb-thanks-accent">{{ $invoice->company->name ?? 'our company' }}</span>. 
                        We truly appreciate your business and the trust you've placed in us!
                    </div>
                </div>

                @if($isInternal)
                <div class="mb-section-card">
                    <div style="font-size:18px; font-weight:800; margin-bottom:15px;">Client Messaging</div>
                    <div class="mb-sms-fields">
                        <label class="mb-summary-label">Customer Phone</label>
                        <input type="text" id="invoice_sms_phone" value="{{ $invoice->customer_phone }}" style="width:100%; background:#0f172a; border:1px solid rgba(255,255,255,.08); color:#fff; border-radius:12px; padding:12px; margin-bottom:15px;">
                        <label class="mb-summary-label">SMS Preview</label>
                        <textarea id="invoice_sms_message" rows="3" style="width:100%; background:#0f172a; border:1px solid rgba(255,255,255,.08); color:#fff; border-radius:12px; padding:12px;">Hi {{ $invoice->customer_name }}, your invoice #{{ $invoice->invoice_no }} is ready: {{ $publicUrl }}</textarea>
                    </div>
                    <button type="button" class="mb-pay-btn mb-btn-sms" style="margin-top:15px; width:100%;" onclick="sendInvoiceSms()">
                        <i class="fa-solid fa-paper-plane"></i> Send SMS
                    </button>
                </div>
                @endif
            </div>

            <div class="mb-right-panel">
                {{-- 1. Big Header --}}
                <div class="mb-summary-card" style="text-align:center; padding:40px 20px;">
                    <div class="mb-summary-label">Amount Due Now</div>
                    <div class="mb-summary-total">
                        @php
                            $dueNow = ($invoice->deposit_amount > 0 && $invoice->amount_paid < $invoice->deposit_amount) 
                                      ? ($invoice->deposit_amount - $invoice->amount_paid) 
                                      : ($invoice->total - $invoice->amount_paid);
                        @endphp
                        ${{ number_format($dueNow, 2) }}
                    </div>
                </div>

                {{-- 2. Action Button --}}
                <div class="mb-section-card">
                    <div style="font-size:18px; font-weight:800; margin-bottom:20px;">Payment Flow</div>
                    @if($invoice->status !== 'paid')
                        <a href="{{ route('invoice.pay', $invoice->invoice_no) }}" class="mb-pay-btn mb-full-pay-btn" style="padding:20px;">
                            <i class="fa-solid fa-credit-card"></i> 
                            {{ $invoice->status === 'partial' ? 'Pay Remaining Balance' : 'Pay Invoice Now' }}
                        </a>
                    @else
                        <div style="background:rgba(34,197,94,0.1); color:#4ade80; padding:20px; border-radius:16px; text-align:center; font-weight:800; border:1px solid rgba(34,197,94,0.2);">
                            <i class="fa-solid fa-circle-check"></i> PAID IN FULL
                        </div>
                    @endif
                </div>

                {{-- 3. Dynamic Deposit/Balance Cards --}}
                <div class="mb-deposit-highlight">
                    @if($invoice->deposit_amount > 0 && ($invoice->amount_paid < $invoice->deposit_amount))
                        <div>
                            <small class="mb-summary-label">Deposit Due Now</small>
                            <div style="font-size:24px; font-weight:900; color:#4ade80;">
                                ${{ number_format($invoice->deposit_amount - $invoice->amount_paid, 2) }}
                            </div>
                        </div>
                        <div>
                            <small class="mb-summary-label">Balance After Deposit</small>
                            <div style="font-weight:900; color:#fff; font-size:24px;">
                                ${{ number_format($invoice->total - $invoice->deposit_amount, 2) }}
                            </div>
                        </div>
                    @else
                        <div style="grid-column: span 2;">
                            <small class="mb-summary-label">Total Remaining Balance</small>
                            <div style="font-size:28px; font-weight:900; color:#fff;">
                                ${{ number_format($invoice->total - $invoice->amount_paid, 2) }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 4. Financial Breakdown --}}
                <div class="mb-section-card">
                    <div style="display:flex; justify-content:space-between; color:#94a3b8; font-size:15px; margin-bottom:8px;">
                        <span>Subtotal</span>
                        <span style="color:#fff;">${{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:#94a3b8; font-size:15px; margin-bottom:15px;">
                        <span>Tax</span>
                        <span style="color:#fff;">$0.00</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:#fff; font-size:24px; font-weight:900; border-top:1px solid rgba(255,255,255,0.05); padding-top:15px;">
                        <span>Total</span>
                        <span style="color:#38bdf8;">${{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>

                {{-- 5. Final Paid Card --}}
                <div class="mb-section-card" style="text-align:center; border:1px solid rgba(56,189,248,0.2);">
                    <div class="mb-summary-label">Total Paid</div>
                    <div style="font-size:28px; font-weight:900; color:#4ade80;">${{ number_format($invoice->amount_paid ?? 0, 2) }}</div>
                </div>

                @if($isInternal)
                <div class="mt-2">
                    <label class="mb-payment-mode-row">
                        <input type="radio" name="payment_mode" value="self" checked>
                        <span>Customer will pay themselves</span>
                    </label>

                    <label class="mb-payment-mode-row">
                        <input type="radio" name="payment_mode" value="manual">
                        <span>I am charging customer manually</span>
                    </label>

                    <div id="manual-charge-mode" style="margin-top:20px; opacity: 0.5; pointer-events: none; transition: 0.3s;">
                        <p style="font-size:11px; color:#94a3b8; margin-bottom:15px;">Manual Charge Mode: Use these buttons if you are collecting payment directly by phone, in person, or assisting the customer.</p>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                            <a href="{{ route('invoice.pay', $invoice->invoice_no) }}" class="mb-pay-btn mb-manual-btn-pay">
                                <i class="fa-solid fa-credit-card"></i> Pay Now
                            </a>
                            <a href="{{ route('invoice.pay', ['invoice_no' => $invoice->invoice_no, 'payment_type' => 'deposit']) }}" class="mb-pay-btn mb-manual-btn-dep">
                                <i class="fa-solid fa-money-bill-1-wave"></i> Pay Deposit
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
<script>
    function copyInvoiceLink() {
        const url = @json($publicUrl);
        navigator.clipboard.writeText(url).then(() => {
            alert('Public invoice link copied to clipboard.');
        });
    }

    function sendInvoiceSms() {
        const phone = document.getElementById('invoice_sms_phone')?.value || '';
        const message = document.getElementById('invoice_sms_message')?.value || '';

        if (!phone.trim()) { alert('Please enter a phone number.'); return; }

        fetch('{{ route('invoice.send_sms', $invoice->invoice_no) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone, message })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) alert('SMS sent successfully!');
            else alert('Error: ' + data.message);
        })
        .catch(() => alert('Error sending SMS.'));
    }

    @if($isInternal)
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="payment_mode"]');
        const manualSection = document.getElementById('manual-charge-mode');

        radios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if(e.target.value === 'manual') {
                    manualSection.style.opacity = '1';
                    manualSection.style.pointerEvents = 'auto';
                } else {
                    manualSection.style.opacity = '0.5';
                    manualSection.style.pointerEvents = 'none';
                }
            });
        });
    });
    @endif
</script>
@endpush
