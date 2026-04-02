@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-white fw-bold">
                {{ current_company()->name ?? 'Business' }} <span class="text-sky-400">Dashboard</span>
            </h2>
            <p class="text-secondary small">Overview of invoices, revenue, and recent payments</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger px-4 py-2 shadow-lg">
                Sign Out
            </button>
        </form>
    </div>

    <div class="d-flex flex-wrap gap-3 mb-4">
        <a href="{{ route('customers.create') }}" class="btn btn-success">
            + New Customer
        </a>

        <a href="{{ route('customers.index') }}" class="btn btn-primary">
            View Customers
        </a>

        <a href="{{ route('invoice.create') }}" class="btn btn-primary">
            + Create Invoice
        </a>

        <a href="{{ route('invoice.history') }}" class="btn btn-dark">
            History
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary mb-2">Total Revenue</h6>
                <div class="fs-3 fw-bold text-success">${{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary mb-2">This Month</h6>
                <div class="fs-3 fw-bold text-success">${{ number_format($thisMonthRevenue, 2) }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary mb-2">Paid</h6>
                <div class="fs-3 fw-bold text-success">{{ $paidCount }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="glass-card p-4 h-100">
                <h6 class="text-secondary mb-2">Unpaid</h6>
                <div class="fs-3 fw-bold text-danger">{{ $unpaidCount }}</div>
            </div>
        </div>
    </div>

    <div class="glass-card p-4 mb-4">
        <h6 class="text-secondary mb-2">Outstanding Balance</h6>
        <div class="fs-3 fw-bold text-danger">${{ number_format($outstandingBalance, 2) }}</div>
    </div>

    <div class="glass-card p-4 mb-5">
        <h5 class="text-white mb-4">Monthly Revenue ({{ now()->year }})</h5>
        <canvas id="revenueChart"></canvas>
    </div>

    <div class="glass-card p-4">
        <h5 class="text-white mb-4">Recent Payments</h5>

        <div class="table-responsive">
            <table class="table table-dark table-borderless align-middle">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date Paid</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_no }}</td>
                            <td>{{ $invoice->customer_name }}</td>
                            <td>${{ number_format($invoice->total, 2) }}</td>
                            <td>{{ optional($invoice->paid_at)->format('m/d/Y h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-secondary">No payments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <a href="{{ route('invoice.history') }}" class="btn btn-primary mt-3">
            Invoice History
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            label: 'Revenue',
            data: [
                @for($i = 1; $i <= 12; $i++)
                    {{ $monthlyData[$i] ?? 0 }}@if($i < 12),@endif
                @endfor
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endsection
