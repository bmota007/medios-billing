@extends('layouts.app')

@section('content')
<div style="max-width:700px;margin:60px auto;text-align:center;color:white;">
    
    <h2>🚫 Subscription Required</h2>

    <p>Your trial has ended or your subscription is inactive.</p>

    <p>Please activate your subscription to continue using MediosBilling.</p>

    <a href="/subscription" style="background:#38bdf8;color:#000;padding:12px 20px;border-radius:8px;text-decoration:none;">
        Activate Subscription
    </a>

</div>
@endsection
