@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="text-white fw-bold">Platform <span class="accent-text">Billing</span></h2>
        <p class="text-secondary small">Subscription revenue, renewals, and platform health.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-info border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Total MRR</div>
                <h2 class="text-white fw-bold">${{ number_format($stats['mrr'] ?? 0, 2) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-success border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Paid Subs</div>
                <h2 class="text-white fw-bold">{{ $stats['paid_subscriptions'] ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-warning border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Active Tenants</div>
                <h2 class="text-white fw-bold">{{ $stats['active_companies'] ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card h-100 border-start border-danger border-4">
                <div class="text-white-50 small uppercase fw-bold mb-2">Failed</div>
                <h2 class="text-white fw-bold">{{ $stats['failed_subscriptions'] ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="glass-card">
        <h5 class="text-white mb-4">Subscription History</h5>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="text-secondary small uppercase">
                    <tr style="background: rgba(255,255,255,0.02);">
                        <th class="ps-4">Company</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Interval</th>
                        <th class="text-end pe-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptionInvoices as $invoice)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                        <td class="ps-4">
                            <div class="fw-bold text-white">{{ $invoice->company->name ?? 'Unknown' }}</div>
                            <div class="small text-secondary">{{ $invoice->company->email ?? '' }}</div>
                        </td>
                        <td class="fw-bold text-info">${{ number_format($invoice->amount, 2) }}</td>
                        <td>
                            <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : 'bg-danger' }} px-3 text-uppercase" style="font-size: 10px;">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="text-secondary small uppercase">{{ $invoice->interval ?? 'Month' }}</td>
                        <td class="text-end text-secondary small pe-4">{{ $invoice->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-secondary">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $subscriptionInvoices->links() }}
        </div>
    </div>
</div>

<style>
    .accent-text { color: #38bdf8; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .dashboard-card { 
        background: rgba(30, 41, 59, 0.6); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255, 255, 255, 0.1); 
        border-radius: 1rem; 
        padding: 1.5rem; 
    }
</style>
@endsection
