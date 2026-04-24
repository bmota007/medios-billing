@extends('layouts.app')

@section('content')

<div class="pricing-wrap">

    <div class="hero">

        <span class="mini-badge">MEDIOS BILLING</span>

        <h1>Simple Pricing For Growing Businesses</h1>

        <p>
            Professional invoicing, estimates, contracts, subscriptions,
            customer portals and payment collection — all in one platform.
        </p>

    </div>

    <div class="billing-toggle">
        <button class="toggle-btn active" onclick="setMonthly()">Monthly</button>
        <button class="toggle-btn" onclick="setYearly()">Yearly</button>
    </div>

    <div class="plans-grid">

        @foreach($plans as $plan)

        <div class="price-card {{ strtolower($plan->name) == 'pro' ? 'featured' : '' }}">

            @if($plan->badge)
                <div class="plan-badge">{{ $plan->badge }}</div>
            @endif

            <h2>{{ $plan->name }}</h2>

            <div class="price">
                <span class="currency">$</span>
                <span class="monthly-price">{{ number_format($plan->price,0) }}</span>
                <span class="yearly-price" style="display:none;">{{ number_format($plan->yearly_price / 12,0) }}</span>
                <small>/mo</small>
            </div>

            <ul class="features">
                @foreach(explode(',', $plan->features) as $feature)
                    <li>✔ {{ trim($feature) }}</li>
                @endforeach
            </ul>

            <a href="/register?plan={{ $plan->slug }}" class="plan-btn">
                Get Started
            </a>

        </div>

        @endforeach

    </div>

</div>

<style>

body{
    background:linear-gradient(135deg,#07111f,#08192d,#06101d);
}

.pricing-wrap{
    max-width:1400px;
    margin:auto;
    padding:60px 25px;
}

.hero{
    text-align:center;
    margin-bottom:35px;
}

.mini-badge{
    display:inline-block;
    background:rgba(37,99,235,.18);
    color:#93c5fd;
    padding:8px 18px;
    border-radius:999px;
    font-size:13px;
    font-weight:700;
    margin-bottom:18px;
}

.hero h1{
    font-size:58px;
    color:#fff;
    font-weight:900;
    margin-bottom:18px;
}

.hero p{
    max-width:800px;
    margin:auto;
    color:#94a3b8;
    font-size:20px;
    line-height:1.6;
}

.billing-toggle{
    display:flex;
    justify-content:center;
    gap:12px;
    margin:35px 0 45px;
}

.toggle-btn{
    background:#0f172a;
    border:1px solid rgba(255,255,255,.08);
    color:#fff;
    padding:14px 24px;
    border-radius:14px;
    cursor:pointer;
    font-weight:700;
}

.toggle-btn.active{
    background:#2563eb;
}

.plans-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:24px;
}

.price-card{
    background:linear-gradient(180deg,#081222,#09182b);
    border:1px solid rgba(255,255,255,.06);
    border-radius:24px;
    padding:34px 28px;
    color:#fff;
    box-shadow:0 25px 50px rgba(0,0,0,.25);
    position:relative;
}

.price-card.featured{
    transform:scale(1.04);
    border:1px solid #2563eb;
}

.plan-badge{
    display:inline-block;
    background:#2563eb;
    color:#fff;
    padding:7px 14px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    margin-bottom:18px;
}

.price-card h2{
    font-size:34px;
    font-weight:800;
    margin-bottom:20px;
}

.price{
    margin-bottom:25px;
    font-size:52px;
    font-weight:900;
}

.currency{
    font-size:24px;
    vertical-align:top;
}

.price small{
    font-size:18px;
    color:#94a3b8;
    font-weight:500;
}

.features{
    list-style:none;
    padding:0;
    margin:0 0 28px;
}

.features li{
    padding:9px 0;
    color:#dbeafe;
    border-bottom:1px solid rgba(255,255,255,.04);
}

.plan-btn{
    display:block;
    text-align:center;
    background:#2563eb;
    color:#fff;
    text-decoration:none;
    padding:16px;
    border-radius:14px;
    font-weight:800;
}

.plan-btn:hover{
    background:#1d4ed8;
}

@media(max-width:1200px){
    .plans-grid{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:768px){

.hero h1{
    font-size:38px;
}

.hero p{
    font-size:17px;
}

.plans-grid{
    grid-template-columns:1fr;
}

.price-card.featured{
    transform:none;
}

}

</style>

<script>

function setMonthly(){
    document.querySelectorAll('.monthly-price').forEach(el => el.style.display='inline');
    document.querySelectorAll('.yearly-price').forEach(el => el.style.display='none');

    document.querySelectorAll('.toggle-btn')[0].classList.add('active');
    document.querySelectorAll('.toggle-btn')[1].classList.remove('active');
}

function setYearly(){
    document.querySelectorAll('.monthly-price').forEach(el => el.style.display='none');
    document.querySelectorAll('.yearly-price').forEach(el => el.style.display='inline');

    document.querySelectorAll('.toggle-btn')[1].classList.add('active');
    document.querySelectorAll('.toggle-btn')[0].classList.remove('active');
}

</script>

@endsection
