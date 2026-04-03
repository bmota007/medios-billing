@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="text-white fw-bold">Manual <span class="accent-text">SaaS Onboarding</span></h2>
        <p class="text-secondary small">Onboard a high-end client with a custom 7-day trial and automated billing.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-green-500/10 border-green-500/20 text-green-500 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger bg-red-500/10 border-red-500/20 text-red-500 mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.manual-charge.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="glass-card mb-4">
                    <h5 class="text-white mb-4"><i class="fa-solid fa-user-plus me-2 text-info"></i> Business Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-secondary small uppercase fw-bold">Company Name</label>
                            <input type="text" name="company_name" class="form-control bg-transparent border-slate-700 text-white" placeholder="e.g. Pronto Painting" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-secondary small uppercase fw-bold">Admin Email</label>
                            <input type="email" name="email" class="form-control bg-transparent border-slate-700 text-white" placeholder="owner@email.com" required>
                        </div>
                    </div>
                </div>

                <div class="glass-card">
                    <h5 class="text-white mb-4"><i class="fa-solid fa-calendar-check me-2 text-success"></i> Subscription Strategy</h5>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="text-secondary small uppercase fw-bold">Custom Rate ($)</label>
                            <input type="number" name="amount" step="0.01" class="form-control bg-transparent border-slate-700 text-white fs-4 fw-bold" value="49.00" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-secondary small uppercase fw-bold">Billing Interval</label>
                            <select name="interval" class="form-control bg-transparent border-slate-700 text-white">
                                <option value="month" class="bg-slate-900">Monthly Billing</option>
                                <option value="year" class="bg-slate-900">Yearly Billing (Best for Retention)</option>
                                <option value="one_time" class="bg-slate-900">One-Time Project/Setup Fee</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 rounded bg-slate-800/50 border border-slate-700 mb-4">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                            <div>
                                <label class="text-white fw-bold d-block">Enable Recurring Billing</label>
                                <small class="text-secondary">System will auto-charge the card every cycle (Month/Year).</small>
                            </div>
                            <input class="form-check-input" type="checkbox" name="is_subscription" value="1" checked style="width: 3rem; height: 1.5rem;">
                        </div>
                    </div>

                    <div class="p-3 rounded bg-blue-500/5 border border-blue-500/20">
                        <div class="form-check form-switch d-flex justify-content-between align-items-center ps-0">
                            <div>
                                <label class="text-info fw-bold d-block">Capture Card for 7-Day Trial</label>
                                <small class="text-secondary small">Apple/Google Model: Card captured now, auto-charged in 7 days.</small>
                            </div>
                            <input class="form-check-input" type="checkbox" name="require_card_upfront" value="1" checked style="width: 3rem; height: 1.5rem;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="glass-card text-center sticky-top" style="top: 100px;">
                    <h5 class="text-white mb-4">Summary</h5>
                    <div class="mb-4 p-3 bg-slate-900 rounded border border-slate-800 text-start">
                        <p class="small text-secondary mb-1">Status: <span class="text-info fw-bold">Trialing</span></p>
                        <p class="small text-secondary mb-1">Grace Period: <span class="text-white">5 Days</span></p>
                        <p class="small text-secondary mb-0">Automatic Locking: <span class="text-danger">Enabled</span></p>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mb-3 shadow-lg">
                        <i class="fa-solid fa-paper-plane me-2"></i> SEND ONBOARDING LINK
                    </button>
                    <p class="text-secondary small px-2">
                        The client will receive an email to verify their identity and securely add their payment method to start their 7-day trial.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .accent-text { color: #38bdf8; }
    .glass-card {
        background: rgba(30, 41, 59, 0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1rem;
        padding: 1.5rem;
    }
    .form-check-input:checked {
        background-color: #38bdf8;
        border-color: #38bdf8;
    }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
</style>
@endsection
