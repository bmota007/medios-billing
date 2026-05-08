<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pay Invoice</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: white;
    margin: 0;
}

.container {
    max-width: 600px;
    margin: 40px auto;
    background: #111827;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.5);
}

h1 {
    text-align: center;
    margin-bottom: 10px;
}

.total {
    text-align: center;
    font-size: 28px;
    margin-bottom: 30px;
    color: #22c55e;
}

.card {
    background: #1f2937;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: 0.2s;
}

.card:hover {
    transform: scale(1.02);
    background: #374151;
}

.card-title {
    font-size: 18px;
    font-weight: bold;
}

.card-desc {
    font-size: 13px;
    opacity: 0.7;
}

.hidden {
    display: none;
}

.btn {
    display: block;
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    text-align: center;
    margin-top: 10px;
    text-decoration: none;
    font-weight: bold;
}

.btn-stripe { background:#3b82f6; color:white; }
.btn-venmo { background:#008CFF; color:white; }
.btn-zelle { background:#6d1ed1; color:white; }
.btn-whatsapp { background:#25D366; color:white; }

.copy-box {
    background: #000;
    padding: 10px;
    border-radius: 6px;
    margin-top: 10px;
    text-align: center;
}
</style>
</head>

<body>

<div class="container">

    <h1>{{ $invoice->company->name }}</h1>

    <div class="total">
        ${{ number_format($invoice->total, 2) }}
    </div>

<!-- STRIPE -->

<a href="{{ url('/invoice/checkout/'.$invoice->invoice_no) }}"
   class="btn btn-stripe"
   style="
      display:block;
      text-decoration:none;
      margin-bottom:20px;
      padding:25px;
      font-size:22px;
   ">

    💳 PAY WITH CARD

</a>

    <!-- VENMO -->
    @if($invoice->company->accept_venmo && $invoice->company->venmo_value)
    <div class="card" onclick="toggle('venmo')">
        <div class="card-title">📱 Venmo</div>
        <div class="card-desc">Tap to open Venmo</div>
    </div>

    <div id="venmo" class="hidden">
        <a href="https://venmo.com/{{ str_replace('@','',$invoice->company->venmo_value) }}?txn=pay&amount={{ $invoice->total }}"
           target="_blank"
           class="btn btn-venmo">
           Open Venmo
        </a>
    </div>
    @endif

    <!-- ZELLE -->
    @if($invoice->company->accept_zelle && $invoice->company->zelle_value)
    <div class="card" onclick="toggle('zelle')">
        <div class="card-title">⚡ Zelle</div>
        <div class="card-desc">Send manually</div>
    </div>

    <div id="zelle" class="hidden">
        <div class="copy-box">
            {{ $invoice->company->zelle_value }}
        </div>
    </div>
    @endif

    <!-- CHECK PAYMENT -->
    <div class="card" onclick="toggle('check')">
        <div class="card-title">🧾 Pay with Check</div>
        <div class="card-desc">Submit your check details</div>
    </div>

    <div id="check" class="hidden">
        <form action="{{ route('invoice.manual.payment', $invoice->invoice_no) }}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" value="check">

            <input type="text" name="check_number" placeholder="Check Number" class="btn" required>
            <textarea name="payment_notes" placeholder="Notes (optional)" class="btn"></textarea>

            <button type="submit" class="btn btn-zelle">
                Submit Check Payment
            </button>
        </form>
    </div>

    <!-- WHATSAPP -->
    <a href="https://wa.me/1XXXXXXXXXX?text=I%20just%20paid%20Invoice%20{{ $invoice->invoice_no }}"
       target="_blank"
       class="btn btn-whatsapp">
       Confirm Payment via WhatsApp
    </a>

</div>

<script>
function toggle(id) {
    document.querySelectorAll('.hidden').forEach(el => el.style.display = 'none');
    document.getElementById(id).style.display = 'block';
}
</script>

</body>
</html>
