<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; padding: 20px; margin: 0; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #0ea5e9; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 800; color: #ffffff; text-transform: uppercase; letter-spacing: 1px; }
        .content { padding: 40px; text-align: center; }
        .content h2 { color: #1e293b; margin-top: 0; font-size: 22px; font-weight: 700; }
        .quote-box { background: #f1f5f9; border-radius: 12px; padding: 20px; margin: 25px 0; display: inline-block; width: 80%; }
        .label { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; }
        .value { font-size: 18px; color: #0ea5e9; font-weight: 800; }
        .btn { background-color: #f59e0b; color: #ffffff !important; padding: 16px 40px; text-decoration: none; border-radius: 10px; font-weight: 800; font-size: 16px; display: inline-block; margin-top: 10px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>MEDIOS BILLING</h1>
        </div>
        <div class="content">
            <h2>Quote Ready for Review</h2>
            <p>Hello <strong>{{ $quote->customer->name }}</strong>,</p>
            <p>A new professional estimate has been prepared for you from <strong>{{ $quote->company->name }}</strong>.</p>
            
            <div class="quote-box">
                <span class="label">Quote Number</span>
                <span class="value">#{{ $quote->quote_number }}</span>
            </div>

            <div style="margin-top: 20px;">
                <a href="{{ $link }}" class="btn">View & Approve Quote</a>
            </div>
        </div>
        <div class="footer">
            Sent via Medios Billing Platform<br>
            &copy; {{ date('Y') }} Medios Billing.
        </div>
    </div>
</body>
</html>
