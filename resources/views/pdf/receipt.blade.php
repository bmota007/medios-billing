<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; color:#333; }
        .container { padding:30px; }
        .header { display:flex; justify-content:space-between; align-items:center; }
        .title { font-size:28px; font-weight:bold; }
        .paid { color:green; font-weight:bold; font-size:18px; }
        .section { margin-top:25px; }
        table { width:100%; border-collapse:collapse; margin-top:15px; }
        th { background:#111827; color:white; padding:10px; text-align:left; }
        td { padding:10px; border-bottom:1px solid #ddd; }
        .total { text-align:right; font-size:18px; font-weight:bold; margin-top:20px; }
        .company { text-align:right; }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <div>
            <div class="title">RECEIPT</div>
            <div>#{{ $invoice->invoice_no }}</div>
            <div class="paid">PAID ✔</div>
        </div>

        <div class="company">
            <strong>{{ $invoice->company->name ?? 'Company' }}</strong><br>
            {{ $invoice->company->email ?? '' }}
        </div>
    </div>

    <div class="section">
        <strong>Billed To:</strong><br>
        {{ $invoice->customer_name }}<br>
        {{ $invoice->customer_email }}
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach(json_decode($invoice->items, true) as $item)
                <tr>
                    <td>{{ $item['desc'] }}</td>
                    <td>{{ $item['qty'] }}</td>
                    <td>${{ number_format($item['price'], 2) }}</td>
                    <td>${{ number_format($item['qty'] * $item['price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        Total Paid: ${{ number_format($invoice->total, 2) }}
    </div>

</div>

</body>
</html>
