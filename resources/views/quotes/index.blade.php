@extends('layouts.admin')

@section('content')
<div class="container-fluid">

<div class="crm-header mb-4">
    <div>
        <h2 class="crm-title">Service <span class="text-sky-400">Quotes</span></h2>
        <p class="crm-subtitle">Track and manage your outgoing estimates</p>
    </div>

    <div class="crm-actions">
        <form method="GET" action="{{ route('quotes.index') }}" class="crm-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
                type="text"
                name="search"
                placeholder="Search..."
                value="{{ request('search') }}"
            >
        </form>

        <a href="{{ route('quotes.create') }}" class="btn btn-success crm-btn">
            + New Quote
        </a>
    </div>
</div>

<div class="row">

@forelse($quotes as $quote)

<div class="col-12 mb-3">
    <div class="crm-row">

        <div class="crm-left">
            <div class="avatar-circle" style="background: #a855f7;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>

            <div>
                <div class="crm-name">#{{ $quote->quote_number ?? $quote->id }}</div>
                <div class="crm-sub">{{ $quote->customer->name ?? 'Deleted Customer' }}</div>
                <div class="crm-sub small text-secondary">
                    {{ $quote->created_at ? $quote->created_at->format('M d, Y') : 'N/A' }}
                </div>
            </div>
        </div>

        <div class="crm-center">
            <div class="crm-stat">
                <span>Amount</span>
                <strong>${{ number_format($quote->total ?? 0, 2) }}</strong>
            </div>

            <div class="crm-stat">
                <span>Items</span>
                <strong>{{ isset($quote->items) ? $quote->items->count() : 0 }}</strong>
            </div>

            <div class="crm-stat">
                <span>Status</span>
                @php
                    $status = strtolower($quote->status ?? 'draft');
                    $color = match($status) {
                        'approved' => 'text-success',
                        'sent' => 'text-sky-400',
                        'declined' => 'text-danger',
                        default => 'text-secondary',
                    };
                @endphp
                <strong class="{{ $color }}">
                    {{ strtoupper($status) }}
                </strong>
            </div>
        </div>

        <div class="crm-right">
            <div class="d-flex flex-md-column flex-row justify-content-end gap-2 align-items-center">
                <a href="{{ route('quotes.show', $quote->id) }}" class="crm-link">View</a>
                <a href="{{ route('quotes.edit', $quote->id) }}" class="crm-link text-warning">Edit</a>
                
                @if(isset($quote->status) && strtolower($quote->status) === 'approved')
                    <span class="badge bg-success text-white">SIGNED</span>
                @endif
            </div>
        </div>

    </div>
</div>

@empty

<div class="col-12">
    <div class="glass-card text-center py-5">
        <i class="fa-solid fa-file-circle-xmark fa-3x text-secondary mb-3"></i>
        <p class="text-secondary">No quotes found.</p>
    </div>
</div>

@endforelse

</div>

@if(method_exists($quotes, 'links'))
<div class="mt-4">
    {{ $quotes->links() }}
</div>
@endif

</div>

<style>
/* Header/Actions logic is identical to Customers for consistency */
.crm-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.crm-title { font-size: 28px; font-weight: 700; color: white; }
.crm-subtitle { font-size: 14px; color: #94a3b8; }
.crm-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.crm-search { display: flex; align-items: center; background: #0f172a; padding: 8px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); }
.crm-search input { background: transparent; border: none; color: white; margin-left: 10px; outline: none; width: 150px; }

/* Row logic */
.crm-row { display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.9); padding: 20px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
.crm-left { display: flex; gap: 15px; width: 30%; }
.avatar-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0; }
.crm-name { font-size: 16px; font-weight: 700; color: white; }
.crm-sub { font-size: 12px; color: #94a3b8; }

.crm-center { display: flex; gap: 40px; flex-grow: 1; justify-content: center; }
.crm-stat { display: flex; flex-direction: column; align-items: center; }
.crm-stat span { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
.crm-stat strong { font-size: 15px; color: white; }

.crm-right { width: 15%; }
.crm-link { font-size: 13px; color: #38bdf8; text-decoration: none; }

/* Mobile stacking */
@media (max-width: 767.98px) {
    .crm-row { flex-direction: column; align-items: flex-start; gap: 1.2rem; }
    .crm-left, .crm-center, .crm-right { width: 100% !important; text-align: left; }
    .crm-center { justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.1); padding: 15px 0; gap: 10px; }
    .crm-right .d-flex { justify-content: flex-start !important; flex-direction: row !important; flex-wrap: wrap; }
    .crm-stat { align-items: flex-start; }
}
</style>
@endsection
