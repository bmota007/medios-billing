<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #0f172a; color: #ffffff; margin: 0; padding: 0; }
        .wrapper { padding: 40px; background-color: #0f172a; }
        .container { max-width: 600px; margin: 0 auto; background: #1e293b; border-radius: 24px; padding: 40px; border: 1px solid rgba(255,255,255,0.1); }
        .accent { color: #38bdf8; font-weight: bold; }
        h1 { font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 20px; }
        /* Brightened the grey text to #cbd5e1 for better readability on dark bg */
        p { color: #cbd5e1; line-height: 1.6; font-size: 16px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <h1>Checking in on your <span class="accent">experience</span></h1>
            <p>Hello {{ $company->name }},</p>
            <p>We noticed you’ve been using <span class="accent">Medios Billing</span> for three days now. Our primary goal is to ensure our platform provides the professional edge your business operations deserve.</p>
            <p>Is the system meeting your expectations? Do you have any questions regarding invoice customization or your automation settings?</p>
            <p><strong>We are here to assist.</strong> Simply reply to this email if there is anything we can do to help you get the most out of your trial period.</p>
            <p>Best regards,<br><span class="accent">The Medios Billing Team</span></p>
        </div>
        <div class="footer">&copy; 2026 Medios Billing Platform</div>
    </div>
</body>
</html>
