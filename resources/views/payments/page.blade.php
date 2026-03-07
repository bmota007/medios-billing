<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Pay Invoice</title>
</head>
<body style="font-family:Arial; background:#f4f6f9; padding:40px;">

<div style="max-width:600px; margin:auto; background:white; padding:30px; border-radius:10px;">

<h2>Invoice #{{ $invoice->invoice_no }}</h2>

<p>
Amount Due:
<strong>${{ number_format($invoice->total,2) }}</strong>
</p>

<form method="POST" action="{{ route('invoice.checkout') }}">
    @csrf
    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

    <button type="submit"
        style="background:#2563eb;
               color:white;
               padding:14px 20px;
               border:none;
               border-radius:6px;
               cursor:pointer;
               font-weight:bold;">
        Pay with Credit Card
    </button>
</form>

</div>

</body>
</html>
