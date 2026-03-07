@extends('layouts.admin')

@section('content')

<div class="mb-4">
<h2>Company Control Panel</h2>
<h4 class="text-muted">{{ $company->name }}</h4>
</div>


<div class="row g-4">

<!-- Owner Info -->

<div class="col-md-6">
<div class="dashboard-card">

<h5><i class="fa-solid fa-user"></i> Owner Information</h5>

<hr>

@foreach($company->users as $user)

<div class="mb-3">
<strong>{{ $user->name }}</strong><br>
<span class="text-muted">{{ $user->email }}</span>
</div>

@endforeach

</div>
</div>


<!-- Company Statistics -->

<div class="col-md-6">
<div class="dashboard-card">

<h5><i class="fa-solid fa-chart-simple"></i> Company Statistics</h5>

<hr>

<div class="row text-center">

<div class="col-4">
<h4>{{ $company->users->count() }}</h4>
<p class="text-muted">Users</p>
</div>

<div class="col-4">
<h4>{{ $invoiceCount }}</h4>
<p class="text-muted">Invoices</p>
</div>

<div class="col-4">
<h4>${{ number_format($invoiceTotal,2) }}</h4>
<p class="text-muted">Revenue</p>
</div>

</div>

</div>
</div>

</div>


<!-- ADMIN TOOLS -->

<div class="row g-4 mt-3">

<div class="col-md-12">
<div class="dashboard-card">

<h5><i class="fa-solid fa-shield-halved"></i> Admin Tools</h5>

<hr>

<div class="d-flex gap-3 flex-wrap">

<button class="btn btn-warning">
<i class="fa-solid fa-key"></i> Reset Password
</button>

<button class="btn btn-info">
<i class="fa-solid fa-envelope"></i> Change Email
</button>

<button class="btn btn-secondary">
<i class="fa-solid fa-user-secret"></i> Login As Company
</button>

<button class="btn btn-danger">
<i class="fa-solid fa-ban"></i> Suspend Company
</button>

<button class="btn btn-dark">
<i class="fa-solid fa-trash"></i> Delete Company
</button>

</div>

</div>
</div>

</div>


<div class="row g-4 mt-3">

<!-- Recent Invoices -->

<div class="col-md-12">
<div class="dashboard-card">

<h5><i class="fa-solid fa-file-invoice"></i> Recent Invoices</h5>

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

@forelse($recentInvoices as $invoice)

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

@empty

<tr>
<td colspan="5" class="text-center text-muted">
No invoices found
</td>
</tr>

@endforelse

</tbody>

</table>

</div>
</div>

</div>


<a href="/admin/companies" class="btn btn-primary mt-4">
<i class="fa-solid fa-arrow-left"></i> Back to Companies
</a>

@endsection
