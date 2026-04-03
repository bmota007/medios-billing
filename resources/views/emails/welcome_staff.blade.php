<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #020617; color: #ffffff; margin: 0; padding: 0; }
        .wrapper { width: 100%; padding: 40px 0; }
        .container { max-width: 600px; margin: 0 auto; background: #0f172a; border-radius: 24px; overflow: hidden; border: 1px solid #1e293b; }
        .hero { background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); padding: 40px; text-align: center; }
        .content { padding: 40px; }
        .step { display: flex; margin-bottom: 25px; }
        .step-num { width: 30px; height: 30px; background: #38bdf8; color: #000; border-radius: 50%; text-align: center; line-height: 30px; font-weight: bold; margin-right: 15px; flex-shrink: 0; }
        .creds-box { background: #1e293b; border-radius: 12px; padding: 20px; margin: 20px 0; border-left: 4px solid #38bdf8; }
        .btn { display: block; background: #38bdf8; color: #000000 !important; padding: 16px; border-radius: 12px; text-decoration: none; font-weight: 800; text-align: center; margin-top: 30px; }
        .warning-box { background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); padding: 15px; border-radius: 8px; font-size: 13px; color: #fbbf24; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="hero">
                <h1 style="margin:0; color: #ffffff; font-size: 28px;">Welcome to the Team</h1>
                <p style="color: rgba(255,255,255,0.8); margin-top: 10px;">{{ $details['company'] }} is ready for you.</p>
            </div>
            <div class="content">
                <p style="color: #94a3b8;">Hello {{ $details['name'] }}, your company has granted you access to the Medios Billing platform. Follow these steps to get started:</p>
                
                <div class="step">
                    <div class="step-num">1</div>
                    <div><strong>Access Your Account</strong><br><span style="color: #64748b; font-size: 13px;">Login with your temporary credentials.</span></div>
                </div>

                <div class="creds-box">
                    <div style="color: #94a3b8; font-size: 12px; margin-bottom: 5px;">CREDENTIALS</div>
                    <div style="color: #ffffff; font-weight: bold;">User: {{ $details['email'] }}</div>
                    <div style="color: #38bdf8; font-weight: bold;">Pass: {{ $details['temp_password'] }}</div>
                </div>

                <div class="step">
                    <div class="step-num">2</div>
                    <div><strong>Legal & Confidentiality</strong><br><span style="color: #64748b; font-size: 13px;">You must sign the digital non-disclosure agreement on your first login.</span></div>
                </div>

                <div class="warning-box">
                    <strong>CONFIDENTIALITY NOTICE:</strong> This platform contains private trade secrets. Unauthorized sharing of data will result in immediate termination and legal action.
                </div>

                <a href="{{ url('/login') }}" class="btn">START ONBOARDING</a>
            </div>
        </div>
    </div>
</body>
</html>
