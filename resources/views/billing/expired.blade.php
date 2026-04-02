@extends('layouts.admin')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="glass-card text-center p-5" style="max-width: 600px;">
        <i class="fa-solid fa-hourglass-end text-warning mb-4" style="font-size: 4rem;"></i>
        <h2 class="text-white fw-bold">Trial Period <span class="text-sky-400">Expired</span></h2>
        <p class="text-secondary mt-3">Please activate your subscription to continue.</p>
        
        <div class="bg-slate-900 rounded p-4 my-4 border border-slate-800 text-white">
            <h4>$30.00 / mo</h4>
        </div>

        <form action="{{ route('billing.pay') }}" method="POST" id="payment-form">
            @csrf
            <script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="{{ env('STRIPE_KEY') }}"
                data-amount="3000"
                data-name="Medios Billing"
                data-description="Monthly Subscription"
                data-image="https://mediosbilling.com/logo.png"
                data-locale="auto"
                data-label="ACTIVATE SUBSCRIPTION FOR $30/MO">
            </script>
        </form>
    </div>
</div>
@endsection
