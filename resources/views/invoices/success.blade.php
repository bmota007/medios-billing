<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Complete</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: radial-gradient(circle at top, #0f172a, #020617);
    color: white;
}

.container {
    max-width: 700px;
    margin: 80px auto;
    text-align: center;
}

.card {
    background: #0f172a;
    border-radius: 18px;
    padding: 50px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
}

.check {
    font-size: 70px;
    margin-bottom: 20px;
}

h1 {
    font-size: 38px;
    margin-bottom: 10px;
}

.company {
    font-size: 16px;
    opacity: 0.6;
    margin-bottom: 30px;
}

.amount {
    font-size: 28px;
    color: #22c55e;
    margin: 20px 0;
}

.details {
    background: #020617;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
    text-align: left;
}

.details p {
    margin: 8px 0;
    font-size: 14px;
    opacity: 0.8;
}

.btn {
    display: inline-block;
    margin-top: 30px;
    padding: 14px 25px;
    border-radius: 10px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.footer {
    margin-top: 25px;
    font-size: 12px;
    opacity: 0.5;
}
</style>
</head>

<body>

<div class="container">

    <div class="card">

        <div class="check">✅</div>

        <h1>Payment Confirmed</h1>

        <div class="company">
            {{ $invoice->company->name }}
        </div>

        <div class="amount">
${{ number_format($invoice->amount_paid, 2) }}
        </div>

        <div class="details">
            <p><strong>Invoice:</strong> {{ $invoice->invoice_no }}</p>
            <p><strong>Status:</strong> Paid</p>
            <p><strong>Date:</strong> {{ now()->format('M d, Y h:i A') }}</p>
            <p><strong>Method:</strong> Card (Stripe)</p>
        </div>

        <button onclick="window.close()" class="btn">Close this window</button>

        <div class="footer">
            Secure payment powered by Stripe
        </div>

    </div>

</div>

</body>
</html>
