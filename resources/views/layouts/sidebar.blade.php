@php
    use Illuminate\Support\Str;

    $logoUrl = null;
    $currentCompany = null;

    // 1. Identify context
    if (session()->has('impersonator_id')) {
        // If impersonating, show the client's logo
        $currentCompany = auth()->user()->company;
    } elseif (auth()->check()) {
        // If Super Admin, look for the 'Medios Billing' company record
        if (auth()->user()->role === 'super_admin' || auth()->user()->is_admin) {
            $currentCompany = \App\Models\Company::where('name', 'Medios Billing')->first();
        } else {
            $currentCompany = auth()->user()->company;
        }
    }

    // 2. Resolve Path
    if ($currentCompany) {
        $path = $currentCompany->logo ?? $currentCompany->logo_path ?? null;
        if (!empty($path)) {
            $logoUrl = Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : asset('storage/' . ltrim($path, '/'));
        }
    }

    $isImpersonating = session()->has('impersonator_id');
    $isSuperAdmin = auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->is_admin);
@endphp

{{-- Sidebar Branding Section --}}
<div class="sidebar-header text-center mb-4">
    @if($logoUrl)
        <img src="{{ $logoUrl }}" style="max-width:160px; max-height:90px; object-fit:contain; filter: drop-shadow(0 0 5px rgba(0,0,0,0.2));">
    @else
        <h3 class="fw-bold text-white mb-0">
            Medios<span style="color: var(--accent-blue)">Billing</span>
        </h3>
    @endif

    @if($currentCompany)
        <div style="font-size:11px; color:#94a3b8; margin-top:8px; text-transform: uppercase; letter-spacing: 1px;">
            {{ $currentCompany->name }}
        </div>
    @endif
</div>

<div class="d-flex flex-column">
    {{-- ADMINISTRATION SECTION: Only visible to Super Admins NOT currently impersonating --}}
    @if($isSuperAdmin && !$isImpersonating)
        <div class="px-3 py-2 text-xs font-bold text-uppercase text-muted" style="opacity: 0.5;">
            ADMINISTRATION
        </div>

        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie me-2"></i> SaaS Overview
        </a>

        <a href="{{ route('admin.companies') }}" class="nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}">
            <i class="fa-solid fa-building me-2"></i> Managed Companies
        </a>

        <a href="{{ route('admin.brand') }}" class="nav-link {{ request()->routeIs('admin.brand') ? 'active' : '' }}">
            <i class="fa-solid fa-palette me-2"></i> Platform Branding
        </a>

        <div class="my-3" style="border-top: 1px solid rgba(255,255,255,0.1);"></div>
    @endif

    {{-- BUSINESS SECTION: Visible to everyone --}}
    <div class="px-3 py-2 text-xs font-bold text-uppercase text-muted" style="opacity: 0.5;">
        BUSINESS
    </div>

    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-gauge me-2"></i> Dashboard
    </a>

    <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-users me-2"></i> Customers
    </a>

    <a href="{{ route('invoice.history') }}" class="nav-link {{ request()->routeIs('invoice.*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice me-2"></i> Invoices
    </a>

    <a href="{{ route('quotes.index') }}" class="nav-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
        <i class="fa-solid fa-file-signature me-2"></i> Quotes
    </a>

    <a href="{{ route('company.settings') }}" class="nav-link {{ request()->routeIs('company.settings') ? 'active' : '' }}">
        <i class="fa-solid fa-sliders me-2"></i> Brand Settings
    </a>

    <div class="my-3" style="border-top: 1px solid rgba(255,255,255,0.1);"></div>

    {{-- Logout Section --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="nav-link text-danger border-0 bg-transparent w-100 text-start">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
        </button>
    </form>
</div>
