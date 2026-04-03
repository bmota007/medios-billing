@extends('layouts.admin')

@section('content')
<div class="container-fluid">

<div class="crm-header mb-4">
    <div>
        <h2 class="crm-title">Customers</h2>
        <p class="crm-subtitle">Manage your clients, invoices, and relationships</p>
    </div>

    <div class="crm-actions">
        <form method="GET" action="{{ route('customers.index') }}" class="crm-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                type="text"
                name="search"
                placeholder="Search..."
                value="{{ request('search') }}"
            >
        </form>

        <a href="{{ route('customers.create') }}" class="btn btn-success crm-btn">
            + New
        </a>
    </div>
</div>

<div class="row">
@forelse($customers as $customer)
<div class="col-12 mb-3">
    <div class="crm-row">
        <div class="crm-left">
            <div class="avatar-circle">
                {{ strtoupper(substr($customer->name,0,1)) }}
            </div>
            <div>
                <div class="crm-name">{{ $customer->name }}</div>
                <div class="crm-sub text-truncate" style="max-width: 200px;">{{ $customer->email }}</div>
                <div class="crm-sub">{{ $customer->phone ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="crm-center">
            <div class="crm-stat">
                <span>Total</span>
                <strong>${{ number_format($customer->invoices_sum_total ?? 0, 2) }}</strong>
            </div>
            <div class="crm-stat">
                <span>Invoices</span>
                <strong>{{ $customer->invoices_count ?? 0 }}</strong>
            </div>
            <div class="crm-stat">
                <span>Paid</span>
                <strong class="text-success">{{ $customer->paid_invoices_count ?? 0 }}</strong>
            </div>
            <div class="crm-stat">
                <span>Unpaid</span>
                <strong class="text-danger">{{ $customer->unpaid_invoices_count ?? 0 }}</strong>
            </div>
            <div class="crm-stat">
                <span>Status</span>
                @php
                    $total = $customer->invoices_sum_total ?? 0;
                    $paid = $customer->paid_invoices_count ?? 0;
                    $unpaid = $customer->unpaid_invoices_count ?? 0;

                    if ($total >= 5000) { $label = 'VIP'; $color = 'text-warning'; }
                    elseif ($paid > 0 && $unpaid == 0) { $label = 'Good'; $color = 'text-success'; }
                    elseif ($unpaid > 0) { $label = 'At Risk'; $color = 'text-danger'; }
                    else { $label = 'New'; $color = 'text-secondary'; }
                @endphp
                <strong class="{{ $color }}">{{ $label }}</strong>
            </div>
        </div>

        <div class="crm-right">
            <div class="d-flex flex-md-column flex-row justify-content-end gap-2 align-items-center">
                <a href="{{ route('customers.show', $customer->id) }}" class="crm-link">View</a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="crm-link text-warning">Edit</a>
                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="crm-link text-danger border-0 bg-transparent">Delete</button>
                </form>
                <a href="{{ route('invoice.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-primary">
                    ⚡ Invoice
                </a>
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="glass-card text-center py-5">
        <i class="fa-solid fa-users-slash fa-3x text-secondary mb-3"></i>
        <p class="text-secondary">No customers found.</p>
    </div>
</div>
@endforelse
</div>

<div class="mt-4">
    {{ $customers->links() }}
</div>
</div>

<style>
.crm-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.crm-title { font-size: 28px; font-weight: 700; color: white; }
.crm-subtitle { font-size: 14px; color: #94a3b8; }
.crm-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.crm-search { display: flex; align-items: center; background: #0f172a; padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); }
.crm-search input { background: transparent; border: none; color: white; margin-left: 10px; outline: none; width: 150px; }
.crm-row { display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.9); padding: 20px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
.crm-left { display: flex; gap: 15px; width: 25%; }
.avatar-circle { width: 45px; height: 45px; background: #0ea5e9; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0; }
.crm-name { font-size: 16px; font-weight: 700; color: white; }
.crm-sub { font-size: 12px; color: #94a3b8; }
.crm-center { display: flex; gap: 30px; flex-grow: 1; justify-content: center; }
.crm-stat { display: flex; flex-direction: column; align-items: center; }
.crm-stat span { font-size: 11px; color: #64748b; text-transform: uppercase; }
.crm-stat strong { font-size: 15px; color: white; }
.crm-right { width: 15%; }
.crm-link { font-size: 13px; color: #38bdf8; text-decoration: none; }

/* MOBILE RESPONSIVENESS (PHONES) */
@media (max-width: 767.98px) {
    .crm-row { flex-direction: column; align-items: flex-start; gap: 1.5rem; }
    .crm-left, .crm-center, .crm-right { width: 100% !important; text-align: left; }
    .crm-center { justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 15px 0; }
    .crm-right .d-flex { justify-content: flex-start !important; }
    .crm-stat { align-items: flex-start; }
}
</style>
@endsection
