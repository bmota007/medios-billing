@extends('layouts.admin')

@section('content')
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="text-white font-bold mb-1">
            Service <span class="text-sky-400">Quotes</span>
        </h2>
        <p class="text-secondary small">Proposals, estimates, and customer approvals</p>
    </div>

    <a href="{{ route('quotes.create') }}" class="btn btn-primary px-4 shadow-lg font-bold">
        <i class="fa-solid fa-plus-circle mr-2"></i> Create Quote
    </a>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="glass-card p-4 border-start border-warning border-4">
            <p class="text-secondary small uppercase font-bold mb-1">Drafts</p>
            <p class="text-white h3 mb-0 font-bold">{{ $draftCount ?? 0 }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card p-4 border-start border-info border-4">
            <p class="text-secondary small uppercase font-bold mb-1">Sent</p>
            <p class="text-white h3 mb-0 font-bold">{{ $sentCount ?? 0 }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="glass-card p-4 border-start border-success border-4">
            <p class="text-secondary small uppercase font-bold mb-1">Approved</p>
            <p class="text-success h3 mb-0 font-bold">{{ $approvedCount ?? 0 }}</p>
        </div>
    </div>
</div>

<div class="glass-card">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="text-secondary small uppercase">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Contract</th> {{-- ADDED HEADER --}}
                    <th class="text-end">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($quotes ?? [] as $quote)
                <tr>
                    <td class="text-info font-bold">
                        #{{ $quote->id ?? '-' }}
                    </td>

                    <td>
                        <div class="text-white font-bold">
                            {{ optional($quote->customer)->name ?? 'Unknown Customer' }}
                        </div>
                        <div class="text-secondary small">
                            {{ optional($quote->customer)->email ?? '' }}
                        </div>
                    </td>

                    <td class="text-center text-white font-bold">
                        ${{ number_format((float) ($quote->total ?? 0), 2) }}
                    </td>

                    <td class="text-center">
                        <span class="badge bg-secondary">
                            {{ ucfirst($quote->status ?? 'draft') }}
                        </span>
                    </td>

                    {{-- NEW CONTRACT VISIBILITY LOGIC --}}
                    <td class="text-center">
                        @if(($quote->contract_status ?? '') === 'signed')
                            <span class="text-success small d-block font-bold">
                                <i class="fa-solid fa-file-signature"></i> Signed
                            </span>
                            <span class="text-secondary" style="font-size: 10px;">
                                By {{ $quote->signed_by }}
                            </span>
                        @else
                            <span class="text-secondary small">Pending</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-sm action-btn-pro px-3">
                            View
                        </a>

                        <form action="{{ route('quotes.destroy', $quote->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Delete this quote?')"
                                class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">
                        No quotes found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.font-bold { font-weight: 700; }
.uppercase { text-transform: uppercase; }
.glass-card {
    background: rgba(30, 41, 59, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 10px;
    padding: 20px;
}
.action-btn-pro {
    color: #38bdf8 !important;
    border: 1px solid #38bdf8 !important;
    background: rgba(56, 189, 248, 0.1);
    font-weight: 700;
    font-size: 0.7rem;
    transition: all 0.2s ease;
}
.action-btn-pro:hover {
    background: #38bdf8 !important;
    color: #ffffff !important;
    border-color: #38bdf8 !important;
    transform: translateY(-1px);
}
</style>

</div>
@endsection
