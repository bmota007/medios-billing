<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Medios Billing</title>
</head>
<body style="margin:0;padding:0;background:#0b1220;font-family:Arial,sans-serif;color:#ffffff;">

<div style="max-width:600px;margin:auto;background:#0b1220;border-radius:10px;overflow:hidden;">

    <!-- HEADER -->
    <div style="background:linear-gradient(90deg,#3b82f6,#06b6d4);padding:30px;text-align:center;">
        <h1 style="margin:0;color:white;">Medios Billing</h1>
    </div>

    <!-- BODY -->
    <div style="padding:30px;text-align:center;">
        <h2 style="color:#22c55e;">ACCOUNT ACTIVATED</h2>

        <h1>Welcome, {{ $user->name }}</h1>

        <p>Your business command center is ready.</p>

        <p>🚀 Full access to invoices, quotes, clients & revenue tracking</p>
        <p style="color:#22c55e;">✅ Your free trial has started</p>

        <div style="margin-top:20px;background:#111827;padding:20px;border-radius:10px;">
            <p><strong>Plan:</strong> {{ ucfirst($company->plan) }}</p>
            <p><strong>Status:</strong> Active</p>
        </div>

        <a href="{{ url('/dashboard') }}"
           style="display:inline-block;margin-top:30px;padding:15px 25px;background:#3b82f6;color:white;text-decoration:none;border-radius:6px;">
           ACCESS DASHBOARD
        </a>

        <p style="margin-top:20px;font-size:12px;color:#9ca3af;">
            Secure • Cancel anytime • No hidden fees
        </p>
    </div>

</div>

</body>
</html>
