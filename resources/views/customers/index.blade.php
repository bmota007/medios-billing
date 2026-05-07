@extends('layouts.app')

@section('content')

@php
$customerTotal = $customers->count();
$totalRevenue = 0;
$totalInvoices = 0;
$paidInvoices = 0;
$pendingInvoices = 0;

foreach($customers as $c){
    $totalRevenue += (float)($c->total_spent ?? 0);
    $totalInvoices += (int)($c->invoice_count ?? 0);
    $paidInvoices += (int)($c->paid_count ?? 0);
    $pendingInvoices += (int)($c->pending_count ?? 0);
}
@endphp

<style>
:root{
--bg:#030712;
--card:rgba(15,23,42,.88);
--line:rgba(255,255,255,.06);
--blue:#3b82f6;
--purple:#8b5cf6;
--green:#10b981;
--orange:#f59e0b;
--pink:#ec4899;
--text:#ffffff;
--muted:#94a3b8;
}

body{
background:
radial-gradient(circle at top left, rgba(59,130,246,.10), transparent 30%),
radial-gradient(circle at bottom right, rgba(139,92,246,.08), transparent 25%),
#020617 !important;
color:#fff;
}

.c-wrap{max-width:1850px;margin:auto;padding:28px;}
.c-head{
display:flex;justify-content:space-between;align-items:center;
gap:20px;margin-bottom:22px;
}
.c-title{font-size:46px;font-weight:900;line-height:1;}
.c-sub{color:var(--muted);margin-top:8px;font-size:15px;}

.new-btn{
display:inline-flex;align-items:center;gap:12px;
padding:16px 26px;
border-radius:16px;
font-weight:800;
font-size:20px;
text-decoration:none;
color:#fff !important;
background:linear-gradient(135deg,var(--blue),var(--purple));
box-shadow:0 10px 30px rgba(59,130,246,.28);
border:1px solid rgba(255,255,255,.08);
}

.kpi-grid{
display:grid;
grid-template-columns:repeat(5,1fr);
gap:16px;
margin-bottom:22px;
}

.kpi{
background:var(--card);
border:1px solid var(--line);
border-radius:22px;
padding:20px;
min-height:125px;
}

.k-label{font-size:12px;color:var(--muted);font-weight:700;text-transform:uppercase;}
.k-value{font-size:38px;font-weight:900;line-height:1;margin-top:10px;}
.k-sub{margin-top:8px;font-size:13px;font-weight:700;}

.table-card{
background:var(--card);
border:1px solid var(--line);
border-radius:24px;
padding:22px;
}

.top-tools{
display:flex;justify-content:space-between;gap:15px;align-items:center;
margin-bottom:20px;
}

.search{
background:rgba(255,255,255,.02);
border:1px solid var(--line);
border-radius:14px;
padding:12px 16px;
color:#fff;
min-width:320px;
}

.table{
width:100%;
color:#fff;
margin:0;
}

.table thead th{
border:none;
color:var(--muted);
font-size:12px;
text-transform:uppercase;
padding:16px 12px;
}

.table tbody td{
border-top:1px solid rgba(255,255,255,.04);
padding:18px 12px;
vertical-align:middle;
}

.avatar{
width:42px;height:42px;border-radius:50%;
display:grid;place-items:center;
font-weight:900;
background:linear-gradient(135deg,var(--blue),#2563eb);
}

.status{
padding:6px 12px;
border-radius:999px;
font-size:11px;
font-weight:800;
display:inline-block;
}

.s-new{background:rgba(59,130,246,.12);color:#60a5fa;}
.money{color:#10b981;font-weight:800;}

.action{
width:38px;height:38px;border-radius:12px;
display:inline-grid;place-items:center;
text-decoration:none;color:#fff !important;
margin-right:6px;
}

.a-view{background:#1d4ed8;}
.a-edit{background:#7c3aed;}
.a-del{background:#7f1d1d;}
.a-inv{background:#065f46;}

@media(max-width:1400px){
.kpi-grid{grid-template-columns:1fr 1fr;}
}

@media(max-width:768px){
.c-head,.top-tools{flex-direction:column;align-items:stretch;}
.kpi-grid{grid-template-columns:1fr;}
.c-title{font-size:34px;}
.new-btn{justify-content:center;}
.search{min-width:100%;}
}
</style>

<div class="c-wrap">

<div class="c-head">
    <div>
        <div class="c-title">Customers</div>
        <div class="c-sub">Manage your clients, contacts, and relationships in one place.</div>
    </div>

    <a href="{{ route('customers.create') }}" class="new-btn">
        ➕ New Customer
    </a>
</div>

<div class="kpi-grid">

<div class="kpi">
<div class="k-label">Total Customers</div>
<div class="k-value">{{ $customerTotal }}</div>
<div class="k-sub" style="color:#60a5fa;">Active Records</div>
</div>

<div class="kpi">
<div class="k-label">Total Revenue</div>
<div class="k-value">${{ number_format($totalRevenue,2) }}</div>
<div class="k-sub" style="color:#10b981;">From Customers</div>
</div>

<div class="kpi">
<div class="k-label">Total Invoices</div>
<div class="k-value">{{ $totalInvoices }}</div>
<div class="k-sub" style="color:#f59e0b;">Created</div>
</div>

<div class="kpi">
<div class="k-label">Paid Invoices</div>
<div class="k-value">{{ $paidInvoices }}</div>
<div class="k-sub" style="color:#8b5cf6;">Paid</div>
</div>

<div class="kpi">
<div class="k-label">Pending</div>
<div class="k-value">{{ $pendingInvoices }}</div>
<div class="k-sub" style="color:#ec4899;">Pending</div>
</div>

</div>

<div class="table-card">

<div class="top-tools">
    <div style="font-size:24px;font-weight:900;">All Customers</div>
    <input type="text" class="search" placeholder="Search customers...">
</div>

<div class="table-responsive">
<table class="table">
<thead>
<tr>
<th>Customer</th>
<th>Email</th>
<th>Phone</th>
<th>Total Invoices</th>
<th>Total Revenue</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

@forelse($customers as $customer)

<tr>
<td>
<div style="display:flex;align-items:center;gap:12px;">
<div class="avatar">{{ strtoupper(substr($customer->name,0,1)) }}</div>
<div>
<div style="font-weight:800;">{{ $customer->name }}</div>
<div style="color:#94a3b8;font-size:13px;">Joined {{ optional($customer->created_at)->format('M d, Y') }}</div>
</div>
</div>
</td>

<td>{{ $customer->email }}</td>
<td>{{ $customer->phone }}</td>
<td>{{ $customer->invoice_count ?? 0 }}</td>
<td class="money">${{ number_format($customer->total_spent ?? 0,2) }}</td>

<td>
<span class="status s-new">New</span>
</td>

<td>
<a href="{{ route('customers.show',$customer->id) }}" class="action a-view">👁</a>
<a href="{{ route('customers.edit',$customer->id) }}" class="action a-edit">✏️</a>

<form action="{{ route('customers.destroy',$customer->id) }}" method="POST" style="display:inline;">
@csrf
@method('DELETE')
<button type="submit" class="action a-del" style="border:none;">🗑</button>
</form>

<a href="{{ route('invoice.create') }}?customer={{ $customer->id }}" class="action a-inv">⚡</a>
</td>
</tr>

@empty
<tr>
<td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">
No customers found.
</td>
</tr>
@endforelse

</tbody>
</table>
</div>

</div>

</div>

@endsection
