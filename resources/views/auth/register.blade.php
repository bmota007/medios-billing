<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account | Medios Billing</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:Arial,Helvetica,sans-serif;
    background:linear-gradient(135deg,#07122a,#0b1d44,#132f6d);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:30px;
    color:#fff;
}
.wrap{width:100%;max-width:520px}
.card{
    background:rgba(255,255,255,.06);
    border:1px solid rgba(255,255,255,.08);
    border-radius:22px;
    padding:38px;
    backdrop-filter:blur(12px);
    box-shadow:0 30px 60px rgba(0,0,0,.35);
}
.logo{font-size:34px;font-weight:800;margin-bottom:8px}
.logo span{color:#37b8ff}
.sub{color:#d5e6ff;margin-bottom:24px;font-size:15px}
.planbox{
    background:rgba(55,184,255,.12);
    border:1px solid rgba(55,184,255,.35);
    padding:14px 16px;
    border-radius:14px;
    margin-bottom:22px;
}
.planbox strong{color:#7dd7ff;font-size:15px}
label{
    display:block;
    margin-bottom:7px;
    font-size:14px;
    color:#dce9ff;
}
.input{
    width:100%;
    padding:14px 15px;
    border:none;
    outline:none;
    border-radius:12px;
    background:#fff;
    color:#111;
    font-size:15px;
    margin-bottom:16px;
}
.btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    background:linear-gradient(90deg,#1aa7ff,#45d0ff);
    color:#001529;
    font-weight:800;
    font-size:16px;
    cursor:pointer;
}
.bottom{
    margin-top:18px;
    text-align:center;
    font-size:14px;
    color:#d5e6ff;
}
.bottom a{
    color:#7dd7ff;
    text-decoration:none;
    font-weight:700;
}
.errors{
    background:#7a1020;
    border:1px solid #ff5e79;
    color:#fff;
    padding:14px;
    border-radius:12px;
    margin-bottom:18px;
    font-size:14px;
}
.errors ul{margin-left:18px;margin-top:6px}
.small{
    margin-top:18px;
    text-align:center;
    font-size:12px;
    color:#9ab8e8;
}
@media(max-width:600px){
.card{padding:24px}
}
</style>
</head>
<body>

@php
$plan = request('plan','starter');

$plans = [
    'starter' => ['name'=>'Starter','price'=>'$49/mo'],
    'growth'  => ['name'=>'Growth','price'=>'$79/mo'],
    'pro'     => ['name'=>'Pro','price'=>'$129/mo'],
    'premium' => ['name'=>'Premium','price'=>'$249/mo'],
];

$current = $plans[$plan] ?? $plans['starter'];
@endphp

<div class="wrap">
<div class="card">

<div class="logo">Medios<span>Billing</span></div>
<div class="sub">Launch your business billing portal in minutes.</div>

<div class="planbox">
<strong>Selected Plan:</strong><br>
{{ $current['name'] }} — {{ $current['price'] }}
</div>

@if ($errors->any())
<div class="errors">
<strong>Please fix the following:</strong>
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}">
@csrf
<input type="hidden" name="plan" value="{{ request("plan") }}">

<input type="hidden" name="plan" value="{{ $plan }}">

<label>Company Name</label>
<input type="text" name="company_name" class="input" value="{{ old('company_name') }}" required>

<label>Your Full Name</label>
<input type="text" name="name" class="input" value="{{ old('name') }}" required>

<label>Email Address</label>
<input type="email" name="email" class="input" value="{{ old('email') }}" required>

<label>Password</label>
<input type="password" name="password" class="input" required>

<label>Confirm Password</label>
<input type="password" name="password_confirmation" class="input" required>

<button type="submit" class="btn">Create Account & Continue</button>

</form>

<div class="bottom">
Already have an account?
<a href="{{ route('login') }}">Login</a>
</div>

<div class="small">
Secure signup powered by Medios Billing
</div>

</div>
</div>

</body>
</html>
