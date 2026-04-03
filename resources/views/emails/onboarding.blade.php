<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #0f172a; color: #ffffff; margin: 0; padding: 0; }
        .wrapper { padding: 40px; background-color: #0f172a; }
        .container { max-width: 600px; margin: 0 auto; background: #1e293b; border-radius: 24px; padding: 40px; border: 1px solid rgba(255,255,255,0.1); }
        .logo { font-size: 24px; font-weight: 800; color: #ffffff; text-align: center; margin-bottom: 30px; }
        .accent { color: #38bdf8; font-weight: bold; }
        h1 { font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 20px; }
        p { color: #cbd5e1; line-height: 1.6; font-size: 16px; }
        .plan-box { background: rgba(56, 189, 248, 0.05); border: 1px solid #38bdf8; border-radius: 12px; padding: 20px; margin: 25px 0; }
        .button { display: block; background-color: #38bdf8; color: #0f172a !important; text-decoration: none; padding: 18px 30px; border-radius: 12px; font-weight: 800; text-align: center; margin-top: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="logo">MEDIOS <span class="accent">BILLING</span></div>
        <div class="container">
            <h1>Welcome to the <span class="accent">Elite</span> Experience</h1>
            <p>Hello {{ $company->name }},</p>
            <p>Your premium billing platform is ready for setup. We have created a custom pricing plan tailored to your business needs:</p>
            
            <div class="plan-box">
                <strong style="color: #ffffff;">Rate:</strong> ${{ number_format((float)($company->custom_price ?? 40.00), 2) }} / {{ $company->billing_interval ?? 'month' }}<br>
                <strong style="color: #ffffff;">Trial:</strong> 7 Days Free
            </div>

            <p>To activate your dashboard and start your trial, please verify your email and securely add your payment method. You will not be charged until the trial concludes.</p>

            <a href="{{ url('/onboarding/setup/' . $token) }}" class="button">VERIFY & START TRIAL</a>
        </div>
        <div class="footer">&copy; 2026 Medios Billing Platform</div>
    </div>
</body>
</html>
