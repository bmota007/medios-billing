@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-white font-bold mb-1">Invoice <span class="text-sky-400">History</span></h2>
            <p class="text-secondary small">Manage and track all business billing records</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('invoice.create') }}" class="btn btn-primary px-4 shadow-lg">
                <i class="fa-solid fa-plus-circle mr-2"></i> Create Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="glass-card mb-4 py-3">
        <form method="GET" action="{{ route('invoice.history') }}" class="row g-3 align-items-center px-3">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-slate-900 border-slate-700 text-secondary border-end-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-slate-900 border-slate-700 text-white border-start-0" placeholder="Search name, email or #..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info w-100 font-bold">Search</button>
            </div>
        </form>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="text-secondary small uppercase tracking-wider">
                    <tr>
                        <th class="border-0">Invoice #</th>
                        <th class="border-0">Customer</th>
                        <th class="border-0 text-center">Total</th>
                        <th class="border-0 text-center">Status</th>
                        <th class="border-0 text-end">Management</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse ($invoices as $inv)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                            <td class="py-4">
                                <a href="{{ route('invoice.view', $inv->invoice_no) }}" class="text-sky-400 font-mono font-bold text-decoration-none">
                                    #{{ $inv->invoice_no }}
                                </a>
                            </td>

                            <td>
                                <div class="text-white font-bold">{{ $inv->customer_name }}</div>
                                <div class="text-secondary small">{{ $inv->customer_email }}</div>
                            </td>

                            <td class="text-center text-white font-bold">
                                ${{ number_format($inv->total, 2) }}
                            </td>

                            <td class="text-center">
                                @if($inv->status === 'paid')
                                    <span class="badge status-badge status-paid"><i class="fa-solid fa-check-circle mr-1"></i> PAID</span>
                                @else
                                    <span class="badge status-badge status-unpaid"><i class="fa-solid fa-circle-exclamation mr-1"></i> UNPAID</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="{{ route('invoice.view', $inv->invoice_no) }}" class="btn btn-sm btn-outline-info" title="View/Manage">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>

                                    <form method="POST" action="{{ route('invoice.destroy', $inv->id) }}" onsubmit="return confirm('Delete this invoice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-secondary">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
