@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-white font-bold mb-1">Invoice <span class="text-sky-400">Records</span></h2>
            <p class="text-secondary small">Manage and track your business billing history</p>
        </div>
        <div class="d-flex gap-3">
             <a href="{{ route('invoice.export.csv') }}" class="btn btn-outline-secondary border-slate-700 text-slate-300 px-4">
                <i class="fa-solid fa-file-export mr-2"></i> Export
            </a>
            <a href="{{ route('invoice.create') }}" class="btn btn-primary px-4 shadow-lg">
                <i class="fa-solid fa-plus-circle mr-2"></i> Create Invoice
            </a>
        </div>
    </div>

    <div class="glass-card mb-4 py-3">
        <form method="GET" action="{{ route('invoice.history') }}" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-slate-700 text-secondary border-end-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-transparent border-slate-700 text-white border-start-0" 
                           placeholder="Search by invoice # or customer name..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sky w-100">Filter Results</button>
            </div>
        </form>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="text-secondary small uppercase tracking-wider">
                    <tr>
                        <th class="border-0 pb-3">Invoice #</th>
                        <th class="border-0 pb-3">Customer</th>
                        <th class="border-0 pb-3">Amount</th>
                        <th class="border-0 pb-3">Status</th>
                        <th class="border-0 pb-3">Date</th>
                        <th class="border-0 pb-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($invoices as $invoice)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                        <td class="py-4">
                            <span class="text-sky-400 font-mono font-bold">#{{ $invoice->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-slate-800 rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:35px; height:35px; margin-right: 12px;">
                                    <i class="fa-solid fa-user text-slate-500" style="font-size: 0.8rem;"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold">{{ $invoice->customer_name }}</div>
                                    <div class="text-secondary" style="font-size: 0.75rem;">{{ $invoice->customer_email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="text-white font-bold">${{ number_format($invoice->total, 2) }}</div>
                        </td>
                        <td>
                            @if($invoice->status == 'paid')
                                <span class="badge bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-2">
                                    <i class="fa-solid fa-check-circle mr-1"></i> PAID
                                </span>
                            @else
                                <span class="badge bg-orange-500/10 text-orange-400 border border-orange-500/20 px-3 py-2">
                                    <i class="fa-solid fa-clock mr-1"></i> UNPAID
                                </span>
                            @endif
                        </td>
                        <td class="text-secondary small">
                            {{ $invoice->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-link text-slate-500 p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis-vertical fa-lg"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg border-slate-700">
                                    <li><a class="dropdown-item py-2" href="{{ route('invoice.view', $invoice->id) }}"><i class="fa-solid fa-eye mr-2 text-sky-400"></i> View Online</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('invoice.pdf', $invoice->id) }}"><i class="fa-solid fa-file-pdf mr-2 text-danger"></i> Download PDF</a></li>
                                    <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-paper-plane mr-2 text-warning"></i> Resend Email</a></li>
                                    <li><hr class="dropdown-divider opacity-10"></li>
                                    @if($invoice->status != 'paid')
                                    <li>
                                        <form action="{{ route('invoice.markPaid', $invoice->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 text-emerald-400"><i class="fa-solid fa-check mr-2"></i> Mark as Paid</button>
                                        </form>
                                    </li>
                                    @endif
                                    <li>
                                        <form action="{{ route('invoice.destroy', $invoice->id) }}" method="POST" onsubmit="return confirm('Delete this invoice forever?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="fa-solid fa-trash mr-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .btn-sky { background: #0ea5e9; color: white; transition: 0.3s; }
    .btn-sky:hover { background: #0284c7; color: white; transform: translateY(-1px); }
    .table-dark { --bs-table-bg: transparent; }
    .dropdown-item { font-size: 0.85rem; transition: 0.2s; }
    .dropdown-item:hover { background: rgba(56, 189, 248, 0.1); color: #38bdf8; }
</style>
@endsection
