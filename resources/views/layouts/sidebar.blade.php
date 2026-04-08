<div class="h-full flex flex-col p-5 text-sm">

    <!-- LOGO -->
    <div class="mb-8">
        <h1 class="text-lg font-bold text-white">
            Medios<span style="color:#38bdf8;">Billing</span>
        </h1>
        <p class="text-xs text-gray-400">MEDIOS BILLING</p>
    </div>

    <!-- ADMIN -->
    @if(auth()->user()->role === 'super_admin')
    <div class="mb-6">
        <p class="text-xs text-gray-500 uppercase mb-3">Administration</p>

        <a href="{{ route('admin.dashboard') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('admin/dashboard') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-chart-pie mr-2"></i> SaaS Overview
        </a>

        <a href="{{ route('admin.companies') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('admin/companies*') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-building mr-2"></i> Managed Companies
        </a>

        <a href="{{ route('admin.brand') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('admin/brand*') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-palette mr-2"></i> Platform Branding
        </a>
    </div>
    @endif

    <!-- BUSINESS -->
    <div class="mb-6">
        <p class="text-xs text-gray-500 uppercase mb-3">Business</p>

        <a href="{{ route('dashboard') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('dashboard') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-home mr-2"></i> Dashboard
        </a>

        <a href="{{ route('customers.index') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('customers*') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-users mr-2"></i> Customers
        </a>

        <a href="{{ route('invoice.history') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('invoices*') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-file-invoice mr-2"></i> Invoices
        </a>

        <a href="{{ route('quotes.index') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('quotes*') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-file-signature mr-2"></i> Quotes
        </a>

        <a href="{{ route('company.settings') }}"
           class="block py-2 px-3 rounded hover:bg-gray-700 {{ request()->is('company/settings*') ? 'bg-gray-700 text-blue-400' : '' }}">
            <i class="fa fa-cog mr-2"></i> Brand Settings
        </a>
    </div>

    <!-- SALES TEAM -->
    @if(auth()->user()->role === 'super_admin')
    <div class="mb-6">
        <p class="text-xs text-yellow-400 uppercase mb-3">Sales Team</p>

        <a href="{{ route('admin.sales.overview') }}"
           class="block py-2 px-3 rounded hover:bg-yellow-500/10 {{ request()->is('admin/sales/overview*') ? 'text-yellow-400' : '' }}">
            <i class="fa fa-chart-line mr-2"></i> Sales Overview
        </a>

        <a href="{{ route('admin.sales.subscriptions') }}"
           class="block py-2 px-3 rounded hover:bg-yellow-500/10 {{ request()->is('admin/sales/subscriptions*') ? 'text-yellow-400' : '' }}">
            <i class="fa fa-credit-card mr-2"></i> Active Subscriptions
        </a>

        <a href="{{ route('admin.sales.onboarding') }}"
           class="block py-2 px-3 rounded hover:bg-yellow-500/10 {{ request()->is('admin/sales/onboarding*') ? 'text-yellow-400' : '' }}">
            <i class="fa fa-rocket mr-2"></i> Manual Onboarding
        </a>

        <a href="{{ route('admin.sales.promos') }}"
           class="block py-2 px-3 rounded hover:bg-yellow-500/10 {{ request()->is('admin/sales/promos*') ? 'text-yellow-400' : '' }}">
            <i class="fa fa-tags mr-2"></i> Promos & Credits
        </a>
    </div>
    @endif

    <!-- LOGOUT -->
    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left py-2 px-3 rounded text-red-400 hover:bg-red-500/20">
                <i class="fa fa-sign-out-alt mr-2"></i> Logout
            </button>
        </form>
    </div>

</div>
