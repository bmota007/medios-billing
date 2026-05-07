@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="crm-header mb-4">
        <div>
            <h2 class="crm-title text-white font-bold">Platform <span class="text-sky-400">Branding</span></h2>
            <p class="crm-subtitle text-secondary">Manage global SaaS appearance and payment integrations</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('platform.branding.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Visual Identity --}}
            <div class="col-md-6 mb-4">
                <div class="dashboard-card h-100" style="background: rgba(15,23,42,0.9); padding: 25px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05);">
                    <h4 class="text-white mb-4"><i class="fa-solid fa-palette me-2 text-sky-400"></i> Visual Identity</h4>
                    
                    <div class="mb-4">
                        <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Platform Logo</label>
                        <input type="file" name="logo" class="form-control custom-input">
                        @if($branding->logo ?? false)
                            <img src="{{ asset('storage/' . $branding->logo) }}" class="mt-2" style="height: 40px;">
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Platform Name</label>
                        <input type="text" name="platform_name" class="form-control custom-input" value="{{ $branding->platform_name ?? 'Medios Billing' }}">
                    </div>
                </div>
            </div>

            {{-- Stripe Platform Keys --}}
            <div class="col-md-6 mb-4">
                <div class="dashboard-card h-100" style="background: rgba(15,23,42,0.9); padding: 25px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05);">
                    <h4 class="text-white mb-4"><i class="fa-solid fa-credit-card me-2 text-sky-400"></i> Stripe Payment Integration</h4>
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Live Publishable Key</label>
                            <input type="text" name="stripe_live_pub_key" class="form-control custom-input" value="{{ $branding->stripe_live_pub_key ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Live Secret Key</label>
                            <input type="password" name="stripe_live_secret_key" class="form-control custom-input" value="{{ $branding->stripe_live_secret_key ?? '' }}">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Test Publishable Key</label>
                            <input type="text" name="stripe_test_pub_key" class="form-control custom-input" value="{{ $branding->stripe_test_pub_key ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Test Secret Key</label>
                            <input type="password" name="stripe_test_secret_key" class="form-control custom-input" value="{{ $branding->stripe_test_secret_key ?? '' }}">
                        </div>
                    </div>

                    {{-- WHSEC BOX --}}
                    <div class="mb-3">
                        <label class="text-warning small uppercase fw-bold mb-2 d-block">Stripe Webhook Secret (WHSEC)</label>
                        <input type="password" name="stripe_webhook_secret" class="form-control custom-input" value="{{ $branding->stripe_webhook_secret ?? '' }}" placeholder="whsec_...">
                        <small class="text-secondary small italic">Required to activate subscriptions automatically after payment.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary px-5 py-3 fw-bold" style="border-radius: 12px;">Save Business Settings</button>
        </div>
    </form>
</div>

<style>
    .custom-input { background: #0f172a !important; border: 1px solid rgba(255,255,255,0.1) !important; color: white !important; }
</style>
@endsection
