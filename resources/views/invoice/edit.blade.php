@extends('layouts.app')

@section('content')

<div class="invoice-wrapper">

    <div class="header">
        <h1>Create Invoice</h1>
        <p>Professional billing with real-time preview, deposit tracking, and remaining balance scheduling</p>
    </div>

<form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
    @csrf
    @method('PUT')

        {{-- Hidden Calculated Fields --}}
        <input type="hidden" name="deposit_amount" id="deposit_amount_input">
        <input type="hidden" name="remaining_balance" id="remaining_balance_input">
        <input type="hidden" name="tax_amount" id="tax_amount_input">
        <input type="hidden" name="grand_total" id="grand_total_input">
        <input type="hidden" name="subtotal_amount" id="subtotal_amount_input">

        <div class="invoice-grid">

            {{-- LEFT COLUMN: INPUTS --}}
            <div class="form-side">

                {{-- CLIENT INFO --}}
                <div class="card shadow-glass">
                    <h3><i class="fa-solid fa-user-tag me-2"></i> Client Info</h3>
                    
                    <div class="input-group">
                        <label>Select Existing Customer</label>
                        <select id="customer_id" class="styled-select">
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                data-name="{{ e($c->name) }}"
                                data-email="{{ e($c->email ?? '') }}"
                                data-phone="{{ e($c->phone ?? '') }}">
                                {{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="input-group">
                        <input id="customer_name" name="customer_name" placeholder="Customer Name" required class="styled-input">
                        <input id="customer_email" name="customer_email" placeholder="Email" required class="styled-input">
                        <input id="customer_phone" name="customer_phone" placeholder="Phone" class="styled-input">
                    </div>
                </div>

                {{-- SCHEDULE --}}
                <div class="card shadow-glass">
                    <h3><i class="fa-solid fa-calendar-days me-2"></i> Invoice Schedule</h3>

                    <div class="grid-2">
                        <div class="input-group">
                            <label>Invoice Date</label>
                            <input type="date" name="invoice_date" value="{{ now()->format('Y-m-d') }}" class="styled-input visible-calendar">
                        </div>
                        <div class="input-group">
                            <label>Deposit Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="styled-input visible-calendar">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Remaining Balance Due Date</label>
                        <input type="date" name="remaining_due_date" id="remaining_due_date" class="styled-input visible-calendar">
                    </div>

                    <div class="auto-charge-container">
                        <label class="toggle-switch">
                            <input type="checkbox" name="auto_charge_enabled">
                            <span class="slider"></span>
                            <span class="label-text">Auto-charge remaining balance on due date</span>
                        </label>
                    </div>
                </div>

                {{-- ITEMS --}}
                <div class="card shadow-glass">
                    <div class="flex-between">
                        <h3><i class="fa-solid fa-list-ul me-2"></i> Line Items</h3>
                        <button type="button" class="btn-secondary" onclick="addItem()">+ Add Item</button>
                    </div>
                    
                    <div id="items-container" class="items-list">
                        {{-- Items injected here --}}
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: PREVIEW --}}
            <div class="preview-side">
                <div class="preview-card shadow-glass">
                    <div class="preview-header">
                        <h2 style="color:{{ $company->primary_color ?? '#0ea5e9' }}">{{ $company->name }}</h2>
                        <span class="badge">DRAFT PREVIEW</span>
                    </div>

                    <div id="preview_items" class="preview-items-area">
                        {{-- Preview rows injected here --}}
                    </div>

                    <div class="totals-area">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span id="subtotal" class="font-bold">$0.00</span>
                        </div>

                        <div class="total-row tax-input-row">
                            <span>Tax %</span>
                            <input id="tax_percent" name="tax_percent" type="number" value="0" step="0.01" class="small-input visible-arrows" oninput="calc()">
                        </div>

                        <div class="total-row">
                            <span>Tax Amount</span>
                            <span id="tax">$0.00</span>
                        </div>

                        <div class="total-row grand-total">
                            <span>Total</span>
                            <span id="grand">$0.00</span>
                        </div>

                        <div class="deposit-area">
                            <div class="deposit-config">
                                <span>Deposit %</span>
                                <input id="deposit_percent" name="deposit_percent" value="35" class="small-input visible-arrows" oninput="calc()">
                            </div>
                            <div class="total-row highlight-blue">
                                <span>Deposit Required</span>
                                <span id="deposit_amount" class="font-bold">$0.00</span>
                            </div>
                            <div class="total-row">
                                <span>Remaining Balance</span>
                                <span id="remaining_amount">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn-generate">
                        <i class="fa-solid fa-paper-plane me-2"></i> Generate & View Invoice
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<style>
/* RECOVERY CSS - FULL WIDTH WOW EFFECT */
.invoice-wrapper { width: 100%; margin: 0 auto; }
.invoice-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; align-items: start; }

@media (max-width: 1100px) { .invoice-grid { grid-template-columns: 1fr; } }

.card { background: rgba(30, 41, 59, 0.5); border: 1px solid rgba(255,255,255,0.1); padding: 25px; border-radius: 16px; margin-bottom: 25px; backdrop-filter: blur(5px); }
.preview-card { background: #020617; border: 2px solid #0ea5e9; padding: 30px; border-radius: 20px; position: sticky; top: 20px; }

.styled-input, .styled-select { 
    width: 100%; padding: 12px; margin-top: 8px; background: #0f172a; 
    color: #fff; border: 1px solid #334155; border-radius: 8px; font-size: 14px;
}
.styled-input:focus { border-color: #0ea5e9; outline: none; }

/* 🔥 FORCE CALENDAR ICON VISIBILITY */
.visible-calendar::-webkit-calendar-picker-indicator {
    display: block !important;
    background-color: #38bdf8;
    padding: 5px;
    cursor: pointer;
    border-radius: 3px;
    filter: invert(0); /* Keeps the icon sharp on dark background */
}

/* 🔥 FORCE NUMBER ARROWS VISIBILITY */
.visible-arrows::-webkit-inner-spin-button,
.visible-arrows::-webkit-outer-spin-button {
    opacity: 1 !important;
    background: #334155;
    cursor: pointer;
    height: 30px;
}

.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.input-group { margin-bottom: 15px; }
.input-group label { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

.item-row { display: grid; grid-template-columns: 2fr 0.5fr 1fr 40px; gap: 10px; margin-bottom: 10px; background: rgba(15, 23, 42, 0.8); padding: 10px; border-radius: 8px; }

.btn-secondary { background: rgba(56, 189, 248, 0.1); color: #38bdf8; border: 1px solid #38bdf8; padding: 8px 15px; border-radius: 8px; cursor: pointer; transition: 0.3s; }
.btn-secondary:hover { background: #38bdf8; color: #fff; }

.btn-generate { 
    width: 100%; padding: 18px; background: linear-gradient(135deg, #0ea5e9, #2563eb); 
    color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 16px; 
    margin-top: 25px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.4);
}

/* TOTALS STYLING */
.totals-area { border-top: 1px solid #1e293b; margin-top: 20px; padding-top: 20px; }
.total-row { display: flex; justify-content: space-between; margin-bottom: 10px; color: #94a3b8; }
.grand-total { border-top: 1px solid #334155; padding-top: 10px; font-size: 20px; color: #fff; font-weight: 800; }
.highlight-blue { color: #38bdf8; font-size: 18px; }
.small-input { width: 80px; background: #0f172a; border: 1px solid #334155; color: #fff; text-align: center; border-radius: 4px; padding: 5px; }

.preview-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.badge { background: #1e293b; color: #38bdf8; font-size: 10px; padding: 4px 8px; border-radius: 4px; }
</style>

<script>
let itemIndex = 0;

// CONNECT CUSTOMER SELECTION
document.getElementById('customer_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    if (selected.value) {
        document.getElementById('customer_name').value = selected.getAttribute('data-name');
        document.getElementById('customer_email').value = selected.getAttribute('data-email');
        document.getElementById('customer_phone').value = selected.getAttribute('data-phone');
    } else {
        document.getElementById('customer_name').value = '';
        document.getElementById('customer_email').value = '';
        document.getElementById('customer_phone').value = '';
    }
});

// IMPROVED ITEM HANDLING
function addItem() {
    const container = document.getElementById('items-container');
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `
        <input placeholder="Service/Description" name="items[${itemIndex}][description]" class="styled-input" oninput="updatePreview()">
        <input type="number" value="1" name="items[${itemIndex}][qty]" class="styled-input text-center visible-arrows" oninput="calc()">
        <input type="number" value="0" step="0.01" name="items[${itemIndex}][price]" class="styled-input visible-arrows" oninput="calc()">
        <button type="button" onclick="this.parentElement.remove(); calc();" style="background:transparent; color:#ef4444; border:none; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
    `;
    container.appendChild(div);
    itemIndex++;
    calc();
}

function updatePreview() {
    const previewArea = document.getElementById('preview_items');
    previewArea.innerHTML = '';
    
    document.querySelectorAll('.item-row').forEach(row => {
        const desc = row.children[0].value || 'New Service Item';
        const qty = row.children[1].value;
        const price = row.children[2].value;
        
        const p = document.createElement('div');
        p.className = 'total-row';
        p.style.fontSize = '13px';
        p.innerHTML = `<span>${qty}x ${desc}</span> <span>$${(qty * price).toFixed(2)}</span>`;
        previewArea.appendChild(p);
    });
}

function calc() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const q = parseFloat(row.children[1].value) || 0;
        const p = parseFloat(row.children[2].value) || 0;
        subtotal += q * p;
    });

    const taxP = parseFloat(document.getElementById('tax_percent').value) || 0;
    const tax = subtotal * (taxP / 100);
    const total = subtotal + tax;

    const depP = parseFloat(document.getElementById('deposit_percent').value) || 0;
    const dep = total * (depP / 100);
    const rem = total - dep;

    document.getElementById('subtotal').innerText = '$' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('tax').innerText = '$' + tax.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('grand').innerText = '$' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('deposit_amount').innerText = '$' + dep.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('remaining_amount').innerText = '$' + rem.toLocaleString(undefined, {minimumFractionDigits: 2});

    document.getElementById('subtotal_amount_input').value = subtotal;
    document.getElementById('tax_amount_input').value = tax;
    document.getElementById('grand_total_input').value = total;
    document.getElementById('deposit_amount_input').value = dep;
    document.getElementById('remaining_balance_input').value = rem;

    updatePreview();
}

addItem();
</script>

@endsection
