<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Portal | Medios Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-font-awesome-key.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
            --accent: #38bdf8;
        }

        body {
            background: #020617;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: white;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .mesh-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(at 50% 0%, rgba(30, 58, 138, 0.3) 0, transparent 50%);
            z-index: -1;
        }

        .checkout-wrapper {
            max-width: 500px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .hero-amount {
            text-align: center;
            margin-bottom: 50px;
        }

        .amount-display {
            font-size: 4rem;
            font-weight: 800;
            letter-spacing: -3px;
            margin: 10px 0;
            color: #fff;
        }

        .pay-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .pay-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(56, 189, 248, 0.15);
        }

        .icon-circle {
            width: 56px; height: 56px;
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-right: 20px;
            font-size: 1.5rem;
        }

        .card-bg { color: #38bdf8; background: rgba(56, 189, 248, 0.15); }
        .zelle-bg { color: #6d1ed1; background: rgba(109, 30, 209, 0.15); }
        .venmo-bg { color: #008CFF; background: rgba(0, 140, 255, 0.15); }
        .cash-bg { color: #10b981; background: rgba(16, 185, 129, 0.15); }

        .pay-details {
            max-height: 0; overflow: hidden;
            transition: all 0.4s ease; opacity: 0;
        }

        .pay-details.show {
            max-height: 500px; opacity: 1;
            margin-top: 24px; padding-top: 24px;
            border-top: 1px solid var(--glass-border);
        }

        .btn-action {
            background: var(--accent);
            color: #020617; font-weight: 700;
            padding: 16px; border-radius: 16px;
            width: 100%; border: none;
            text-transform: uppercase;
        }

        .custom-field {
            background: rgba(0,0,0,0.3) !important;
            border: 1px solid var(--glass-border) !important;
            color: white !important;
            padding: 15px !important;
            border-radius: 12px !important;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="mesh-bg"></div>

<div class="checkout-wrapper">
    <div class="hero-amount">
        <div class="text-secondary small text-uppercase fw-bolder">Balance Due</div>
        <h1 class="amount-display">${{ number_format($invoice->total, 2) }}</h1>
        <div class="opacity-50 small">Invoice #{{ $invoice->invoice_no }}</div>
    </div>

    <div class="methods">
        @if($invoice->company->accept_card)
        <form method="POST" action="{{ route('invoice.checkout', $invoice->invoice_no) }}">
            @csrf
            <div class="pay-card d-flex align-items-center" onclick="this.closest('form').submit()">
                <div class="icon-circle card-bg"><i class="fa-solid fa-credit-card"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">Credit or Debit Card</h6>
                    <small class="text-secondary">Visa, Mastercard, Apple Pay</small>
                </div>
                <i class="fa-solid fa-chevron-right opacity-30"></i>
            </div>
        </form>
        @endif

        @if($invoice->company->accept_zelle)
        <div class="pay-card" onclick="togglePay('zelle-form')">
            <div class="d-flex align-items-center">
                <div class="icon-circle zelle-bg"><i class="fa-solid fa-bolt-lightning"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">Zelle Transfer</h6>
                    <small class="text-secondary">Instant bank transfer</small>
                </div>
                <i class="fa-solid fa-chevron-down opacity-30"></i>
            </div>
            <div id="zelle-form" class="pay-details">
                <div class="text-center mb-3">Send to: <strong class="text-info">{{ $invoice->company->zelle_value }}</strong></div>
                <form method="POST" action="{{ route('invoice.manual.payment', $invoice->invoice_no) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="zelle">
                    <textarea name="payment_notes" class="form-control custom-field" placeholder="Confirmation code..." onclick="event.stopPropagation()"></textarea>
                    <button class="btn-action">I've Sent the Payment</button>
                </form>
            </div>
        </div>
        @endif

        @if($invoice->company->accept_venmo)
        <div class="pay-card" onclick="togglePay('venmo-form')">
            <div class="d-flex align-items-center">
                <div class="icon-circle venmo-bg"><i class="fa-brands fa-venmo"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">Venmo</h6>
                    <small class="text-secondary">Pay via Venmo app</small>
                </div>
                <i class="fa-solid fa-chevron-down opacity-30"></i>
            </div>
            <div id="venmo-form" class="pay-details">
                <div class="text-center mb-4">
                    <div class="small text-secondary mb-1">Venmo Username:</div>
                    <div class="fs-4 fw-bold text-primary">{{ $invoice->company->venmo_value ?? '@company' }}</div>
                </div>
                <form method="POST" action="{{ route('invoice.manual.payment', $invoice->invoice_no) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="venmo">
                    <textarea name="payment_notes" class="form-control custom-field" placeholder="Optional note" onclick="event.stopPropagation()"></textarea>
                    <button class="btn-action">Paid via Venmo</button>
                </form>
            </div>
        </div>
        @endif

        {{-- OFFLINE PAYMENT BLOCK - RESTRUCTURED TO PREVENT CLOSING --}}
        <div class="pay-card">
            <div class="d-flex align-items-center" onclick="togglePay('cash-form')">
                <div class="icon-circle cash-bg"><i class="fa-solid fa-money-check-dollar"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">Offline Payment</h6>
                    <small class="text-secondary">Paying with Check or Cash</small>
                </div>
                <i class="fa-solid fa-chevron-down opacity-30"></i>
            </div>
            <div id="cash-form" class="pay-details">
                <form method="POST" action="{{ route('invoice.manual.payment', $invoice->invoice_no) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="check">
                    <input type="text" name="check_number" class="form-control custom-field" placeholder="Check Number (if applicable)" onclick="event.stopPropagation()">
                    <textarea name="payment_notes" class="form-control custom-field" placeholder="Notes (e.g. 'Handing check to technician')" onclick="event.stopPropagation()"></textarea>
                    <button type="submit" class="btn-action">Confirm Offline Payment</button>
                </form>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('invoice.public_view', $invoice->invoice_no) }}" class="text-secondary text-decoration-none small">Return to Invoice</a>
    </div>
</div>

<script>
function togglePay(formId) {
    const form = document.getElementById(formId);
    const isOpen = form.classList.contains('show');
    
    // Close all other open drawers first
    document.querySelectorAll('.pay-details').forEach(el => {
        if(el.id !== formId) el.classList.remove('show');
    });

    // Toggle the clicked one
    if (!isOpen) {
        form.classList.add('show');
    } else {
        form.classList.remove('show');
    }
}

// 🔥 CRITICAL: Prevent the drawer from closing when clicking inside ANY fields
document.querySelectorAll('.pay-details input, .pay-details textarea').forEach(element => {
    element.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
</body>
</html>
