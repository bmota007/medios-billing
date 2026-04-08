<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; margin: 0; padding: 0; color: #1e293b; }
        .wrapper { padding: 40px; }
        .header { margin-bottom: 50px; width: 100%; }
        .logo-container { float: right; text-align: right; width: 50%; }
        .title-container { float: left; width: 50%; }
        .logo-text { font-size: 20px; font-weight: bold; color: #0f172a; }
        .invoice-title { font-size: 32px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 5px; }
        .muted { color: #64748b; font-size: 12px; }
        .bill-to-section { background: #f8fafc; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #e2e8f0; width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f172a; color: white; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
        .text-right { text-align: right; }
        .total-section { margin-top: 30px; float: right; width: 250px; }
        .total-row { font-size: 18px; font-weight: bold; color: #0f172a; border-top: 2px solid #0f172a !important; }
        .payment-status-card { float: left; width: 300px; margin-top: 30px; background: #f0fdf4; padding: 15px; border-radius: 8px; border: 1px solid #bbf7d0; }
        .footer { margin-top: 80px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="title-container">
            <div class="invoice-title">{{ $invoice->status === 'paid' ? 'RECEIPT' : 'INVOICE' }}</div>
            <div class="muted">Number: #{{ $invoice->invoice_no }}</div>
            <div class="muted">Date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') }}</div>
            @if($invoice->due_date && $invoice->status !== 'paid')
                <div class="muted">Due Date: {{ \Carbon\Carbon::parse($invoice->due_date)->format('m/d/Y') }}</div>
            @endif
        </div>
        <div class="logo-container">
            @if(!empty($invoice->company->logo))
                <img src="{{ public_path('storage/'.$invoice->company->logo) }}" style="max-height:70px; margin-bottom:10px;">
            @endif
            <div class="logo-text">{{ $invoice->company->name }}</div>
            <div class="muted">{{ $invoice->company->email }}</div>
            <div class="muted">{{ $invoice->company->phone }}</div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="bill-to-section">
        <div class="muted" style="margin-bottom: 5px; font-weight: bold; text-transform: uppercase;">Billed To:</div>
        <strong>{{ $invoice->customer_name }}</strong><br>
        {{ $invoice->customer_email }}<br>
        @if($invoice->customer_phone) {{ $invoice->customer_phone }}<br> @endif
        @if($invoice->street_address)
            {{ $invoice->street_address }}<br>
            {{ $invoice->city_state_zip }}
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $items = json_decode($invoice->items, true) ?? []; @endphp
            @foreach($items as $item)
            <tr>
                <td>{{ $item['service_name'] ?? ($item['desc'] ?? ($item['service'] ?? 'Service')) }}</td>
                <td class="text-right">{{ $item['qty'] ?? ($item['quantity'] ?? 1) }}</td>
                <td class="text-right">${{ number_format($item['price'] ?? ($item['unit_price'] ?? 0), 2) }}</td>
                <td class="text-right">${{ number_format(($item['qty'] ?? 1) * ($item['price'] ?? 0), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="width: 100%;">
        <div class="payment-status-card">
            @if($invoice->status === 'paid')
                <strong style="color: #166534; text-transform: uppercase;">Payment Confirmed</strong><br>
                <span class="muted">Date: {{ \Carbon\Carbon::parse($invoice->paid_at)->format('m/d/Y') }}</span><br>
                <span class="muted">Method: {{ strtoupper($invoice->payment_method ?? 'Card') }}</span>
            @else
                <strong style="color: #b45309; text-transform: uppercase;">Payment Pending</strong><br>
                <span class="muted">Please process payment by the due date.</span>
            @endif
        </div>
        <div class="total-section">
            <table style="margin-top: 0;">
                <tr>
                    <td class="muted" style="border:none;">Subtotal:</td>
                    <td class="text-right" style="border:none;">${{ number_format($invoice->total, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td style="border:none;">TOTAL:</td>
                    <td class="text-right" style="border:none;">${{ number_format($invoice->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="footer">
        @if($invoice->status === 'paid')
            Thank you for your business! This is your official receipt for payment.
        @else
            Please process your payment via the link sent to your email. Thank you!
        @endif
        <br><br>
        <strong>{{ $invoice->company->name }}</strong>
    </div>
</div>
</body>
</html>
