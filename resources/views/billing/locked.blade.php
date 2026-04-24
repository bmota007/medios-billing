<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activate Subscription | Medios Billing</title>

    <style>
        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            padding:0;
            font-family:Inter,Arial,Helvetica,sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56,189,248,.10), transparent 28%),
                radial-gradient(circle at bottom right, rgba(59,130,246,.10), transparent 28%),
                linear-gradient(135deg,#020617,#0f172a,#1e293b);
            color:#fff;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:30px 16px;
        }

        .wrap{
            width:100%;
            max-width:760px;
        }

        .card{
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);
            border-radius:26px;
            padding:42px 34px;
            box-shadow:
                0 25px 70px rgba(0,0,0,.45),
                inset 0 1px 0 rgba(255,255,255,.04);
            backdrop-filter:blur(10px);
        }

        .badge{
            display:inline-block;
            padding:8px 14px;
            border-radius:999px;
            background:rgba(16,185,129,.12);
            color:#34d399;
            font-size:13px;
            font-weight:700;
            letter-spacing:.2px;
            margin-bottom:18px;
        }

        h1{
            margin:0 0 12px;
            font-size:38px;
            line-height:1.1;
            font-weight:800;
        }

        .sub{
            color:#cbd5e1;
            font-size:17px;
            line-height:1.7;
            margin-bottom:28px;
        }

        .grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
            margin-bottom:28px;
        }

        .mini{
            background:rgba(255,255,255,.03);
            border:1px solid rgba(255,255,255,.07);
            border-radius:18px;
            padding:18px;
        }

        .mini-title{
            color:#94a3b8;
            font-size:13px;
            margin-bottom:8px;
        }

        .mini-value{
            font-size:24px;
            font-weight:800;
        }

        .features{
            background:rgba(255,255,255,.03);
            border:1px solid rgba(255,255,255,.07);
            border-radius:18px;
            padding:22px;
            margin-bottom:28px;
        }

        .features h3{
            margin:0 0 14px;
            font-size:18px;
        }

        .features ul{
            margin:0;
            padding:0;
            list-style:none;
        }

        .features li{
            padding:9px 0;
            color:#dbeafe;
            border-bottom:1px solid rgba(255,255,255,.05);
        }

        .features li:last-child{
            border-bottom:none;
        }

        .actions{
            display:flex;
            gap:14px;
            flex-wrap:wrap;
        }

        .btn{
            display:inline-block;
            padding:16px 24px;
            border-radius:14px;
            text-decoration:none;
            font-weight:800;
            transition:.2s ease;
        }

        .btn-primary{
            background:#38bdf8;
            color:#082f49;
        }

        .btn-primary:hover{
            background:#0ea5e9;
            transform:translateY(-2px);
        }

        .btn-secondary{
            background:rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.08);
            color:#fff;
        }

        .btn-secondary:hover{
            background:rgba(255,255,255,.09);
        }

        .footer{
            margin-top:22px;
            color:#94a3b8;
            font-size:13px;
            line-height:1.6;
        }

        .footer a{
            color:#38bdf8;
            text-decoration:none;
        }

        @media(max-width:720px){

            h1{
                font-size:30px;
            }

            .card{
                padding:30px 22px;
            }

            .grid{
                grid-template-columns:1fr;
            }

            .actions{
                flex-direction:column;
            }

            .btn{
                text-align:center;
                width:100%;
            }
        }
    </style>
</head>
<body>

@php
    $company = auth()->user()->company ?? null;

    $plan = $company->plan_name ?? $company->plan ?? 'Starter';
    $price = number_format((float)($company->monthly_price ?? 49), 2);
@endphp

<div class="wrap">
    <div class="card">

        <div class="badge">SECURE ACTIVATION REQUIRED</div>

        <h1>Your Account Is Almost Ready 🚀</h1>

        <div class="sub">
            Activate your Medios Billing subscription to unlock your dashboard,
            invoices, quotes, customers, and premium business tools.
            Your free trial begins after secure checkout.
        </div>

        <div class="grid">

            <div class="mini">
                <div class="mini-title">Selected Plan</div>
                <div class="mini-value">{{ $plan }}</div>
            </div>

            <div class="mini">
                <div class="mini-title">After Trial</div>
                <div class="mini-value">${{ $price }}/mo</div>
            </div>

        </div>

        <div class="features">
            <h3>Included With Activation</h3>

            <ul>
                <li>✓ Create professional invoices</li>
                <li>✓ Send quotes and approvals</li>
                <li>✓ Manage customers</li>
                <li>✓ Cancel anytime before billing date</li>
                <li>✓ Secure billing powered by Stripe</li>
            </ul>
        </div>

        <div class="actions">

            <a href="{{ route('checkout.subscribe', $company->id ?? 0) }}" class="btn btn-primary">
                Start Free Trial
            </a>

            <a href="{{ route('pricing') }}" class="btn btn-secondary">
                View Plans
            </a>

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="btn btn-secondary">
                Logout
            </a>

        </div>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
            @csrf
        </form>

        <div class="footer">
            No charge today during trial period. Cancel anytime before billing begins.<br>
            Need help? Contact <a href="mailto:support@mediosbilling.com">support@mediosbilling.com</a><br><br>
            © {{ date('Y') }} Medios Billing
        </div>

    </div>
</div>

</body>
</html>
