<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Welcome to Medios Billing</title>
</head>

<body style="margin:0;padding:0;background:#ffffff;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;padding:40px 0;">
<tr>
<td align="center">

<!-- CARD -->
<table width="600" cellpadding="0" cellspacing="0" style="background:#020617;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);">

<!-- HEADER -->
<tr>
<td style="background:linear-gradient(90deg,#3b82f6,#06b6d4);padding:40px;text-align:center;">
    <img src="https://app.mediosbilling.com/public/MediosBilling.png" alt="Medios Billing" style="height:60px;">
</td>
</tr>

<!-- BODY -->
<tr>
<td style="padding:40px;text-align:center;color:#fff;">

    <div style="display:inline-block;background:#064e3b;color:#22c55e;padding:8px 16px;border-radius:20px;font-size:12px;margin-bottom:20px;">
        ACCOUNT ACTIVATED
    </div>

    <h1 style="margin:0 0 10px 0;font-size:26px;">
        Welcome, {{ $user->name }}
    </h1>

    <p style="color:#94a3b8;margin-bottom:20px;">
        Your business command center is ready.
    </p>

    <p style="color:#38bdf8;font-weight:bold;">
        🚀 Full access to invoices, quotes, clients & revenue tracking
    </p>

    <p style="color:#22c55e;font-weight:bold;margin-top:10px;">
        ✅ No charge today — your trial has started
    </p>

    <!-- PLAN BOX -->
    <div style="background:#020617;border:1px solid #1e293b;border-radius:12px;padding:20px;margin-top:25px;text-align:left;">

<div style="margin-top:25px; padding:20px; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">

    <p><strong>Plan:</strong> {{ ucfirst($company->plan) }}</p>

    <p><strong>Trial:</strong> 5 Days Free</p>

    <p><strong>Billing Starts:</strong> After Trial Ends</p>

    <p><strong>Monthly:</strong>
        @if($company->plan === 'starter')
            $49
        @elseif($company->plan === 'growth')
            $79
        @elseif($company->plan === 'pro')
            $129
        @elseif($company->plan === 'premium')
            $249
        @else
        @endif
    </p>

</div>
        </p>

    </div>

    <!-- BUTTON -->
    <div style="margin-top:30px;">
        <a href="https://app.mediosbilling.com/dashboard" style="
            background:#0ea5e9;
            padding:14px 30px;
            border-radius:10px;
            color:#fff;
            text-decoration:none;
            font-weight:bold;
            display:inline-block;
        ">
            ACCESS DASHBOARD
        </a>
    </div>

    <p style="margin-top:20px;color:#94a3b8;font-size:13px;">
        🔒 Secure • Cancel anytime • No hidden fees
    </p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
