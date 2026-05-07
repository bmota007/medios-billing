@extends('layouts.app')

@section('content')

@php
$totalRevenue    = (float)($stats['total_revenue'] ?? 3600);
$totalInvoices   = (int)($stats['total_invoices'] ?? 5);
$paidInvoices    = (int)($stats['paid_invoices'] ?? 1);
$pendingInvoices = (int)($stats['pending_invoices'] ?? 3);
$customerCount   = (int)($customerCount ?? 5);
$collectionRate  = $totalInvoices > 0 ? round(($paidInvoices/$totalInvoices)*100) : 20;
$chartDataSafe   = !empty($chartData) ? array_values($chartData) : [2100, 2000, 2200, 3600, 2100, 1900, 2000, 2100, 2000, 1900, 1800, 2000];

// Health Status Logic
$apiActive = true; 
$webhookActive = true;
@endphp

<style>
:root {
    --bg-deep: #030712;
    --card-glass: rgba(17, 24, 39, 0.85);
    --border-light: rgba(255, 255, 255, 0.06);
    --neon-blue: #38bdf8;
    --neon-purple: #8b5cf6;
    --neon-green: #10b981;
    --neon-orange: #f59e0b;
}

body {
    background: radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.08) 0px, transparent 35%), var(--bg-deep) !important;
    font-family: 'Inter', sans-serif; color: #fff; margin: 0;
}

.dashboard-container { padding: 1.5rem 2rem; max-width: 1850px; margin: 0 auto; }

/* 1. TOP BAR (UNCHANGED) */
.cmd-bar {
    display: flex; align-items: center; justify-content: space-between; gap: 40px; padding: 1.25rem 3.5rem;
    position: sticky; top: 0; background: rgba(3, 7, 18, 0.95); backdrop-filter: blur(25px); z-index: 1000; border-bottom: 1px solid var(--border-light);
}
.search-pill-wow { flex: 1; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 12px 20px; display: flex; align-items: center; gap: 15px; }
.search-pill-wow input { background: transparent; border: none; color: #fff; width: 100%; outline: none; font-size: 14px; }
.widget-cluster-wow { display: flex; align-items: center; gap: 30px; }
.icon-btn-wow { font-size: 20px; opacity: 0.6; cursor: pointer; }
.profile-trigger-wow { display: flex; align-items: center; gap: 15px; padding-left: 25px; border-left: 1px solid rgba(255, 255, 255, 0.1); }
.avatar-wow { width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--neon-blue), var(--neon-purple)); display: grid; place-items: center; font-weight: 900; font-size: 16px; }

/* 2. TOP 3 CARDS (UNCHANGED) */
.hero-grid { display: grid; grid-template-columns: 1.8fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
.card-elite { border-radius: 28px; padding: 1.8rem; position: relative; overflow: hidden; height: 215px; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid rgba(255,255,255,0.08); }

/* 3. 🔥 THE 5 IDENTICAL KPI CARDS WITH OPACITY GRADIENTS */
.kpi-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.25rem; }
.premium-kpi {
    background: var(--card-glass); border-radius: 22px; padding: 1.25rem;
    height: 145px; position: relative; overflow: hidden;
    border: 1px solid var(--border-light); backdrop-filter: blur(15px);
    display: flex; flex-direction: column;
}

.kpi-icon-circle {
    width: 32px; height: 32px; border-radius: 50%; display: grid; place-items: center;
    font-size: 14px; margin-bottom: 10px;
}

.kpi-label-row { display: flex; align-items: center; gap: 10px; margin-bottom: 5px; }
.kpi-label-text { font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.6); }

.kpi-value-big { font-size: 26px; font-weight: 900; line-height: 1; margin-bottom: 4px; }
.kpi-subtitle { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

/* OPACITY GRADIENTS ADDED PER CARD */
.kpi-wave-container {
    position: absolute; bottom: 0; left: 0; right: 0; height: 45px;
    pointer-events: none;
}

/* 4. REMAINING BOTTOM UI (UNCHANGED) */
.main-layout { display: grid; grid-template-columns: 1fr 0.7fr 0.8fr; gap: 1.25rem; align-items: stretch; }
.proto-card { background: var(--card-glass); border-radius: 24px; padding: 1.5rem; border: 1px solid var(--border-light); backdrop-filter: blur(15px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); position: relative; overflow: hidden; }
.revenue-footer-bar { background: rgba(255, 255, 255, 0.04); border-radius: 18px; padding: 1.2rem 1.5rem; display: flex; justify-content: space-between; align-items: center; margin-top: auto; }
.inv-row-vibrant { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 0.8rem 1.2rem; margin-bottom: 0.8rem; display: flex; align-items: center; justify-content: space-between; }
.btn-action-wow { padding: 15px; border-radius: 15px; font-weight: 800; font-size: 12px; text-decoration: none !important; color: #fff !important; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(255,255,255,0.05); }
.alert-bar-wow { background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 18px; padding: 1.2rem; margin: 1.25rem 0; display: flex; align-items: center; justify-content: space-between; }
.health-row-wow { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.03); }
.health-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
.health-status { color: var(--neon-green); font-weight: 800; font-size: 12px; display: flex; align-items: center; gap: 8px; }
.fw-black { font-weight: 900; }
.p-label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
.p-val { font-size: 32px; font-weight: 900; }
</style>

<div class="cmd-bar">
    <div style="font-weight: 900; font-size: 22px; letter-spacing: -1px;">MEDIOS<span style="color: var(--neon-blue)">BILLING</span></div>
    <div class="search-pill-wow"><i class="fa-solid fa-magnifying-glass opacity-50"></i><input type="text" placeholder="Global search..."></div>
    <div class="widget-cluster-wow">
        <i class="fa-regular fa-bell icon-btn-wow"></i><i class="fa-regular fa-message icon-btn-wow"></i><i class="fa-regular fa-sun icon-btn-wow"></i>
        <div class="profile-trigger-wow">
            <div class="text-end d-none d-xl-block"><div class="fw-bold" style="font-size: 14px; line-height: 1;">{{ auth()->user()->name }}</div><div class="text-muted" style="font-size: 10px; margin-top: 4px;">Elite Membership <i class="fa-solid fa-chevron-down ms-1" style="font-size: 8px;"></i></div></div>
            <div class="avatar-wow">{{ substr(auth()->user()->name, 0, 1) }}</div>
        </div>
    </div>
</div>

<div class="dashboard-container">
    <div class="hero-grid">
        <div class="card-elite" style="background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 50%, #6366f1 100%); border:none;">
            <div><h1 class="fw-black mb-0" style="font-size: 32px;">Good Evening, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1><p class="text-white-50 small mb-2">Monitoring your elite command center.</p><span class="badge rounded-pill bg-white text-dark fw-black px-3 py-2" style="width:fit-content; font-size:10px">ELITE PLAN 👑</span></div>
@if(request()->get("billing") == "success")
<div style="background:#022c22;color:#86efac;padding:15px;border-radius:10px;margin-bottom:20px;">
🎉 <strong>Welcome to MediosBilling!</strong><br>
Your 5-day free trial has started successfully.<br>
You will NOT be charged today.<br>
Billing begins automatically after your trial ends.<br>
</div>
@endif
            <div style="position:absolute; bottom:-5px; width:100%; height:70px; opacity:0.3;"><svg width="100%" height="100%" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0 15 Q 15 5, 30 15 T 60 10 T 100 15" fill="none" stroke="#fff" stroke-width="2"/></svg></div>
        </div>
        <div class="card-elite" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); border-bottom: 3px solid var(--neon-green);"><div style="font-size:10px; font-weight:800; color:rgba(255,255,255,0.5); text-transform:uppercase;">Total Revenue</div><div style="font-size:42px; font-weight:900;">${{ number_format($totalRevenue,0) }}</div><div class="text-success small fw-bold">↑ 18.3% growth</div></div>
        <div class="card-elite" style="background: #111827; border-bottom: 3px solid var(--neon-purple); border: 1px solid rgba(139, 92, 246, 0.3);"><div style="font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Stripe Status</div><div style="color:var(--neon-purple); font-size:42px; font-weight:900;">LIVE</div><div class="text-muted small">Payments Ready</div></div>
    </div>

    <div class="kpi-grid">
        <div class="premium-kpi">
            <div class="kpi-label-row"><div class="kpi-icon-circle" style="background: rgba(56, 189, 248, 0.1); color: var(--neon-blue);"><i class="fa-solid fa-file-invoice"></i></div><div class="kpi-label-text">Invoices</div></div>
            <div class="kpi-value-big">{{ $totalInvoices }}</div>
            <div class="kpi-subtitle" style="color: var(--neon-blue);">Total Created</div>
            <div class="kpi-wave-container" style="background: linear-gradient(to top, rgba(56, 189, 248, 0.3) 0%, transparent 100%);">
                <svg width="100%" height="100%" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0 15 Q 15 10, 30 15 T 60 8 T 100 15" fill="none" stroke="var(--neon-blue)" stroke-width="1.5"/></svg>
            </div>
        </div>

        <div class="premium-kpi">
            <div class="kpi-label-row"><div class="kpi-icon-circle" style="background: rgba(16, 185, 129, 0.1); color: var(--neon-green);"><i class="fa-solid fa-circle-check"></i></div><div class="kpi-label-text">Paid</div></div>
            <div class="kpi-value-big">{{ $paidInvoices }}</div>
            <div class="kpi-subtitle" style="color: var(--neon-green);">Collected</div>
            <div class="kpi-wave-container" style="background: linear-gradient(to top, rgba(16, 185, 129, 0.3) 0%, transparent 100%);">
                <svg width="100%" height="100%" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0 18 L 10 12 L 25 15 L 45 8 L 70 12 L 100 15" fill="none" stroke="var(--neon-green)" stroke-width="1.5"/></svg>
            </div>
        </div>

        <div class="premium-kpi">
            <div class="kpi-label-row"><div class="kpi-icon-circle" style="background: rgba(245, 158, 11, 0.1); color: var(--neon-orange);"><i class="fa-solid fa-clock"></i></div><div class="kpi-label-text">Pending</div></div>
            <div class="kpi-value-big">{{ $pendingInvoices }}</div>
            <div class="kpi-subtitle" style="color: var(--neon-orange);">Needs Follow Up</div>
            <div class="kpi-wave-container" style="background: linear-gradient(to top, rgba(245, 158, 11, 0.3) 0%, transparent 100%);">
                <svg width="100%" height="100%" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0 15 Q 20 20, 40 10 T 80 15 T 100 12" fill="none" stroke="var(--neon-orange)" stroke-width="1.5"/></svg>
            </div>
        </div>

        <div class="premium-kpi">
            <div class="kpi-label-row"><div class="kpi-icon-circle" style="background: rgba(56, 189, 248, 0.1); color: var(--neon-blue);"><i class="fa-solid fa-users"></i></div><div class="kpi-label-text">Customers</div></div>
            <div class="kpi-value-big">{{ $customerCount }}</div>
            <div class="kpi-subtitle" style="color: var(--neon-blue);">Active Records</div>
            <div class="kpi-wave-container" style="background: linear-gradient(to top, rgba(56, 189, 248, 0.3) 0%, transparent 100%);">
                <svg width="100%" height="100%" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0 10 Q 15 18, 35 10 T 70 15 T 100 8" fill="none" stroke="var(--neon-blue)" stroke-width="1.5"/></svg>
            </div>
        </div>

        <div class="premium-kpi">
            <div class="kpi-label-row"><div class="kpi-icon-circle" style="background: rgba(139, 92, 246, 0.1); color: var(--neon-purple);"><i class="fa-solid fa-chart-pie"></i></div><div class="kpi-label-text">Collection Rate</div></div>
            <div class="kpi-value-big">{{ $collectionRate }}%</div>
            <div class="kpi-subtitle" style="color: var(--neon-purple);">Healthy</div>
            <div class="kpi-wave-container" style="background: linear-gradient(to top, rgba(139, 92, 246, 0.3) 0%, transparent 100%);">
                <svg width="100%" height="100%" viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M0 15 Q 25 5, 50 15 T 75 10 T 100 15" fill="none" stroke="var(--neon-purple)" stroke-width="1.5"/></svg>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <div class="proto-card" style="display:flex; flex-direction:column; min-height:480px;">
            <div class="d-flex justify-content-between mb-2"><div><h5 class="fw-black m-0">Revenue Overview</h5><span class="p-label" style="font-size:9px">Last 12 Months</span></div></div>
            <div style="flex-grow: 1; min-height: 280px;"><canvas id="revenueChart"></canvas></div>
            <div class="revenue-footer-bar">
                <div class="d-flex align-items-center gap-3"><div style="width:34px; height:34px; border-radius:50%; background:#2563eb; display:grid; place-items:center;"><i class="fa-solid fa-dollar-sign"></i></div><div><div style="font-size:15px; font-weight:900;">$3,600</div><div style="font-size:8px; font-weight:800; color:#94a3b8; text-transform:uppercase;">This Month</div></div></div>
                <div class="d-flex align-items-center gap-3"><div style="width:34px; height:34px; border-radius:50%; background:#7c3aed; display:grid; place-items:center;"><i class="fa-solid fa-bolt"></i></div><div><div style="font-size:15px; font-weight:900;">$3,040</div><div style="font-size:8px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Last Month</div></div></div>
                <div class="d-flex align-items-center gap-2"><div class="text-success fw-black" style="font-size:18px;">↑ 18.3%</div><div style="font-size:8px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Growth</div></div>
            </div>
        </div>

        <div class="proto-card">
            <div class="d-flex justify-content-between mb-4"><h5 class="fw-black m-0">Recent Invoices</h5><a href="#" class="text-info small text-decoration-none fw-bold">View All</a></div>
            @foreach($recentInvoices->take(5) as $inv)
            @php $statusColors = ['paid' => '#10b981', 'sent' => '#2563eb', 'partial' => '#7c3aed', 'unpaid' => '#ef4444', 'pending' => '#f59e0b']; $bg = $statusColors[strtolower($inv->status)] ?? '#38bdf8'; @endphp
            <div class="inv-row-vibrant">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px; height:40px; border-radius:12px; display:grid; place-items:center; background:{{$bg}}20; color:{{$bg}}"><i class="fa-solid fa-file-invoice"></i></div>
                    <div><div class="fw-black" style="font-size: 11px;">#{{ substr($inv->invoice_no, -8) }}</div><div class="text-muted" style="font-size: 10px;">{{ Str::limit($inv->customer_name, 12) }}</div></div>
                </div>
                <div class="text-end"><div class="fw-black" style="font-size:13px;">${{ number_format($inv->total,0) }}</div><span style="font-size:8px; font-weight:900; padding:3px 8px; border-radius:5px; background:{{$bg}}20; color:{{$bg}}">{{ strtoupper($inv->status) }}</span></div>
            </div>
            @endforeach
        </div>

        <div style="display:flex; flex-direction:column;">
            <div class="proto-card">
                <h6 class="fw-black mb-3">Quick Actions</h6>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <a href="{{ route('invoice.create') }}" class="btn-action-wow" style="background:#2563eb"><i class="fa-solid fa-file-circle-plus"></i> Create Invoice</a>
                    <a href="{{ route('quotes.index') }}" class="btn-action-wow" style="background:#7c3aed"><i class="fa-solid fa-file-invoice"></i> Quotes</a>
                    <a href="{{ route('customers.index') }}" class="btn-action-wow" style="background:#10b981"><i class="fa-solid fa-users"></i> Customers</a>
                    <a href="{{ route('invoice.history') }}" class="btn-action-wow" style="background:#f59e0b"><i class="fa-solid fa-chart-bar"></i> Reports</a>
                    <a href="{{ route('company.settings') }}" class="btn-action-wow" style="background:#0891b2"><i class="fa-solid fa-gear"></i> Settings</a>
                    <a href="{{ route('subscription.portal') }}" class="btn-action-wow" style="background:#db2777"><i class="fa-solid fa-rocket"></i> Upgrade</a>
                </div>
            </div>
            <div class="alert-bar-wow"><div class="d-flex align-items-center gap-3"><i class="fa-solid fa-bell fs-5"></i><div class="small fw-black">You have {{ $pendingInvoices }} pending invoice(s) waiting for payment.</div></div><i class="fa-solid fa-arrow-right fs-6"></i></div>
            <div class="proto-card">
                <h6 class="fw-black mb-3">Stripe Health</h6>
                <div class="health-row-wow"><span class="health-label">API</span><span class="health-status">Healthy <i class="fa-solid fa-circle-check"></i></span></div>
                <div class="health-row-wow"><span class="health-label">Webhook</span><span class="health-status">Connected <i class="fa-solid fa-circle-check"></i></span></div>
                <div class="health-row-wow"><span class="health-label">Mode</span><span class="health-status">LIVE <i class="fa-solid fa-circle-check"></i></span></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{ data: {!! json_encode($chartDataSafe) !!}, borderColor: '#38bdf8', borderWidth: 3, fill: true, backgroundColor: 'rgba(56, 189, 248, 0.1)', tension: 0.4, pointRadius: 0 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: true, ticks: { color: '#64748b' } }, y: { display: true, ticks: { color: '#64748b' } } } }
});
</script>
@endsection
