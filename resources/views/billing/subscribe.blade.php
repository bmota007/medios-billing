@extends('layouts.app')

@section('content')

@php
$user = auth()->user();
$company = $user->company;

if (!$company) {
    $company = (object) [
        'name' => 'Medios Billing',
        'custom_price' => 70.00,
        'monthly_price' => 70.00,
        'stripe_mode' => 'live',
        'stripe_publishable_key' => env('STRIPE_KEY'),
        'stripe_test_publishable_key' => env('STRIPE_KEY'),
    ];
}

$price = $company->custom_price ?? $company->monthly_price ?? 70.00;

$stripeKey = $company->stripe_mode === 'live'
    ? $company->stripe_publishable_key
    : $company->stripe_test_publishable_key;
@endphp

<div class="billing-wrap">

    <div class="billing-card">

        <div class="brand-badge">
            Medios Billing
        </div>

        <h1 class="title">
            Activate Your Subscription
        </h1>

        <p class="subtitle">
            Premium business billing platform for modern companies
        </p>

        <div class="company-box">
            {{ $company->name }}
        </div>

        <div class="price-box">
            <span class="currency">$</span>{{ number_format($price,2) }}
            <small>/month</small>
        </div>

        <div class="billing-note">
            Charged today. Future renewals process on the <strong>3rd of each month</strong>.
        </div>

        <div class="feature-grid">

            <div class="feature-item">✔ Unlimited Invoices</div>
            <div class="feature-item">✔ Customer Portal</div>
            <div class="feature-item">✔ Payment Links</div>
            <div class="feature-item">✔ PDF Billing</div>
            <div class="feature-item">✔ Admin Dashboard</div>
            <div class="feature-item">✔ Priority Support</div>

        </div>

<a href="{{ route('checkout.subscribe', $company->id ?? 3) }}" class="checkout-btn">
    Activate Subscription
</a>

        <div class="secure-note">
            🔒 Secure recurring billing powered by Stripe
        </div>

    </div>

</div>

<style>

body{
    background: radial-gradient(circle at top left,#14213d,#08111f 55%,#050b18);
}

.billing-wrap{
    min-height: calc(100vh - 80px);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 20px;
}

.billing-card{
    width:100%;
    max-width:760px;
    background:linear-gradient(180deg,#0b1220,#0b1428);
    border:1px solid rgba(255,255,255,.06);
    border-radius:26px;
    padding:55px;
    color:#fff;
    text-align:center;
    box-shadow:0 30px 60px rgba(0,0,0,.35);
}

.brand-badge{
    display:inline-block;
    padding:8px 18px;
    border-radius:999px;
    background:rgba(59,130,246,.15);
    color:#93c5fd;
    font-size:13px;
    font-weight:700;
    letter-spacing:.5px;
    margin-bottom:20px;
}

.title{
    font-size:46px;
    font-weight:800;
    margin-bottom:10px;
    line-height:1.1;
}

.subtitle{
    color:#94a3b8;
    font-size:18px;
    margin-bottom:30px;
}

.company-box{
    display:inline-block;
    padding:12px 20px;
    border-radius:14px;
    background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.05);
    color:#dbeafe;
    font-weight:600;
    margin-bottom:25px;
}

.price-box{
    font-size:64px;
    font-weight:800;
    line-height:1;
    margin-bottom:14px;
}

.price-box .currency{
    font-size:34px;
    vertical-align:top;
}

.price-box small{
    font-size:20px;
    color:#94a3b8;
    font-weight:500;
}

.billing-note{
    color:#22c55e;
    font-size:16px;
    margin-bottom:28px;
}

.feature-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
    margin-bottom:35px;
}

.feature-item{
    background:rgba(255,255,255,.02);
    border:1px solid rgba(255,255,255,.04);
    padding:14px;
    border-radius:14px;
    text-align:left;
    color:#e2e8f0;
}

.secure-note{
    color:#94a3b8;
    font-size:14px;
}

.checkout-btn{
    display:inline-block;
    width:100%;
    max-width:360px;
    padding:18px 25px;
    background:#2563eb;
    color:#fff;
    font-size:22px;
    font-weight:700;
    border-radius:14px;
    text-decoration:none;
    transition:.2s;
    margin-top:10px;
    margin-bottom:25px;
}

.checkout-btn:hover{
    background:#1d4ed8;
    transform:translateY(-2px);
}


@media(max-width:768px){

.billing-card{
    padding:35px 22px;
}

.title{
    font-size:34px;
}

.price-box{
    font-size:48px;
}

.feature-grid{
    grid-template-columns:1fr;
}

}

</style>

@endsection
