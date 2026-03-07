<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: Arial;
    background:#f4f6f9;
    margin:0;
    padding:30px;
}

.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

h1 { margin:0; }

.grid {
    display:grid;
    grid-template-columns: repeat(4, 1fr);
    gap:20px;
    margin-bottom:40px;
}

.card {
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
}

.card h3 {
    margin:0 0 10px 0;
    font-size:14px;
    color:#777;
}

.card .amount {
    font-size:24px;
    font-weight:bold;
}

.paid { color:#27ae60; }
.unpaid { color:#e74c3c; }

.chart-container {
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 4px 12px rgba(0,0,0,.08);
    margin-bottom:30px;
}

table {
    width:100%;
    background:white;
    border-collapse:collapse;
}

th, td {
    padding:12px;
    border-bottom:1px solid #eee;
}

th { background:#fafafa; }

.btn {
    padding:10px 18px;
    border-radius:6px;
    text-decoration:none;
    font-weight:600;
    display:inline-block;
    color:white;
}
</style>
</head>

<body>

<div class="header">
<h1>{{ current_company()->name }} — Admin Dashboard</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                style="background:#dc2626;
                       color:white;
                       padding:8px 14px;
                       border:none;
                       border-radius:6px;
                       cursor:pointer;">
            Sign Out
        </button>
    </form>
</div>

<div style="margin-bottom:25px; display:flex; gap:15px; flex-wrap:wrap;">

    <a href="{{ route('customers.create') }}"
       style="background:#10b981;color:white;padding:12px 18px;border-radius:6px;text-decoration:none;font-weight:600;">
        + New Customer
    </a>

    <a href="{{ route('customers.index') }}"
       style="background:#6366f1;color:white;padding:12px 18px;border-radius:6px;text-decoration:none;font-weight:600;">
        View Customers
    </a>

    <a href="{{ route('invoice.form') }}"
       style="background:#2563eb;color:white;padding:12px 18px;border-radius:6px;text-decoration:none;font-weight:600;">
        + Create Invoice
    </a>

    <a href="{{ route('invoice.history') }}"
       style="background:#111827;color:white;padding:12px 18px;border-radius:6px;text-decoration:none;font-weight:600;">
        History
    </a>

</div>

<div class="grid">
    <div class="card">
        <h3>Total Revenue</h3>
        <div class="amount paid">${{ number_format($totalRevenue,2) }}</div>
    </div>

    <div class="card">
        <h3>This Month</h3>
        <div class="amount paid">${{ number_format($thisMonthRevenue,2) }}</div>
    </div>

    <div class="card">
        <h3>Paid</h3>
        <div class="amount paid">{{ $paidCount }}</div>
    </div>

    <div class="card">
        <h3>Unpaid</h3>
        <div class="amount unpaid">{{ $unpaidCount }}</div>
    </div>
</div>

<div class="card" style="margin-bottom:30px;">
    <h3>Outstanding Balance</h3>
    <div class="amount unpaid">${{ number_format($outstandingBalance,2) }}</div>
</div>

<div class="chart-container">
    <h3>Monthly Revenue ({{ now()->year }})</h3>
    <canvas id="revenueChart"></canvas>
</div>

<h2>Recent Payments</h2>

<table>
<thead>
<tr>
    <th>Invoice</th>
    <th>Customer</th>
    <th>Amount</th>
    <th>Date Paid</th>
</tr>
</thead>
<tbody>
@forelse($recentPayments as $invoice)
<tr>
    <td>{{ $invoice->invoice_no }}</td>
    <td>{{ $invoice->customer_name }}</td>
    <td>${{ number_format($invoice->total,2) }}</td>
    <td>{{ optional($invoice->paid_at)->format('m/d/Y H:i') }}</td>
</tr>
@empty
<tr>
    <td colspan="4">No payments yet.</td>
</tr>
@endforelse
</tbody>
</table>

<br><br>

<a href="{{ route('invoice.history') }}" class="btn" style="background:#0657bd;">
    Invoice History
</a>

<script>
const ctx = document.getElementById('revenueChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            label: 'Revenue',
            data: [
                @for($i=1; $i<=12; $i++)
                    {{ $monthlyData[$i] ?? 0 }},
                @endfor
            ],
            backgroundColor: '#0657bd'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display:false }
        }
    }
});
</script>

</body>
</html>
