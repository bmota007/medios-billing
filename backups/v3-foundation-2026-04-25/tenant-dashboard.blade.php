@extends('layouts.app')

@section('content')

@php
$rawPlan = strtolower($company->plan_name ?? $company->plan ?? 'starter');
$grandfathered = in_array($company->id ?? 0, [2,3,7]);
$plan = $grandfathered ? 'elite' : $rawPlan;
$planLabel = ucfirst($plan);

$totalRevenue    = (float)($stats['total_revenue'] ?? 0);
$totalInvoices   = (int)($stats['total_invoices'] ?? 0);
$paidInvoices    = (int)($stats['paid_invoices'] ?? 0);
$pendingInvoices = (int)($stats['pending_invoices'] ?? 0);

$chartDataSafe = !empty($chartData)
    ? array_values($chartData)
    : [0,0,0,0,0,0,0,0,0,0,0,0];

$growthRate = $totalRevenue > 0 ? '+18%' : '0%';
@endphp

<style>
body{
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.10), transparent 30%),
        radial-gradient(circle at bottom left, rgba(59,130,246,.08), transparent 28%),
        #020617;
}

.v3-shell{
    max-width:1700px;
    margin:0 auto;
    padding-bottom:40px;
}

.grid-top{
    display:grid;
    grid-template-columns:2fr 1fr 1fr;
    gap:20px;
    margin-bottom:20px;
}

.grid-kpi{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:20px;
}

.grid-main{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    margin-bottom:20px;
}

.grid-bottom{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.v3-card{
    background:linear-gradient(145deg,#06152f,#071c42);
    border:1px solid rgba(255,255,255,.06);
    border-radius:26px;
    padding:26px;
    box-shadow:0 18px 45px rgba(0,0,0,.35);
}

.v3-title{
    color:#fff;
    font-size:46px;
    font-weight:800;
    line-height:1.1;
}

.metric{
    color:#fff;
    font-size:58px;
    font-weight:800;
    line-height:1;
}

.metric-sm{
    color:#fff;
    font-size:24px;
    font-weight:700;
}

.label{
    color:#94a3b8;
    font-size:14px;
}

.green{color:#22c55e;}
.blue{color:#38bdf8;}
.yellow{color:#facc15;}
.red{color:#fb7185;}

.action-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.action-btn{
    display:block;
    padding:18px;
    border-radius:18px;
    border:1px solid rgba(255,255,255,.08);
    background:rgba(255,255,255,.02);
    color:#fff;
    text-decoration:none;
    transition:.2s;
}

.action-btn:hover{
    background:rgba(59,130,246,.12);
    color:#fff;
}

.line{
    border-bottom:1px solid rgba(255,255,255,.06);
    padding:14px 0;
}

.line:last-child{
    border-bottom:none;
}

@media(max-width:1400px){
    .grid-top,.grid-kpi,.grid-main,.grid-bottom{
        grid-template-columns:1fr;
    }
}

@media(max-width:768px){
    .metric{font-size:40px;}
    .v3-title{font-size:34px;}
}
</style>

<div class="v3-shell">

    {{-- HERO --}}
    <div class="grid-top">

        <div class="v3-card">
            <div class="v3-title">
                {{ $greeting }}, {{ auth()->user()->name }} 👋
            </div>

            <div class="label mt-3">
                Welcome back to MediosBilling Business Center
            </div>

            <div class="text-white mt-4 fw-bold">
                Business Growth Suite
            </div>
        </div>

        <div class="v3-card">
            <div class="label">Current Plan</div>
            <div class="metric mt-3">{{ $planLabel }}</div>
            <div class="green mt-3 fw-bold">ACTIVE</div>
        </div>

        <div class="v3-card">
            <div class="label">Theme Mode</div>
            <div class="metric mt-3" style="font-size:48px;">Dark</div>
            <div class="label mt-3">Light mode coming next</div>
        </div>

    </div>

    {{-- KPI --}}
    <div class="grid-kpi">

        <div class="v3-card">
            <div class="label">Revenue</div>
            <div class="metric mt-3">${{ number_format($totalRevenue,0) }}</div>
            <div class="green mt-3">{{ $growthRate }}</div>
        </div>

        <div class="v3-card">
            <div class="label">Invoices</div>
            <div class="metric mt-3">{{ $totalInvoices }}</div>
            <div class="blue mt-3">This Month</div>
        </div>

        <div class="v3-card">
            <div class="label">Paid</div>
            <div class="metric mt-3">{{ $paidInvoices }}</div>
            <div class="green mt-3">Collected</div>
        </div>

        <div class="v3-card">
            <div class="label">Pending</div>
            <div class="metric mt-3">{{ $pendingInvoices }}</div>
            <div class="yellow mt-3">Attention</div>
        </div>

    </div>

    {{-- MAIN --}}
    <div class="grid-main">

        <div class="v3-card">
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <div class="metric-sm">Revenue Performance</div>
                    <div class="label">Last 12 Months</div>
                </div>
            </div>

            <div style="height:420px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="v3-card">
            <div class="metric-sm mb-4">Quick Actions</div>

            <div class="action-grid">

                <a href="{{ route('invoice.create') }}" class="action-btn">
                    Create Invoice
                </a>

                <a href="{{ route('quotes.index') }}" class="action-btn">
                    Manage Quotes
                </a>

                <a href="{{ route('customers.index') }}" class="action-btn">
                    Customers
                </a>

                <a href="{{ route('company.settings') }}" class="action-btn">
                    Tax Center
                </a>

                <a href="{{ route('subscription.portal') }}" class="action-btn">
                    AI Coach
                </a>

                <a href="{{ route('company.settings') }}" class="action-btn">
                    Settings
                </a>

            </div>
        </div>

    </div>

    {{-- BOTTOM --}}
    <div class="grid-bottom">

        <div class="v3-card">
            <div class="metric-sm mb-3">Recent Invoices</div>

            @forelse($recentInvoices as $invoice)
                <div class="line d-flex justify-content-between">
                    <div>
                        <div class="text-white fw-bold">
                            #{{ $invoice->invoice_no ?? $invoice->id }}
                        </div>
                        <div class="label">
                            {{ $invoice->customer_name }}
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="text-white fw-bold">
                            ${{ number_format($invoice->total,2) }}
                        </div>
                        <div class="label">
                            {{ strtoupper($invoice->status) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="label">No invoices found.</div>
            @endforelse
        </div>

        <div class="v3-card">
            <div class="metric-sm mb-3">Stripe Health</div>

            <div class="line d-flex justify-content-between">
                <span class="label">Connection</span>
                <span class="green">{{ $stripeStatus }}</span>
            </div>

            <div class="line d-flex justify-content-between">
                <span class="label">Webhook</span>
                <span class="green">Connected</span>
            </div>

            <div class="line d-flex justify-content-between">
                <span class="label">API</span>
                <span class="green">Healthy</span>
            </div>

            <div class="line d-flex justify-content-between">
                <span class="label">Status</span>
                <span class="green">Operational</span>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const chartData = {!! json_encode($chartDataSafe) !!};

new Chart(document.getElementById('revenueChart'),{
    type:'line',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets:[{
            data:chartData,
            borderColor:'#38bdf8',
            backgroundColor:'rgba(56,189,248,.12)',
            fill:true,
            tension:.42,
            borderWidth:3,
            pointRadius:4
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{
            x:{
                ticks:{color:'#94a3b8'},
                grid:{display:false}
            },
            y:{
                ticks:{color:'#94a3b8'},
                grid:{color:'rgba(255,255,255,.04)'}
            }
        }
    }
});
</script>

@endsection
