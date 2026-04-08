@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-white">Active Subscriptions</h2>
            <p class="text-secondary">Master Ledger of all Tenant Accounts</p>
        </div>
        <a href="/sales/onboarding" class="btn btn-warning fw-bold text-dark px-4 py-2">
            <i class="fa fa-plus-circle me-2"></i>New Subscription
        </a>
    </div>

    <div class="card rounded-4 border-0 shadow-lg" style="background: #1e293b;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead style="background: rgba(0,0,0,0.2);">
                        <tr class="text-secondary small text-uppercase" style="letter-spacing: 1px;">
                            <th class="px-4 py-3">Company Information</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Monthly Rev</th>
                            <th>Joined</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $comp)
                        <tr class="align-middle border-secondary" style="border-bottom-width: 1px !important;">
                            <td class="px-4 py-4">
                                <div class="fw-bold text-white fs-6">{{ $comp->name }}</div>
                                <div class="small text-secondary">{{ $comp->email }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $comp->plan == 'Elite' ? 'bg-purple' : ($comp->plan == 'Growth' ? 'bg-primary' : 'bg-info text-dark') }} px-3 py-2">
                                    {{ $comp->plan ?? 'Starter' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2 {{ $comp->subscription_status == 'active' ? 'bg-success' : 'bg-warning' }}" style="width: 8px; height: 8px;"></div>
                                    <span class="fw-bold {{ $comp->subscription_status == 'active' ? 'text-success' : 'text-warning' }}">
                                        {{ strtoupper($comp->subscription_status ?? 'Trial') }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="text-white fw-bold">${{ number_format($comp->monthly_price ?? 0, 2) }}</span>
                            </td>
                            <td class="text-secondary">
                                {{ $comp->created_at->format('m/d/Y') }}
                            </td>
                            <td class="text-end px-4">
                                <div class="btn-group">
                                    <a href="/impersonate/{{ $comp->id }}" class="btn btn-sm btn-outline-light border-secondary px-3">Support</a>
                                    <button class="btn btn-sm btn-outline-warning ms-2"><i class="fa fa-ellipsis-v"></i></button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-purple { background-color: #a855f7; color: white; }
    .table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02); }
</style>
@endsection
