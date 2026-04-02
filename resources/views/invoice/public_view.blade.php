@extends('layouts.admin')

@section('content')
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4 d-flex flex-wrap gap-2 align-items-center justify-content-center justify-content-lg-start">
        <a href="{{ route('invoice.history') }}" class="btn btn-dark border-secondary px-4">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        
        @if(Route::has('invoice.edit'))
            <a href="{{ route('invoice.edit', $invoice->invoice_no) }}" class="btn btn-warning font-bold">
                <i class="fas fa-edit me-1"></i> Edit Invoice
            </a>
        @endif

        <form action="{{ route('invoice.send_email', $invoice->invoice_no) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success font-bold text-white">
                <i class="fas fa-paper-plane me-1"></i> Send Invoice
            </button>
        </form>

        <a href="{{ route('invoice.download', $invoice->invoice_no) }}" class="btn btn-primary font-bold">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>

        <button onclick="window.print()" class="btn btn-info font-bold text-white">
            <i class="fas fa-print me-1"></i> Print
        </button>
        
        <button onclick="copyInvoiceLink()" class="btn btn-secondary font-bold">
            <i class="fas fa-link me-1"></i> Copy Public Link
        </button>
    </div>

    <div style="background: #0f172a; border-radius: 20px; padding: 40px; max-width: 1000px; margin: auto; border: 1px solid rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white mb-1">INVOICE #{{ $invoice->invoice_no }}</h2>
                <span class="badge {{ $invoice->status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill">
                    {{ strtoupper($invoice->status) }}
                </span>
            </div>
            <div class="text-end text-info fs-5 font-bold">
                {{ $invoice->company->name ?? '' }}
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div style="background: #1e293b; border-radius: 15px; padding: 25px; height: 100%;">
                    <div class="text-secondary small uppercase fw-bold">Billed To</div>
                    <h5 class="text-white mt-2 mb-1">{{ $invoice->customer_name }}</h5>
                    <div class="text-secondary small">{{ $invoice->customer_email }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div style="background: #1e293b; border-radius: 15px; padding: 25px; height: 100%; text-align: right;">
                    <div class="text-secondary small uppercase fw-bold">Amount Due</div>
                    <h2 style="color: #38bdf8; font-weight: 800;" class="mt-2 mb-0">${{ number_format($invoice->total, 2) }}</h2>
                </div>
            </div>
        </div>

        <div style="background: #1e293b; border-radius: 15px; padding: 25px; margin-bottom: 20px;">
            <table class="table table-dark table-borderless align-middle mb-0">
                <thead>
                    <tr class="border-bottom border-secondary">
                        <th class="ps-0">Description</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end pe-0">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $items = json_decode($invoice->items, true) ?? []; @endphp
                    @forelse($items as $item)
                        <tr>
                            <td class="ps-0">{{ $item['service_name'] ?? ($item['desc'] ?? 'Service') }}</td>
                            <td class="text-end">{{ $item['qty'] ?? ($item['quantity'] ?? 1) }}</td>
                            <td class="text-end">${{ number_format($item['price'] ?? ($item['unit_price'] ?? 0), 2) }}</td>
                            <td class="text-end pe-0 font-bold text-white">${{ number_format(($item['qty'] ?? ($item['quantity'] ?? 1)) * ($item['price'] ?? ($item['unit_price'] ?? 0)), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4">No items listed.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function copyInvoiceLink() {
        const link = "{{ route('invoice.public_view', $invoice->invoice_no) }}";
        navigator.clipboard.writeText(link).then(() => { alert('Link Copied!'); });
    }
</script>
@endsection
