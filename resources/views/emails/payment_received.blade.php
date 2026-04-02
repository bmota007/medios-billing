@extends('emails.layout')

@section('content')

<h2 style="margin-top:0; color: #1e293b;">Payment Confirmation ✅</h2>

<p>Hello {{ $invoice->customer_name }},</p>
<p>Thank you for your payment. Your transaction has been successfully processed for <strong>{{ $invoice->company->name }}</strong>.</p>

<div style="background: #f8fafc; padding: 20px; border-radius: 12px; margin: 20px 0; border: 1px solid #e2e8f0;">
    <p style="margin: 0 0 10px 0;"><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</p>
    <p style="margin: 0 0 10px 0;"><strong>Amount Paid:</strong> ${{ number_format($invoice->total, 2) }}</p>
    <p style="margin: 0;"><strong>Status:</strong> Paid</p>
</div>

<p>Attached to this email you will find your official PDF receipt. You can also view it online by clicking the button below:</p>

<br>

<div style="text-align: center;">
    <a href="{{ url('/invoice/view/'.$invoice->invoice_no) }}" 
       style="background:#16a34a; color:white; padding:14px 24px; border-radius:8px; text-decoration:none; display: inline-block; font-weight: bold;">
       View Online Receipt
    </a>
</div>

<br>

<p style="color: #64748b; font-size: 14px;">
    If you have any questions regarding this payment, please contact {{ $invoice->company->name }} directly.
</p>

@endsection
