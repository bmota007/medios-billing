<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medios Billing | Platform</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --accent-blue: #38bdf8;
            --sidebar-width: 260px;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        html, body {
            overflow-x: hidden;
            position: relative;
            background: radial-gradient(circle at top left, #1e293b, #0f172a) !important;
            color: #f8fafc !important;
            font-family: 'Inter', sans-serif !important;
            margin: 0 !important;
            min-height: 100vh !important;
        }

        .mobile-header {
            display: none;
            background: #0f172a;
            border-bottom: 1px solid var(--glass-border);
            padding: 10px 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1050;
        }

        .sidebar {
            width: var(--sidebar-width) !important;
            background: #0f172a !important;
            border-right: 1px solid var(--glass-border) !important;
            position: fixed !important;
            height: 100vh !important;
            z-index: 2000 !important;
            left: 0 !important;
            top: 0 !important;
            padding: 2rem 0 !important;
            overflow-x: hidden;
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: var(--sidebar-width) !important;
            padding: 3rem !important;
            min-height: 100vh !important;
            width: calc(100% - var(--sidebar-width)) !important;
            transition: all 0.3s ease;
        }

        .impersonation-banner {
            background: #facc15;
            color: #000;
            padding: 12px;
            text-align: center;
            font-weight: 800;
            position: fixed;
            top: 0; left: 0; width: 100%;
            z-index: 9999;
            border-bottom: 2px solid #000;
        }

        .has-banner .mobile-header { top: 48px; }
        .has-banner .sidebar { top: 48px !important; height: calc(100vh - 48px) !important; }
        .has-banner .main-content { padding-top: 7rem !important; }

        .dashboard-card, .card, .table-container {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 1rem !important;
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
        }

        .nav-link { display: flex !important; align-items: center !important; padding: 0.8rem 1.5rem !important; color: #94a3b8 !important; text-decoration: none !important; white-space: nowrap; }
        .nav-link:hover, .nav-link.active { background: rgba(56, 189, 248, 0.1) !important; color: var(--accent-blue) !important; border-left: 3px solid var(--accent-blue) !important; }
        .nav-link i { width: 25px; margin-right: 10px; }
        .table { color: #f8fafc !important; }
        .nav-section { text-transform: uppercase; letter-spacing: 1px; font-size: 0.7rem; color: #64748b; margin-top: 1.5rem; }

        @media (max-width: 991.98px) {
            .mobile-header { display: flex; align-items: center; justify-content: space-between; }
            .sidebar { transform: translateX(-100%); visibility: hidden; }
            .sidebar.show { transform: translateX(0); visibility: visible; }
            .main-content { margin-left: 0 !important; width: 100% !important; padding: 1.5rem !important; padding-top: 5rem !important; }
        }
    </style>
</head>
<body class="{{ session()->has('impersonator_id') ? 'has-banner' : '' }}">

    @if(session()->has('impersonator_id'))
        <div class="impersonation-banner">
            <i class="fa-solid fa-user-secret me-2"></i> 
            SUPPORT MODE: Impersonating {{ auth()->user()->company->name ?? 'Client' }}
            <a href="{{ route('admin.stopImpersonating') }}" style="background: #000; color: #fff; padding: 5px 15px; border-radius: 20px; text-decoration: none; margin-left: 20px; font-size: 12px;">EXIT</a>
        </div>
    @endif

    <div class="mobile-header">
        <h4 class="text-white mb-0">Medios<span class="text-info">Billing</span></h4>
        <button class="btn text-white border-0" id="sidebarToggle">
            <i class="fa-solid fa-bars fa-xl"></i>
        </button>
    </div>

    <div class="sidebar" id="sidebarMenu">
        <div class="text-center mb-4">
            <h4 class="text-white font-bold">MEDIOS<span class="text-info">BILLING</span></h4>
        </div>

        @if(auth()->user()->role === 'super_admin' || auth()->user()->is_admin)
            <div class="nav-section mb-2 ps-3 font-bold">Administration</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> SaaS Overview
            </a>

            <a href="{{ route('admin.companies') }}" class="nav-link {{ request()->routeIs('admin.companies') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Managed Companies
            </a>

            <a href="{{ route('admin.billing') }}" class="nav-link {{ request()->routeIs('admin.billing') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card text-info"></i> Platform Billing
            </a>

            <a href="{{ route('admin.brand') }}" class="nav-link {{ request()->routeIs('admin.brand') ? 'active' : '' }}">
                <i class="fa-solid fa-palette"></i> Platform Branding
            </a>

            <a href="{{ route('admin.manual-charge.create') }}" class="nav-link text-warning fw-bold">
                <i class="fa-solid fa-bolt"></i> Quick Manual Charge
            </a>
            
            <hr class="border-secondary opacity-20 my-3">
        @endif

        <div class="nav-section mb-2 ps-3 font-bold text-white-50">Business</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> Customers
        </a>
        <a href="{{ route('invoice.history') }}" class="nav-link {{ request()->is('invoices*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i> Invoices
        </a>
        <a href="{{ route('quotes.index') }}" class="nav-link {{ request()->is('quotes*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i> Quotes
        </a>
        
        <hr class="border-secondary opacity-20 my-3">
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="nav-link bg-transparent border-0 w-100 text-start">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebarMenu').classList.toggle('show');
        });
    </script>
</body>
</html>
