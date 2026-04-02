@extends('layouts.admin')

@section('content')
@php
    /** * FIX #2: Safety check for items. 
     * Using collect() to ensure we can map even if it's an array or object.
     */
    $defaultItems = !empty($quote->items) ? collect($quote->items)->map(function($item) {
        return [
            'service' => $item->service_name ?? ($item['service_name'] ?? ''),
            'description' => $item->description ?? ($item['description'] ?? ''),
            'qty' => $item->quantity ?? ($item['quantity'] ?? 1),
            'price' => $item->unit_price ?? ($item['unit_price'] ?? 0),
            'total' => $item->line_total ?? ($item['line_total'] ?? 0),
        ];
    })->toArray() : [
        [
            'service' => '',
            'description' => '',
            'qty' => 1,
            'price' => 0,
            'total' => 0,
        ]
    ];

    $oldItems = old('items', $defaultItems);
    $oldDepositType = old('deposit_type', $quote->deposit_type ?? 'none');
@endphp

<div class="container-fluid py-4">
    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="text-white fw-bold mb-1">
                <i class="fa-solid fa-file-invoice me-2 text-primary"></i>
                {{ isset($quote) ? 'Update Quote' : 'Create New Quote' }}
            </h2>
            <p class="text-secondary mb-0">Build a professional proposal with custom services and payment terms.</p>
        </div>
        <a href="{{ route('quotes.index') }}" class="btn btn-outline-light rounded-pill px-4">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Quotes
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm mb-4 border-0">
            <div class="d-flex align-items-center mb-2">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Please fix the following errors:</strong>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($quote) ? route('quotes.update', $quote->id) : route('quotes.store') }}" method="POST" id="quoteForm" enctype="multipart/form-data">
        @csrf
        @if(isset($quote))
            @method('PUT')
        @endif

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden quote-card">
            <div class="card-body p-4 p-lg-5">

                {{-- BLOCK 1: CUSTOMER & DATE INFO --}}
                <div class="mb-5">
                    <h5 class="text-primary fw-bold mb-4 uppercase small letter-spacing-1">
                        <i class="fa-solid fa-user-tag me-2"></i> Client & Timeline Details
                    </h5>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <label for="customer_id" class="form-label text-white fw-semibold">Select Customer</label>
                            <select name="customer_id" id="customer_id" class="form-select form-select-lg quote-input" required>
                                <option value="">Choose a client...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $quote->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}{{ $customer->email ? ' (' . $customer->email . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <label for="quote_date" class="form-label text-white fw-semibold">Quote Date</label>
                            <input
                                type="date"
                                name="quote_date"
                                id="quote_date"
                                class="form-control form-control-lg quote-input quote-date-input"
                                value="{{ old('quote_date', isset($quote->quote_date) ? \Carbon\Carbon::parse($quote->quote_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            >
                        </div>

                        <div class="col-lg-3">
                            <label for="valid_until" class="form-label text-white fw-semibold">Expiration Date</label>
                            <input
                                type="date"
                                name="valid_until"
                                id="valid_until"
                                class="form-control form-control-lg quote-input quote-date-input"
                                value="{{ old('valid_until', isset($quote->valid_until) ? \Carbon\Carbon::parse($quote->valid_until)->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}"
                            >
                        </div>
                    </div>
                </div>

                <hr class="my-5 border-secondary-subtle opacity-25">

                {{-- BLOCK 2: LINE ITEMS --}}
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <div>
                            <h5 class="text-primary fw-bold mb-1 uppercase small letter-spacing-1">
                                <i class="fa-solid fa-list-check me-2"></i> Service Items & Pricing
                            </h5>
                            <p class="text-secondary small mb-0">Define the services and pricing for this proposal.</p>
                        </div>
                        <button type="button" onclick="addItemRow()" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                            <i class="fa-solid fa-plus me-2"></i>Add Line Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle quote-table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="min-width: 220px;">Service Name</th>
                                    <th style="min-width: 260px;">Description</th>
                                    <th style="width: 110px;">Qty</th>
                                    <th style="width: 140px;">Unit Price</th>
                                    <th style="width: 140px;">Line Total</th>
                                    <th style="width: 80px;" class="text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody id="quoteItemsBody">
                                @foreach($oldItems as $index => $item)
                                    <tr class="item-row">
                                        <td>
                                            <input type="text" name="items[{{ $index }}][service]" class="form-control quote-input item-service" value="{{ $item['service'] ?? '' }}" placeholder="e.g. Consultation">
                                        </td>
                                        <td>
                                            <textarea name="items[{{ $index }}][description]" class="form-control quote-input item-description" rows="2" placeholder="Brief details...">{{ $item['description'] ?? '' }}</textarea>
                                        </td>
                                        <td>
                                            <input type="number" step="1" min="0" name="items[{{ $index }}][qty]" class="form-control quote-input item-qty" value="{{ $item['qty'] ?? 1 }}" oninput="calculateRow(this)">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent text-secondary border-end-0">$</span>
                                                <input type="number" step="0.01" min="0" name="items[{{ $index }}][price]" class="form-control quote-input item-price border-start-0" value="{{ $item['price'] ?? 0 }}" oninput="calculateRow(this)">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text bg-transparent text-secondary border-end-0">$</span>
                                                <input type="number" step="0.01" min="0" name="items[{{ $index }}][total]" class="form-control quote-input item-total border-start-0" value="{{ $item['total'] ?? 0 }}" readonly>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-link text-danger p-0" onclick="removeItemRow(this)">
                                                <i class="fa-solid fa-circle-xmark fs-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-lg-4">
                            <div class="totals-box rounded-4 p-4 shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary small">Subtotal</span>
                                    <span class="text-white fw-bold">$<span id="subtotal_display">0.00</span></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary small">Required Deposit</span>
                                    <span class="text-warning fw-bold">$<span id="deposit_display">0.00</span></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-secondary-subtle opacity-25">
                                    <span class="text-secondary small">Remaining Balance</span>
                                    <span class="text-info fw-bold">$<span id="remaining_display">0.00</span></span>
                                </div>

                                <input type="hidden" name="subtotal" id="subtotal" value="{{ old('subtotal', $quote->subtotal ?? 0) }}">
                                <input type="hidden" name="total" id="total" value="{{ old('total', $quote->total ?? 0) }}">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-white fw-bold">Grand Total</span>
                                    <span class="text-primary fs-4 fw-bold">$<span id="total_display">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-5 border-secondary-subtle opacity-25">

                {{-- BLOCK 3: PAYMENT TERMS & AUTOMATION --}}
                <div class="mb-5">
                    <h5 class="text-primary fw-bold mb-4 uppercase small letter-spacing-1">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i> Payment & Automation Settings
                    </h5>
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label text-white fw-semibold">Deposit Structure</label>
                            <select name="deposit_type" id="deposit_type" class="form-select form-select-lg quote-input" onchange="updateDepositUI(); calculateTotals();">
                                <option value="none" {{ $oldDepositType == 'none' ? 'selected' : '' }}>Full Payment (No Deposit)</option>
                                <option value="percentage" {{ $oldDepositType == 'percentage' ? 'selected' : '' }}>Percentage of Total</option>
                                <option value="fixed" {{ $oldDepositType == 'fixed' ? 'selected' : '' }}>Fixed Flat Amount</option>
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label text-white fw-semibold" id="deposit_value_label">Value</label>
                            <input type="number" step="0.01" min="0" name="deposit_value" id="deposit_value" class="form-control form-control-lg quote-input" value="{{ old('deposit_value', $quote->deposit_value ?? '') }}" placeholder="Enter value..." oninput="calculateTotals()">
                            <small class="text-secondary mt-2 d-block" id="deposit_value_help"></small>
                        </div>

                        <div class="col-lg-4">
                            <label class="form-label text-white fw-semibold">Balance Due Date</label>
                            <input 
                                type="date" 
                                name="remaining_due_date" 
                                id="remaining_due_date" 
                                class="form-control form-control-lg quote-input quote-date-input" 
                                value="{{ old('remaining_due_date', isset($quote->remaining_due_date) ? \Carbon\Carbon::parse($quote->remaining_due_date)->format('Y-m-d') : '') }}"
                            >
                        </div>
                    </div>

                    {{-- INTEGRATED CHECKBOX BLOCK --}}
                    <div class="row g-3 mt-3">
                        <div class="col-lg-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="contract_required" 
                                       class="form-check-input"
                                       value="1"
                                       {{ old('contract_required', $quote->contract_required ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label text-white">Contract Required</label>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="signature_required" 
                                       class="form-check-input"
                                       value="1"
                                       {{ old('signature_required', $quote->signature_required ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label text-white">Signature Required</label>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-check">
                                <input type="checkbox" 
                                       name="auto_convert_after_contract" 
                                       class="form-check-input"
                                       value="1"
                                       {{ old('auto_convert_after_contract', $quote->auto_convert_after_contract ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label text-white">Auto Convert After Contract</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-5 border-secondary-subtle opacity-25">

                {{-- BLOCK 4: ADDITIONAL NOTES --}}
                <div class="mb-4">
                    <h5 class="text-primary fw-bold mb-4 uppercase small letter-spacing-1">
                        <i class="fa-solid fa-comment-dots me-2"></i> Client-Facing Notes
                    </h5>
                    <textarea name="customer_notes" id="customer_notes" rows="6" class="form-control quote-input" placeholder="Terms, conditions, or a thank you message for the client...">{{ old('customer_notes', $quote->customer_notes ?? '') }}</textarea>
                    <p class="text-secondary small mt-2"><i class="fa-solid fa-circle-info me-1"></i> These notes appear on the PDF and the client portal.</p>
                </div>

                {{-- FORM ACTIONS --}}
                <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top border-secondary-subtle">
                    <a href="{{ route('quotes.index') }}" class="btn btn-outline-light btn-lg px-5 rounded-pill small fw-bold">
                        Discard
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-5 rounded-pill shadow-lg fw-bold">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        {{ isset($quote) ? 'Save Changes' : 'Publish Quote' }}
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

<style>
.letter-spacing-1 { letter-spacing: 1px; }
.quote-card, .payment-box, .totals-box { background: rgba(15, 23, 42, 0.95); border: 1px solid rgba(255,255,255,0.06); backdrop-filter: blur(12px); border-radius: 20px; }
.quote-input { background: #020617 !important; border: 1px solid #1e293b !important; color: #fff !important; border-radius: 12px; transition: all 0.2s; }
.quote-input:focus { background: #0f172a !important; border-color: #38bdf8 !important; box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1) !important; }
.quote-table { color: #fff; border-collapse: separate; border-spacing: 0 12px; }
.quote-table thead th { border: none !important; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding-left: 15px; }
.quote-table tbody tr { background: #020617; transition: transform 0.2s; }
.quote-table tbody tr:hover { transform: scale(1.005); }
.quote-table td { padding: 14px 10px; border: none !important; vertical-align: middle; }
.quote-table td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; padding-left: 15px; }
.quote-table td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

/* Table Specific Input Styling */
.quote-table input,
.quote-table textarea {
    background: #020617 !important;
    border: 1px solid #1e293b !important;
    color: #fff !important;
    border-radius: 10px;
    padding: 10px;
}

.quote-table textarea {
    resize: none;
}

.quote-table input:focus,
.quote-table textarea:focus {
    border-color: #38bdf8 !important;
    background: #0f172a !important;
    outline: none;
}

.form-check-input { background-color: #020617; border-color: #1e293b; }
.form-check-input:checked { background-color: #38bdf8; border-color: #38bdf8; }
.btn-primary { background: #0ea5e9; border: none; }
.btn-primary:hover { background: #0284c7; }
</style>

<script>
    let itemIndex = {{ count($oldItems) }};

    function addItemRow() {
        const tbody = document.getElementById('quoteItemsBody');
        const row = document.createElement('tr');
        row.classList.add('item-row');
        row.innerHTML = `
            <td><input type="text" name="items[${itemIndex}][service]" class="form-control quote-input item-service" placeholder="Service name"></td>
            <td><textarea name="items[${itemIndex}][description]" class="form-control quote-input item-description" rows="2" placeholder="Description"></textarea></td>
            <td><input type="number" step="1" min="0" name="items[${itemIndex}][qty]" class="form-control quote-input item-qty" value="1" oninput="calculateRow(this)"></td>
            <td>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-secondary border-end-0 small">$</span>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][price]" class="form-control quote-input item-price border-start-0" value="0" oninput="calculateRow(this)">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-secondary border-end-0 small">$</span>
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][total]" class="form-control quote-input item-total border-start-0" value="0.00" readonly>
                </div>
            </td>
            <td class="text-center"><button type="button" class="btn btn-link text-danger p-0" onclick="removeItemRow(this)"><i class="fa-solid fa-circle-xmark fs-4"></i></button></td>
        `;
        tbody.appendChild(row);
        itemIndex++;
    }

    function removeItemRow(button) {
        const rows = document.querySelectorAll('#quoteItemsBody .item-row');
        if (rows.length > 1) {
            button.closest('tr').remove();
            calculateTotals();
        }
    }

    function calculateRow(input) {
        const row = input.closest('tr');
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        row.querySelector('.item-total').value = (qty * price).toFixed(2);
        calculateTotals();
    }

    function updateDepositUI() {
        const type = document.getElementById('deposit_type').value;
        const label = document.getElementById('deposit_value_label');
        const input = document.getElementById('deposit_value');
        const help = document.getElementById('deposit_value_help');

        if (type === 'percentage') { 
            label.textContent = 'Deposit Percentage (%)'; 
            input.placeholder = 'e.g. 25';
            help.textContent = 'Client will pay this % upfront.';
            input.disabled = false;
        } else if (type === 'fixed') { 
            label.textContent = 'Deposit Amount ($)'; 
            input.placeholder = 'e.g. 500';
            help.textContent = 'Client will pay this fixed amount upfront.';
            input.disabled = false;
        } else { 
            label.textContent = 'No Deposit Required'; 
            input.placeholder = '0.00';
            help.textContent = 'Client pays full amount after approval.';
            input.disabled = true;
            input.value = '';
        }
    }

    function calculateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-total').forEach(input => { subtotal += parseFloat(input.value) || 0; });

        const depType = document.getElementById('deposit_type').value;
        const depVal = parseFloat(document.getElementById('deposit_value').value) || 0;
        let depAmt = 0;

        if (depType === 'percentage') { depAmt = subtotal * (depVal / 100); }
        else if (depType === 'fixed') { depAmt = depVal; }

        if (depAmt > subtotal) depAmt = subtotal;

        document.getElementById('subtotal_display').textContent = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('deposit_display').textContent = depAmt.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('remaining_display').textContent = (subtotal - depAmt).toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('total_display').textContent = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total').value = subtotal.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateDepositUI();
        document.querySelectorAll('.item-row').forEach(row => calculateRow(row.querySelector('.item-qty')));
    });
</script>
@endsection
