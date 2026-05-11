@extends('layouts.admin')

@section('content')
@php
$isPublicView = request()->routeIs('invoice.public_view');
@endphp

@php
$items = json_decode($invoice->items, true) ?? [];

$subtotal = $invoice->subtotal ?? $invoice->total ?? 0;
$tax = $invoice->tax_amount ?? 0;
$total = $invoice->total ?? 0;

$deposit = $invoice->deposit_amount;

$remaining = $invoice->remaining_balance;

if(!$remaining || $remaining <= 0){
    $remaining = $total - $deposit;
}
@endphp

<div class="invoice-shell">
@if(session('success'))
<div class="success-banner">
    {{ session('success') }}
</div>
@endif

    <div class="topbar">
@if(!$isPublicView)

        <div class="left-actions">
            <a href="{{ url('/invoices') }}" class="top-btn">
                ← Back
            </a>
        </div>

<div class="right-actions">

<a href="{{ route('invoice.edit',$invoice->id) }}"
   class="action-btn dark">
    ✏ Edit Invoice
</a>

<form method="POST"
      action="{{ route('invoice.send.existing') }}"
      style="display:inline;">
    @csrf

    <input type="hidden"
           name="invoice_id"
           value="{{ $invoice->id }}">

    <button type="submit"
            class="action-btn send-btn">
        ✉️ Send
    </button>
</form>

@if($invoice->status === 'sent')

<form action="{{ route('invoice.resend',$invoice->id) }}"
      method="POST"
      style="display:inline;">
    @csrf

    <button type="submit"
            class="action-btn resend-btn">
        ↻ Resend
    </button>
</form>

@else

<form action="{{ route('invoice.send.existing') }}"
      method="POST"
      style="display:inline;">
    @csrf

    <input type="hidden"
           name="invoice_id"
           value="{{ $invoice->id }}">

    <button type="submit"
            class="action-btn send-btn">
        ✓ Send
    </button>
</form>

@endif

    <a href="{{ route('invoice.pdf',$invoice->id) }}"
       class="action-btn blue">
        Download PDF
    </a>

    <a href="#"
       onclick="window.print(); return false;"
       class="action-btn dark">
        Print
    </a>

    <a href="{{ url('/invoice/pay/'.$invoice->invoice_no) }}"
       class="action-btn green">
        Pay Now
    </a>

</div>

</div>
@endif
<div class="invoice-card">

        <div class="invoice-header">

            <div>

                <div class="mini-title">
                    PROFESSIONAL INVOICE
@if($invoice->status === 'paid')

<div style="margin-top:15px;margin-bottom:20px;">

    <span style="
        background:#16a34a;
        color:white;
        padding:12px 22px;
        border-radius:10px;
        font-size:18px;
        font-weight:800;
        display:inline-block;
    ">
        ✅ PAID IN FULL
    </span>

</div>

@endif

  </div>

                <h1>
                    Invoice #{{ $invoice->invoice_no }}
                </h1>

                <span class="status {{ $invoice->status }}">
                    {{ strtoupper($invoice->status) }}
                </span>

            </div>

            <div class="company-info">

                <h2>
                    {{ auth()->user()->company->name ?? 'Company' }}
                </h2>

                <p>
                    {{ auth()->user()->company->email ?? '' }}
                </p>

                <p>
                    {{ auth()->user()->company->phone ?? '' }}
                </p>

            </div>

        </div>

        <div class="grid-top">

            <div class="card-block">

                <small>BILLED TO</small>

                <h3>
                    {{ $invoice->customer_name
                        ?? $invoice->customer->name
                        ?? 'Customer' }}
                </h3>

                <p>
                    {{ $invoice->customer_email
                        ?? $invoice->customer->email
                        ?? '' }}
                </p>

            </div>

            <div class="card-block amount-due">

                <small>AMOUNT DUE</small>

                <div class="amount">
                    ${{ number_format($total,2) }}
                </div>

            </div>

        </div>

        <div class="grid-dates">

            <div class="date-card">
                <small>INVOICE DATE</small>

                <h4>
                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                </h4>
            </div>

            @if($invoice->deposit_due_date)
            <div class="date-card">
                <small>DEPOSIT DUE</small>

                <h4>
                    {{ \Carbon\Carbon::parse($invoice->deposit_due_date)->format('M d, Y') }}
                </h4>
            </div>
            @endif

            @if($invoice->remaining_due_date)
            <div class="date-card">
                <small>REMAINING DUE</small>

                <h4>
                    {{ \Carbon\Carbon::parse($invoice->remaining_due_date)->format('M d, Y') }}
                </h4>
            </div>
            @else
            <div class="date-card">
                <small>DUE DATE</small>

                <h4>
                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                </h4>
            </div>
            @endif

        </div>

        <div class="content-grid">

            {{-- LEFT COLUMN --}}
            <div class="left-column">

                <div class="panel">

                    <h2>Services & Items</h2>

                    <table class="invoice-table">

                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>

                        @foreach($items as $item)

                            <tr>

                                <td>{{ $item['desc'] ?? '' }}</td>

                                <td>{{ $item['qty'] ?? 0 }}</td>

                                <td>
                                    ${{ number_format($item['price'] ?? 0,2) }}
                                </td>

                                <td>
                                    ${{ number_format($item['line_total'] ?? 0,2) }}
                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>
@if(!$isPublicView)

                <div class="panel sms-panel">

                    <h2>Client Messaging</h2>

                    <p class="sms-text">
                        Use this section to quickly text the invoice link
                        to the client using your Telnyx setup.
                    </p>

                    <label>Customer Phone</label>

                    <input type="text"
                           value="{{ $invoice->customer_phone ?? '' }}"
                           class="sms-input">

                    <label>SMS Preview</label>

                    <textarea class="sms-preview">Hi {{ $invoice->customer_name ?? 'Customer' }}, your invoice #{{ $invoice->invoice_no }} is ready:

{{ url('/invoice/view/'.$invoice->invoice_no) }}</textarea>

                    <button class="sms-btn">
                        ✈ Send SMS
                    </button>

                    <p class="sms-footer">
                        This gives the owner a fast way to send the invoice
                        by text from inside the invoice page.
                    </p>

</div>

<div class="panel test-email-panel">

    <h2>Send Test Email</h2>

    <form method="POST"
          action="{{ route('invoice.test.email',$invoice->id) }}">

        @csrf

        <input type="email"
               name="test_email"
               class="test-email-input"
               placeholder="you@example.com"
               required>

        <button type="submit"
                class="test-email-btn">

            Send Test

        </button>

    </form>

</div>

@endif

</div>

{{-- RIGHT COLUMN --}}


            <div class="right-column">

                <div class="panel payment-panel">

                    <h2>Payment Flow</h2>

                    <a href="{{ url('/invoice/pay/'.$invoice->invoice_no) }}"
                       class="pay-btn">
                        💳 Pay Full Invoice Now
                    </a>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong>${{ number_format($subtotal,2) }}</strong>
                    </div>

                    <div class="summary-row">
                        <span>Tax</span>
                        <strong>${{ number_format($tax,2) }}</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total</span>
                        <strong>${{ number_format($total,2) }}</strong>
                    </div>

                    <div class="payment-boxes">

                        <div class="pay-box">

  <small>DEPOSIT REQUIRED</small>

        <h3>

            ${{ number_format($deposit,2) }}

        </h3>

    </div>

@if($invoice->remaining_balance > 0)

    <div class="pay-box">

        <small>REMAINING BALANCE</small>

        <h3>

            ${{ number_format($remaining,2) }}

        </h3>

    </div>

@endif

</div>

@if(!$isPublicView)

    <div class="payment-options">

        <label class="radio-option">

            <input type="radio" checked>

            <span>Customer will pay themselves</span>

        </label>

        <label class="radio-option">

            <input type="radio">

            <span>I am charging customer manually</span>

        </label>

    </div>

    <div class="manual-text">

        <strong>Manual Charge Mode</strong>

        <p>

            Use these buttons if you are collecting payment

            directly by phone, in person, or assisting the customer.

        </p>

    </div>

@endif

@if(

    $invoice->remaining_balance > 0 &&

    $invoice->status !== 'paid'

)

<div class="manual-buttons">

    <button class="manual-btn blue-btn">
        💳 Pay Now
    </button>

    <button class="manual-btn gold-btn">
        💳 Pay Deposit
    </button>

</div>
@endif

@if(
    $invoice->status === 'partial' &&
    $invoice->remaining_balance > 0 &&
    $invoice->stripe_customer_id &&
    $invoice->stripe_payment_method_id
)

<form method="POST"
      action="{{ route('invoice.charge.remaining', $invoice->id) }}"
      style="margin-top:18px;">

    @csrf

    <button type="submit"
            class="manual-btn"
            style="
                width:100%;
                background:linear-gradient(
                    135deg,
                    #16a34a,
                    #22c55e
                );
            ">

        ⚡ Charge Remaining Balance Now

    </button>

</form>

@endif
                </div>

            </div>

        </div>

    </div>

</div>

@if($invoice->snapshots->count())

<div style="margin-top:25px;">

    <h3 style="margin-bottom:15px;">

        Invoice Archive

    </h3>

    @foreach($invoice->snapshots as $snapshot)

        <a

            href="{{ route('invoice.snapshot.show', $snapshot->id) }}"


            style="

                display:inline-block;

                margin-right:10px;

                margin-bottom:10px;

                background:#1d4ed8;

                color:white;

                padding:10px 16px;

                border-radius:8px;

                text-decoration:none;

                font-weight:bold;

            "

        >

            {{ strtoupper($snapshot->snapshot_type) }}

        </a>

    @endforeach

</div>

@endif

<style>

.success-banner{
background:rgba(16,185,129,.15);
border:1px solid #10b981;
color:#10b981;
padding:18px;
border-radius:16px;
margin-bottom:25px;
text-align:center;
font-weight:800;
font-size:16px;
}

.invoice-shell{
padding:30px;
color:#fff;
background:
radial-gradient(circle at top left,#122041,#050816);
min-height:100vh;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
gap:15px;
flex-wrap:wrap;
}

.right-actions{
display:flex;
gap:12px;
flex-wrap:wrap;
}

.top-btn,
.action-btn{
padding:12px 18px;
border-radius:12px;
text-decoration:none;
color:#fff;
font-weight:700;
}

.action-btn.blue{
background:#2563eb;
}

.action-btn.green{
background:#10b981;
}

.action-btn.dark{
background:#1e293b;
}

.invoice-card{
max-width:1200px;
margin:auto;
background:#050b1d;
border:1px solid #172554;
border-radius:30px;
padding:40px;
box-shadow:0 30px 80px rgba(0,0,0,.45);
}

.invoice-header{
display:flex;
justify-content:space-between;
gap:30px;
margin-bottom:35px;
flex-wrap:wrap;
}

.mini-title{
font-size:12px;
letter-spacing:2px;
color:#38bdf8;
margin-bottom:10px;
}

.invoice-header h1{
font-size:58px;
margin:0;
line-height:1.1;
}

.status{
display:inline-block;
margin-top:18px;
padding:10px 16px;
border-radius:999px;
background:#1d4ed8;
font-size:12px;
font-weight:700;
}

.company-info{
text-align:right;
}

.company-info h2{
margin:0;
font-size:48px;
line-height:1.1;
}

.company-info p{
margin-top:8px;
color:#e2e8f0;
}

.grid-top{
display:grid;
grid-template-columns:2fr 1fr;
gap:22px;
margin-bottom:22px;
}

.card-block{
background:#111b34;
padding:35px;
border-radius:24px;
}

.card-block small{
display:block;
margin-bottom:12px;
color:#cbd5e1;
font-size:12px;
letter-spacing:1px;
}

.card-block h3{
font-size:34px;
margin:0;
}

.card-block p{
margin-top:10px;
color:#dbeafe;
}

.amount-due .amount{
font-size:60px;
font-weight:800;
color:#38bdf8;
margin-top:18px;
}

.grid-dates{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:22px;
margin-bottom:25px;
}

.date-card{
background:#111b34;
padding:28px;
border-radius:22px;
}

.date-card small{
display:block;
margin-bottom:10px;
color:#cbd5e1;
}

.date-card h4{
margin:0;
font-size:28px;
}

.content-grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:25px;
align-items:start;
}

.left-column,
.right-column{
display:flex;
flex-direction:column;
gap:25px;
}

.panel{
background:#091224;
padding:30px;
border-radius:25px;
}

.panel h2{
margin-top:0;
margin-bottom:25px;
font-size:32px;
}

.invoice-table{
width:100%;
border-collapse:collapse;
}

.invoice-table th{
text-align:left;
padding-bottom:18px;
color:#94a3b8;
font-size:13px;
}

.invoice-table td{
padding:18px 0;
border-top:1px solid rgba(255,255,255,.06);
font-size:16px;
}

.summary-row,
.summary-total{
display:flex;
justify-content:space-between;
margin-top:18px;
}

.summary-row{
font-size:15px;
}

.summary-total{
font-size:42px;
font-weight:800;
margin-top:28px;
align-items:center;
}

.payment-boxes{
display:grid;
grid-template-columns:1fr 1fr;
gap:18px;
margin-top:28px;
}

.pay-box{
background:#13203d;
padding:24px;
border-radius:18px;
}

.pay-box small{
display:block;
margin-bottom:12px;
color:#cbd5e1;
font-size:12px;
}

.pay-box h3{
margin:0;
font-size:36px;
}

.pay-btn{
display:block;
margin-bottom:28px;
background:linear-gradient(135deg,#2563eb,#60a5fa);
padding:20px;
text-align:center;
border-radius:18px;
font-weight:800;
text-decoration:none;
color:#fff;
font-size:18px;
}

.sms-text{
color:#94a3b8;
font-size:14px;
line-height:1.7;
margin-bottom:20px;
}

.sms-panel label{
display:block;
margin-top:18px;
margin-bottom:8px;
font-size:13px;
font-weight:700;
color:#cbd5e1;
}

.sms-input,
.sms-preview{
width:100%;
background:#0b1730;
border:1px solid rgba(255,255,255,.08);
border-radius:14px;
padding:14px;
color:#fff;
font-size:14px;
}

.sms-preview{
min-height:120px;
resize:none;
}

.sms-btn{
width:100%;
margin-top:20px;
border:none;
padding:16px;
border-radius:16px;
background:linear-gradient(135deg,#14b8a6,#34d399);
color:#fff;
font-weight:800;
font-size:16px;
cursor:pointer;
}

.sms-footer{
margin-top:12px;
font-size:12px;
color:#94a3b8;
}

.payment-options{
margin-top:22px;
display:flex;
flex-direction:column;
gap:12px;
}

.radio-option{
display:flex;
align-items:center;
gap:10px;
font-size:14px;
color:#dbeafe;
}

.radio-option input{
accent-color:#2563eb;
}

.manual-text{
margin-top:22px;
}

.manual-text strong{
display:block;
margin-bottom:8px;
color:#cbd5e1;
font-size:18px;
}

.manual-text p{
font-size:13px;
line-height:1.7;
color:#64748b;
}

.manual-buttons{
display:grid;
grid-template-columns:1fr 1fr;
gap:14px;
margin-top:22px;
}

.manual-btn{
border:none;
padding:16px;
border-radius:16px;
font-weight:800;
font-size:15px;
color:#fff;
cursor:pointer;
}

.test-email-panel{
margin-top:25px;
}

.test-email-input{
width:100%;
background:#020617;
border:1px solid rgba(255,255,255,.08);
border-radius:18px;
padding:22px;
color:#fff;
font-size:18px;
margin-top:20px;
}

.test-email-btn{
width:100%;
margin-top:25px;
border:none;
padding:22px;
border-radius:22px;
background:linear-gradient(135deg,#4f46e5,#818cf8);
color:#fff;
font-size:20px;
font-weight:800;
cursor:pointer;
}

.blue-btn{
background:linear-gradient(135deg,#1d4ed8,#60a5fa);
}

.gold-btn{
background:linear-gradient(135deg,#a16207,#f59e0b);
}

.send-btn{
background:linear-gradient(135deg,#2563eb,#3b82f6);
}

.resend-btn{
background:linear-gradient(135deg,#0f766e,#14b8a6);
}

@media(max-width:1100px){

@media(max-width:768px){

.invoice-shell{
padding:12px;
overflow-x:hidden;
}

.invoice-card{
padding:18px;
border-radius:20px;
overflow:hidden;
}

.invoice-header{
flex-direction:column;
align-items:flex-start;
gap:20px;
}

.invoice-header h1{
font-size:clamp(28px,8vw,42px);
line-height:1.1;
word-break:break-word;
}

.company-info{
text-align:left;
width:100%;
}

.company-info h2{
font-size:clamp(26px,7vw,38px);
line-height:1.1;
word-break:break-word;
}

.topbar{
flex-direction:column;
align-items:stretch;
}

.right-actions{
width:100%;
display:grid;
grid-template-columns:1fr 1fr;
gap:10px;
}

.action-btn,
.top-btn{
width:100%;
text-align:center;
justify-content:center;
display:flex;
align-items:center;
}

.grid-top,
.grid-dates,
.payment-boxes,
.manual-buttons{
grid-template-columns:1fr;
}

.content-grid{
grid-template-columns:1fr;
}

.card-block,
.date-card,
.panel{
padding:18px;
}

.amount-due .amount{
font-size:42px;
word-break:break-word;
}

.summary-total{
font-size:30px;
flex-direction:column;
align-items:flex-start;
gap:10px;
}

.pay-box h3{
font-size:28px;
word-break:break-word;
}

.invoice-table{
display:block;
overflow-x:auto;
white-space:nowrap;
}

.invoice-table th,
.invoice-table td{
font-size:14px;
padding:12px 8px;
}

.sms-input,
.sms-preview,
.test-email-input{
font-size:16px;
}

.pay-btn{
font-size:16px;
padding:18px;
}

}

.content-grid{
grid-template-columns:1fr;
}

.grid-top{
grid-template-columns:1fr;
}

.grid-dates{
grid-template-columns:1fr;
}

.invoice-header{
flex-direction:column;
}

.company-info{
text-align:left;
}

.invoice-header h1{
font-size:42px;
}

.company-info h2{
font-size:38px;
}

}

</style>

@endsection
