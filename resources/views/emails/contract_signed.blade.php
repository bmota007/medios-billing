@extends('emails.layout')

@section('content')

<h2 style="margin-top:0;">Contract Signed ✍️</h2>

<p><strong>Great news!</strong> A contract has been signed.</p>

<hr>

<p><strong>Company:</strong> {{ $quote->company->name ?? 'Company' }}</p>
<p><strong>Quote #:</strong> {{ $quote->quote_number }}</p>
<p><strong>Customer:</strong> {{ $quote->customer->name ?? '' }}</p>

<hr>

<p><strong>Invoice Created:</strong> {{ $invoice->invoice_no }}</p>

<div style="margin-top:20px;text-align:center;">
    <a href="{{ url('/invoice/view/'.$invoice->invoice_no) }}"
       style="background:#0ea5e9;color:white;padding:12px 20px;border-radius:6px;text-decoration:none;">
        View Invoice
    </a>
</div>

@endsection
