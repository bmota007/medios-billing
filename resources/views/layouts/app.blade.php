<!DOCTYPE html>
<html lang="en">
<head>
    {{-- =========================================================
    | HEAD / META
    ========================================================== --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medios Billing | B2B SaaS</title>

    {{-- =========================================================
    | GLOBAL ASSETS
    ========================================================== --}}
    <link rel="stylesheet" href="/build/assets/app-CN7OGbqs.css">
    <script src="/build/assets/app-CBbTb_k3.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- =========================================================
    | GLOBAL LAYOUT STYLES
    ========================================================== --}}
    <style>
    :root {
        --accent-blue: #38bdf8;
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    body {
        background: radial-gradient(circle at top left, #1e293b, #0f172a) !important;
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        margin: 0;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .support-mode-bar {
        background: #facc15;
        color: #000;
        text-align: center;
        padding: 10px;
        font-weight: 800;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 2000;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .support-mode-bar a {
        color: #000;
        text-decoration: underline;
        margin-left: 10px;
    }

    .sidebar {
        width: 260px;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        border-right: 1px solid var(--glass-border);
        position: fixed;
        height: 100vh;
        z-index: 1000;
        top: {{ session()->has('impersonator_id') ? '44px' : '0' }};
    }

    .main-content {
        margin-left: 260px;
        padding: 2rem;
        width: calc(100% - 260px);
        min-height: 100vh;
        margin-top: {{ session()->has('impersonator_id') ? '44px' : '0' }};
        display: flex;
        flex-direction: column;
    }
/* =========================================================
| INVOICE FOCUS MODE (NO SIDEBAR / CENTERED)
========================================================= */

.invoice-focus-mode ~ * .sidebar,
body:has(.invoice-focus-mode) .sidebar {
    display: none !important;
}

body:has(.invoice-focus-mode) .main-content {
    margin-left: 0 !important;
    width: 100% !important;
    display: flex;
    justify-content: center;
    padding: 40px 20px;
}

    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0;
        padding: 0;
    }

    h2, h3, h5 {
        font-weight: 700;
        color: #fff;
    }

    @media (max-width: 992px) {
        .main-content {
            padding: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .sidebar {
            display: none;
        }
    }
    </style>

    {{-- =========================================================
    | PAGE-SPECIFIC STYLES
    ========================================================== --}}
    @stack('page_styles')
</head>

<body class="{{ session()->has('impersonator_id') ? 'is-impersonating' : '' }}">

    {{-- =========================================================
    | SUPPORT MODE BAR
    ========================================================== --}}
    @if(session()->has('impersonator_id'))
        <div class="support-mode-bar">
            <i class="fa-solid fa-user-shield me-2"></i>
            You are in SUPPORT MODE (Impersonating)
            <a href="{{ route('admin.stopImpersonating') }}">
                Return to Admin
            </a>
        </div>
    @endif

    {{-- =========================================================
    | SIDEBAR
    ========================================================== --}}
    <div class="sidebar">
        @include('layouts.sidebar')
    </div>

    {{-- =========================================================
    | MAIN CONTENT
    ========================================================== --}}
    <div class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    {{-- =========================================================
    | PAGE-SPECIFIC SCRIPTS
    ========================================================== --}}
    @stack('page_scripts')

</body>
</html>
