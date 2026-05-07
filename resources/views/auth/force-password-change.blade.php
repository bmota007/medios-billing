@extends('layouts.app')

@section('content')
<style>
body{
    margin:0;
    min-height:100vh;
    background:
        radial-gradient(circle at top right, rgba(59,130,246,.18), transparent 28%),
        radial-gradient(circle at bottom left, rgba(124,58,237,.14), transparent 30%),
        linear-gradient(135deg,#06101f,#08162c,#0a1b35);
}

.mbx-wrap{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px 20px;
}

.mbx-card{
    width:100%;
    max-width:560px;
    background:rgba(9,20,45,.92);
    border:1px solid rgba(255,255,255,.08);
    border-radius:28px;
    padding:42px;
    box-shadow:0 30px 80px rgba(0,0,0,.45);
}

.mbx-title{
    font-size:56px;
    line-height:1.02;
    font-weight:900;
    color:#ffffff;
    margin:0 0 18px;
}

.mbx-sub{
    font-size:18px;
    color:rgba(255,255,255,.62);
    margin:0 0 34px;
}

.mbx-input{
    width:100%;
    height:78px;
    border-radius:22px;
    border:2px solid #2563eb;
    background:#f3f4f6;
    color:#111827 !important;
    font-size:28px;
    font-weight:700;
    padding:0 24px;
    margin-bottom:22px;
    outline:none;
    box-sizing:border-box;
}

.mbx-input::placeholder{
    color:#6b7280 !important;
    font-weight:600;
}

.mbx-input:focus{
    border-color:#38bdf8;
    box-shadow:0 0 0 4px rgba(56,189,248,.15);
}

.mbx-btn{
    width:100%;
    height:82px;
    border:0;
    border-radius:24px;
    cursor:pointer;
    font-size:32px;
    font-weight:900;
    color:#fff;
    background:linear-gradient(90deg,#2563eb,#06b6d4);
    box-shadow:0 18px 45px rgba(37,99,235,.28);
}

.mbx-btn:hover{
    transform:translateY(-1px);
}

.mbx-error{
    background:rgba(239,68,68,.12);
    border:1px solid rgba(239,68,68,.30);
    color:#fecaca;
    padding:14px 16px;
    border-radius:16px;
    margin-bottom:20px;
    font-weight:700;
}

@media(max-width:700px){
    .mbx-card{padding:26px;}
    .mbx-title{font-size:42px;}
    .mbx-input{height:66px;font-size:22px;}
    .mbx-btn{height:70px;font-size:24px;}
}
</style>

<div class="mbx-wrap">
    <div class="mbx-card">
        <h1 class="mbx-title">Secure Your Account</h1>
        <p class="mbx-sub">Please create a new password before continuing.</p>

        @if ($errors->any())
            <div class="mbx-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.force.update') }}">
            @csrf

            <input
                type="password"
                name="password"
                class="mbx-input"
                placeholder="New Password"
                required
                autofocus
            >

            <input
                type="password"
                name="password_confirmation"
                class="mbx-input"
                placeholder="Confirm Password"
                required
            >

            <button type="submit" class="mbx-btn">
                Secure My Account
            </button>
        </form>
    </div>
</div>
@endsection
