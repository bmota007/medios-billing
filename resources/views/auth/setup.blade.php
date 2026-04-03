<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Setup | Medios Billing</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        body {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            color: #f8fafc;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 15px; /* Added padding so it doesn't touch screen edges */
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 2rem; /* Balanced padding */
            width: 100%;
            max-width: 480px; /* Slightly narrower for better mobile fit */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            box-sizing: border-box; /* Crucial for responsiveness */
        }

        .logo-text { font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0.5rem; font-size: 1.5rem; }
        .form-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; color: #94a3b8; margin-bottom: 0.4rem; }
        .text-info-custom { color: #38bdf8; }

        .form-control {
            background: rgba(15, 23, 42, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            padding: 0.7rem 0.9rem;
            border-radius: 0.75rem;
            font-size: 16px; /* Prevents iOS auto-zoom on focus */
        }

        .stripe-placeholder {
            background: rgba(56, 189, 248, 0.05);
            border: 1px dashed rgba(56, 189, 248, 0.3);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 0.5rem;
        }

        .btn-activate {
            background-color: #38bdf8 !important;
            border: none !important;
            font-weight: 800;
            padding: 0.9rem;
            color: #0f172a !important;
            border-radius: 0.75rem;
            text-transform: uppercase;
            margin-top: 1rem;
            width: 100%;
        }

        /* Mobile specific tweaks */
        @media (max-width: 480px) {
            .glass-card { padding: 1.5rem; border-radius: 1.25rem; }
            .logo-text { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<div class="glass-card">
    <div class="text-center mb-4">
        <h2 class="logo-text">MEDIOS<span class="text-info-custom">BILLING</span></h2>
        <h4 class="fw-bold mb-1">Finalize Your <span class="text-info-custom">Setup</span></h4>
        <p class="text-secondary small">Account for: <span class="text-white fw-semibold">{{ $company->name }}</span></p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 bg-danger text-white small mb-4" style="--bs-bg-opacity: .2;">
            <ul class="mb-0 px-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('onboarding.complete') }}" method="POST" id="setup-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label">Set Your Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="mb-4">
            <label class="form-label d-flex align-items-center" style="color: #38bdf8;">
                <i class="fa-solid fa-shield-halved me-2"></i> Payment Verification
            </label>
            <div class="stripe-placeholder">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-white-50" style="font-size: 0.65rem;">Secure Capture via Stripe</span>
                    <i class="fa-brands fa-cc-visa fa-lg text-secondary"></i>
                </div>
                <input type="hidden" name="payment_method" value="pm_card_visa">
                <div class="text-info-custom small font-monospace" style="font-size: 0.6rem;">
                    [ STRIPE ELEMENTS ACTIVE ]
                </div>
            </div>
            <p class="mt-2 text-secondary" style="font-size: 0.65rem; line-height: 1.3;">
                <i class="fa-solid fa-circle-info me-1"></i> No charge today. Trial ends in 7 days.
            </p>
        </div>

        <button type="submit" class="btn btn-activate shadow">
            Start My Free Trial
        </button>
    </form>
</div>

</body>
</html>
