@php
    $brandCompany = null;
    $userRole = null;

    if (auth()->check()) {
        $userRole = auth()->user()->role;
        $brandCompany = auth()->user()->company;

        if (!$brandCompany && $userRole === 'super_admin') {
            $brandCompany = \App\Models\Company::whereNotNull('logo_path')
                ->where('logo_path', '!=', '')
                ->orderBy('id')
                ->first();
        }
    }

    $rawPlan = 'starter';

    if (auth()->check() && auth()->user()->company) {
        $rawPlan = strtolower(
            auth()->user()->company->plan_name
            ?? auth()->user()->company->plan
            ?? 'starter'
        );
    }

    if ($rawPlan === 'free' || $rawPlan === '') {
        $rawPlan = 'starter';
    }

    if (in_array($rawPlan, ['premium', 'professional'])) {
        $rawPlan = 'pro';
    }

    $isStarter = $rawPlan === 'starter';
    $isGrowth  = $rawPlan === 'growth';
    $isPro     = $rawPlan === 'pro';

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN FULL BYPASS
    |--------------------------------------------------------------------------
    */
    $isSuper = $userRole === 'super_admin';

    /*
    |--------------------------------------------------------------------------
    | ROLE ACCESS FLAGS
    |--------------------------------------------------------------------------
    */
    $canCustomers = $isSuper || in_array($userRole, [
        'owner','admin','regional_director','manager','sales_director','support'
    ]);

    $canQuotes = $isSuper || in_array($userRole, [
        'owner','admin','regional_director','manager','sales_director'
    ]);

    $canInvoices = $isSuper || in_array($userRole, [
        'owner','admin','regional_director','manager','accounting'
    ]);

    $canUsers = $isSuper || in_array($userRole, [
        'owner','admin','regional_director','manager'
    ]);

    $canBrand = $isSuper || in_array($userRole, [
        'owner','admin','regional_director'
    ]);
@endphp

<div class="h-full flex flex-col px-4 py-5 text-sm text-slate-200">

    <!-- LOGO -->
    <div class="mb-6 pb-4 border-b border-white/5">
        <div class="flex items-center gap-3">

            @if($brandCompany && $brandCompany->logo_path)
                <img src="{{ asset('storage/'.$brandCompany->logo_path) }}"
                     style="width:42px;height:42px;border-radius:12px;object-fit:cover;box-shadow:0 10px 25px rgba(37,99,235,.35);flex-shrink:0;">
            @else
                <div style="width:42px;height:42px;border-radius:12px;background:linear-gradient(135deg,#2563eb,#06b6d4);display:flex;align-items:center;justify-content:center;font-weight:800;color:white;box-shadow:0 10px 25px rgba(37,99,235,.35);flex-shrink:0;">
                    M
                </div>
            @endif

            <div class="min-w-0">
                <h1 class="text-lg font-bold leading-none text-white truncate">
                    Medios<span class="text-sky-400">Billing</span>
                </h1>

                <p class="text-[10px] uppercase tracking-[2px] text-slate-500 mt-1">
                    Command Center
                </p>
            </div>

        </div>
    </div>

    <!-- NAV -->
    <div class="flex-1 overflow-y-auto pr-1 space-y-5">

        {{-- ADMIN SECTION --}}
        @if($isSuper)
        <div>
            <p class="text-[10px] uppercase tracking-[2px] text-slate-500 mb-2 px-2">
                Administration
            </p>

            <div class="space-y-1">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('admin') || request()->is('admin/dashboard') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                    <span>SaaS Overview</span>
                </a>

                <a href="{{ route('admin.companies') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('admin/companies*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-building w-5 text-center"></i>
                    <span>Managed Companies</span>
                </a>

                <a href="{{ route('admin.brand') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('admin/brand*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-palette w-5 text-center"></i>
                    <span>Platform Branding</span>
                </a>

            </div>
        </div>
        @endif


        {{-- BUSINESS SECTION --}}
        @auth
        <div>

            <p class="text-[10px] uppercase tracking-[2px] text-slate-500 mb-2 px-2">
                Business
            </p>

            <div class="space-y-1">

                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('dashboard') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-house w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                @if($canCustomers)
                <a href="{{ route('customers.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('customers*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span>Customers</span>
                </a>
                @endif

                @if($canInvoices)
                <a href="{{ route('invoice.history') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('invoice*') || request()->is('invoices*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                    <span>Invoices</span>
                </a>
                @endif

                @if($canQuotes)
                <a href="{{ route('quotes.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('quotes*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-file-signature w-5 text-center"></i>
                    <span>Quotes</span>
                </a>
                @endif

                @if(!$isSuper)
                <a href="{{ route('subscription.portal') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('subscription*') ? 'bg-emerald-500/15 text-emerald-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-credit-card w-5 text-center"></i>
                    <span>Subscription</span>
                </a>
                @endif

                @if(($isGrowth || $isPro || $isSuper) && $canUsers)
                <a href="{{ route('company.users') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('company/users*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center"></i>
                    <span>Team Users</span>
                </a>
                @endif

                @if($isPro || $isSuper)
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition hover:bg-slate-800 text-slate-200">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    <span>Advanced Reports</span>
                </a>
                @endif

                @if($canBrand)
                <a href="{{ route('company.settings') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('company/settings*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-gear w-5 text-center"></i>
                    <span>Brand Settings</span>
                </a>
                @endif

            </div>
        </div>
        @endauth


        {{-- SALES TEAM --}}
        @if($isSuper)
        <div>
            <p class="text-[10px] uppercase tracking-[2px] text-yellow-400 mb-2 px-2">
                Sales Team
            </p>

            <div class="space-y-1">

                <a href="{{ route('admin.sales.overview') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    <span>Sales Overview</span>
                </a>

                <a href="{{ route('admin.sales.subscriptions') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition">
                    <i class="fa-solid fa-credit-card w-5 text-center"></i>
                    <span>Subscriptions</span>
                </a>

                <a href="{{ route('admin.sales.onboarding') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition">
                    <i class="fa-solid fa-rocket w-5 text-center"></i>
                    <span>Onboarding</span>
                </a>

                <a href="{{ route('admin.sales.promos') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span>Promos</span>
                </a>

            </div>
        </div>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="mt-5 pt-4 border-t border-white/5">

        @auth
        <div class="flex items-center gap-3 mb-3 px-2">

            <div style="width:36px;height:36px;border-radius:999px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">
                {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
            </div>

            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">
                    {{ auth()->user()->name }}
                </div>

                <div class="text-[11px] text-slate-500 truncate">
                    {{ ucfirst(str_replace('_',' ',auth()->user()->role)) }}
                </div>
            </div>

        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-red-400 hover:bg-red-500/10 transition">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                <span>Logout</span>
            </button>
        </form>
        @endauth

    </div>

</div>
