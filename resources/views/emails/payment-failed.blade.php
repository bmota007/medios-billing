<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #0f172a; color: #ffffff; margin: 0; padding: 0; }
        .wrapper { padding: 40px; background-color: #0f172a; }
        .container { max-width: 600px; margin: 0 auto; background: #1e293b; border-radius: 24px; padding: 40px; border: 1px solid rgba(239, 68, 68, 0.3); }
        .accent-red { color: #ef4444; font-weight: bold; }
        h1 { font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 20px; }
        p { color: #cbd5e1; line-height: 1.6; font-size: 16px; }
        .alert-box { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; border-radius: 12px; padding: 20px; margin: 25px 0; color: #fecaca; }
        .button { display: block; background-color: #ef4444; color: #ffffff !important; text-decoration: none; padding: 18px 30px; border-radius: 12px; font-weight: 800; text-align: center; margin-top: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <h1>Payment <span class="accent-red">Failed</span></h1>
            <p>Hello {{ $company->name }},</p>
            <p>We were unable to process your subscription payment for <span class="accent-red">Medios Billing</span>.</p>
            
            <div class="alert-box">
                <strong>Status:</strong> Payment Failed (Attempt Day {{ $failDays }})<br>
                @if($failDays >= 3)
                    <strong>Warning:</strong> Your account is currently <strong>LOCKED</strong>.
                @else
                    <strong>Warning:</strong> Your account will be locked in {{ 3 - $failDays }} days.
                @endif
            </div>

            <p>To prevent account deletion (scheduled for Day 7 of failure), please update your payment method immediately.</p>

            <a href="https://app.mediosbilling.com/subscribe" class="button">UPDATE PAYMENT METHOD</a>
        </div>
        <div class="footer">&copy; 2026 Medios Billing Platform</div>
    </div>
</body>
</html>
