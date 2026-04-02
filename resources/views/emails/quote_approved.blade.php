<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial; background:#f4f6f8; padding:20px;">

<div style="max-width:600px;margin:auto;background:white;border-radius:10px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,0.05);">

    <!-- HEADER -->
    <div style="background:#0ea5e9;padding:20px;text-align:center;color:white;">
        <h2 style="margin:0;">Quote Approved 🎉</h2>
    </div>

    <!-- BODY -->
    <div style="padding:25px;color:#333;">

        <p><strong>Good news!</strong> A customer has approved a quote.</p>

        <hr>

        <p><strong>Company:</strong> {{ $quote->company->name }}</p>
        <p><strong>Quote #:</strong> {{ $quote->quote_number }}</p>
        <p><strong>Customer:</strong> {{ $quote->customer->name }}</p>
        <p><strong>Email:</strong> {{ $quote->customer->email }}</p>
        <p><strong>Total:</strong> ${{ number_format($quote->total, 2) }}</p>

        <hr>

        <p style="color:#555;">
            Next step: Contract will be sent for signature.
        </p>

        <!-- BUTTON -->
        <div style="text-align:center;margin-top:25px;">
            <a href="{{ url('/q/'.$quote->public_token) }}"
               style="background:#0ea5e9;color:white;padding:12px 20px;text-decoration:none;border-radius:6px;">
               View Quote
            </a>
        </div>

    </div>

</div>

</body>
</html>
