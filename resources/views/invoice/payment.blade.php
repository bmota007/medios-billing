<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment Portal | Medios Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        }

        .pay-card-header {
            display: flex;
            align-items: center;
            cursor: pointer;
            width: 100%;
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
            max-height: 800px; opacity: 1;
            margin-top: 24px; padding-top: 24px;
            border-top: 1px solid var(--glass-border);
        }

        .btn-action {
            background: var(--accent);
            color: #020617; font-weight: 700;
            padding: 16px; border-radius: 16px;
            width: 100%; border: none;
            text-transform: uppercase;
            margin-top: 10px;
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
        @php
            $amountDueNow = ($invoice->deposit_amount > 0 && $invoice->status != 'partial' && $invoice->status != 'deposit_paid')
                ? $invoice->deposit_amount
                : ($invoice->remaining_balance ?? $invoice->total);
        @endphp

        <h1 class="amount-display">${{ number_format($amountDueNow, 2) }}</h1>
        <div class="opacity-50 small">Invoice #{{ $invoice->invoice_no }}</div>
    </div>

    <div class="methods">
{{-- CARD PAYMENT --}}
        @if($invoice->company->accept_card)
        <div class="pay-card">
            {{-- CHANGED stripe.checkout TO invoice.checkout --}}
            <a href="{{ route('invoice.checkout', $invoice->invoice_no) }}" class="text-decoration-none pay-card-header">
                <div class="icon-circle card-bg"><i class="fa-solid fa-credit-card"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-white">Credit or Debit Card</h6>
                    <small class="text-secondary">Visa, Mastercard, Apple Pay</small>
                </div>
                <i class="fa-solid fa-chevron-right opacity-30 text-white"></i>
            </a>
        </div>
        @endif

        {{-- ZELLE PAYMENT --}}
        @if($invoice->company->accept_zelle)
        <div class="pay-card">
            <div class="pay-card-header" onclick="togglePay('zelle-form')">
                <div class="icon-circle zelle-bg"><i class="fa-solid fa-bolt-lightning"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">Zelle Transfer</h6>
                    <small class="text-secondary">Instant bank transfer</small>
                </div>
                <i class="fa-solid fa-chevron-down opacity-30"></i>
            </div>

            <div id="zelle-form" class="pay-details">
                <div class="text-center mb-3">
                    Send to: <strong class="text-info">{{ $invoice->company->zelle_value }}</strong>
                </div>

                <form method="POST" action="{{ route('invoice.manual.payment', $invoice->invoice_no) }}">
                    @csrf
                    <input type="hidden" name="payment_method" value="zelle">
                    
                    <label class="small text-secondary mb-1">Confirm Amount</label>
                    <input type="number" step="0.01" name="amount_paid" value="{{ $amountDueNow }}" class="form-control custom-field" required>

                    <label class="small text-secondary mb-1">Confirmation Details</label>
                    <textarea name="payment_notes" class="form-control custom-field" placeholder="Enter confirmation code or Zelle name..." required></textarea>

                    <button type="submit" class="btn-action">I've Sent the Zelle Payment</button>
                </form>
            </div>
        </div>
        @endif

        {{-- VENMO PAYMENT --}}
        @if($invoice->company->accept_venmo)
        <div class="pay-card">
            <div class="pay-card-header" onclick="togglePay('venmo-form')">
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
                    
                    <label class="small text-secondary mb-1">Confirm Amount</label>
                    <input type="number" step="0.01" name="amount_paid" value="{{ $amountDueNow }}" class="form-control custom-field" required>

                    <textarea name="payment_notes" class="form-control custom-field" placeholder="Optional note or Venmo name..."></textarea>
                    
                    <button type="submit" class="btn-action">Paid via Venmo</button>
                </form>
            </div>
        </div>
        @endif

        {{-- OFFLINE PAYMENT --}}
        <div class="pay-card">
            <div class="pay-card-header" onclick="togglePay('cash-form')">
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
                    
                    <label class="small text-secondary mb-1">Check Number</label>
                    <input type="text" name="check_number" class="form-control custom-field" placeholder="Check Number (if applicable)">
                    
                    <label class="small text-secondary mb-1">Notes</label>
                    <textarea name="payment_notes" class="form-control custom-field" placeholder="Notes (e.g. 'Handing check to technician')"></textarea>
                    
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
    
    document.querySelectorAll('.pay-details').forEach(el => {
        if(el.id !== formId) el.classList.remove('show');
    });

    if (!isOpen) {
        form.classList.add('show');
    } else {
        form.classList.remove('show');
    }
}
</script>
</body>
</html>
