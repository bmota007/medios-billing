<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
body {
    font-family: DejaVu Sans, sans-serif;
    background: #ffffff;
    color: #0f172a;
    font-size: 13px;
}

.container {
    max-width: 720px;
    margin: auto;
}

/* HEADER */
.header {
    text-align: center;
    margin-bottom: 30px;
}

.logo {
    font-size: 22px;
    font-weight: bold;
    color: #0ea5e9;
}

.title {
    font-size: 28px;
    font-weight: 800;
    margin-top: 10px;
}

.badge {
    display: inline-block;
    margin-top: 10px;
    padding: 6px 14px;
    background: #16a34a;
    color: white;
    border-radius: 999px;
    font-size: 12px;
    font-weight: bold;
}

/* BOXES */
.box {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

th {
    background: #f8fafc;
    text-align: left;
    padding: 10px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 10px;
    border-bottom: 1px solid #e2e8f0;
}

.right {
    text-align: right;
}

/* TOTAL */
.total-box {
    margin-top: 20px;
    text-align: right;
}

.total {
    font-size: 22px;
    font-weight: 800;
    color: #0ea5e9;
}

/* FOOTER */
.footer {
    margin-top: 40px;
    text-align: center;
    font-size: 11px;
    color: #64748b;
}
</style>

</head>
<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            {{ $invoice->company->name ?? 'Company' }}
        </div>

        <div class="title">
            Payment Receipt
        </div>

        @if($invoice->status === 'paid')
            <div class="badge">PAID</div>
        @endif

        <div style="margin-top:10px;color:#64748b;">
            Receipt #: {{ $invoice->invoice_no }}
        </div>
    </div>

    <!-- CUSTOMER -->
    <div class="box">
        <strong>Billed To</strong><br><br>
        {{ $invoice->customer_name }}<br>
        {{ $invoice->customer_email }}
    </div>

    <!-- DATES -->
    <div class="box">
        <strong>Transaction Details</strong><br><br>

        Date:
        {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}

        <br>

        Payment Method:
        {{ ucfirst($invoice->payment_method ?? 'Online Payment') }}
    </div>

    <!-- ITEMS -->
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Price</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>${{ number_format($item['price'], 2) }}</td>
                <td class="right">
                    ${{ number_format($item['qty'] * $item['price'], 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTAL -->
    <div class="total-box">
        <div>Subtotal: ${{ number_format($invoice->subtotal_amount, 2) }}</div>
        <div>Tax: ${{ number_format($invoice->tax_amount, 2) }}</div>

        <div class="total">
            Total Paid: ${{ number_format($invoice->total, 2) }}
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        This receipt confirms that your payment has been successfully processed.<br>
        Thank you for your business.
    </div>

</div>

</body>
