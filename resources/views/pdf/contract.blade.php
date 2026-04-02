<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Agreement - {{ $quote->quote_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #1e293b; line-height: 1.6; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #0ea5e9; padding-bottom: 20px; margin-bottom: 30px; background: #f8fafc; padding-top: 20px; }
        .company-name { font-size: 22px; font-weight: bold; color: #0ea5e9; text-transform: uppercase; }
        .contract-title { font-size: 26px; margin: 10px 0; color: #0f172a; }
        .container { padding: 30px; }
        .content { margin-bottom: 50px; background: #ffffff; }
        .signature-section { margin-top: 60px; width: 100%; }
        .sig-box { width: 45%; border-top: 1px solid #64748b; padding-top: 10px; display: inline-block; vertical-align: top; }
        .spacer { width: 8%; display: inline-block; }
        .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $quote->company->name ?? 'Service Provider' }}</div>
        <h1 class="contract-title">Service Agreement</h1>
        <p>Reference: {{ $quote->quote_number }} | Date: {{ date('M d, Y') }}</p>
    </div>

    <div class="container">
        <div class="content">
            {!! $contract_body !!}
        </div>

        <div class="signature-section">
            <div class="sig-box">
                <span style="font-size: 11px; color: #64748b; font-weight: bold;">CUSTOMER SIGNATURE</span><br>
                <p style="font-size: 16px; font-weight: bold; margin: 10px 0;">{{ $quote->signed_by ?? $quote->customer->name }}</p>
                <p style="font-size: 11px;">IP: {{ request()->ip() }} | Date: {{ $quote->contract_signed_at ? $quote->contract_signed_at->format('M d, Y') : date('M d, Y') }}</p>
            </div>
            <div class="spacer"></div>
            <div class="sig-box">
                <span style="font-size: 11px; color: #64748b; font-weight: bold;">AUTHORIZED PROVIDER</span><br>
                <p style="font-size: 16px; font-weight: bold; margin: 10px 0;">{{ $quote->company->name }}</p>
                <p style="font-size: 11px;">Digitally Authenticated via Medios Billing</p>
            </div>
        </div>
    </div>

    <div class="footer">
        This is a legally binding electronic document.
    </div>
</body>
</html>
