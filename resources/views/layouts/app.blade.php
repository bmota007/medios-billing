<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medios Billing | B2B SaaS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
    --accent-blue: #38bdf8;
    --glass-border: rgba(255, 255, 255, 0.1);
}

/* GLOBAL */
body {
    background: radial-gradient(circle at top left, #1e293b, #0f172a) !important;
    color: #f8fafc;
    font-family: 'Inter', sans-serif;
    margin: 0;
    min-height: 100vh;
}

/* SUPPORT BAR */
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

/* SIDEBAR */
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

/* MAIN CONTENT (🔥 FINAL FIX) */
.main-content {
    margin-left: 260px;
    padding: 2rem;
    max-width: 1250px; /* 🔥 THIS FIXES CARD WIDTH */
    width: calc(100% - 260px);
    margin-top: {{ session()->has('impersonator_id') ? '44px' : '0' }};
}

/* CENTER CONTENT */
.container-fluid {
    width: 100%;
    margin: 0 auto;
}

/* GRID FIX */
.row.g-4 {
    display: flex;
    flex-wrap: wrap;
}

/* TYPOGRAPHY */
h2, h3, h5 {
    font-weight: 700;
    color: #fff;
}

/* RESPONSIVE */
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
</head>

<body class="{{ session()->has('impersonator_id') ? 'is-impersonating' : '' }}">

    {{-- SUPPORT MODE BAR --}}
    @if(session()->has('impersonator_id'))
        <div class="support-mode-bar">
            <i class="fa-solid fa-user-shield me-2"></i>
            You are in SUPPORT MODE (Impersonating)
            <a href="{{ route('admin.stopImpersonating') }}">
                Return to Admin
            </a>
        </div>
    @endif

    {{-- SIDEBAR --}}
    <div class="sidebar">
        @include('layouts.sidebar')
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

</body>
</html>
