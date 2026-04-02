@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-5">
        <h2 class="text-white fw-bold">Integrations <span class="text-sky-400">& Settings</span></h2>
        <p class="text-secondary small">Manage your payment processors and business configurations.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="glass-card h-100">
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-brands fa-stripe text-info fs-1 me-3"></i>
                    <div>
                        <h4 class="text-white mb-0">Stripe Payments</h4>
                        <span class="badge {{ $company->client_stripe_key ? 'bg-success' : 'bg-warning text-dark' }} small">
                            {{ $company->client_stripe_key ? 'Active' : 'Not Configured' }}
                        </span>
                    </div>
                </div>

                <p class="text-secondary small mb-4">
                    Connect your Stripe account to allow customers to pay invoices via Credit Card or Apple Pay.
                </p>

                @if(session('success'))
                    <div class="alert alert-success bg-success/10 border-success/20 text-success small">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST" autocomplete="off">
                    @csrf
                    {{-- Hidden inputs to trick browser autofill --}}
                    <input type="text" style="display:none" name="prevent_autofill_user">
                    <input type="password" style="display:none" name="prevent_autofill_pwd">

                    <div class="mb-3">
                        <label class="text-label text-secondary small">Stripe Public Key (pk_live_...)</label>
                        <input type="text" 
                               name="client_stripe_key" 
                               class="form-control bg-slate-900 border-slate-700 text-white mt-1" 
                               placeholder="pk_live_xxxxxxxxxxxx"
                               value="{{ $company->client_stripe_key }}"
                               autocomplete="new-password">
                    </div>

                    <div class="mb-4">
                        <label class="text-label text-secondary small">Stripe Secret Key (sk_live_...)</label>
                        <input type="password" 
                               name="client_stripe_secret" 
                               class="form-control bg-slate-900 border-slate-700 text-white mt-1" 
                               placeholder="sk_live_xxxxxxxxxxxx"
                               value="{{ $company->client_stripe_secret }}"
                               autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                        <i class="fa-solid fa-floppy-disk me-2"></i> SAVE STRIPE CONFIG
                    </button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="glass-card h-100 border-dashed border-slate-700" style="opacity: 0.5;">
                <div class="d-flex align-items-center mb-4">
                    <i class="fa-brands fa-paypal text-secondary fs-1 me-3"></i>
                    <h4 class="text-white mb-0">PayPal</h4>
                </div>
                <p class="text-secondary small">Coming soon: Accept payments via PayPal and Venmo directly on your glass invoices.</p>
                <button disabled class="btn btn-outline-secondary w-100">COMING SOON</button>
            </div>
        </div>
    </div>
</div>
@endsection
