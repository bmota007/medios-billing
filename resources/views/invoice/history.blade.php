<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice History</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 20px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

h1 { margin: 0; }

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th { background: #f0f0f0; }

.search-box { margin-bottom: 15px; }

.search-box input {
    padding: 8px;
    width: 280px;
}

.btn {
    padding: 8px 14px;
    background: #0657bd;
    color: #fff;
    border: none;
    cursor: pointer;
    border-radius: 6px;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    margin-right: 6px;
}

.btn:hover { opacity: 0.9; }

.logout-btn {
    background: none;
    border: none;
    color: #cc0000;
    cursor: pointer;
    text-decoration: underline;
    font-size: 14px;
}

.status-paid {
    background:#6ac259;
    color:white;
    padding:6px 10px;
    border-radius:4px;
    font-size:12px;
    font-weight:bold;
}

.status-unpaid {
    background:#e74c3c;
    color:white;
    padding:6px 10px;
    border-radius:4px;
    font-size:12px;
    font-weight:bold;
}

.mark-paid-form select,
.mark-paid-form input {
    padding: 5px;
    font-size: 12px;
}
</style>
</head>

<body>

<div class="header">

    <div class="header-left">
        <h1>Invoice History</h1>

        <a href="{{ route('invoice.form') }}"
           class="btn"
           style="background:#2563eb;">
            + Create Invoice
        </a>

        <a href="{{ route('admin.dashboard') }}"
           class="btn"
           style="background:#111827;">
            Dashboard
        </a>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">Log out</button>
    </form>

</div>

<div class="search-box">
    <form method="GET" action="{{ route('invoice.history') }}">
        <input
            type="text"
            name="search"
            placeholder="Search name or email…"
            value="{{ request('search') }}"
        >
        <button type="submit" class="btn">Search</button>
    </form>
</div>

<table>
<thead>
<tr>
    <th>Invoice #</th>
    <th>Customer</th>
    <th>Email</th>
    <th>Total</th>
    <th>Sent</th>
    <th>Actions</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
@forelse ($invoices as $inv)
<tr>

    <td>
        <a href="{{ route('invoice.view', $inv) }}"
           target="_blank"
           style="color:#0657bd; font-weight:bold; text-decoration:underline;">
            {{ $inv->invoice_no }}
        </a>
    </td>

    <td>{{ $inv->customer_name }}</td>
    <td>{{ $inv->customer_email }}</td>
    <td>${{ number_format($inv->total, 2) }}</td>
    <td>{{ optional($inv->sent_at)->format('m/d/Y H:i') }}</td>

    <td>

        <a href="{{ route('invoice.view', $inv) }}"
           target="_blank"
           class="btn">
            View PDF
        </a>

        @if($inv->status !== 'paid')

            <!-- Resend -->
            <form method="POST"
                  action="{{ route('invoice.resend', $inv) }}"
                  style="display:inline;">
                @csrf
                <button type="submit"
                        class="btn"
                        style="background:#f39c12;">
                    Resend
                </button>
            </form>

            <!-- Mark Paid -->
            <form method="POST"
                  action="{{ route('invoice.markPaid', $inv->id) }}"
                  class="mark-paid-form"
                  style="display:inline-flex; gap:6px; align-items:center;">
                @csrf

                <select name="payment_method" required>
                    <option value="">Method</option>
                    <option value="zelle">Zelle</option>
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                </select>

                <input type="text"
                       name="check_number"
                       placeholder="Check #"
                       style="width:90px;">

                <button type="submit"
                        style="background:#10b981;
                               color:white;
                               padding:6px 10px;
                               border-radius:6px;
                               border:none;">
                    Mark Paid
                </button>
            </form>

        @endif

    </td>

    <td>
        @if($inv->status === 'paid')
            <span class="status-paid">✔ PAID</span>
        @else
            <span class="status-unpaid">UNPAID</span>
        @endif
    </td>

</tr>
@empty
<tr>
    <td colspan="7">No invoices found.</td>
</tr>
@endforelse
</tbody>
</table>

<br>

{{ $invoices->links() }}

</body>
</html>
