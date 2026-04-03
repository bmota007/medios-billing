@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    
    <div class="dashboard-card mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="text-white fw-bold mb-1">{{ $customer->name }}</h2>
                @if($customer->company_name)
                    <p class="text-info small mb-2"><i class="fa-solid fa-building me-1"></i> {{ $customer->company_name }}</p>
                @endif
                <div class="d-flex flex-wrap gap-3 mt-2">
                    @if($customer->email)
                        <span class="text-secondary small"><i class="fa-solid fa-envelope me-1"></i> {{ $customer->email }}</span>
                    @endif
                    @if($customer->phone)
                        <span class="text-secondary small"><i class="fa-solid fa-phone me-1"></i> {{ $customer->phone }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-primary btn-sm px-3">
                    <i class="fa-solid fa-user-pen me-1"></i> Edit
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4 border-bottom border-secondary border-opacity-25 pb-3">
        <button class="tab-link active" onclick="openTab(event,'summary')">Summary</button>
        <button class="tab-link" onclick="openTab(event,'quotes')">Quotes</button>
        <button class="tab-link" onclick="openTab(event,'invoices')">Invoices</button>
        <button class="tab-link" onclick="openTab(event,'emails')">Emails</button>
        <button class="tab-link" onclick="openTab(event,'notes')">Notes</button>
    </div>

    <div id="summary" class="tab-content active">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="dashboard-card text-center py-4 border-start border-primary border-4">
                    <p class="text-secondary small uppercase fw-bold mb-1">Quotes</p>
                    <h2 class="text-white mb-0">{{ $customer->quotes->count() }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card text-center py-4 border-start border-success border-4">
                    <p class="text-secondary small uppercase fw-bold mb-1">Invoices</p>
                    <h2 class="text-white mb-0">{{ $customer->invoices->count() }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card text-center py-4 border-start border-info border-4">
                    <p class="text-secondary small uppercase fw-bold mb-1">Total Value</p>
                    <h2 class="text-white mb-0">${{ number_format($customer->invoices->sum('total'), 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div id="quotes" class="tab-content">
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white fw-bold mb-0">Quotes</h4>
                <a href="{{ route('quotes.create',['customer_id'=>$customer->id]) }}" class="btn btn-info btn-sm fw-bold">
                    <i class="fa-solid fa-plus me-1"></i> New Quote
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr class="text-secondary small uppercase">
                            <th>Quote ID</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->quotes as $quote)
                        <tr>
                            <td class="text-info fw-bold">#{{ $quote->id }}</td>
                            <td class="text-white">${{ number_format($quote->total, 2) }}</td>
                            <td><span class="badge bg-{{ $quote->status == 'approved' ? 'success' : 'warning' }}">{{ ucfirst($quote->status) }}</span></td>
                            <td class="text-secondary small">{{ $quote->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-outline-info btn-sm">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-secondary">No quotes found for this customer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="invoices" class="tab-content">
        <div class="dashboard-card">
            <h4 class="text-white fw-bold mb-4">Invoices</h4>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr class="text-secondary small uppercase">
                            <th>Invoice ID</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->invoices as $invoice)
                        <tr>
                            <td class="text-info fw-bold">#{{ $invoice->id }}</td>
                            <td class="text-white">${{ number_format($invoice->total, 2) }}</td>
                            <td><span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($invoice->status) }}</span></td>
                            <td class="text-secondary small">{{ $invoice->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('invoice.view', $invoice->id) }}" class="btn btn-outline-info btn-sm">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-4 text-secondary">No invoices found for this customer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="emails" class="tab-content text-secondary text-center py-5 dashboard-card">Email history coming soon...</div>
    <div id="notes" class="tab-content text-secondary text-center py-5 dashboard-card">Internal notes coming soon...</div>

</div>

<style>
    .tab-link {
        padding: 10px 20px;
        background: transparent;
        border: none;
        color: #94a3b8;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        border-radius: 8px;
    }
    .tab-link:hover { color: #fff; background: rgba(255,255,255,0.05); }
    .tab-link.active {
        color: #38bdf8;
        background: rgba(56, 189, 248, 0.1);
    }
    .tab-content { display: none; margin-top: 20px; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
</style>

<script>
    function openTab(evt, tabName) {
        var tabs = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabs.length; i++) { tabs[i].classList.remove("active"); }
        var links = document.getElementsByClassName("tab-link");
        for (var i = 0; i < links.length; i++) { links[i].classList.remove("active"); }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
</script>
@endsection
