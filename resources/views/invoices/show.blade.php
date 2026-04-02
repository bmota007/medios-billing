@extends('layouts.app')

@section('content')

<div class="card">

<div style="display:flex;justify-content:space-between;margin-bottom:25px;">

<div>
<h1 style="margin:0;">Invoice</h1>
<p>#{{ $invoice->invoice_no }}</p>
</div>

<div style="text-align:right;">
<p style="margin:0;font-weight:bold;">{{ auth()->user()->company->name ?? '' }}</p>
<p style="margin:0;">{{ auth()->user()->company->email ?? '' }}</p>
<p style="margin:0;">{{ auth()->user()->company->phone ?? '' }}</p>
</div>

</div>

<hr>

<div style="display:flex;justify-content:space-between;margin-top:20px;margin-bottom:20px;">

<div>

<strong>Bill To</strong>

<p>{{ $invoice->customer_name }}</p>
<p>{{ $invoice->customer_email }}</p>

@if($invoice->street_address)
<p>{{ $invoice->street_address }}</p>
@endif

@if($invoice->city_state_zip)
<p>{{ $invoice->city_state_zip }}</p>
@endif

</div>

<div style="text-align:right;">

<p><strong>Date:</strong> {{ $invoice->invoice_date }}</p>
<p><strong>Due:</strong> {{ $invoice->due_date }}</p>

@if($invoice->status == 'paid')
<p style="color:green;font-weight:bold;">PAID</p>
@else
<p style="color:red;font-weight:bold;">UNPAID</p>
@endif

</div>

</div>

<table>

<thead>

<tr>

<th>Service</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>

</tr>

</thead>

<tbody>

@foreach($invoice->items ?? [] as $item)

<tr>


<td>{{ $item['desc'] ?? '' }}</td>

<td>{{ number_format($item['qty'] ?? 0,2) }}</td>

<td>${{ number_format($item['price'] ?? 0,2) }}</td>
<td>${{ number_format($item['line_total'] ?? 0,2) }}</td>
</tr>

@endforeach

</tbody>

</table>

<div style="margin-top:25px;display:flex;justify-content:flex-end;">

<div style="width:250px;">

<div style="display:flex;justify-content:space-between;border-bottom:1px solid #ddd;padding-bottom:6px;">
<span>Subtotal</span>
<span>${{ number_format($invoice->total,2) }}</span>
</div>

<div style="display:flex;justify-content:space-between;font-weight:bold;padding-top:8px;">
<span>Total</span>
<span>${{ number_format($invoice->total,2) }}</span>
</div>

</div>

</div>

<div style="margin-top:30px;display:flex;gap:10px;">

<a href="{{ route('invoice.pdf',$invoice->id) }}" class="btn btn-blue">
Download PDF
</a>

<button class="btn btn-green">
Send Invoice
</button>

<button class="btn btn-purple">
Pay Now
</button>

</div>

</div>

@endsection
