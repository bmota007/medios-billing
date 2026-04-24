<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    padding:0;
    background:#0f172a;
    font-family:Arial, Helvetica, sans-serif;
}

.wrapper{
    padding:35px 15px;
    background:#0f172a;
}

.container{
    max-width:620px;
    margin:0 auto;
    background:linear-gradient(145deg,#111827,#1e293b,#0f172a);
    border-radius:26px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.08);
}

.header{
    padding:38px 35px;
    text-align:center;
    background:linear-gradient(135deg,#0284c7,#2563eb,#0ea5e9);
}

.logo{
    font-size:28px;
    font-weight:900;
    color:#ffffff;
    letter-spacing:.4px;
}

.logo span{
    color:#dbeafe;
}

.hero{
    padding:35px;
}

h1{
    color:#ffffff;
    font-size:30px;
    line-height:1.2;
    margin:0 0 18px 0;
}

p{
    color:#cbd5e1;
    font-size:16px;
    line-height:1.7;
    margin:0 0 16px 0;
}

.badge{
    display:inline-block;
    padding:8px 14px;
    border-radius:999px;
    background:#10b98122;
    color:#10b981;
    font-size:12px;
    font-weight:800;
    letter-spacing:1px;
    margin-bottom:18px;
}

.card{
    margin:28px 0;
    padding:22px;
    border-radius:18px;
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.08);
}

.row{
    margin-bottom:10px;
    color:#ffffff;
    font-size:15px;
}

.row span{
    color:#94a3b8;
    display:inline-block;
    width:145px;
}

.button{
    display:block;
    margin-top:28px;
    text-align:center;
    background:#38bdf8;
    color:#082f49 !important;
    text-decoration:none;
    font-weight:900;
    padding:18px;
    border-radius:14px;
    font-size:15px;
}

.notice{
    margin-top:24px;
    padding:18px;
    border-radius:16px;
    background:#f59e0b12;
    border:1px solid #f59e0b33;
    color:#fde68a;
    font-size:14px;
    line-height:1.6;
}

.footer{
    text-align:center;
    padding:25px;
    color:#64748b;
    font-size:12px;
}
</style>
</head>

<body>

@php
    $plan = $company->plan_name ?? $company->plan ?? 'Starter';
    $price = (float)($company->monthly_price ?? 49);
    $trialEnds = !empty($company->trial_ends_at)
        ? \Carbon\Carbon::parse($company->trial_ends_at)->format('F d, Y')
        : now()->addDays(5)->format('F d, Y');

    $status = strtolower($company->subscription_status ?? '');
    $isTrial = in_array($status, ['trialing','pending_checkout','active']) && !empty($company->trial_ends_at);
@endphp

<div class="wrapper">

<div class="container">

    <div class="header">
        <div class="logo">Medios<span>Billing</span></div>
    </div>

    <div class="hero">

        <div class="badge">
            ACCOUNT ACTIVATED
        </div>

        <h1>Welcome, {{ $company->name }}</h1>

        <p>
            Thank you for choosing Medios Billing. Your account has been successfully activated and your business portal is now ready.
        </p>

        <div class="card">

            <div class="row">
                <span>Plan:</span> {{ $plan }}
            </div>

            <div class="row">
                <span>Monthly Price:</span> ${{ number_format($price,2) }}
            </div>

            <div class="row">
                <span>Status:</span> Active
            </div>

            <div class="row">
                <span>Login URL:</span> app.mediosbilling.com
            </div>

        </div>

        @if($isTrial)
        <div class="notice">
            Your complimentary trial period is now active and ends on <strong>{{ $trialEnds }}</strong>.<br><br>
            If you do not wish to continue service, please cancel before the trial expiration date to avoid your first monthly charge of <strong>${{ number_format($price,2) }}</strong>.
        </div>
        @else
        <div class="notice">
            Your selected subscription is active and your next billing cycle will renew automatically unless cancelled from your billing portal.
        </div>
        @endif

        <a href="https://app.mediosbilling.com/login" class="button">
            ACCESS YOUR DASHBOARD
        </a>

        <p style="margin-top:22px;">
            Need assistance? Simply reply to this email and our team will help you.
        </p>

    </div>

    <div class="footer">
        © 2026 Medios Billing · Professional Billing Software for Growing Businesses
    </div>

</div>
</div>

</body>
</html>
