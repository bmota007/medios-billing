<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6; }
        .container { padding: 20px; border: 1px solid #e2e8f0; border-radius: 10px; max-width: 600px; }
        .button { background: #0ea5e9; color: white !important; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; margin-top: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Invoice #{{ $invoice->invoice_no }}</h2>
        <p>Hello <strong>{{ $invoice->customer_name }}</strong>,</p>
        
        <p>A new invoice has been generated for you from <strong>{{ $invoice->company->name }}</strong>.</p>
        
        <p><strong>Total Amount Due:</strong> ${{ number_format($invoice->total, 2) }}</p>
        
        <p>You can find the detailed invoice attached as a PDF to this email. You can also view and pay the invoice online by clicking the button below:</p>

        <a href="{{ route('invoice.public_view', $invoice->invoice_no) }}" class="button">
            View & Pay Online
        </a>

        <div class="footer">
            <p>Thank you for your business!<br>
            {{ $invoice->company->name }}</p>
        </div>
    </div>
</body>
</html>
