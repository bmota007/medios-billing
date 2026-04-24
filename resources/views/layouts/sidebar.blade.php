<div class="h-full flex flex-col px-4 py-5 text-sm text-slate-200">

    <!-- LOGO / BRAND -->
    <div class="mb-6 pb-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div style="
                width:42px;
                height:42px;
                border-radius:12px;
                background:linear-gradient(135deg,#2563eb,#06b6d4);
                display:flex;
                align-items:center;
                justify-content:center;
                font-weight:800;
                color:white;
                box-shadow:0 10px 25px rgba(37,99,235,.35);
                flex-shrink:0;
            ">
                M
            </div>

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

        {{-- SUPER ADMIN --}}
        @if(auth()->check() && auth()->user()->role === 'super_admin')
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


        {{-- BUSINESS USERS --}}
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

                <a href="{{ route('customers.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('customers*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span>Customers</span>
                </a>

                <a href="{{ route('invoice.history') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('invoices*') || request()->is('invoice*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                    <span>Invoices</span>
                </a>

                <a href="{{ route('quotes.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('quotes*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-file-signature w-5 text-center"></i>
                    <span>Quotes</span>
                </a>

                {{-- TENANT ONLY SUBSCRIPTION --}}
                @if(auth()->user()->role !== 'super_admin')
                <a href="{{ route('subscription.portal') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('subscription*') ? 'bg-emerald-500/15 text-emerald-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-credit-card w-5 text-center"></i>
                    <span>Subscription</span>
                </a>
                @endif

                <a href="{{ route('company.settings') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('company/settings*') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-gear w-5 text-center"></i>
                    <span>Brand Settings</span>
                </a>

            </div>
        </div>
        @endauth


        {{-- SALES TEAM / OWNER METRICS --}}
        @if(auth()->check() && auth()->user()->role === 'super_admin')
        <div>
            <p class="text-[10px] uppercase tracking-[2px] text-yellow-400 mb-2 px-2">
                Sales Team
            </p>

            <div class="space-y-1">

                <a href="{{ route('admin.sales.overview') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('sales/overview*') || request()->is('admin/sales/overview*') ? 'bg-yellow-500/10 text-yellow-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    <span>Sales Overview</span>
                </a>

                <a href="{{ route('admin.sales.subscriptions') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('sales/subscriptions*') || request()->is('admin/sales/subscriptions*') ? 'bg-yellow-500/10 text-yellow-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-credit-card w-5 text-center"></i>
                    <span>Subscriptions</span>
                </a>

                <a href="{{ route('admin.sales.onboarding') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('sales/onboarding*') || request()->is('admin/sales/onboarding*') ? 'bg-yellow-500/10 text-yellow-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-rocket w-5 text-center"></i>
                    <span>Onboarding</span>
                </a>

                <a href="{{ route('admin.sales.promos') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl transition {{ request()->is('sales/promos*') || request()->is('admin/sales/promos*') ? 'bg-yellow-500/10 text-yellow-300' : 'hover:bg-slate-800 text-slate-200' }}">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span>Promos</span>
                </a>

            </div>
        </div>
        @endif


        {{-- GUEST --}}
        @guest
        <div>
            <p class="text-[10px] uppercase tracking-[2px] text-slate-500 mb-2 px-2">
                Public Pages
            </p>

            <div class="space-y-1">

                <a href="/"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-800 text-slate-200 transition">
                    <i class="fa-solid fa-house w-5 text-center"></i>
                    <span>Home</span>
                </a>

                <a href="{{ route('pricing') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl {{ request()->is('pricing') ? 'bg-sky-500/15 text-sky-300' : 'hover:bg-slate-800 text-slate-200' }} transition">
                    <i class="fa-solid fa-tags w-5 text-center"></i>
                    <span>Pricing</span>
                </a>

                <a href="{{ route('login') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-800 text-slate-200 transition">
                    <i class="fa-solid fa-right-to-bracket w-5 text-center"></i>
                    <span>Login</span>
                </a>

                <a href="/register"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-800 text-slate-200 transition">
                    <i class="fa-solid fa-user-plus w-5 text-center"></i>
                    <span>Start Trial</span>
                </a>

            </div>
        </div>
        @endguest

    </div>

    <!-- FOOTER -->
    <div class="mt-5 pt-4 border-t border-white/5">

        @auth
        <div class="flex items-center gap-3 mb-3 px-2">

            <div style="
                width:36px;
                height:36px;
                border-radius:999px;
                background:linear-gradient(135deg,#1d4ed8,#7c3aed);
                display:flex;
                align-items:center;
                justify-content:center;
                font-weight:700;
                color:white;
                flex-shrink:0;
            ">
                {{ strtoupper(substr(auth()->user()->name ?? 'U',0,1)) }}
            </div>

            <div class="min-w-0">
                <div class="text-sm font-semibold text-white truncate">
                    {{ auth()->user()->name ?? 'User' }}
                </div>

                <div class="text-[11px] text-slate-500 truncate">
                    {{ auth()->user()->role === 'super_admin' ? 'Administrator' : 'Business User' }}
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


        @guest
        <div class="px-2">

            <div class="text-sm font-semibold text-white">
                Welcome Visitor
            </div>

            <div class="text-[11px] text-slate-500 mb-3">
                Explore Medios Billing
            </div>

            <a href="{{ route('login') }}"
               class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-sky-300 hover:bg-sky-500/10 transition">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Login</span>
            </a>

        </div>
        @endguest

    </div>

</div>
