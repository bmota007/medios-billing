<!DOCTYPE html>
<html>
<head>
    <title>Pay Invoice</title>
</head>
<body style="font-family:Arial; padding:40px;">

<h2>Invoice {{ $invoice->invoice_no }}</h2>

<p><strong>Total Due:</strong> ${{ number_format($invoice->total, 2) }}</p>

@if($invoice->status === 'paid')
    <p style="color:green; font-weight:bold;">
        This invoice has already been paid.
    </p>
@else
    <form method="POST" action="{{ route('invoice.checkout') }}">
        @csrf
        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

        <button type="submit" style="
            padding:12px 25px;
            background:#1e3a8a;
            color:white;
            border:none;
            border-radius:6px;
            font-size:16px;">
            Pay with Credit Card
        </button>
    </form>
@endif

</body>
</html>
