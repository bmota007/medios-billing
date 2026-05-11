@extends('layouts.admin')
@php
$isPublicReceipt = request()->routeIs('receipt.public');
@endphp

@section('content')

@php

$invoice = (object) ($data['invoice'] ?? []);

$company = (object) ($invoice->company ?? []);

$customer = (object) ($invoice->customer ?? []);

$items = json_decode($invoice->items ?? '[]', true);

$type = strtoupper($snapshot->snapshot_type);

@endphp

<div class="snap-shell">

<div class="snap-topbar">

<a href="{{ route('invoice.view',$invoice->id) }}"
   class="snap-btn dark-btn">

@if(!$isPublicReceipt)
    ← Back To Invoice
@endif

</a>

<div class="snap-actions">

<button onclick="window.print()"
        class="snap-btn blue-btn">

    Print

</button>

<a
    href="{{
        $isPublicReceipt
            ? route('receipt.public.download', $snapshot->public_token)
            : route('invoice.snapshot.download', $snapshot->id)
    }}"
    class="action-btn blue"
>
    ⬇ Download PDF
</a>

@if(!$isPublicReceipt)

<a
    href="{{ route('invoice.snapshot.email', $snapshot->id) }}"
    class="action-btn green"
>
    ✉ Email Receipt
</a>

@endif

@if(!$isPublicReceipt)

<button
    onclick="copySnapshotLink()"
    class="action-btn dark"
    type="button"
>
    🔗 Share Link
</button>

@endif

@if(!$isPublicReceipt)

<a href="{{ route('invoice.view',$invoice->id) }}"
   class="snap-btn green-btn">

    Open Live Invoice

</a>

@endif

</div>

</div>

<div class="snap-card">

<div class="snap-header">

<div>

<div class="mini-label">

ARCHIVED SNAPSHOT

</div>

<div class="snapshot-badge">

{{ $type }}

</div>

<h1>

Invoice #{{ $invoice->invoice_no }}

</h1>

<p class="created-at">

Archived:
{{ \Carbon\Carbon::parse($snapshot->created_at)->format('M d, Y h:i A') }}

</p>

</div>

<div class="company-side">

<h2>{{ $company->name ?? 'Company' }}</h2>

<p>{{ $company->email ?? '' }}</p>

<p>{{ $company->phone ?? '' }}</p>

</div>

</div>

<div class="grid-top">

<div class="info-card">

<small>BILLED TO</small>

<h3>{{ $invoice->customer_name ?? '' }}</h3>

<p>{{ $invoice->customer_email ?? '' }}</p>

<p>{{ $invoice->customer_phone ?? '' }}</p>

</div>

<div class="amount-card">

<small>TOTAL</small>

<div class="amount">

${{ number_format($invoice->total ?? 0,2) }}

</div>

</div>

</div>

<div class="panel">

<h2>Services & Items</h2>

<table class="snap-table">

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

<td>${{ number_format($item['price'] ?? 0,2) }}</td>

<td>${{ number_format($item['line_total'] ?? 0,2) }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

<div class="payment-grid">

<div class="pay-card">

<small>SUBTOTAL</small>

<h3>${{ number_format($invoice->subtotal ?? 0,2) }}</h3>

</div>

<div class="pay-card">

<small>DEPOSIT</small>

<h3>${{ number_format($invoice->deposit_amount ?? 0,2) }}</h3>

</div>

<div class="pay-card">

<small>REMAINING</small>

<h3>${{ number_format($invoice->remaining_balance ?? 0,2) }}</h3>

</div>

<div class="pay-card">

<small>STATUS</small>

<h3>{{ strtoupper($invoice->status ?? '') }}</h3>

</div>

</div>

@if(isset($invoice->payment_method))

<div class="panel">

<h2>Payment Information</h2>

<div class="payment-info">

<div>
<strong>Payment Method:</strong>
{{ strtoupper($invoice->payment_method ?? '') }}
</div>

@if($invoice->payment_reference)
<div>
<strong>Reference:</strong>
{{ $invoice->payment_reference }}
</div>
@endif

@if($invoice->paid_at)
<div>
<strong>Paid At:</strong>
{{ \Carbon\Carbon::parse($invoice->paid_at)->format('M d, Y h:i A') }}
</div>
@endif

</div>

</div>

@endif

</div>

</div>

<script>
function copySnapshotLink()
{
    navigator.clipboard.writeText(
        "{{ route('receipt.public', $snapshot->public_token) }}"
    );

    alert('Public receipt link copied!');
}
</script>

<style>

.snap-shell{
padding:40px;
background:
radial-gradient(circle at top left,#122041,#050816);
min-height:100vh;
color:white;
}

.snap-topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
flex-wrap:wrap;
gap:15px;
}

.snap-actions{
display:flex;
gap:12px;
}

.snap-btn{
padding:14px 18px;
border-radius:12px;
text-decoration:none;
font-weight:800;
border:none;
cursor:pointer;
color:white;
}

.blue-btn{
background:#2563eb;
}

.green-btn{
background:#10b981;
}

.dark-btn{
background:#1e293b;
}

.snap-card{
max-width:1200px;
margin:auto;
background:#050b1d;
border-radius:30px;
padding:40px;
border:1px solid #172554;
}

.snap-header{
display:flex;
justify-content:space-between;
gap:20px;
margin-bottom:35px;
flex-wrap:wrap;
}

.snapshot-badge{
display:inline-block;
background:#2563eb;
padding:10px 18px;
border-radius:999px;
font-weight:800;
margin-top:10px;
margin-bottom:18px;
}

.mini-label{
color:#38bdf8;
font-size:12px;
letter-spacing:2px;
margin-bottom:10px;
}

.created-at{
color:#94a3b8;
}

.company-side{
text-align:right;
}

.company-side h2{
font-size:48px;
margin:0;
}

.grid-top{
display:grid;
grid-template-columns:2fr 1fr;
gap:22px;
margin-bottom:25px;
}

.info-card,
.amount-card,
.panel,
.pay-card{
background:#111b34;
padding:28px;
border-radius:22px;
}

.amount{
font-size:52px;
font-weight:900;
color:#38bdf8;
margin-top:12px;
}

.snap-table{
width:100%;
border-collapse:collapse;
}

.snap-table th,
.snap-table td{
padding:16px;
border-bottom:1px solid rgba(255,255,255,.06);
text-align:left;
}

.payment-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:18px;
margin-top:25px;
}

.pay-card h3{
margin-top:12px;
font-size:28px;
}

.payment-info{
display:flex;
flex-direction:column;
gap:14px;
}

@media(max-width:768px){

.snap-shell{
padding:15px;
}

.snap-card{
padding:20px;
}

.grid-top,
.payment-grid{
grid-template-columns:1fr;
}

.snap-header{
flex-direction:column;
}

.company-side{
text-align:left;
}

.amount{
font-size:38px;
}

}

</style>

@endsection
