@extends('layouts.admin')

@section('content')
<div class="container-fluid">

<!-- CRM HEADER -->
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
                placeholder="Search customers..."
                value="{{ request('search') }}"
            >
        </form>

        <a href="{{ route('customers.create') }}" class="btn btn-success crm-btn">
            + New Customer
        </a>

    </div>

</div>

<!-- CUSTOMER LIST -->
<div class="row">

@forelse($customers as $customer)

<div class="col-12 mb-3">

    <div class="crm-row">

        <!-- LEFT -->
        <div class="crm-left">
            <div class="avatar-circle">
                {{ strtoupper(substr($customer->name,0,1)) }}
            </div>

            <div>
                <div class="crm-name">{{ $customer->name }}</div>
                <div class="crm-sub">{{ $customer->email }}</div>
                <div class="crm-sub">{{ $customer->phone ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- CENTER -->

<!-- CENTER -->
<div class="crm-center">

    <div class="crm-stat">
        <span>Total</span>
        <strong>
            ${{ number_format($customer->invoices_sum_total ?? 0, 2) }}
        </strong>
    </div>

    <div class="crm-stat">
        <span>Invoices</span>
        <strong>{{ $customer->invoices_count ?? 0 }}</strong>
    </div>

    <div class="crm-stat">
        <span>Paid</span>
        <strong class="text-success">
            {{ $customer->paid_invoices_count ?? 0 }}
        </strong>
    </div>

    <div class="crm-stat">
        <span>Unpaid</span>
        <strong class="text-danger">
            {{ $customer->unpaid_invoices_count ?? 0 }}
        </strong>
    </div>

    <div class="crm-stat">
        <span>Status</span>

        @php
            $total = $customer->invoices_sum_total ?? 0;
            $paid = $customer->paid_invoices_count ?? 0;
            $unpaid = $customer->unpaid_invoices_count ?? 0;

            if ($total >= 5000) {
                $label = 'VIP';
                $color = 'text-warning';
            } elseif ($paid > 0 && $unpaid == 0) {
                $label = 'Good';
                $color = 'text-success';
            } elseif ($unpaid > 0) {
                $label = 'At Risk';
                $color = 'text-danger';
            } else {
                $label = 'New';
                $color = 'text-secondary';
            }
        @endphp

        <strong class="{{ $color }}">
            {{ $label }}
        </strong>
    </div>

</div>
        <!-- RIGHT -->
        <div class="crm-right">

            <a href="{{ route('customers.show', $customer->id) }}" class="crm-link">
                View
            </a>

            <a href="{{ route('customers.edit', $customer->id) }}" class="crm-link text-warning">
                Edit
            </a>

            <form action="{{ route('customers.destroy', $customer->id) }}"
                  method="POST"
                  onsubmit="return confirm('Delete this customer?')">
                @csrf
                @method('DELETE')

                <button class="crm-link text-danger border-0 bg-transparent">
                    Delete
                </button>
            </form>

            <a href="{{ route('invoice.create', ['customer_id' => $customer->id]) }}"
               class="btn btn-sm btn-primary mt-2">
                ⚡ Invoice
            </a>

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

<!-- PAGINATION -->
<div class="mt-4">
    {{ $customers->links() }}
</div>

</div>

<style>

/* HEADER */
.crm-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.crm-title {
    font-size: 28px;
    font-weight: 700;
    color: white;
}

.crm-subtitle {
    font-size: 14px;
    color: #94a3b8;
}

.crm-actions {
    display: flex;
    gap: 15px;
}

/* SEARCH */
.crm-search {
    display: flex;
    align-items: center;
    background: #0f172a;
    padding: 10px 15px;
    border-radius: 10px;
    min-width: 260px;
}

.crm-search input {
    background: transparent;
    border: none;
    color: white;
    margin-left: 10px;
    outline: none;
}

/* ROW */
.crm-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(15,23,42,0.9);
    padding: 20px;
    border-radius: 14px;
    transition: 0.2s;
}

.crm-row:hover {
    transform: translateY(-2px);
}

/* LEFT */
.crm-left {
    display: flex;
    gap: 15px;
    width: 30%;
}

.avatar-circle {
    width: 50px;
    height: 50px;
    background: #0ea5e9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

/* TEXT */
.crm-name {
    font-size: 16px;
    font-weight: 700;
    color: white;
}

.crm-sub {
    font-size: 13px;
    color: #94a3b8;
}

/* CENTER */
.crm-center {
    display: flex;
    gap: 40px;
}

.crm-stat span {
    font-size: 12px;
    color: #64748b;
}

.crm-stat strong {
    font-size: 16px;
    color: white;
}

/* RIGHT */
.crm-right {
    text-align: right;
}

.crm-link {
    display: block;
    font-size: 13px;
    color: #38bdf8;
}

.crm-link:hover {
    text-decoration: underline;
}

</style>

@endsection
