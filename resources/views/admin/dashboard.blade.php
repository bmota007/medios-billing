@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="mb-5">
        <h2 class="text-white font-bold">
            @if(auth()->user()->is_admin)
                Super Admin <span style="color: #38bdf8">Dashboard</span>
            @else
                {{ auth()->user()->company->name ?? 'Company' }} <span style="color: #38bdf8">Overview</span>
            @endif
        </h2>
    </div>

    {{-- Stats Row (4-Across on Desktop) --}}
    <div class="row g-4 mb-4">
        @if(auth()->user()->is_admin)
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Total Companies</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ $companies ?? 0 }}</h2>
                    <i class="fa-solid fa-building text-primary opacity-50"></i>
                </div>
            </div>
        </div>
        @endif

        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Total Revenue</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-success">${{ number_format($revenue ?? 0, 2) }}</h2>
                    <i class="fa-solid fa-dollar-sign text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">MRR</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-info">${{ number_format($mrr ?? 0, 2) }}</h2>
                    <i class="fa-solid fa-chart-line text-info opacity-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Active Tenants</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">{{ $activeCompanies ?? 0 }}</h2>
                    <i class="fa-solid fa-users-gear opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Second Row: Activity Table --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="dashboard-card">
                <h5 class="text-white mb-4">Recent Activity</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-hover border-transparent">
                        <thead class="text-muted small uppercase">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices ?? [] as $invoice)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                <td class="py-3">#{{ $invoice->id }}</td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td class="font-bold">${{ number_format($invoice->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-3">{{ $invoice->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No recent activity found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .bg-success-subtle { background: rgba(16, 185, 129, 0.1) !important; border: 1px solid rgba(16, 185, 129, 0.2); }
</style>
@endsection
