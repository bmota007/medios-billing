@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-5">
        <h2 class="fw-bold text-white">Sales Command Center</h2>
        <p class="text-secondary">Platform Performance & Revenue Metrics</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="p-4 rounded-4 shadow-lg border-0 h-100" style="background: linear-gradient(145deg, #1e293b, #0f172a); border-left: 4px solid #fbbf24 !important;">
                <div class="text-secondary small fw-bold text-uppercase mb-2">Monthly MRR</div>
                <h3 class="text-white fw-bold mb-0">${{ number_format($stats['total_mrr'] ?? 0, 2) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 rounded-4 shadow-lg border-0 h-100" style="background: linear-gradient(145deg, #1e293b, #0f172a); border-left: 4px solid #38bdf8 !important;">
                <div class="text-secondary small fw-bold text-uppercase mb-2">Active Tenants</div>
                <h3 class="text-white fw-bold mb-0">{{ $stats['active_tenants'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 rounded-4 shadow-lg border-0 h-100" style="background: linear-gradient(145deg, #1e293b, #0f172a); border-left: 4px solid #a855f7 !important;">
                <div class="text-secondary small fw-bold text-uppercase mb-2">New Leads</div>
                <h3 class="text-white fw-bold mb-0">{{ $stats['new_leads'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 rounded-4 shadow-lg border-0 h-100" style="background: linear-gradient(145deg, #1e293b, #0f172a); border-left: 4px solid #22c55e !important;">
                <div class="text-secondary small fw-bold text-uppercase mb-2">Total Collected</div>
                <h4 class="text-success fw-bold mb-0">${{ number_format($stats['total_revenue_collected'] ?? 0, 0) }}</h4>
            </div>
        </div>
    </div>

    <div class="card rounded-4 border-0 shadow-lg" style="background: #1e293b;">
        <div class="card-body p-5 text-center">
            <div class="mb-4"><i class="fa fa-rocket text-warning" style="font-size: 3rem;"></i></div>
            <h4 class="text-white fw-bold">Manual Onboarding System</h4>
            <p class="text-secondary mx-auto mb-4" style="max-width: 500px;">Bypass the marketing site to onboard partners or manual deals.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/sales/onboarding" class="btn btn-warning px-5 py-3 fw-bold text-dark rounded-pill shadow">Start Manual Onboarding</a>
                <a href="/sales/subscriptions" class="btn btn-outline-light px-5 py-3 rounded-pill">View All Subscriptions</a>
            </div>
        </div>
    </div>
</div>
@endsection
