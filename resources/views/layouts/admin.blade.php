<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medios Billing | Super Admin</title>

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

        body {
            background: radial-gradient(circle at top left, #1e293b, #0f172a) !important;
            color: #f8fafc !important;
            font-family: 'Inter', sans-serif !important;
            margin: 0 !important;
            min-height: 100vh !important;
        }

        /* --- THE YELLOW BANNER FIX --- */
        .impersonation-banner {
            background: #facc15;
            color: #000;
            padding: 10px;
            text-align: center;
            font-weight: 700;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
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
            overflow-x: hidden; /* Stops text messy wrapping */
        }

        .main-content {
            margin-left: var(--sidebar-width) !important;
            padding: 3rem !important;
            min-height: 100vh !important;
            width: calc(100% - var(--sidebar-width)) !important;
        }

        /* Adjust content if banner is present */
        .has-banner .sidebar { top: 44px; height: calc(100vh - 44px); }
        .has-banner .main-content { padding-top: 5rem !important; }

        .dashboard-card, .card, .table-container {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(12px) !important;
            border: 1px solid var(--glass-border) !important;
            border-radius: 1rem !important;
            padding: 1.5rem !important;
            margin-bottom: 2rem !important;
        }

        .nav-link {
            display: flex !important;
            align-items: center !important;
            padding: 0.8rem 1.5rem !important;
            color: #94a3b8 !important;
            text-decoration: none !important;
            white-space: nowrap; /* Stops menu items from stacking/breaking */
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(56, 189, 248, 0.1) !important;
            color: var(--accent-blue) !important;
            border-left: 3px solid var(--accent-blue) !important;
        }

        .nav-link i { width: 25px; margin-right: 10px; }
        
        .table { color: #f8fafc !important; }
    </style>
</head>
<body class="{{ session()->has('impersonator_id') ? 'has-banner' : '' }}">

    {{-- THE YELLOW BANNER --}}
    @if(session()->has('impersonator_id'))
        <div style="background: #facc15; color: #000; padding: 12px; text-align: center; font-weight: 800; position: fixed; top: 0; left: 0; width: 100%; z-index: 9999; border-bottom: 2px solid #000;">
            <i class="fa-solid fa-user-secret me-2"></i> 
            SUPPORT MODE: Impersonating {{ auth()->user()->company->name ?? 'Client' }}
            <a href="{{ route('admin.stopImpersonating') }}" style="background: #000; color: #fff; padding: 5px 15px; border-radius: 20px; text-decoration: none; margin-left: 20px; font-size: 12px;">
                EXIT & RETURN TO ADMIN
            </a>
        </div>
        <style>
            .sidebar { top: 48px !important; height: calc(100vh - 48px) !important; }
            .main-content { padding-top: 6rem !important; }
        </style>
    @endif

    <div class="sidebar">
        @include('layouts.sidebar')
    </div>

    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>
