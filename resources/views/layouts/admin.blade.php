<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
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
            width: var(--sidebar-width);
            background: #0f172a;
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            z-index: 2000;
            left: 0;
            top: 0;
            padding: 2rem 0;
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 3rem;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
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
        .has-banner .sidebar { top: 48px; height: calc(100vh - 48px); }
        .has-banner .main-content { padding-top: 7rem !important; }

        .dashboard-card, .card, .table-container {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            white-space: nowrap;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(56, 189, 248, 0.1);
            color: var(--accent-blue);
            border-left: 3px solid var(--accent-blue);
        }

        @media (max-width: 991.98px) {
            .mobile-header { display: flex; align-items: center; justify-content: space-between; }
            .sidebar { transform: translateX(-100%); visibility: hidden; }
            .sidebar.show { transform: translateX(0); visibility: visible; }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 15px !important;
                padding-top: 5rem !important;
                overflow-x: hidden;
            }
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
        <button class="btn text-white border-0" id="sidebarToggle"><i class="fa-solid fa-bars fa-xl"></i></button>
    </div>

    <div class="sidebar" id="sidebarMenu">
        @include('layouts.sidebar')
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
