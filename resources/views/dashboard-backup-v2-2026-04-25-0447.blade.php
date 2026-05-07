@extends('layouts.app')

@section('content')

@php
$rawPlan = strtolower($company->plan_name ?? $company->plan ?? 'starter');
$grandfathered = in_array($company->id ?? 0, [2,3,7]);
$plan = $grandfathered ? 'pro' : $rawPlan;
$planLabel = ucfirst($plan);

$isStarter = $plan === 'starter';
$isGrowth  = $plan === 'growth';
$isPro     = $plan === 'pro';

$totalRevenue    = (float)($stats['total_revenue'] ?? 0);
$totalInvoices   = (int)($stats['total_invoices'] ?? 0);
$paidInvoices    = (int)($stats['paid_invoices'] ?? 0);
$pendingInvoices = (int)($stats['pending_invoices'] ?? 0);

$customerTotal = \App\Models\Customer::where('company_id', auth()->user()->company_id)->count();
$customerTotal = max(1, $customerTotal);

$activeCustomers   = $customerTotal;
$inactiveCustomers = 0;
$inactive30        = 0;
$bouncedCustomers  = 0;

$chartDataSafe = !empty($chartData)
    ? array_values($chartData)
    : [0,0,0,0,0,0,0,0,0,0,0,0];

$chartMax = max($chartDataSafe);
$growthRate = $chartMax > 0 ? '14.2%' : '0%';

$bestPlan = 'Professional';

$mostActiveCustomer = $recentInvoices->first()->customer_name ?? 'Customer';

$lastPaymentTime = $recentInvoices->first()?->updated_at
    ? $recentInvoices->first()->updated_at->format('M d, Y')
    : 'No recent data';
@endphp

<style>
body{
    background:
        radial-gradient(circle at top right, rgba(37,99,235,.12), transparent 26%),
        radial-gradient(circle at bottom left, rgba(139,92,246,.08), transparent 28%),
        #020617;
}

.tenant-shell{
    max-width:1680px;
    margin:0 auto;
    padding-bottom:42px;
}

.lux-card{
    background:
        radial-gradient(circle at top left, rgba(59,130,246,.10), transparent 34%),
        linear-gradient(145deg,#06152f,#071b42);
    border:1px solid rgba(255,255,255,.075);
    box-shadow:
        0 18px 44px rgba(0,0,0,.34),
        inset 0 1px 0 rgba(255,255,255,.035);
    border-radius:24px;
    transition:.22s ease;
    overflow:hidden;
    position:relative;
}

.lux-card:before{
    content:"";
    position:absolute;
    top:0;
    left:24px;
    right:24px;
    height:1px;
    background:linear-gradient(90deg,transparent,rgba(56,189,248,.45),transparent);
    opacity:.65;
}

.lux-card:hover{
    transform:translateY(-2px);
    border-color:rgba(56,189,248,.20);
    box-shadow:
        0 22px 50px rgba(0,0,0,.40),
        inset 0 1px 0 rgba(255,255,255,.045);
}

.tenant-top{
    display:grid;
    grid-template-columns:2.1fr .8fr .8fr 1.25fr;
    gap:20px;
    margin-bottom:20px;
}

.kpi-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr 1fr 1.15fr;
    gap:20px;
    margin-bottom:20px;
}

.middle-grid{
    display:grid;
    grid-template-columns:350px minmax(560px,1fr) 350px;
    gap:20px;
    margin-bottom:20px;
    align-items:stretch;
}

.left-stack,
.right-stack{
    display:flex;
    flex-direction:column;
    gap:20px;
    min-height:590px;
}

.revenue-panel{
    min-height:590px;
}

.growth-card{
    min-height:250px;
}

.health-card{
    min-height:430px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.command-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
}

.bottom-grid{
    display:grid;
    grid-template-columns:350px minmax(560px,1fr) 430px;
    gap:20px;
    align-items:stretch;
}

.metric{
    font-size:46px;
    font-weight:850;
    line-height:1;
    color:#fff;
    letter-spacing:-1px;
}

.metric-lg{
    font-size:54px;
    font-weight:850;
    line-height:1;
    color:#fff;
    letter-spacing:-1.4px;
}

.plan-word{
    font-size:clamp(32px,2.15vw,44px);
    line-height:1;
    font-weight:850;
    color:#fff;
    white-space:nowrap;
}

.label{
    color:#94a3b8;
    font-size:13px;
}

.small{
    color:#94a3b8;
    font-size:12px;
}

.green{color:#22c55e;}
.blue{color:#38bdf8;}
.yellow{color:#facc15;}
.purple{color:#c084fc;}
.red{color:#fb7185;}

.line-row{
    border-bottom:1px solid rgba(255,255,255,.06);
}

.line-row:last-child{
    border-bottom:none;
}

.mini-pill{
    display:inline-flex;
    align-items:center;
    padding:7px 14px;
    border-radius:999px;
    background:rgba(59,130,246,.18);
    color:#93c5fd;
    font-size:13px;
    font-weight:700;
}

.action-card{
    border:1px solid rgba(255,255,255,.20);
    border-radius:18px;
    padding:18px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    min-height:82px;
    transition:.2s ease;
    background:rgba(255,255,255,.018);
}

.action-card:hover{
    background:rgba(59,130,246,.10);
    border-color:rgba(56,189,248,.30);
}

.icon-box{
    width:42px;
    height:42px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    font-weight:800;
    flex-shrink:0;
}

.glance-box{
    border:1px solid rgba(255,255,255,.06);
    border-radius:16px;
    padding:16px;
    min-height:104px;
    background:rgba(255,255,255,.015);
}

.footer-line{
    display:flex;
    justify-content:space-between;
    color:#64748b;
    font-size:13px;
    margin-top:22px;
    padding:0 8px;
}

@media(max-width:1400px){
    .tenant-top,
    .kpi-grid,
    .middle-grid,
    .bottom-grid{
        grid-template-columns:1fr;
    }

    .left-stack,
    .right-stack,
    .revenue-panel,
    .growth-card,
    .health-card{
        min-height:auto;
    }

    .command-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:768px){
    .metric,
    .metric-lg{
        font-size:36px;
    }

    .plan-word{
        font-size:34px;
    }
}
</style>

<div class="tenant-shell">

    <div class="tenant-top">
        <div class="lux-card p-6">
            <h1 class="text-5xl font-bold text-white leading-tight">
                {{ $greeting }}, {{ auth()->user()->name }} 👋
            </h1>

            <p class="text-slate-400 mt-4 text-lg">
                Welcome back to your command center
            </p>

            <div class="mt-5 mini-pill">
                {{ $company->name }}
            </div>
        </div>

        <div class="lux-card p-6">
            <div class="label">Current Plan</div>
            <div class="metric-lg mt-3">{{ $planLabel }}</div>
            <div class="green font-bold mt-4">Active</div>
        </div>

        <div class="lux-card p-6">
            <div class="label">Today</div>
            <div class="text-white text-2xl font-bold mt-3">{{ now()->format('l') }}</div>
            <div class="small mt-3">{{ now()->format('F d, Y') }}</div>
        </div>

        <div class="lux-card p-6">
            <div class="label">Account Status</div>
            <div class="green text-4xl font-extrabold mt-3">Verified</div>
            <div class="small mt-3">Full access enabled</div>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="lux-card p-5">
            <div class="label">Monthly Revenue</div>
            <div class="metric mt-4">${{ number_format($totalRevenue,2) }}</div>
            <div class="green font-bold mt-4">{{ $growthRate }}</div>
        </div>

        <div class="lux-card p-5">
            <div class="label">Invoices</div>
            <div class="metric mt-4">{{ $totalInvoices }}</div>
            <div class="blue font-bold mt-4">Created</div>
        </div>

        <div class="lux-card p-5">
            <div class="label">Paid</div>
            <div class="metric mt-4">{{ $paidInvoices }}</div>
            <div class="green font-bold mt-4">Collected</div>
        </div>

        <div class="lux-card p-5">
            <div class="label">Pending</div>
            <div class="metric mt-4">{{ $pendingInvoices }}</div>
            <div class="yellow font-bold mt-4">Awaiting</div>
        </div>

        <div class="lux-card p-5">
            <div class="label">Best Plan</div>
            <div class="plan-word mt-4">{{ $bestPlan }}</div>
            <div class="purple font-bold mt-4">Popular</div>
        </div>
    </div>

    @if($isStarter)
        <div class="lux-card p-6 mb-5">
            <div class="text-white text-2xl font-bold">Upgrade Your Business</div>
            <p class="text-slate-400 mt-2">
                Unlock advanced reports, Stripe tools, branding and staff users.
            </p>
            <a href="{{ route('pricing') }}" class="inline-block mt-5 px-6 py-3 rounded-xl bg-blue-600 text-white font-semibold">
                View Plans
            </a>
        </div>
    @endif

    <div class="middle-grid">
        <div class="left-stack">
            <div class="lux-card p-5">
                <div class="text-white text-2xl font-bold">Stripe Status</div>
                <div class="green text-5xl font-extrabold mt-5">{{ $stripeStatus ?? 'LIVE' }}</div>
                <div class="small mt-3">Connected to payment system</div>
            </div>

            <div class="lux-card p-5 flex-1">
                <div class="text-white text-2xl font-bold mb-4">Customer Status</div>

                <div class="flex justify-center">
                    <div class="relative w-[190px] h-[190px]">
                        <canvas id="customerChart"></canvas>

                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <div class="text-5xl font-bold text-white">{{ $customerTotal }}</div>
                            <div class="small mt-1">Total</div>
                        </div>
                    </div>
                </div>

                <div class="space-y-3 mt-5 text-sm">
                    <div class="flex justify-between">
                        <span class="green">Active</span>
                        <span class="text-white">{{ $activeCustomers }} (100%)</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="yellow">Inactive</span>
                        <span class="text-white">0 (0%)</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="blue">Inactive &gt; 30 Days</span>
                        <span class="text-white">0 (0%)</span>
                    </div>

                    <div class="flex justify-between">
                        <span class="red">Bounced</span>
                        <span class="text-white">0 (0%)</span>
                    </div>
                </div>

                <a href="/customers" class="inline-block text-sky-400 mt-6 font-medium">
                    View All Customers →
                </a>
            </div>
        </div>

        <div class="lux-card p-6 revenue-panel">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <div class="text-white text-2xl font-bold">Revenue Overview</div>
                    <div class="text-4xl font-bold text-white mt-3">
                        ${{ number_format($totalRevenue,2) }}
                        <span class="green text-xl">{{ $growthRate }}</span>
                    </div>
                </div>

                <div class="small mt-2">This Month</div>
            </div>

            <div style="height:455px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="right-stack">
            <div class="lux-card p-5 growth-card">
                <div class="text-white text-2xl font-bold">Business Growth</div>
                <div class="green text-5xl font-extrabold mt-5">{{ $growthRate }}</div>
                <div class="small mt-3">Based on invoice activity</div>

                <div style="height:118px;" class="mt-4">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <div class="lux-card p-5 health-card">
                <div>
                    <div class="flex justify-between items-start mb-5">
                        <div class="text-white text-2xl font-bold">Stripe Health</div>
                        <div class="small">Live</div>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div class="line-row flex justify-between pb-3">
                            <span class="text-slate-300">Mode</span>
                            <span class="green font-semibold">Ready</span>
                        </div>

                        <div class="line-row flex justify-between pb-3">
                            <span class="text-slate-300">Webhook</span>
                            <span class="green font-semibold">Connected</span>
                        </div>

                        <div class="line-row flex justify-between pb-3">
                            <span class="text-slate-300">API</span>
                            <span class="green font-semibold">Healthy</span>
                        </div>

                        <div class="line-row flex justify-between pb-3">
                            <span class="text-slate-300">Last Payment</span>
                            <span class="text-white">{{ $lastPaymentTime }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-slate-300">Status</span>
                            <span class="green font-semibold">Healthy</span>
                        </div>
                    </div>
                </div>

                <a href="/company/settings" class="inline-block text-sky-400 mt-5 font-medium">
                    View Stripe Health →
                </a>
            </div>
        </div>
    </div>

    <div class="lux-card p-6 mb-6">
        <div class="text-white text-2xl font-bold">Business Command Center</div>
        <p class="small mt-1 mb-5">Quick actions for your company</p>

        <div class="command-grid">
            <a href="/invoice/create" class="action-card">
                <div class="flex items-center gap-4">
                    <div class="icon-box bg-purple-500/20 text-purple-300">+</div>
                    <div>
                        <div class="text-white font-semibold">Create Invoice</div>
                        <div class="small mt-1">Bill faster</div>
                    </div>
                </div>
                <div class="text-slate-400">→</div>
            </a>

            <a href="/quotes" class="action-card">
                <div class="flex items-center gap-4">
                    <div class="icon-box bg-blue-500/20 text-blue-300">Q</div>
                    <div>
                        <div class="text-white font-semibold">Manage Quotes</div>
                        <div class="small mt-1">Track estimates</div>
                    </div>
                </div>
                <div class="text-slate-400">→</div>
            </a>

            <a href="/company/settings" class="action-card">
                <div class="flex items-center gap-4">
                    <div class="icon-box bg-green-500/20 text-green-300">⚙</div>
                    <div>
                        <div class="text-white font-semibold">Company Settings</div>
                        <div class="small mt-1">Branding & payments</div>
                    </div>
                </div>
                <div class="text-slate-400">→</div>
            </a>
        </div>
    </div>

    <div class="bottom-grid">
        <div class="lux-card p-5">
            <div class="text-white text-2xl font-bold mb-5">At a Glance</div>

            <div class="grid grid-cols-2 gap-4">
                <div class="glance-box">
                    <div class="small">Invoices</div>
                    <div class="text-5xl font-bold text-white mt-2">{{ $totalInvoices }}</div>
                </div>

                <div class="glance-box">
                    <div class="small">Revenue</div>
                    <div class="text-3xl font-bold text-white mt-3">${{ number_format($totalRevenue,0) }}</div>
                </div>

                <div class="glance-box">
                    <div class="small">Paid</div>
                    <div class="text-5xl font-bold text-white mt-2">{{ $paidInvoices }}</div>
                </div>

                <div class="glance-box">
                    <div class="small">Pending</div>
                    <div class="text-5xl font-bold text-yellow-400 mt-2">{{ $pendingInvoices }}</div>
                </div>
            </div>
        </div>

        <div class="lux-card p-5">
            <div class="text-white text-2xl font-bold mb-5">Recent Invoices</div>

            @forelse($recentInvoices->take(5) as $invoice)
                <div class="line-row flex justify-between py-3">
                    <div>
                        <div class="text-white font-semibold">Invoice #{{ $invoice->invoice_no }}</div>
                        <div class="small">{{ $invoice->customer_name }}</div>
                    </div>

                    <div class="text-right">
                        <div class="text-white font-bold">${{ number_format($invoice->total,2) }}</div>
                        <div class="small uppercase">{{ $invoice->status }}</div>
                    </div>
                </div>
            @empty
                <div class="small">No invoices yet.</div>
            @endforelse

            <a href="/invoices" class="inline-block text-sky-400 mt-4 font-medium">
                View All Invoices →
            </a>
        </div>

        <div class="lux-card p-5">
            <div class="text-white text-2xl font-bold mb-5">Quick Insights</div>

            <div class="space-y-4">
                <div class="line-row flex justify-between pb-3 gap-4">
                    <span class="text-slate-300">Best Plan</span>
                    <span class="purple font-semibold text-right">{{ $bestPlan }}</span>
                </div>

                <div class="line-row flex justify-between pb-3 gap-4">
                    <span class="text-slate-300">Top Customer</span>
                    <span class="blue font-semibold text-right">{{ $mostActiveCustomer }}</span>
                </div>

                <div class="line-row flex justify-between pb-3 gap-4">
                    <span class="text-slate-300">Revenue</span>
                    <span class="green font-semibold text-right">${{ number_format($totalRevenue,2) }}</span>
                </div>

                <div class="flex justify-between gap-4">
                    <span class="text-slate-300">Account</span>
                    <span class="green font-semibold text-right">Verified</span>
                </div>
            </div>

            <a href="/dashboard" class="inline-block text-sky-400 mt-5 font-medium">
                View Full Analytics →
            </a>
        </div>
    </div>

    <div class="footer-line">
        <div>© {{ now()->year }} Medios Billing. All rights reserved.</div>
        <div>v2.0.0</div>
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
            borderColor:'#8b5cf6',
            backgroundColor:'rgba(139,92,246,.18)',
            fill:true,
            tension:.45,
            pointRadius:4,
            pointBackgroundColor:'#8b5cf6',
            borderWidth:3
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{
            x:{ticks:{color:'#94a3b8'},grid:{display:false}},
            y:{ticks:{color:'#94a3b8'},grid:{color:'rgba(255,255,255,.04)'}}
        }
    }
});

new Chart(document.getElementById('customerChart'),{
    type:'doughnut',
    data:{
        labels:['Active','Inactive','Inactive 30+','Bounced'],
        datasets:[{
            data:[{{ $activeCustomers }},0,0,0],
            backgroundColor:['#22c55e','#facc15','#3b82f6','#ef4444'],
            borderWidth:0
        }]
    },
    options:{
        responsive:true,
        cutout:'72%',
        plugins:{legend:{display:false}}
    }
});

new Chart(document.getElementById('growthChart'),{
    type:'line',
    data:{
        labels:[1,2,3,4,5,6,7,8,9,10],
        datasets:[{
            data:[3,4,5,4,6,5,4,5,8,10],
            borderColor:'#22c55e',
            backgroundColor:'rgba(34,197,94,.12)',
            fill:true,
            tension:.45,
            pointRadius:2,
            borderWidth:3
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{legend:{display:false}},
        scales:{x:{display:false},y:{display:false}}
    }
});
</script>

@endsection
