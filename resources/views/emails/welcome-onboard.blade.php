<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #0f172a; color: #ffffff; margin: 0; padding: 0; }
        .wrapper { padding: 40px; background-color: #0f172a; }
        .container { max-width: 600px; margin: 0 auto; background: #1e293b; border-radius: 24px; padding: 40px; border: 1px solid rgba(255,255,255,0.1); }
        .accent-green { color: #10b981; font-weight: bold; }
        h1 { font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 20px; }
        p { color: #cbd5e1; line-height: 1.6; font-size: 16px; }
        .plan-box { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 20px; margin: 25px 0; color: #ffffff; }
        .button { display: block; background-color: #38bdf8; color: #0f172a !important; text-decoration: none; padding: 18px 30px; border-radius: 12px; font-weight: 800; text-align: center; margin-top: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <h1>You’re Officially <span class="accent-green">Onboard!</span></h1>
            <p>Hello {{ $company->name }},</p>
            <p>Your 7-day trial has successfully concluded, and your professional subscription is now active.</p>
            
            <div class="plan-box">
                <strong>Plan:</strong> Growth Monthly<br>
                <strong>Amount Charged:</strong> ${{ number_format((float)($company->custom_price ?? 49.00), 2) }}<br>
                <strong>Next Bill Date:</strong> {{ now()->addMonth()->format('M d, Y') }}
            </div>

            <p>Your dashboard is fully unlocked and ready for unlimited use. Thank you for choosing Medios Billing.</p>

            <a href="https://app.mediosbilling.com/dashboard" class="button">ACCESS DASHBOARD</a>
        </div>
        <div class="footer">&copy; 2026 Medios Billing Platform</div>
    </div>
</body>
</html>
