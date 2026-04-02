@extends('layouts.admin') {{-- Hard-locked to admin layout for the 260px sidebar gap --}}

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h2 class="text-white font-bold">
                {{ auth()->user()->company->name ?? 'Business' }} <span style="color: var(--accent-blue)">Dashboard</span>
            </h2>
            <p class="text-secondary">Business performance overview</p>
        </div>
    </div>

    {{-- Stats Row: 4 Cards Across using the Glass Design --}}
    <div class="row g-4 mb-4">
        {{-- Total Revenue --}}
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Total Revenue</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-success">${{ number_format($revenue ?? 0, 2) }}</h2>
                    <i class="fa-solid fa-dollar-sign text-success opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Total Invoices --}}
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Total Invoices</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-white">{{ $invoices ?? 0 }}</h2>
                    <i class="fa-solid fa-file-invoice text-primary opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Paid Invoices --}}
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Paid</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-info">{{ $paidInvoices ?? 0 }}</h2>
                    <i class="fa-solid fa-check-double text-info opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Pending Invoices --}}
        <div class="col-md-3">
            <div class="dashboard-card h-100">
                <div class="text-muted small uppercase mb-2">Pending</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0 text-warning">{{ $pendingInvoices ?? 0 }}</h2>
                    <i class="fa-solid fa-clock text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Overview Chart --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-4">Revenue Overview</h3>
                <div id="revenueChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- Recent Invoices Table --}}
    <div class="row g-4">
        <div class="col-12">
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-4">Recent Invoices</h3>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr class="text-muted small uppercase">
                                <th class="pb-3">Invoice</th>
                                <th class="pb-3">Customer</th>
                                <th class="pb-3">Amount</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-light">
                            @foreach($recentInvoices as $invoice)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                <td class="py-3 font-mono text-sky-400">#{{ $invoice->id }}</td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td class="text-white font-semibold">${{ number_format($invoice->total, 2) }}</td>
                                <td>
                                    @if($invoice->status == 'paid')
                                        <span class="badge bg-success-subtle text-success px-3">PAID</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning px-3">PENDING</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts Section --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var options = {
            series: [{ 
                name: 'Revenue', 
                data: [31, 40, 28, 51, 42, 109, 100] 
            }],
            chart: { 
                type: 'area', 
                height: 350, 
                toolbar: { show: false }, 
                foreColor: '#94a3b8',
                background: 'transparent'
            },
            colors: ['#38bdf8'],
            stroke: { curve: 'smooth', width: 3 },
            fill: { 
                type: 'gradient', 
                gradient: { 
                    shadeIntensity: 1, 
                    opacityFrom: 0.5, 
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                } 
            },
            xaxis: { 
                categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            grid: { borderColor: 'rgba(255,255,255,0.05)' },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#revenueChart"), options).render();
    });
</script>

<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .bg-success-subtle { background: rgba(16, 185, 129, 0.1) !important; border: 1px solid rgba(16, 185, 129, 0.2); }
    .bg-warning-subtle { background: rgba(245, 158, 11, 0.1) !important; border: 1px solid rgba(245, 158, 11, 0.2); }
    .table-dark { --bs-table-bg: transparent; }
</style>
@endsection
