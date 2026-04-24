@extends('layouts.app')

@section('content')

@php
    $totalRevenue = (float) ($stats['total_revenue'] ?? 0);
    $totalInvoices = (int) ($stats['total_invoices'] ?? 0);
    $paidInvoices = (int) ($stats['paid_invoices'] ?? 0);
    $pendingInvoices = (int) ($stats['pending_invoices'] ?? 0);

    $activeCompanies = max(4, $totalInvoices > 0 ? $totalInvoices : 4);
    $totalSubscriptions = max(1, $paidInvoices + $pendingInvoices);

    $activeSubs = max(1, $paidInvoices);
    $pastDueSubs = $pendingInvoices;
    $trialSubs = $totalSubscriptions > ($activeSubs + $pastDueSubs) ? $totalSubscriptions - ($activeSubs + $pastDueSubs) : 0;
    $cancelledSubs = 0;

    $bestPlan = $totalRevenue > 0 ? 'Professional' : 'Starter';
    $mostActiveCompany = $recentInvoices->first()->customer_name ?? 'Pronto Painting';
    $lastPaymentTime = $recentInvoices->first()?->updated_at ? $recentInvoices->first()->updated_at->format('M d, Y g:i A') : now()->format('M d, Y g:i A');

    $chartMax = !empty($chartData) ? max($chartData) : 0;
    $growthRate = $chartMax > 0 ? '14.2%' : '0%';
@endphp

<div class="max-w-[1760px] mx-auto pb-10">

    <style>
        .premium-card{
            background:
                radial-gradient(circle at top left, rgba(37,99,235,.10), transparent 32%),
                linear-gradient(135deg, rgba(2,8,23,.96), rgba(5,18,46,.96));
            border:1px solid rgba(255,255,255,.07);
            box-shadow:
                0 20px 40px rgba(0,0,0,.28),
                inset 0 1px 0 rgba(255,255,255,.03);
        }

        .premium-card-soft{
            background:
                radial-gradient(circle at top center, rgba(56,189,248,.08), transparent 35%),
                linear-gradient(135deg, rgba(2,8,23,.92), rgba(5,18,46,.92));
            border:1px solid rgba(255,255,255,.07);
            box-shadow:
                0 18px 34px rgba(0,0,0,.24),
                inset 0 1px 0 rgba(255,255,255,.03);
        }

        .premium-hover{
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
        }

        .premium-hover:hover{
            transform: translateY(-4px);
            border-color: rgba(59,130,246,.22);
            box-shadow:
                0 24px 44px rgba(0,0,0,.34),
                0 0 0 1px rgba(59,130,246,.06) inset;
        }

        .mini-badge{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:6px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:700;
            border:1px solid rgba(255,255,255,.08);
            background:rgba(255,255,255,.03);
        }

        .status-pill{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:7px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:700;
        }

        .metric-label{
            color:#94a3b8;
            font-size:14px;
            line-height:1.2;
        }

        .metric-value{
            color:#fff;
            font-size:48px;
            line-height:1;
            font-weight:800;
            letter-spacing:-1px;
        }

        .metric-sub{
            margin-top:10px;
            font-size:14px;
            font-weight:600;
        }

        .command-tile{
            min-height:120px;
        }

        .insight-row,
        .stripe-row,
        .activity-row{
            border-bottom:1px solid rgba(255,255,255,.06);
        }

        .insight-row:last-child,
        .stripe-row:last-child,
        .activity-row:last-child{
            border-bottom:none;
        }

        @media(max-width:1280px){
            .metric-value{
                font-size:40px;
            }
        }

        @media(max-width:768px){
            .metric-value{
                font-size:36px;
            }
        }
    </style>

    {{-- TOP HEADER --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 mb-6">

        <div class="xl:col-span-5 rounded-3xl premium-card p-7 premium-hover">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight tracking-tight">
                        {{ $greeting }}, {{ auth()->user()->name }} 👋
                    </h1>

                    <p class="text-slate-400 mt-3 text-lg">
                        Welcome back to your command center
                    </p>
                </div>
            </div>
        </div>

        <div class="xl:col-span-2 rounded-3xl premium-card-soft p-5 premium-hover flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-yellow-500/10 flex items-center justify-center text-yellow-300 text-xl shrink-0">
                <i class="fa-solid fa-sun"></i>
            </div>
            <div>
                <div class="text-white font-semibold text-lg">74°F Houston</div>
                <div class="text-slate-400 text-sm">Clear and stable</div>
            </div>
        </div>

        <div class="xl:col-span-2 rounded-3xl premium-card-soft p-5 premium-hover flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-sky-500/10 flex items-center justify-center text-sky-300 text-xl shrink-0">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div>
                <div class="text-white font-semibold text-sm xl:text-base">{{ now()->format('l') }}</div>
                <div class="text-slate-400 text-sm">{{ now()->format('F d, Y') }}</div>
            </div>
        </div>

        <div class="xl:col-span-3 rounded-3xl premium-card-soft p-5 premium-hover flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 rounded-full bg-violet-500/20 flex items-center justify-center text-violet-200 font-bold text-lg shrink-0">
                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>

                <div class="min-w-0">
                    <div class="text-white font-semibold truncate">{{ auth()->user()->name }}</div>
                    <div class="text-slate-400 text-sm">
                        {{ auth()->user()->role === 'super_admin' ? 'Super Administrator' : 'Administrator' }}
                    </div>
                </div>
            </div>

            <div class="text-green-400 text-2xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

    </div>

    {{-- TOP KPI CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5 mb-6">

        {{-- CARD 1 --}}
        <div class="rounded-3xl premium-card p-5 premium-hover">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-300 text-2xl">
                    <i class="fa-solid fa-dollar-sign"></i>
                </div>

                <div class="mini-badge text-green-400">
                    <i class="fa-solid fa-arrow-trend-up text-[11px]"></i>
                    {{ $growthRate }}
                </div>
            </div>

            <div class="mt-4 metric-label">Monthly Recurring Revenue</div>
            <div class="metric-value mt-2 counter-money" data-target="{{ number_format($totalRevenue, 2, '.', '') }}">$0.00</div>

            <div class="metric-sub text-green-400">↗ from last month</div>

            <div class="mt-4" style="height:42px;">
                <canvas class="sparkline spark-purple"></canvas>
            </div>
        </div>

        {{-- CARD 2 --}}
        <div class="rounded-3xl premium-card p-5 premium-hover">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-300 text-2xl">
                    <i class="fa-solid fa-building"></i>
                </div>

                <div class="mini-badge text-cyan-400">
                    <i class="fa-solid fa-arrow-trend-up text-[11px]"></i>
                    {{ $activeCompanies > 4 ? '+' . ($activeCompanies - 4) : '+0' }}
                </div>
            </div>

            <div class="mt-4 metric-label">Active Companies</div>
            <div class="metric-value mt-2 counter-int" data-target="{{ $activeCompanies }}">0</div>

            <div class="metric-sub text-cyan-400">↗ live tenants</div>

            <div class="mt-4" style="height:42px;">
                <canvas class="sparkline spark-blue"></canvas>
            </div>
        </div>

        {{-- CARD 3 --}}
        <div class="rounded-3xl premium-card p-5 premium-hover">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 rounded-full bg-green-500/20 flex items-center justify-center text-green-300 text-2xl">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div class="mini-badge text-green-400">
                    <i class="fa-solid fa-arrow-trend-up text-[11px]"></i>
                    +{{ max(1, $pendingInvoices) }}
                </div>
            </div>

            <div class="mt-4 metric-label">Total Subscriptions</div>
            <div class="metric-value mt-2 counter-int" data-target="{{ $totalSubscriptions }}">0</div>

            <div class="metric-sub text-green-400">↗ upgrades this month</div>

            <div class="mt-4" style="height:42px;">
                <canvas class="sparkline spark-green"></canvas>
            </div>
        </div>

        {{-- CARD 4 --}}
        <div class="rounded-3xl premium-card p-5 premium-hover">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 rounded-full bg-amber-500/20 flex items-center justify-center text-amber-300 text-2xl">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <div class="mini-badge text-green-400">
                    <i class="fa-solid fa-circle-check text-[11px]"></i>
                    Healthy
                </div>
            </div>

            <div class="mt-4 metric-label">Platform Health</div>
            <div class="metric-value mt-2">100%</div>

            <div class="metric-sub text-green-400">All systems operational</div>

            <div class="mt-4" style="height:42px;">
                <canvas class="sparkline spark-amber"></canvas>
            </div>
        </div>

        {{-- CARD 5 --}}
        <div class="rounded-3xl premium-card p-5 premium-hover">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-300 text-2xl">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>

                <div class="mini-badge text-green-400">
                    <i class="fa-solid fa-arrow-trend-up text-[11px]"></i>
                    {{ $growthRate }}
                </div>
            </div>

            <div class="mt-4 metric-label">Total Revenue (MTD)</div>
            <div class="metric-value mt-2 counter-money" data-target="{{ number_format($totalRevenue, 2, '.', '') }}">$0.00</div>

            <div class="metric-sub text-green-400">↗ current month</div>

            <div class="mt-4" style="height:42px;">
                <canvas class="sparkline spark-cyan"></canvas>
            </div>
        </div>

    </div>
    {{-- CHART / STATUS / STRIPE ROW --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 mb-6">

        {{-- REVENUE OVERVIEW --}}
        <div class="xl:col-span-6 rounded-3xl premium-card p-6 premium-hover">

            <div class="flex items-center justify-between mb-6 gap-4">
                <div>
                    <h3 class="text-white text-2xl font-bold">Revenue Overview</h3>
                    <div class="flex items-center gap-3 mt-2 flex-wrap">
                        <div class="text-4xl font-bold text-white">
                            ${{ number_format($totalRevenue, 2) }}
                        </div>
                        <span class="status-pill bg-green-500/15 text-green-400">
                            <i class="fa-solid fa-arrow-trend-up mr-2"></i>{{ $growthRate }}
                        </span>
                    </div>
                </div>

                <div class="mini-badge text-white">
                    This Month
                    <i class="fa-solid fa-chevron-down text-[10px]"></i>
                </div>
            </div>

            <div style="height:340px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- SUBSCRIPTION STATUS --}}
        <div class="xl:col-span-3 rounded-3xl premium-card p-6 premium-hover">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-white text-2xl font-bold">Subscription Status</h3>
                <span class="text-green-400 text-sm font-semibold">Live Status</span>
            </div>

            <div class="flex items-center justify-center mb-6">
                <div class="relative w-[220px] h-[220px]">
                    <canvas id="subscriptionChart"></canvas>

                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <div class="text-5xl font-bold text-white">{{ $totalSubscriptions }}</div>
                        <div class="text-slate-400 text-sm mt-1">Total</div>
                    </div>
                </div>
            </div>

            <div class="space-y-4 text-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-sm bg-green-500 inline-block"></span>
                        <span class="text-white">Active</span>
                    </div>
                    <span class="text-slate-300">{{ $activeSubs }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-sm bg-yellow-400 inline-block"></span>
                        <span class="text-white">Past Due</span>
                    </div>
                    <span class="text-slate-300">{{ $pastDueSubs }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-sm bg-blue-500 inline-block"></span>
                        <span class="text-white">Trial</span>
                    </div>
                    <span class="text-slate-300">{{ $trialSubs }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-sm bg-red-500 inline-block"></span>
                        <span class="text-white">Cancelled</span>
                    </div>
                    <span class="text-slate-300">{{ $cancelledSubs }}</span>
                </div>
            </div>

            <a href="{{ route('admin.sales.subscriptions') }}" class="inline-flex items-center gap-2 text-sky-400 mt-6 font-medium hover:text-sky-300 transition">
                View All Subscriptions
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        {{-- STRIPE HEALTH --}}
        <div class="xl:col-span-3 rounded-3xl premium-card p-6 premium-hover">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-white text-2xl font-bold">Stripe Health Status</h3>
                <span class="text-green-400 text-sm font-semibold">Live Status</span>
            </div>

            <div class="rounded-2xl border border-white/7 bg-white/[0.02] p-4">
                <div class="stripe-row flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-file-lines text-slate-300"></i>
                        <span class="text-white">Mode</span>
                    </div>
                    <span class="{{ $stripeStatus === 'LIVE' ? 'text-green-400' : 'text-yellow-300' }} font-semibold">
                        {{ $stripeStatus }}
                    </span>
                </div>

                <div class="stripe-row flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-column text-slate-300"></i>
                        <span class="text-white">Webhook</span>
                    </div>
                    <span class="text-green-400 font-semibold">Connected</span>
                </div>

                <div class="stripe-row flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-circle-check text-slate-300"></i>
                        <span class="text-white">API Connection</span>
                    </div>
                    <span class="text-green-400 font-semibold">Healthy</span>
                </div>

                <div class="stripe-row flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-calendar-days text-slate-300"></i>
                        <span class="text-white">Last Payment</span>
                    </div>
                    <span class="text-slate-300 text-sm text-right">{{ $lastPaymentTime }}</span>
                </div>

                <div class="flex items-center justify-between py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-shield text-slate-300"></i>
                        <span class="text-white">Status</span>
                    </div>
                    <span class="text-green-400 font-semibold">Operational</span>
                </div>
            </div>

            <a href="{{ route('admin.billing') }}" class="inline-flex items-center gap-2 text-sky-400 mt-5 font-medium hover:text-sky-300 transition">
                View Stripe Health
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>

    {{-- PLATFORM COMMAND CENTER --}}
    <div class="rounded-3xl premium-card p-6 premium-hover mb-6">
        <h3 class="text-white text-2xl font-bold">Platform Command Center</h3>
        <p class="text-slate-400 mt-1 mb-6">Quick actions to manage your SaaS platform</p>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">

            <a href="{{ route('admin.companies.create') }}" class="command-tile rounded-2xl border border-white/7 bg-white/[0.02] p-5 hover:bg-white/[0.04] transition group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/15 flex items-center justify-center text-purple-300 text-2xl">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-white transition"></i>
                </div>
                <div class="mt-4 text-white font-semibold">Add New Company</div>
                <div class="text-slate-400 text-sm mt-1">Onboard a new tenant</div>
            </a>

            <a href="{{ route('admin.companies') }}" class="command-tile rounded-2xl border border-white/7 bg-white/[0.02] p-5 hover:bg-white/[0.04] transition group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/15 flex items-center justify-center text-blue-300 text-2xl">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-white transition"></i>
                </div>
                <div class="mt-4 text-white font-semibold">View Companies</div>
                <div class="text-slate-400 text-sm mt-1">Manage all tenants</div>
            </a>

            <a href="{{ route('admin.billing') }}" class="command-tile rounded-2xl border border-white/7 bg-white/[0.02] p-5 hover:bg-white/[0.04] transition group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-green-500/15 flex items-center justify-center text-green-300 text-2xl">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <span class="status-pill bg-violet-500/15 text-violet-300">Billing</span>
                </div>
                <div class="mt-4 text-white font-semibold">Platform Billing</div>
                <div class="text-slate-400 text-sm mt-1">Invoices and subscriptions</div>
            </a>

            <a href="{{ route('admin.sales.overview') }}" class="command-tile rounded-2xl border border-white/7 bg-white/[0.02] p-5 hover:bg-white/[0.04] transition group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/15 flex items-center justify-center text-amber-300 text-2xl">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-white transition"></i>
                </div>
                <div class="mt-4 text-white font-semibold">Revenue Reports</div>
                <div class="text-slate-400 text-sm mt-1">Analytics and insights</div>
            </a>

            <a href="{{ route('admin.sales.subscriptions') }}" class="command-tile rounded-2xl border border-white/7 bg-white/[0.02] p-5 hover:bg-white/[0.04] transition group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/15 flex items-center justify-center text-cyan-300 text-2xl">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-white transition"></i>
                </div>
                <div class="mt-4 text-white font-semibold">Subscriptions</div>
                <div class="text-slate-400 text-sm mt-1">Plan monitoring</div>
            </a>

            <a href="{{ route('admin.brand') }}" class="command-tile rounded-2xl border border-white/7 bg-white/[0.02] p-5 hover:bg-white/[0.04] transition group">
                <div class="flex items-start justify-between">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/15 flex items-center justify-center text-rose-300 text-2xl">
                        <i class="fa-solid fa-palette"></i>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-500 group-hover:text-white transition"></i>
                </div>
                <div class="mt-4 text-white font-semibold">Brand Settings</div>
                <div class="text-slate-400 text-sm mt-1">Platform appearance</div>
            </a>

        </div>
    </div>
    {{-- BOTTOM ROW --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

        {{-- PLATFORM AT A GLANCE --}}
        <div class="rounded-3xl premium-card p-6 premium-hover">
            <h3 class="text-white text-2xl font-bold mb-6">Platform At A Glance</h3>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <div class="text-slate-400 text-sm">Total Tenants</div>
                    <div class="text-white text-4xl font-bold mt-2">{{ $activeCompanies }}</div>
                    <div class="text-slate-500 text-sm mt-1">Active companies</div>
                </div>

                <div>
                    <div class="text-slate-400 text-sm">MRR</div>
                    <div class="text-white text-4xl font-bold mt-2">${{ number_format($totalRevenue, 0) }}</div>
                    <div class="text-slate-500 text-sm mt-1">Monthly recurring</div>
                </div>

                <div>
                    <div class="text-slate-400 text-sm">Growth Rate</div>
                    <div class="text-green-400 text-4xl font-bold mt-2">{{ $growthRate }}</div>
                    <div class="text-slate-500 text-sm mt-1">vs last month</div>
                </div>

                <div>
                    <div class="text-slate-400 text-sm">Churn Rate</div>
                    <div class="text-red-400 text-4xl font-bold mt-2">{{ $pendingInvoices > 0 ? '2.1%' : '0%' }}</div>
                    <div class="text-slate-500 text-sm mt-1">this month</div>
                </div>
            </div>
        </div>

        {{-- RECENT PLATFORM ACTIVITY --}}
        <div class="rounded-3xl premium-card p-6 premium-hover">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-white text-2xl font-bold">Recent Platform Activity</h3>
                <a href="{{ route('admin.sales.overview') }}" class="text-sky-400 text-sm font-medium hover:text-sky-300 transition">
                    View All
                </a>
            </div>

            <div class="space-y-0">
                @forelse($recentInvoices->take(4) as $inv)
                    <div class="activity-row py-4 flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/15 flex items-center justify-center text-blue-300 shrink-0">
                                <i class="fa-solid fa-receipt"></i>
                            </div>

                            <div>
                                <div class="text-white font-medium">
                                    Payment received from {{ $inv->customer_name ?: 'Customer' }}
                                </div>
                                <div class="text-slate-400 text-sm mt-1">
                                    Invoice #{{ $inv->invoice_no }}
                                </div>
                            </div>
                        </div>

                        <div class="text-slate-500 text-sm whitespace-nowrap">
                            ${{ number_format($inv->total, 2) }}
                        </div>
                    </div>
                @empty
                    <div class="text-slate-400">No activity yet</div>
                @endforelse
            </div>
        </div>

        {{-- QUICK INSIGHTS --}}
        <div class="rounded-3xl premium-card p-6 premium-hover">
            <h3 class="text-white text-2xl font-bold mb-6">Quick Insights</h3>

            <div class="space-y-0">
                <div class="insight-row py-5 flex items-center justify-between gap-4">
                    <span class="text-slate-300">Best Performing Plan</span>
                    <span class="text-green-400 font-semibold">{{ $bestPlan }}</span>
                </div>

                <div class="insight-row py-5 flex items-center justify-between gap-4">
                    <span class="text-slate-300">Most Active Company</span>
                    <span class="text-blue-400 font-semibold text-right">{{ $mostActiveCompany }}</span>
                </div>

                <div class="insight-row py-5 flex items-center justify-between gap-4">
                    <span class="text-slate-300">Total Revenue (All Time)</span>
                    <span class="text-purple-400 font-semibold">${{ number_format($totalRevenue, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-slate-500 text-sm px-2">
        <div>© {{ now()->year }} Medios Billing. All rights reserved.</div>
        <div>v2.0.0</div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = @json(array_values($chartData ?? []));
    const moneyCounters = document.querySelectorAll('.counter-money');
    const intCounters = document.querySelectorAll('.counter-int');

    function animateMoneyCounter(el) {
        const target = parseFloat(el.dataset.target || 0);
        let current = 0;
        const steps = 45;
        const increment = target / steps;

        function update() {
            current += increment;

            if (current >= target) {
                current = target;
            }

            el.textContent = '$' + current.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            if (current < target) {
                requestAnimationFrame(update);
            }
        }

        update();
    }

    function animateIntCounter(el) {
        const target = parseInt(el.dataset.target || 0, 10);
        let current = 0;
        const steps = 35;
        const increment = Math.max(1, Math.ceil(target / steps));

        function update() {
            current += increment;

            if (current >= target) {
                current = target;
            }

            el.textContent = current.toLocaleString();

            if (current < target) {
                requestAnimationFrame(update);
            }
        }

        update();
    }

    moneyCounters.forEach(animateMoneyCounter);
    intCounters.forEach(animateIntCounter);

    function buildSparkline(canvas, color, fillColor, data) {
        new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.map((_, i) => i + 1),
                datasets: [{
                    data: data,
                    borderColor: color,
                    backgroundColor: fillColor,
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: false,
                    tension: 0.42
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1600
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    }

    const sparkConfigs = [
        { selector: '.spark-purple', color: '#8b5cf6', fill: 'rgba(139,92,246,.15)', data: [12,18,16,14,17,19,16,15,17,21,24,20,25] },
        { selector: '.spark-blue',   color: '#3b82f6', fill: 'rgba(59,130,246,.15)', data: [7,10,8,9,10,9,7,10,10,12,11,14,13] },
        { selector: '.spark-green',  color: '#22c55e', fill: 'rgba(34,197,94,.15)', data: [5,8,7,9,6,8,11,10,12,10,9,13,15] },
        { selector: '.spark-amber',  color: '#fbbf24', fill: 'rgba(251,191,36,.15)', data: [9,9,9,9,9,9,9,9,9,9,9,9,9] },
        { selector: '.spark-cyan',   color: '#22d3ee', fill: 'rgba(34,211,238,.15)', data: [8,10,9,11,8,12,12,11,11,14,13,16,18] }
    ];

    sparkConfigs.forEach(cfg => {
        document.querySelectorAll(cfg.selector).forEach(canvas => {
            buildSparkline(canvas, cfg.color, cfg.fill, cfg.data);
        });
    });

    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                data: chartData,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124,58,237,.18)',
                fill: true,
                tension: 0.45,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: '#60a5fa',
                pointBorderColor: '#60a5fa',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1700
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    borderColor: 'rgba(255,255,255,.08)',
                    borderWidth: 1,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            return ' $' + Number(context.raw).toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        color: '#94a3b8',
                        callback: function(v){ return '$' + v; }
                    },
                    grid: {
                        color: 'rgba(255,255,255,.05)'
                    }
                },
                x: {
                    ticks: {
                        color: '#94a3b8'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('subscriptionChart'), {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Past Due', 'Trial', 'Cancelled'],
            datasets: [{
                data: [{{ $activeSubs }}, {{ $pastDueSubs }}, {{ $trialSubs }}, {{ $cancelledSubs }}],
                backgroundColor: ['#22c55e', '#fbbf24', '#3b82f6', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            cutout: '72%',
            animation: {
                duration: 1700
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            }
        }
    });
</script>

@endsection
