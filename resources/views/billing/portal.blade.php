@extends('layouts.app')

@section('content')

@php
    $company = auth()->user()->company;

    $plan = strtolower($company->plan_name ?? $company->plan ?? 'starter');
    $planLabel = ucfirst($company->plan_name ?? $company->plan ?? 'Starter');

    $price = (float) ($company->monthly_price ?? 49);
    $status = strtolower($company->subscription_status ?? 'trialing');

    $nextBill = !empty($company?->subscription_ends_at)
        ? \Carbon\Carbon::parse($company->subscription_ends_at)->format('F d, Y')
        : 'Not Scheduled';

    $trialEnds = !empty($company?->trial_ends_at)
        ? \Carbon\Carbon::parse($company->trial_ends_at)->format('F d, Y')
        : 'N/A';

    $cancelled = in_array($status, ['cancelled','expired','past_due','unpaid']);
@endphp

<div class="max-w-7xl mx-auto px-4 md:px-6 py-8">

    {{-- HERO --}}
    <div class="rounded-3xl p-8 mb-8 border border-white/10"
         style="background:
         radial-gradient(circle at top right, rgba(59,130,246,.18), transparent 30%),
         linear-gradient(135deg,#020617,#0f172a,#111827);">

        <div class="grid lg:grid-cols-2 gap-8 items-center">

            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold mb-5 border border-emerald-400/20 bg-emerald-500/10 text-emerald-300">
                    <i class="fa-solid fa-crown"></i>
                    BILLING COMMAND CENTER
                </div>

                <h1 class="text-4xl md:text-5xl font-black text-white leading-tight">
                    Manage Your <span class="text-sky-400">Subscription</span>
                </h1>

                <p class="text-slate-300 mt-4 text-lg leading-relaxed">
                    Upgrade plans, manage billing, protect your access, and scale your business with premium tools.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">

                <div class="rounded-2xl p-5 border border-white/10 bg-white/5">
                    <div class="text-slate-400 text-sm">Current Plan</div>
                    <div class="text-2xl font-bold text-white mt-2">{{ $planLabel }}</div>
                </div>

                <div class="rounded-2xl p-5 border border-white/10 bg-white/5">
                    <div class="text-slate-400 text-sm">Monthly Price</div>
                    <div class="text-2xl font-bold text-emerald-400 mt-2">${{ number_format($price,2) }}</div>
                </div>

                <div class="rounded-2xl p-5 border border-white/10 bg-white/5">
                    <div class="text-slate-400 text-sm">Status</div>
                    <div class="text-xl font-bold mt-2 {{ $cancelled ? 'text-red-400' : 'text-green-400' }}">
                        {{ strtoupper($status) }}
                    </div>
                </div>

                <div class="rounded-2xl p-5 border border-white/10 bg-white/5">
                    <div class="text-slate-400 text-sm">Next Bill Date</div>
                    <div class="text-xl font-bold text-amber-300 mt-2">{{ $nextBill }}</div>
                </div>

            </div>

        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="mb-6 rounded-2xl px-5 py-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-2xl px-5 py-4 bg-red-500/10 border border-red-500/20 text-red-300 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    {{-- INCLUDED --}}
    <div class="rounded-3xl p-7 border border-blue-500/20 mb-10"
         style="background:rgba(59,130,246,.08);">

        <h3 class="text-2xl font-bold text-white mb-5">
            Included With Your Subscription
        </h3>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">

            <div class="rounded-xl p-4 bg-white/5 border border-white/10 text-slate-200">✅ Quotes + Invoices</div>
            <div class="rounded-xl p-4 bg-white/5 border border-white/10 text-slate-200">✅ eSign Contracts</div>
            <div class="rounded-xl p-4 bg-white/5 border border-white/10 text-slate-200">✅ Online Payments</div>
            <div class="rounded-xl p-4 bg-white/5 border border-white/10 text-slate-200">✅ Customer CRM</div>
            <div class="rounded-xl p-4 bg-white/5 border border-white/10 text-slate-200">✅ Branding Tools</div>

            <div class="rounded-xl p-4 bg-white/5 border border-white/10 text-slate-200">
                {{ $plan === 'starter' ? '1 User Included' : ($plan === 'growth' ? 'Up to 5 Users + Roles' : 'Unlimited Users + Roles') }}
            </div>

        </div>
    </div>

    {{-- PRICING SECTION --}}
    <div class="mb-10">

        <div class="text-center mb-8">
            <h2 class="text-4xl font-black text-white">Choose Your Growth Level</h2>
            <p class="text-slate-400 mt-3 text-lg">Simple pricing. Powerful results.</p>
        </div>

        <div class="grid xl:grid-cols-3 md:grid-cols-2 gap-8 items-stretch">

            {{-- STARTER --}}
            <div class="rounded-3xl p-8 border border-white/10 flex flex-col relative overflow-hidden shadow-2xl"
                 style="background:
                 radial-gradient(circle at top left, rgba(59,130,246,.12), transparent 35%),
                 linear-gradient(145deg,#0f172a,#111827,#0b1220);">

                @if($plan === 'starter')
                    <div class="absolute top-5 right-5 px-3 py-1 rounded-full text-xs font-bold bg-sky-500 text-white">
                        ACTIVE
                    </div>
                @endif

                <div class="text-slate-300 uppercase tracking-[2px] text-sm font-bold mb-4">Starter</div>

                <div class="mb-5">
                    <span class="text-6xl font-black text-white">$49</span>
                    <span class="text-slate-400 text-lg">/mo</span>
                </div>

                <p class="text-slate-400 mb-6">
                    Perfect for solo owners and new businesses needing speed.
                </p>

                <div class="space-y-3 text-white mb-8">
                    <div>✓ 1 User Included</div>
                    <div>✓ Quotes + Invoices</div>
                    <div>✓ eSign Contracts</div>
                    <div>✓ Payment Links</div>
                    <div>✓ Branding Tools</div>
                </div>

                <div class="mt-auto">
                    @if($plan === 'starter')
                        <div class="w-full py-4 rounded-2xl bg-sky-500/20 text-sky-300 text-center font-bold">
                            CURRENT PLAN
                        </div>
                    @else
                        <form method="POST" action="{{ route('subscription.changePlan') }}">
                            @csrf
                            <input type="hidden" name="plan" value="starter">
                            <button class="w-full py-4 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold transition">
                                Switch to Starter
                            </button>
                        </form>
                    @endif
                </div>

            </div>

            {{-- GROWTH --}}
            <div class="rounded-3xl p-8 border border-sky-400/30 flex flex-col relative overflow-hidden shadow-2xl scale-[1.03]"
                 style="background:
                 radial-gradient(circle at top center, rgba(14,165,233,.20), transparent 35%),
                 linear-gradient(145deg,#08111f,#0f172a,#111827);">

                <div class="absolute top-5 right-5 px-3 py-1 rounded-full text-xs font-bold bg-sky-500 text-white">
                    MOST POPULAR
                </div>

                <div class="text-sky-300 uppercase tracking-[2px] text-sm font-bold mb-4">Growth</div>

                <div class="mb-5">
                    <span class="text-6xl font-black text-white">$99</span>
                    <span class="text-slate-300 text-lg">/mo</span>
                </div>

                <p class="text-slate-300 mb-6">
                    Best for growing teams ready for automation and staff access.
                </p>

                <div class="space-y-3 text-white mb-8">
                    <div>✓ Up to 5 Users</div>
                    <div>✓ Role Permissions</div>
                    <div>✓ Managers / Supervisors</div>
                    <div>✓ SMS Reminders</div>
                    <div>✓ Priority Support</div>
                </div>

                <div class="mt-auto">
                    @if($plan === 'growth')
                        <div class="w-full py-4 rounded-2xl bg-sky-500/20 text-sky-300 text-center font-bold">
                            CURRENT PLAN
                        </div>
                    @else
                        <form method="POST" action="{{ route('subscription.changePlan') }}">
                            @csrf
                            <input type="hidden" name="plan" value="growth">
                            <button class="w-full py-4 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition shadow-lg">
                                Upgrade to Growth
                            </button>
                        </form>
                    @endif
                </div>

            </div>

            {{-- PRO --}}
            <div class="rounded-3xl p-8 border border-violet-400/30 flex flex-col relative overflow-hidden shadow-2xl"
                 style="background:
                 radial-gradient(circle at top right, rgba(168,85,247,.20), transparent 35%),
                 linear-gradient(145deg,#111827,#1e1b4b,#0f172a);">

                @if($plan === 'pro')
                    <div class="absolute top-5 right-5 px-3 py-1 rounded-full text-xs font-bold bg-violet-500 text-white">
                        ACTIVE
                    </div>
                @endif

                <div class="text-violet-300 uppercase tracking-[2px] text-sm font-bold mb-4">Pro</div>

                <div class="mb-5">
                    <span class="text-6xl font-black text-white">$179</span>
                    <span class="text-slate-300 text-lg">/mo</span>
                </div>

                <p class="text-slate-300 mb-6">
                    Executive-level power for serious multi-user companies.
                </p>

                <div class="space-y-3 text-white mb-8">
                    <div>✓ Unlimited Users</div>
                    <div>✓ Advanced Roles</div>
                    <div>✓ Multi Location Teams</div>
                    <div>✓ CPA Ready Center</div>
                    <div>✓ VIP Support</div>
                </div>

                <div class="mt-auto">
                    @if($plan === 'pro')
                        <div class="w-full py-4 rounded-2xl bg-violet-500/20 text-violet-300 text-center font-bold">
                            CURRENT PLAN
                        </div>
                    @else
                        <form method="POST" action="{{ route('subscription.changePlan') }}">
                            @csrf
                            <input type="hidden" name="plan" value="pro">
                            <button class="w-full py-4 rounded-2xl bg-violet-600 hover:bg-violet-700 text-white font-bold transition shadow-lg">
                                Upgrade to Pro
                            </button>
                        </form>
                    @endif
                </div>

            </div>

        </div>
    </div>

    {{-- LOWER SECTION --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- ACCOUNT --}}
        <div class="rounded-3xl p-8 border border-emerald-500/20 shadow-xl"
             style="background:
             radial-gradient(circle at top left, rgba(16,185,129,.12), transparent 35%),
             linear-gradient(145deg,#052e2b,#0f172a,#111827);">

            <h3 class="text-2xl font-black text-white mb-2">
                Account Protection
            </h3>

            <p class="text-slate-300 mb-6">
                Keep service active and avoid interruptions.
            </p>

            <div class="grid grid-cols-2 gap-4 mb-6">

                <div class="rounded-xl bg-white/5 p-4 border border-white/10">
                    <div class="text-slate-400 text-sm">Plan</div>
                    <div class="text-white font-bold mt-1">{{ $planLabel }}</div>
                </div>

                <div class="rounded-xl bg-white/5 p-4 border border-white/10">
                    <div class="text-slate-400 text-sm">Status</div>
                    <div class="text-emerald-400 font-bold mt-1">{{ strtoupper($status) }}</div>
                </div>

            </div>

            <form method="POST" action="{{ route('subscription.cancel') }}">
                @csrf
                <button class="w-full py-4 rounded-2xl bg-red-600 hover:bg-red-700 text-white font-bold transition">
                    Cancel Subscription
                </button>
            </form>

        </div>

        {{-- SUMMARY --}}
        <div class="rounded-3xl p-8 border border-sky-500/20 shadow-xl"
             style="background:
             radial-gradient(circle at top right, rgba(59,130,246,.12), transparent 35%),
             linear-gradient(145deg,#0f172a,#111827,#0b1220);">

            <h3 class="text-2xl font-black text-white mb-6">
                Billing Summary
            </h3>

            <div class="space-y-4">

                <div class="flex justify-between rounded-xl bg-white/5 px-4 py-3">
                    <span class="text-slate-400">Current Plan</span>
                    <span class="text-white font-bold">{{ $planLabel }}</span>
                </div>

                <div class="flex justify-between rounded-xl bg-white/5 px-4 py-3">
                    <span class="text-slate-400">Monthly Amount</span>
                    <span class="text-emerald-400 font-bold">${{ number_format($price,2) }}</span>
                </div>

                <div class="flex justify-between rounded-xl bg-white/5 px-4 py-3">
                    <span class="text-slate-400">Trial Ends</span>
                    <span class="text-amber-300 font-bold">{{ $trialEnds }}</span>
                </div>

                <div class="flex justify-between rounded-xl bg-white/5 px-4 py-3">
                    <span class="text-slate-400">Next Bill</span>
                    <span class="text-sky-300 font-bold">{{ $nextBill }}</span>
                </div>

            </div>

            <a href="{{ route('dashboard') }}"
               class="mt-6 inline-flex w-full justify-center py-4 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition">
                Back to Dashboard
            </a>

        </div>

    </div>

</div>

@endsection
