<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #374151; line-height: 1.6; }
        .container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 12px; }
        .logo-container { text-align: center; margin-bottom: 25px; }
        .button {
            background-color: #38bdf8;
            color: white !important;
            padding: 16px 30px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 10px;
            font-weight: bold;
            margin: 20px 0;
        }
        .summary-box { background: #f9fafb; border-radius: 8px; padding: 20px; border: 1px solid #f3f4f6; }
        .footer { font-size: 12px; color: #9ca3af; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>

@php
    $companyName = $invoice->company->name ?? 'Medios Billing User';
    // Fallback logo if company hasn't uploaded one
    $companyLogo = !empty($invoice->company->logo_path) 
        ? asset('storage/' . $invoice->company->logo_path) 
        : 'https://portal.mcintoshcleaningservice.com/images/mcintosh-logo.png';
@endphp

<div class="container">
    <div class="logo-container">
        <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-width: 180px; height: auto;">
    </div>

    <h2>Hello {{ $invoice->customer_name }},</h2>

    @if(isset($is_receipt) && $is_receipt)
        <p>Thank you for your payment! Your receipt for <strong>Invoice #{{ $invoice->invoice_no }}</strong> is confirmed and attached for your records.</p>
    @else
        <p>You have received a new invoice from <strong>{{ $companyName }}</strong>.</p>
        
        <div class="summary-box">
            <p style="margin: 0;"><strong>Invoice #:</strong> {{ $invoice->invoice_no }}</p>
            <p style="margin: 5px 0;"><strong>Amount Due:</strong> ${{ number_format($invoice->total, 2) }}</p>
            <p style="margin: 0;"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('invoice.public_view', $invoice->invoice_no) }}" class="button">
                VIEW & PAY INVOICE ONLINE
            </a>
        </div>

        <p style="font-size: 13px; color: #6b7280;">If the button above does not work, please use the following link:<br>
        {{ route('invoice.public_view', $invoice->invoice_no) }}</p>
    @endif

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
    
    <p>Thank you for your business,<br>
    <strong>{{ $companyName }}</strong></p>

    <div class="footer">
        Powered by MediosCorp Billing System
    </div>
</div>

</body>
</html>
