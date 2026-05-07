@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="crm-header mb-4">
        <div>
            <h2 class="crm-title">Invoice <span class="text-sky-400">History</span></h2>
            <p class="crm-subtitle">Manage and track all business billing records</p>
        </div>
        <div class="crm-actions">
            <a href="{{ route('invoice.create') }}" class="btn btn-primary crm-btn">
                <i class="fa-solid fa-plus-circle me-2"></i> Create Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="glass-card mb-4 py-3">
        <form method="GET" action="{{ route('invoice.history') }}" class="row g-3 align-items-center px-3">
            <div class="col-md-10">
                <div class="crm-search w-100">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Search name, email or #..." value="{{ request('search') }}" class="w-100">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info w-100 font-bold">Search</button>
            </div>
        </form>
    </div>

    <div class="row">
    @forelse ($invoices as $inv)
    <div class="col-12 mb-3">
        <div class="crm-row">
            <div class="crm-left">
                <div class="avatar-circle" style="background: #0ea5e9;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="crm-name">
                        <a href="{{ route('invoice.view', $inv->invoice_no) }}" class="text-sky-400 text-decoration-none">
                            #{{ $inv->invoice_no }}
                        </a>
                    </div>
                    <div class="text-white small font-bold">{{ $inv->customer_name }}</div>
                    <div class="crm-sub text-truncate" style="max-width: 180px;">{{ $inv->customer_email }}</div>
                </div>
            </div>

            <div class="crm-center">
                <div class="crm-stat">
                    <span>Total Amount</span>
                    <strong class="text-white">${{ number_format($inv->total, 2) }}</strong>
                </div>

                <div class="crm-stat">
                    <span>Status</span>
                    @if($inv->status === 'paid')
                        <strong class="text-success"><i class="fa-solid fa-check-circle me-1"></i> PAID</strong>
                    @else
                        <strong class="text-danger"><i class="fa-solid fa-circle-exclamation me-1"></i> UNPAID</strong>
                    @endif
                </div>
            </div>

            <div class="crm-right">
                <div class="d-flex flex-md-column flex-row justify-content-end gap-2 align-items-center">
                    <a href="{{ route('invoice.view', $inv->invoice_no) }}" class="btn btn-sm btn-outline-info px-3">
                        <i class="fa-solid fa-eye me-1"></i> View
                    </a>

                    <form method="POST" action="{{ route('invoice.destroy', $inv->id) }}" onsubmit="return confirm('Delete this invoice?');" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="glass-card text-center py-5">
            <i class="fa-solid fa-ghost fa-3x text-secondary mb-3"></i>
            <p class="text-secondary">No invoices found.</p>
        </div>
    </div>
    @endforelse
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>

<style>
/* Header logic */
.crm-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
.crm-title { font-size: 28px; font-weight: 700; color: white; }
.crm-subtitle { font-size: 14px; color: #94a3b8; }
.crm-actions { display: flex; gap: 10px; align-items: center; }
.crm-search { display: flex; align-items: center; background: #0f172a; padding: 10px 15px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); }
.crm-search input { background: transparent; border: none; color: white; margin-left: 10px; outline: none; }

/* Row logic */
.crm-row { display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.9); padding: 20px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
.crm-left { display: flex; gap: 15px; width: 30%; }
.avatar-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0; }
.crm-name { font-size: 17px; font-weight: 800; }
.crm-sub { font-size: 12px; color: #94a3b8; }

.crm-center { display: flex; gap: 40px; flex-grow: 1; justify-content: center; }
.crm-stat { display: flex; flex-direction: column; align-items: center; }
.crm-stat span { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
.crm-stat strong { font-size: 15px; }

.crm-right { width: 20%; }

/* Mobile Stacking for Phones */
@media (max-width: 767.98px) {
    .crm-row { flex-direction: column; align-items: flex-start; gap: 1.2rem; }
    .crm-left, .crm-center, .crm-right { width: 100% !important; text-align: left; }
    .crm-center { 
        justify-content: space-between; 
        border-top: 1px solid rgba(255,255,255,0.1); 
        border-bottom: 1px solid rgba(255,255,255,0.1); 
        padding: 15px 0; 
        gap: 10px;
    }
    .crm-right .d-flex { justify-content: flex-start !important; flex-direction: row !important; }
    .crm-stat { align-items: flex-start; }
}
</style>
@endsection
