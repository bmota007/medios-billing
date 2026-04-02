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
        --glass-bg: rgba(15, 23, 42, 0.8);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    body {
        background: radial-gradient(circle at top left, #1e293b, #0f172a) !important;
        color: #f8fafc;
        font-family: 'Inter', sans-serif;
        margin: 0;
        min-height: 100vh;
    }

    .sidebar {
        width: 260px;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        border-right: 1px solid var(--glass-border);
        position: fixed;
        height: 100vh;
        z-index: 1000;
    }

    .main-content {
        margin-left: 260px;
        padding: 3rem;
    }

    /* RESTORE THE DASHBOARD CARDS LOOK */
    .dashboard-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
    }

    .dashboard-card:hover {
        border-color: var(--accent-blue);
        transform: translateY(-5px);
    }

    h2, h3, h5 { font-weight: 700; color: #fff; }
    .text-muted { color: #94a3b8 !important; }

    /* Fix table colors for dark mode */
    .table { color: #f8fafc !important; }
    .table-hover tbody tr:hover { background-color: rgba(255,255,255,0.05); color: #fff; }
</style>
</head>
<body>
    <div class="sidebar">
        @include('layouts.sidebar')
    </div>
    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>
