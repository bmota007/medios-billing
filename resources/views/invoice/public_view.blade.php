@extends('layouts.app')



@php
$isInternal = auth()->check();

    $decodedItems = $items ?? (json_decode($invoice->items, true) ?: []);

    $publicUrl = route('invoice.public_view', $invoice->invoice_no);

@endphp



{{-- =========================================================

| PAGE-SPECIFIC STYLES

========================================================== --}}

@push('page_styles')

<style>

/* =========================================================

| RESET / PAGE WRAPPER

========================================================= */

.mb-invoice-page * {

    box-sizing: border-box;

}



.mb-invoice-page {

    width: 100%;

    color: #f8fafc;

}



/* =========================================================

| TOP BAR

========================================================= */

.mb-invoice-topbar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 16px;

    flex-wrap: wrap;

    margin-bottom: 24px;

}



.mb-invoice-topbar-left,

.mb-invoice-topbar-right {

    display: flex;

    align-items: center;

    gap: 12px;

    flex-wrap: wrap;

}



.mb-top-link {

    color: #fff;

    text-decoration: none;

    font-weight: 700;

    font-size: 15px;

    opacity: .95;

}



.mb-top-link i {

    margin-right: 6px;

}



.mb-inline-form {

    margin: 0;

}



.mb-action-btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    border: none;

    text-decoration: none;

    cursor: pointer;

    border-radius: 12px;

    padding: 12px 16px;

    font-weight: 800;

    font-size: 15px;

    transition: .25s ease;

    color: #fff;

    min-height: 46px;

    white-space: nowrap;

}



.mb-action-btn:hover {

    transform: translateY(-1px);

}



.mb-primary-btn {

    background: linear-gradient(135deg,#0ea5e9,#2563eb);

    box-shadow: 0 12px 24px rgba(37,99,235,.28);

}



.mb-blue-btn {

    background: linear-gradient(135deg,#2563eb,#1d4ed8);

}



.mb-dark-btn {

    background: rgba(30,41,59,.92);

    border: 1px solid rgba(255,255,255,.08);

}



.mb-sms-btn {

    background: linear-gradient(135deg,#14b8a6,#0f766e);

    box-shadow: 0 12px 24px rgba(20,184,166,.18);

}



.mb-disabled-btn {

    opacity: .6;

    cursor: not-allowed;

}



/* =========================================================

| SHELL

========================================================= */
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



/* =========================================================

| HEADER

========================================================= */

.mb-invoice-head {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 24px;

    margin-bottom: 28px;

}



.mb-eyebrow {

    color: #7dd3fc;

    text-transform: uppercase;

    letter-spacing: .08em;

    font-size: 11px;

    font-weight: 800;

    margin-bottom: 10px;

}



.mb-brand-block h1 {

    margin: 0 0 14px;

    font-size: 36px;

    line-height: 1.05;

    color: #fff;

}



.mb-company-block {

    text-align: right;

}



.mb-company-block h2 {

    margin: 0;

    color: #fff;

    font-size: 20px;

    font-weight: 800;

}



.mb-company-sub {

    margin-top: 8px;

    color: #94a3b8;

    font-size: 14px;

}


.mb-left-panel {
    display: flex;
    flex-direction: column;
    gap: 20px;
}


/* =========================================================

| STATUS

========================================================= */

.mb-status-pill {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 8px 14px;

    border-radius: 999px;

    font-size: 12px;

    font-weight: 800;

    letter-spacing: .04em;

}



.mb-status-pending {

    background: rgba(245,158,11,.12);

    color: #fbbf24;

    border: 1px solid rgba(245,158,11,.22);

}



.mb-status-sent {

    background: rgba(37,99,235,.15);

    color: #60a5fa;

    border: 1px solid rgba(37,99,235,.25);

}



.mb-status-viewed {

    background: rgba(14,165,233,.12);

    color: #38bdf8;

    border: 1px solid rgba(14,165,233,.22);

}



.mb-status-deposit_paid {

    background: rgba(217,119,6,.12);

    color: #f59e0b;

    border: 1px solid rgba(245,158,11,.22);

}



.mb-status-paid {

    background: rgba(34,197,94,.12);

    color: #4ade80;

    border: 1px solid rgba(34,197,94,.22);

}



/* =========================================================

| SUMMARY

========================================================= */

.mb-hero-summary {

    display: grid;

    grid-template-columns: 1.2fr .8fr;

    gap: 18px;

    margin-bottom: 18px;

}



.mb-summary-card {

    background: rgba(30,41,59,.72);

    border: 1px solid rgba(255,255,255,.06);

    border-radius: 20px;

    padding: 26px;

}



.mb-summary-label {

    font-size: 12px;

    text-transform: uppercase;

    letter-spacing: .08em;

    color: #94a3b8;

    margin-bottom: 10px;

    font-weight: 800;

}



.mb-summary-name {

    color: #fff;

    font-size: 18px;

    font-weight: 800;

    margin-bottom: 8px;

}



.mb-summary-email {

    color: #cbd5e1;

    font-size: 15px;

}



.mb-amount-card {

    text-align: right;

    display: flex;

    flex-direction: column;

    justify-content: center;

}



.mb-summary-total {

    color: #38bdf8;

    font-size: 40px;

    font-weight: 900;

    line-height: 1.05;

}



/* =========================================================

| META DATES

========================================================= */

.mb-meta-grid {

    display: grid;

    grid-template-columns: repeat(3,1fr);

    gap: 16px;

    margin-bottom: 22px;

}



.mb-meta-box {

    background: rgba(15,23,42,.75);

    border: 1px solid rgba(255,255,255,.06);

    border-radius: 18px;

    padding: 18px;

}



.mb-meta-title {

    display: block;

    color: #94a3b8;

    font-size: 12px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: .05em;

    margin-bottom: 10px;

}



.mb-meta-box strong {

    color: #fff;

    font-size: 16px;

}



/* =========================================================

| MAIN GRID

========================================================= */

.mb-invoice-layout {

    display: grid;

    grid-template-columns: 2fr 1fr;

    gap: 24px;

    align-items: start;

}



.mb-right-panel {

    display: flex;

    flex-direction: column;

    gap: 20px;

}



/* =========================================================

| CARDS

========================================================= */

.mb-section-card {

    background: rgba(15,23,42,.62);

    border: 1px solid rgba(255,255,255,.06);

    border-radius: 22px;

    padding: 24px;

}



.mb-section-title {

    color: #fff;

    font-size: 22px;

    font-weight: 800;

    margin-bottom: 18px;

}



/* =========================================================

| ITEMS TABLE

========================================================= */

.mb-items-table {

    width: 100%;

}



.mb-items-head,

.mb-items-row {

    display: grid;

    grid-template-columns: 2.2fr .6fr 1fr 1fr;

    gap: 16px;

    align-items: center;

}



.mb-items-head {

    color: #94a3b8;

    font-size: 12px;

    font-weight: 800;

    text-transform: uppercase;

    letter-spacing: .05em;

    padding: 0 0 12px;

    border-bottom: 1px solid rgba(255,255,255,.08);

}



.mb-items-row {

    color: #fff;

    padding: 16px 0;

    border-bottom: 1px solid rgba(255,255,255,.05);

}



.mb-items-row:last-child {

    border-bottom: none;

}



.mb-row-total {

    font-weight: 800;

}



.mb-items-empty {

    color: #94a3b8;

    padding: 14px 0;

}



/* =========================================================

| PAYMENT FLOW

========================================================= */

.mb-flow-line {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 12px;

    color: #cbd5e1;

    font-size: 16px;

}



.mb-flow-line strong {

    color: #fff;

}



.mb-grand-line {

    border-top: 1px solid rgba(255,255,255,.08);

    padding-top: 16px;

    margin-top: 6px;

    font-size: 24px;

    font-weight: 900;

}



.mb-deposit-highlight {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 14px;

    margin-top: 20px;

    margin-bottom: 18px;

}



.mb-deposit-highlight > div {

    background: linear-gradient(135deg,rgba(14,165,233,.14),rgba(37,99,235,.10));

    border: 1px solid rgba(56,189,248,.18);

    border-radius: 16px;

    padding: 18px;

}



.mb-deposit-highlight small {

    display: block;

    color: #7dd3fc;

    text-transform: uppercase;

    font-size: 11px;

    font-weight: 800;

    margin-bottom: 8px;

    letter-spacing: .05em;

}



.mb-deposit-highlight strong {

    color: #fff;

    font-size: 28px;

    font-weight: 900;

}



.mb-payment-toggle {

    display: flex;

    flex-direction: column;

    gap: 10px;

    margin: 8px 0 16px;

    color: #cbd5e1;

    font-size: 14px;

}



.mb-payment-toggle label {

    display: flex;

    gap: 10px;

    align-items: center;

    cursor: pointer;

}



.mb-manual-charge-box {

    margin-top: 10px;

}



.mb-manual-label {

    font-size: 13px;

    color: #94a3b8;

    margin-bottom: 12px;

}



.mb-cta-stack {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 14px;

}



.mb-pay-btn {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    text-decoration: none;

    padding: 16px;

    border-radius: 14px;

    font-weight: 900;

    font-size: 16px;

    color: #fff;

}



.mb-full-pay-btn {

    background: linear-gradient(135deg,#2563eb,#60a5fa);

    box-shadow: 0 14px 24px rgba(37,99,235,.22);

}



.mb-deposit-pay-btn {

    background: linear-gradient(135deg,#f59e0b,#d97706);

    box-shadow: 0 14px 24px rgba(245,158,11,.18);

}



/* =========================================================

| SMS CARD

========================================================= */

.mb-sms-note,

.mb-sms-help {

    color: #94a3b8;

    font-size: 14px;

    line-height: 1.6;

}



.mb-sms-note {

    margin-bottom: 16px;

}



.mb-sms-fields label {

    display: block;

    color: #cbd5e1;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 8px;

    margin-top: 12px;

}



.mb-sms-fields input,

.mb-sms-fields textarea {

    width: 100%;

    background: #0f172a;

    border: 1px solid rgba(255,255,255,.08);

    color: #fff;

    border-radius: 14px;

    padding: 14px;

    font-size: 14px;

    box-sizing: border-box;

}



.mb-sms-fields textarea {

    resize: vertical;

    min-height: 120px;

}



.mb-sms-actions {

    margin-top: 16px;

    margin-bottom: 12px;

}



.mb-wide-btn {

    width: 100%;

}



/* =========================================================

| RESPONSIVE

========================================================= */

@media (max-width: 980px) {

    .mb-invoice-head,

    .mb-hero-summary,

    .mb-meta-grid,

    .mb-invoice-layout,

    .mb-deposit-highlight,

    .mb-cta-stack {

        grid-template-columns: 1fr;

        display: grid;

    }



    .mb-invoice-head {

        display: block;

    }



    .mb-company-block {

        text-align: left;

        margin-top: 18px;

    }



    .mb-amount-card {

        text-align: left;

    }



    .mb-items-head,

    .mb-items-row {

        grid-template-columns: 1fr;

        gap: 8px;

    }



    .mb-invoice-shell {

        padding: 24px;

    }



    .mb-invoice-topbar {

        align-items: flex-start;

    }

}



/* =========================================================

| PRINT

========================================================= */

@media print {

    .mb-invoice-topbar,

    .mb-sms-card {

        display: none !important;

    }



    .mb-invoice-shell {

        border: none;

        box-shadow: none;

        padding: 0;

    }



    body {

        background: #fff !important;

    }

}

</style>

@endpush



@section('content')



<div class="mb-invoice-page invoice-focus-mode">



    {{-- =========================================================

    | TOP BAR

    ========================================================== --}}

@if($isInternal)
<div class="mb-invoice-topbar" style="max-width:1100px;margin:0 auto 24px auto;">

    <div class="mb-invoice-topbar-left">

        <a href="{{ route('invoice.history') }}" class="mb-top-link">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>

        <a href="{{ route('invoice.edit', $invoice->id) }}" class="mb-top-link">
            <i class="fa-solid fa-pen-to-square"></i> Edit Invoice
        </a>

    </div>

    <div class="mb-invoice-topbar-right">

        @if(in_array($invoice->status, ['sent','viewed','deposit_paid','paid']))
            <button class="mb-action-btn mb-primary-btn mb-disabled-btn" disabled>
                <i class="fa-solid fa-check"></i> Sent
            </button>
        @else
            <form action="{{ route('invoice.send_email', $invoice->invoice_no) }}" method="POST" class="mb-inline-form">
                @csrf
                <button type="submit" class="mb-action-btn mb-primary-btn">
                    <i class="fa-solid fa-paper-plane"></i> Send Invoice
                </button>
            </form>
        @endif

        <a href="{{ route('invoice.download', $invoice->id) }}" class="mb-action-btn mb-blue-btn">
            <i class="fa-solid fa-file-pdf"></i> Download PDF
        </a>

        <button type="button" class="mb-action-btn mb-dark-btn" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Print
        </button>

        <button type="button" class="mb-action-btn mb-dark-btn" onclick="copyInvoiceLink()">
            <i class="fa-solid fa-link"></i> Copy Public Link
        </button>

        {{-- ✅ ONLY OWNER CAN SEE SMS --}}
        <button type="button" class="mb-action-btn mb-sms-btn" onclick="sendInvoiceSms()">
            <i class="fa-solid fa-comment-sms"></i> Send SMS
        </button>

    </div>

</div>
@endif



    {{-- =========================================================

    | INVOICE SHELL

    ========================================================== --}}

    <div class="mb-invoice-shell">
<div style="position:absolute;top:-12px;right:20px;font-size:12px;color:#94a3b8;">
    Powered by MediosBilling
</div>


        {{-- =========================================================

        | HEADER

        ========================================================== --}}

        <div class="mb-invoice-head">

            <div class="mb-brand-block">

                <div class="mb-eyebrow">Professional Invoice</div>

                <h1>Invoice #{{ $invoice->invoice_no }}</h1>



                <div class="mb-status-pill mb-status-{{ strtolower($invoice->status) }}">

                    {{ strtoupper($invoice->status) }}

                </div>

            </div>



            <div class="mb-company-block">

                <h2>{{ $invoice->company->name ?? 'Medios Billing' }}</h2>

                <div class="mb-company-sub">

                    {{ $invoice->company->email ?? '' }}

                </div>

            </div>

        </div>



        {{-- =========================================================

        | SUMMARY

        ========================================================== --}}

        <div class="mb-hero-summary">

            <div class="mb-summary-card">

                <div class="mb-summary-label">Billed To</div>

                <div class="mb-summary-name">{{ $invoice->customer_name }}</div>

                <div class="mb-summary-email">{{ $invoice->customer_email }}</div>

            </div>



            <div class="mb-summary-card mb-amount-card">

                <div class="mb-summary-label">Amount Due</div>

                <div class="mb-summary-total">${{ number_format((float) $invoice->total, 2) }}</div>

            </div>

        </div>



        {{-- =========================================================

        | META DATES

        ========================================================== --}}

        <div class="mb-meta-grid">

            <div class="mb-meta-box">

                <span class="mb-meta-title">Invoice Date</span>

                <strong>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') : '—' }}</strong>

            </div>



            <div class="mb-meta-box">

                <span class="mb-meta-title">Deposit Due</span>

                <strong>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : '—' }}</strong>

            </div>



            <div class="mb-meta-box">

                <span class="mb-meta-title">Remaining Due</span>

                <strong>{{ $invoice->remaining_due_date ? \Carbon\Carbon::parse($invoice->remaining_due_date)->format('M d, Y') : '—' }}</strong>

            </div>

        </div>



        {{-- =========================================================

        | MAIN GRID

        ========================================================== --}}

        <div class="mb-invoice-layout">



{{-- =====================================================
| LEFT PANEL
====================================================== --}}
<div class="mb-left-panel">
    
    {{-- SERVICES & ITEMS --}}
    <div class="mb-section-card">
        <div class="mb-section-title">Services & Items</div>

        <div class="mb-items-table">
            <div class="mb-items-head">
                <div>Description</div>
                <div>Qty</div>
                <div>Price</div>
                <div>Total</div>
            </div>

            @forelse($decodedItems as $item)
                @php
                    $qty = (float) ($item['qty'] ?? 0);
                    $price = (float) ($item['price'] ?? 0);
                    $lineTotal = $qty * $price;
                @endphp
                <div class="mb-items-row">
                    <div>{{ $item['description'] ?? 'Service' }}</div>
                    <div>{{ rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.') }}</div>
                    <div>${{ number_format($price, 2) }}</div>
                    <div class="mb-row-total">${{ number_format($lineTotal, 2) }}</div>
                </div>
            @empty
                <div class="mb-items-empty">No line items found.</div>
            @endforelse
        </div>
    </div>

{{-- ✅ CLIENT MESSAGING (MOVED HERE) --}}
@if($isInternal)
<div class="mb-section-card mb-sms-card">

    <div class="mb-section-title">Client Messaging</div>

    <div class="mb-sms-note">
        Use this section to quickly text the invoice link to the client using your Telnyx setup.
    </div>

    <div class="mb-sms-fields">
        <label>Customer Phone</label>
        <input type="text" id="invoice_sms_phone" value="{{ $invoice->customer_phone ?? '' }}" placeholder="Enter customer phone">

        <label>SMS Preview</label>
        <textarea id="invoice_sms_message" rows="5">
Hi {{ $invoice->customer_name }}, your invoice #{{ $invoice->invoice_no }} is ready:
{{ $publicUrl }}
        </textarea>
    </div>

    <div class="mb-sms-actions">
        <button type="button" class="mb-action-btn mb-sms-btn mb-wide-btn" onclick="sendInvoiceSms()">
            <i class="fa-solid fa-paper-plane"></i> Send SMS
        </button>
    </div>

    <div class="mb-sms-help">
        This gives the owner a fast way to send the invoice by text from inside the invoice page.
    </div>

</div>
@endif

            {{-- =====================================================

            | RIGHT PANEL

            ====================================================== --}}

            <div class="mb-right-panel">



{{-- PAYMENT FLOW --}}

<div class="mb-section-card">

    <div class="mb-section-title">Payment Flow</div>

    {{-- ✅ SHOW PAID MESSAGE --}}
    @if($invoice->status === 'paid')
        <div style="background:#16a34a;color:white;padding:15px;border-radius:8px;text-align:center;font-weight:600;margin-bottom:18px;">
            ✅ This invoice has been paid
        </div>
    @endif

{{-- ✅ ONLY SHOW PAY BUTTON IF NOT PAID --}}
@if($invoice->status !== 'paid')
    <div style="margin-bottom:18px;">
        <a href="{{ route('invoice.pay', $invoice->invoice_no) }}"
           class="mb-pay-btn mb-full-pay-btn"
           style="width:100%;font-size:18px;padding:18px;">
            <i class="fa-solid fa-credit-card"></i>

            @if($invoice->status === 'partial')
                Pay Remaining Balance
            @else
                {{ $isInternal ? 'Pay Full Invoice Now' : 'Pay Now' }}
            @endif

        </a>
    </div>
@endif

    <div class="mb-flow-line">
        <span>Subtotal</span>
        <strong>${{ number_format((float) ($invoice->subtotal_amount ?? 0), 2) }}</strong>
    </div>

    <div class="mb-flow-line">
        <span>Tax</span>
        <strong>${{ number_format((float) ($invoice->tax_amount ?? 0), 2) }}</strong>
    </div>
                    </div>



                    <div class="mb-flow-line mb-grand-line">

                        <span>Total</span>

                        <strong>${{ number_format((float) ($invoice->total ?? 0), 2) }}</strong>

                    </div>

{{-- ✅ PAYMENT STATUS --}}
<div style="margin-top:10px; font-weight:700; color:#fff;">
    @if($invoice->status === 'paid')
        <span style="color:#4ade80;">PAID IN FULL</span>
    @elseif($invoice->status === 'partial')
        <span style="color:#f59e0b;">PARTIAL PAYMENT RECEIVED</span>
    @else
        <span style="color:#f87171;">PENDING</span>
    @endif
</div>

                    <div class="mb-deposit-highlight">

                        <div>

                            <small>Deposit Required</small>

                            <strong>${{ number_format((float) ($invoice->deposit_amount ?? 0), 2) }}</strong>

                        </div>

                        <div>

                            <small>Remaining Balance</small>

                            <strong>${{ number_format((float) ($invoice->remaining_balance ?? 0), 2) }}</strong>

</div> {{-- END mb-deposit-highlight --}}
@if($invoice->status === 'partial')
<div style="margin-top:10px;">
    <strong>Remaining Balance:</strong> 
    ${{ number_format($invoice->remaining_balance, 2) }}
</div>
@endif

<div style="margin-top:10px;">
    <strong>Total Paid:</strong> 
    ${{ number_format($invoice->amount_paid ?? 0, 2) }}
</div>
                    </div>



{{-- PAYMENT ACTIONS --}}
                @if($isInternal)
                    <div class="mb-payment-toggle">
                        <label>
                            <input type="radio" name="payment_mode" value="self" checked>
                            Customer will pay themselves
                        </label>

                        <label>
                            <input type="radio" name="payment_mode" value="manual">
                            I am charging customer manually
                        </label>
                    </div>

                    <div class="mb-manual-charge-box">
                        <div class="mb-manual-label">
                            <p>
                                <strong>Manual Charge Mode</strong><br>
                                Use these buttons if you are collecting payment directly
                                by phone, in person, or assisting the customer.
                            </p>
                        </div>

                        <div class="mb-cta-stack">
                            <a href="{{ route('invoice.pay', $invoice->invoice_no) }}" class="mb-pay-btn mb-full-pay-btn">
                                <i class="fa-solid fa-credit-card"></i> Pay Now
                            </a>

                            <a href="{{ route('invoice.pay', ['invoice_no' => $invoice->invoice_no, 'payment_type' => 'deposit']) }}" class="mb-pay-btn mb-deposit-pay-btn">
                                <i class="fa-solid fa-money-bill-wave"></i> Pay Deposit
                            </a>
                        </div>
                    </div>
@endif


</div> <!-- mb-right-panel -->

</div> <!-- mb-invoice-layout -->

</div> <!-- mb-invoice-shell -->

</div> <!-- mb-invoice-page -->

@endsection



{{-- =========================================================

| PAGE-SPECIFIC SCRIPTS

========================================================== --}}

@push('page_scripts')
<script>

function copyInvoiceLink() {
    const url = @json($publicUrl);
    navigator.clipboard.writeText(url).then(() => {
        alert('Public invoice link copied.');
    });
}

function sendInvoiceSms() {
    const phone = document.getElementById('invoice_sms_phone')?.value || '';
    const message = document.getElementById('invoice_sms_message')?.value || '';

    if (!phone.trim()) {
        alert('Please enter a customer phone number first.');
        return;
    }

    fetch('{{ route('invoice.send_sms', $invoice->invoice_no) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ phone, message })
    })
    .then(async response => {
        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.error || 'SMS failed');
        }
        return response.json();
    })
    .then(() => alert('SMS sent successfully.'))
    .catch(error => alert(error.message || 'Error sending SMS.'));
}

document.addEventListener('DOMContentLoaded', function () {

    const radios = document.querySelectorAll('input[name="payment_mode"]');
    const manualBox = document.querySelector('.mb-manual-charge-box');

    function updateMode() {
        const selected = document.querySelector('input[name="payment_mode"]:checked')?.value;

        if (!manualBox) return;

        if (selected === 'manual') {
            manualBox.style.opacity = '1';
            manualBox.style.pointerEvents = 'auto';
        } else {
            manualBox.style.opacity = '0.5';
            manualBox.style.pointerEvents = 'none';
        }
    }

    radios.forEach(el => el.addEventListener('change', updateMode));
    updateMode();
});

</script>
@endpush
