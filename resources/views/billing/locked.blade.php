<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Locked | Medios Billing</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 620px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 18px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
        }

        h1 {
            font-size: 32px;
            margin-bottom: 12px;
        }

        p {
            color: #cbd5e1;
            line-height: 1.7;
            margin-bottom: 14px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            margin-top: 18px;
            padding: 14px 22px;
            border-radius: 10px;
            background: #38bdf8;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        .btn:hover {
            background: #0ea5e9;
        }

        .small {
            margin-top: 18px;
            font-size: 13px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Billing Access Locked</h1>
        <p>Your company account does not currently have an active subscription or trial.</p>
        <p>Please contact support or update billing to restore access.</p>

        @if(auth()->check() && auth()->user()->role === 'super_admin')
            <a href="{{ route('admin.companies') }}" class="btn">Back to Managed Companies</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn">Back to Dashboard</a>
        @endif

        <div class="small">
            Medios Billing © {{ date('Y') }}
        </div>
    </div>
</body>
</html>

