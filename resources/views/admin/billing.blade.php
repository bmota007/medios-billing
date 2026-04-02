@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <h1 class="fw-bold text-white mb-1">SaaS Billing</h1>
        <p class="text-light opacity-75 mb-0">Platform subscription revenue, renewals, failed payments, and active accounts.</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-lg h-100" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border-radius: 18px;">
                <div class="card-body">
                    <div class="text-uppercase small text-light opacity-75 mb-2">Monthly Recurring Revenue</div>
                    <div class="fs-2 fw-bold text-white">${{ number_format($stats['mrr'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-lg h-100" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border-radius: 18px;">
                <div class="card-body">
                    <div class="text-uppercase small text-light opacity-75 mb-2">Paid Invoices</div>
                    <div class="fs-2 fw-bold text-success">{{ $stats['paid_subscriptions'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-lg h-100" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border-radius: 18px;">
                <div class="card-body">
                    <div class="text-uppercase small text-light opacity-75 mb-2">Failed Payments</div>
                    <div class="fs-2 fw-bold text-danger">{{ $stats['failed_subscriptions'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-lg h-100" style="background: rgba(255,255,255,0.06); backdrop-filter: blur(10px); border-radius: 18px;">
                <div class="card-body">
                    <div class="text-uppercase small text-light opacity-75 mb-2">Active Companies</div>
                    <div class="fs-2 fw-bold text-info">{{ $stats['active_companies'] ?? 0 }}</div>
                    <div class="small text-light opacity-75 mt-2">Inactive: {{ $stats['inactive_companies'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-lg" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(12px); border-radius: 20px; overflow: hidden;">
        <div class="card-header border-0 py-3 px-4" style="background: rgba(255,255,255,0.04);">
            <h2 class="h5 text-white mb-0">Subscription Invoices</h2>
        </div>

        <div class="card-body p-0">
            @if(isset($subscriptionInvoices) && $subscriptionInvoices->count())
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.04);">
                                <th class="text-light px-4 py-3 border-0">Invoice #</th>
                                <th class="text-light px-3 py-3 border-0">Company</th>
                                <th class="text-light px-3 py-3 border-0">Email</th>
                                <th class="text-light px-3 py-3 border-0">Amount</th>
                                <th class="text-light px-3 py-3 border-0">Status</th>
                                <th class="text-light px-3 py-3 border-0">Period</th>
                                <th class="text-light px-3 py-3 border-0">Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptionInvoices as $invoice)
                                <tr style="border-top: 1px solid rgba(255,255,255,0.06);">
                                    <td class="text-white fw-semibold px-4 py-3">{{ $invoice->invoice_no }}</td>
                                    <td class="text-light px-3 py-3">{{ $invoice->customer_name ?? optional($invoice->company)->name ?? 'N/A' }}</td>
                                    <td class="text-light px-3 py-3">{{ $invoice->customer_email ?? 'N/A' }}</td>
                                    <td class="text-white px-3 py-3">${{ number_format((float) $invoice->amount, 2) }}</td>
                                    <td class="px-3 py-3">
                                        @if($invoice->status === 'paid')
                                            <span class="badge rounded-pill bg-success">Paid</span>
                                        @elseif($invoice->status === 'failed')
                                            <span class="badge rounded-pill bg-danger">Failed</span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-light px-3 py-3">
                                        <small>
                                            {{ optional($invoice->period_start)->format('M d, Y') ?? '—' }}
                                            —
                                            {{ optional($invoice->period_end)->format('M d, Y') ?? '—' }}
                                        </small>
                                    </td>
                                    <td class="text-light px-3 py-3">
                                        <small>{{ optional($invoice->paid_at)->format('M d, Y g:i A') ?? '—' }}</small>
                                    </td>
                                </tr>

                                @if(!empty($invoice->notes))
                                    <tr>
                                        <td colspan="7" class="px-4 pb-3 pt-0 text-light">
                                            <small class="opacity-75"><strong>Notes:</strong> {{ $invoice->notes }}</small>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $subscriptionInvoices->links() }}
                </div>
            @else
                <div class="p-4 text-light opacity-75">
                    No subscription invoices found yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
