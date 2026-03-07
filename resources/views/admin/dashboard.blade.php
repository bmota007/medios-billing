@extends('layouts.admin')

@section('content')

<h2 class="mb-4">
@if(auth()->user()->is_admin)
Super Admin Dashboard
@else
{{ auth()->user()->company->name ?? 'Company' }} Dashboard
@endif
</h2>

<div class="row g-4 mb-4">

<!-- Companies -->
@if(auth()->user()->is_admin)
<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-building fa-2x text-primary mb-2"></i>
<h6>Total Companies</h6>
<h2>{{ $companies ?? 0 }}</h2>
</div>
</div>
@endif

<!-- Users -->
@if(auth()->user()->is_admin)
<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-users fa-2x text-success mb-2"></i>
<h6>Total Users</h6>
<h2>{{ $users ?? 0 }}</h2>
</div>
</div>
@endif

<!-- Invoices -->
<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-file-invoice fa-2x text-warning mb-2"></i>
<h6>Total Invoices</h6>
<h2>{{ $invoices ?? 0 }}</h2>
</div>
</div>

<!-- Revenue -->
<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-dollar-sign fa-2x text-danger mb-2"></i>
<h6>Total Revenue</h6>
<h2>${{ number_format($revenue ?? 0,2) }}</h2>
</div>
</div>

</div>


<!-- SaaS Metrics -->

<div class="row g-4 mb-4">

<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-chart-line fa-2x text-success mb-2"></i>
<h6>MRR</h6>
<h2 class="text-success">${{ number_format($mrr ?? 0,2) }}</h2>
<p class="text-muted">Monthly Recurring Revenue</p>
</div>
</div>

<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-calendar fa-2x text-primary mb-2"></i>
<h6>ARR</h6>
<h2 class="text-primary">${{ number_format($arr ?? 0,2) }}</h2>
<p class="text-muted">Annual Recurring Revenue</p>
</div>
</div>

<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-file-invoice fa-2x text-warning mb-2"></i>
<h6>Invoices This Month</h6>
<h2>{{ $invoicesThisMonth ?? 0 }}</h2>
<p class="text-muted">Current Month</p>
</div>
</div>

<div class="col-md-3">
<div class="dashboard-card">
<i class="fa-solid fa-building fa-2x text-info mb-2"></i>
<h6>Active Companies</h6>
<h2>{{ $activeCompanies ?? 0 }}</h2>
<p class="text-muted">Platform Clients</p>
</div>
</div>

</div>



<div class="row g-4">

<!-- Invoice Stats -->

<div class="col-md-6">
<div class="dashboard-card">

<h5>Invoice Status</h5>
<hr>

<div class="row text-center">

<div class="col-6">
<h3 class="text-success">{{ $paidInvoices ?? 0 }}</h3>
<p>Paid</p>
</div>

<div class="col-6">
<h3 class="text-warning">{{ $pendingInvoices ?? 0 }}</h3>
<p>Pending</p>
</div>

</div>

</div>
</div>


<!-- Revenue Chart -->

<div class="col-md-6">
<div class="dashboard-card">

<h5>Revenue Overview</h5>
<hr>

<canvas id="revenueChart"></canvas>

</div>
</div>

</div>



<!-- Top Companies by Revenue -->

@if(auth()->user()->is_admin)

<div class="row g-4 mt-4">

<div class="col-md-12">

<div class="dashboard-card">

<h5>Top Companies by Revenue</h5>

<hr>

<table class="table table-hover">

<thead>
<tr>
<th>Company</th>
<th>Revenue</th>
<th>Invoices</th>
</tr>
</thead>

<tbody>

@forelse($topCompanies ?? [] as $company)

<tr>
<td>{{ $company->name }}</td>
<td>${{ number_format($company->revenue,2) }}</td>
<td>{{ $company->invoices_count }}</td>
</tr>

@empty

<tr>
<td colspan="3" class="text-center text-muted">
No revenue data yet
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

</div>

@endif



<!-- Recent Invoices -->

<div class="row g-4 mt-4">

<div class="col-md-12">

<div class="dashboard-card">

<h5>Recent Invoices</h5>

<hr>

<table class="table table-hover">

<thead>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>

<tbody>

@foreach($recentInvoices ?? [] as $invoice)

<tr>

<td>#{{ $invoice->id }}</td>

<td>{{ $invoice->customer_name ?? 'Customer' }}</td>

<td>${{ number_format($invoice->total,2) }}</td>

<td>

@if($invoice->status == 'paid')
<span class="badge bg-success">Paid</span>
@else
<span class="badge bg-warning">Pending</span>
@endif

</td>

<td>{{ $invoice->created_at->format('M d, Y') }}</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('revenueChart');

new Chart(ctx, {

type: 'line',

data: {

labels: [
'Jan','Feb','Mar','Apr','May','Jun',
'Jul','Aug','Sep','Oct','Nov','Dec'
],

datasets: [{

label: 'Revenue',

data: [
{{ $revenueTrend[1] ?? 0 }},
{{ $revenueTrend[2] ?? 0 }},
{{ $revenueTrend[3] ?? 0 }},
{{ $revenueTrend[4] ?? 0 }},
{{ $revenueTrend[5] ?? 0 }},
{{ $revenueTrend[6] ?? 0 }},
{{ $revenueTrend[7] ?? 0 }},
{{ $revenueTrend[8] ?? 0 }},
{{ $revenueTrend[9] ?? 0 }},
{{ $revenueTrend[10] ?? 0 }},
{{ $revenueTrend[11] ?? 0 }},
{{ $revenueTrend[12] ?? 0 }}
],

borderColor: '#6366f1',
backgroundColor: 'rgba(99,102,241,0.15)',
fill: true,
tension: 0.4,
pointRadius: 4

}]

},

options: {

responsive: true,

plugins: {
legend: {
display: false
}
},

scales: {

y: {
beginAtZero: true,
ticks: {
callback: function(value) {
return '$' + value;
}
}
}

}

}

});

</script>

@endsection
