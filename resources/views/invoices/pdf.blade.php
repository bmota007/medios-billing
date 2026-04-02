<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size:14px; color: #333; }
        .header { width: 100%; margin-bottom: 20px; }
        .header td { vertical-align: top; border: none; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #e2e8f0; padding:12px; text-align: left; }
        th { background: #f8fafc; font-weight: bold; }
        .total-box { margin-top:20px; text-align:right; border-top: 2px solid #0ea5e9; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <h2 style="color: #0ea5e9; margin: 0;">INVOICE</h2>
                <p style="margin: 5px 0;">#{{ $invoice->invoice_no }}</p>
                <p style="font-size: 12px; color: #64748b;">Date: {{ $invoice->invoice_date->format('M d, Y') }}</p>
            </td>
            <td style="text-align:right">
                <strong style="font-size: 18px;">{{ $invoice->company->name ?? 'Service Provider' }}</strong><br>
                {{ $invoice->company->email ?? '' }}<br>
                {{ $invoice->company->phone ?? '' }}
            </td>
        </tr>
    </table>

    <hr style="border: 0; border-top: 1px solid #e2e8f0;">

    <div style="margin: 20px 0;">
        <h4 style="margin-bottom: 5px; color: #64748b;">Bill To:</h4>
        <p style="margin: 0; font-weight: bold; font-size: 16px;">{{ $invoice->customer_name }}</p>
        <p style="margin: 0; color: #64748b;">{{ $invoice->customer_email }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Service Description</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $items = is_string($invoice->items) ? json_decode($invoice->items) : $invoice->items; 
            @endphp
            @foreach($items as $item)
            <tr>
                <td>{{ $item->service_name ?? $item->desc ?? 'Service' }}</td>
                <td style="text-align: center;">{{ $item->quantity ?? $item->qty }}</td>
                <td style="text-align: right;">${{ number_format($item->unit_price ?? $item->price, 2) }}</td>
                <td style="text-align: right;">${{ number_format($item->line_total ?? $item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        <p style="font-size: 14px; margin: 0;">Subtotal: ${{ number_format($invoice->subtotal, 2) }}</p>
        <h2 style="color: #0ea5e9; margin: 5px 0;">Total Due: ${{ number_format($invoice->total, 2) }}</h2>
        @if($invoice->deposit_amount > 0)
            <p style="font-size: 12px; color: #ef4444;">Note: This invoice reflects the deposit required to begin services.</p>
        @endif
    </div>
</body>
</html>
