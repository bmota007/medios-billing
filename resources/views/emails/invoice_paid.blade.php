<h2>Payment Received</h2>

<p>Hello {{ $invoice->customer_name }},</p>

<p>We have received your payment for Invoice #{{ $invoice->invoice_no }}.</p>

<p><strong>Amount Paid:</strong> ${{ number_format($invoice->total, 2) }}</p>
<p><strong>Payment Method:</strong> {{ ucfirst($invoice->payment_method) }}</p>

@if($invoice->check_number)
<p><strong>Check Number:</strong> {{ $invoice->check_number }}</p>
@endif

<p>Thank you for your business.</p>

<p>McIntosh Cleaning Services</p>
