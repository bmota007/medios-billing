@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="fw-bold text-white mb-1">
                Good Evening, Medios
            </h2>
            <p class="text-secondary small">
                Welcome back to the Medios Billing portal.
            </p>
        </div>

        <div class="weather-card">
            ☀️ 74°F <span>Houston</span>
        </div>
    </div>

    <!-- 🔥 STATS (WIDER + WOW EFFECT) -->
    <div class="stats-wrapper mb-5">

        <div class="stat-card stat-green">
            <p>Total Revenue</p>
            <h2>${{ number_format($revenue ?? 0, 2) }}</h2>
        </div>

        <div class="stat-card stat-blue">
            <p>Total Invoices</p>
            <h2>{{ $totalInvoices ?? 0 }}</h2>
        </div>

        <div class="stat-card stat-cyan">
            <p>Paid Invoices</p>
            <h2>{{ $paidInvoices ?? 0 }}</h2>
        </div>

        <div class="stat-card stat-orange">
            <p>Pending Invoices</p>
            <h2>{{ $pendingInvoices ?? 0 }}</h2>
        </div>

    </div>

    <!-- CHART -->
    <div class="dashboard-box mb-5">
        <h4 class="text-white mb-4">Revenue Overview</h4>
        <canvas id="revenueChart" height="120"></canvas>
    </div>

    <!-- INVOICES -->
    <div class="dashboard-box">

        <div class="d-flex justify-content-between mb-4">
            <h4 class="text-white">Recent Invoices</h4>
            <small class="text-muted">Live activity</small>
        </div>

        @foreach($recentInvoices as $invoice)
        <div class="invoice-card">

            <div class="left">
                <div class="icon">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>

                <div>
                    <strong>#{{ $invoice->invoice_number ?? 'INV' }}</strong>
                    <div class="text-muted small">{{ $invoice->customer_name }}</div>
                </div>
            </div>

            <div class="right">
                <strong>${{ number_format($invoice->total, 2) }}</strong>

                <span class="status 
                    {{ $invoice->status == 'paid' ? 'paid' : ($invoice->status == 'pending' ? 'pending' : 'unpaid') }}">
                    {{ strtoupper($invoice->status) }}
                </span>
            </div>

        </div>
        @endforeach

    </div>

</div>

<!-- CHART -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('revenueChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            data: [0,0,14000,3000,0,0,0,0,0,0,0,0],
            borderColor: '#38bdf8',
            backgroundColor: 'rgba(56,189,248,0.2)',
            tension: 0.5,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 8,
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false
        },
        animation: {
            duration: 1800
        },
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { ticks: { color: '#94a3b8' } },
            y: { ticks: { color: '#94a3b8' } }
        }
    }
});
</script>

<style>

/* 🔥 NEW GRID (FORCE WIDTH + WOW) */
.stats-wrapper {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}

/* CARDS */
.stat-card {
    padding: 30px;
    border-radius: 18px;
    background: rgba(15,23,42,0.7);
    backdrop-filter: blur(12px);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

/* 🔥 GLOW EFFECT */
.stat-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top, rgba(255,255,255,0.15), transparent);
    opacity: 0;
    transition: 0.3s;
}

.stat-card:hover::before {
    opacity: 1;
}

/* HOVER */
.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
}

/* TEXT */
.stat-card p {
    color: #94a3b8;
    font-size: 13px;
}

.stat-card h2 {
    font-size: 32px;
    font-weight: 800;
}

/* COLORS */
.stat-green { border-color: #22c55e; }
.stat-blue { border-color: #3b82f6; }
.stat-cyan { border-color: #06b6d4; }
.stat-orange { border-color: #f59e0b; }

/* WEATHER */
.weather-card {
    background: rgba(15,23,42,0.6);
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: bold;
}

/* BOX */
.dashboard-box {
    background: rgba(15,23,42,0.6);
    padding: 25px;
    border-radius: 16px;
}

/* INVOICES */
.invoice-card {
    display: flex;
    justify-content: space-between;
    padding: 14px;
    border-radius: 12px;
    margin-bottom: 10px;
    transition: 0.2s;
}

.invoice-card:hover {
    background: rgba(255,255,255,0.05);
}

.invoice-card .left {
    display: flex;
    gap: 12px;
    align-items: center;
}

.invoice-card .icon {
    width: 40px;
    height: 40px;
    background: rgba(56,189,248,0.2);
    border-radius: 10px;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#38bdf8;
}

/* STATUS */
.status {
    display:block;
    margin-top:4px;
    padding:3px 10px;
    border-radius:999px;
    font-size:11px;
}

.status.paid { background:#dcfce7; color:#166534; }
.status.pending { background:#fef3c7; color:#92400e; }
.status.unpaid { background:#fee2e2; color:#991b1b; }

/* 🔥 RESPONSIVE */
@media(max-width:992px){
    .stats-wrapper { grid-template-columns: repeat(2,1fr); }
}

@media(max-width:600px){
    .stats-wrapper { grid-template-columns: 1fr; }
}

</style>

@endsection

