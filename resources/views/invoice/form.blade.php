@extends('layouts.app')

@section('content')

<div class="invoice-wrapper">

    <div class="header">
        <h1>Create Invoice</h1>
        <p>Professional billing with real-time preview, deposit tracking, and remaining balance scheduling</p>
    </div>

    <form action="{{ route('invoice.send') }}" method="POST">
        @csrf

        <input type="hidden" name="deposit_amount" id="deposit_amount_input">
        <input type="hidden" name="remaining_balance" id="remaining_balance_input">
        <input type="hidden" name="tax_amount" id="tax_amount_input">
        <input type="hidden" name="grand_total" id="grand_total_input">
        <input type="hidden" name="subtotal_amount" id="subtotal_amount_input">

        <div class="grid">

            {{-- LEFT SIDE --}}
            <div class="left">

                <div class="card">
                    <h3>Client Info</h3>

                    <label class="field-label">Select Customer</label>
                    <select id="customer_id">
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

                    <label class="field-label">Customer Name</label>
                    <input id="customer_name" name="customer_name" placeholder="Customer Name" required>

                    <label class="field-label">Email</label>
                    <input id="customer_email" name="customer_email" placeholder="Email" required>

                    <label class="field-label">Phone</label>
                    <input id="customer_phone" name="customer_phone" placeholder="Phone">
                </div>

                <div class="card">
                    <h3>Invoice Schedule</h3>

                    <div class="two-col">
                        <div>
                            <label class="field-label">Invoice Date</label>
                            <input type="date" name="invoice_date" id="invoice_date" value="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div>
                            <label class="field-label">Deposit Due Date</label>
                            <input type="date" name="due_date" id="due_date" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <label class="field-label">Remaining Balance Due Date</label>
                        <input type="date" name="remaining_due_date" id="remaining_due_date">
                    </div>

                    <div class="schedule-note">
                        The customer will see both the deposit due date and the remaining balance due date on the invoice preview.
                    </div>
                </div>

                <div class="card">
                    <h3>Services & Items</h3>

                    <div class="thead">
                        <span>Description</span>
                        <span>Qty</span>
                        <span>Price</span>
                        <span>Total</span>
                        <span></span>
                    </div>

                    <div id="items"></div>

                    <button type="button" onclick="addItem()" class="btn-add">
                        + Add Line Item
                    </button>
                </div>

            </div>

            {{-- RIGHT SIDE --}}
            <div class="right">

                <div class="preview-card">

                    <div class="preview-header">
                        @if(!empty($company->logo_path))
                            <img src="{{ asset('storage/' . $company->logo_path) }}" class="logo" alt="Logo">
                        @endif
                        <div>
                            <h2 style="color:{{ $company->primary_color ?? '#0ea5e9' }}">{{ $company->name }}</h2>
                            <p class="preview-subtitle">Invoice Preview</p>
                        </div>
                    </div>

                    <div class="preview-client">
                        <div>
                            <small>Billed To</small>
                            <strong id="preview_name">Customer</strong><br>
                            <span id="preview_email"></span>
                        </div>
                    </div>

                    <div class="preview-schedule">
                        <div class="schedule-pill">
                            <small>Deposit Due</small>
                            <strong id="preview_due_date">—</strong>
                        </div>
                        <div class="schedule-pill">
                            <small>Remaining Due</small>
                            <strong id="preview_remaining_due_date">—</strong>
                        </div>
                    </div>

                    <div class="preview-items-wrap">
                        <div class="preview-items-head">
                            <span>Item</span>
                            <span>Total</span>
                        </div>
                        <div class="preview-items" id="preview_items"></div>
                    </div>

                    <div class="preview-totals">

                        <div class="total-row">
                            <span>Subtotal</span>
                            <span id="subtotal">$0.00</span>
                        </div>

                        <div class="tax-row">
                            <label for="tax_percent">Tax (%)</label>
                            <input
                                id="tax_percent"
                                type="number"
                                value="0"
                                min="0"
                                max="100"
                                step="0.01"
                                inputmode="decimal"
                            >
                        </div>

                        <div class="total-row">
                            <span>Tax</span>
                            <span id="tax">$0.00</span>
                        </div>

                        <div class="total-row grand">
                            <span>Total</span>
                            <span id="grand">$0.00</span>
                        </div>

                        <div class="deposit-box">
                            <div class="deposit-head">
                                <span>Deposit Setup</span>
                                <input
                                    type="number"
                                    id="deposit_percent"
                                    name="deposit_percent"
                                    value="35"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    inputmode="decimal"
                                >
                            </div>

                            <div class="deposit-split">
                                <div class="money-card">
                                    <small>Deposit Due Now</small>
                                    <strong id="deposit_amount">$0.00</strong>
                                    <span class="money-note" id="deposit_due_note">Due on —</span>
                                </div>

                                <div class="money-card">
                                    <small>Remaining Balance</small>
                                    <strong id="remaining_amount">$0.00</strong>
                                    <span class="money-note" id="remaining_due_note">Due on —</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <button class="btn-primary">Generate Invoice</button>

                </div>

            </div>

        </div>
    </form>
</div>

<style>
.invoice-wrapper{max-width:1360px;margin:auto;padding:34px}
.header h1{color:#fff;font-size:42px;font-weight:800;line-height:1.05;margin:0 0 8px}
.header p{color:#94a3b8;margin:0 0 26px;font-size:17px}

.grid{display:grid;grid-template-columns:1.08fr 1fr;gap:30px;align-items:start}
.left,.right{min-width:0}

.card{
    background:rgba(15,23,42,.78);
    padding:24px;
    border-radius:20px;
    margin-bottom:22px;
    border:1px solid rgba(255,255,255,.06);
    box-shadow:0 18px 40px rgba(0,0,0,.28);
}

.preview-card{
    background:linear-gradient(145deg,#020617,#0f172a);
    padding:26px;
    border-radius:24px;
    box-shadow:0 24px 50px rgba(0,0,0,.55);
    border:1px solid rgba(255,255,255,.06);
    position:sticky;
    top:28px;
}

.card h3,.preview-card h2{
    margin:0 0 16px;
    color:#fff;
    font-weight:800;
}

.preview-subtitle{
    color:#94a3b8;
    margin:2px 0 0;
    font-size:14px;
}

.logo{
    max-width:84px;
    max-height:84px;
    object-fit:contain;
    margin-right:14px;
}

.preview-header{
    display:flex;
    align-items:center;
    gap:14px;
    margin-bottom:24px;
}

.field-label{
    display:block;
    color:#94a3b8;
    font-size:13px;
    font-weight:700;
    margin:10px 0 6px;
    letter-spacing:.02em;
}

input,select{
    width:100%;
    padding:14px 14px;
    border-radius:12px;
    background:#111c33;
    color:#fff;
    border:1px solid rgba(255,255,255,.08);
    outline:none;
    font-size:16px;
    box-sizing:border-box;
}

input:focus,select:focus{
    border-color:rgba(59,130,246,.7);
    box-shadow:0 0 0 3px rgba(59,130,246,.16);
}

.two-col{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.schedule-note{
    margin-top:14px;
    color:#94a3b8;
    font-size:13px;
    line-height:1.45;
    background:rgba(14,165,233,.08);
    border:1px solid rgba(14,165,233,.16);
    padding:12px 14px;
    border-radius:12px;
}

.thead,.row{
    display:grid;
    grid-template-columns:2.2fr .8fr 1fr 1fr 48px;
    gap:12px;
    align-items:center;
}

.thead{
    color:#94a3b8;
    margin-top:4px;
    margin-bottom:10px;
    font-size:13px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.03em;
}

.row{
    margin-top:12px;
}

.row input{
    margin:0;
}

.line-total{
    background:#111c33;
    border:1px solid rgba(255,255,255,.08);
    color:#fff;
    border-radius:12px;
    padding:14px;
    min-height:50px;
    display:flex;
    align-items:center;
    font-weight:700;
}

.remove{
    background:#ef4444;
    border:none;
    color:#fff;
    border-radius:12px;
    cursor:pointer;
    height:50px;
    font-size:18px;
    font-weight:800;
}

.btn-add{
    margin-top:16px;
    background:linear-gradient(135deg,#0ea5e9,#3b82f6);
    padding:12px 16px;
    border:none;
    border-radius:12px;
    color:#fff;
    font-weight:700;
    cursor:pointer;
}

.preview-client{
    background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.05);
    border-radius:16px;
    padding:16px;
    margin-bottom:18px;
    color:#fff;
}

.preview-client small{
    display:block;
    color:#94a3b8;
    margin-bottom:6px;
    text-transform:uppercase;
    font-size:11px;
    letter-spacing:.05em;
}

.preview-client strong{
    font-size:24px;
}

.preview-client span{
    color:#cbd5e1;
}

.preview-schedule{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
    margin-bottom:18px;
}

.schedule-pill{
    background:rgba(14,165,233,.08);
    border:1px solid rgba(14,165,233,.18);
    border-radius:14px;
    padding:14px 16px;
    color:#fff;
}

.schedule-pill small{
    display:block;
    color:#7dd3fc;
    font-size:12px;
    margin-bottom:4px;
    text-transform:uppercase;
    letter-spacing:.05em;
}

.schedule-pill strong{
    font-size:16px;
}

.preview-items-wrap{
    background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.05);
    border-radius:16px;
    padding:16px;
    margin-bottom:18px;
}

.preview-items-head{
    display:flex;
    justify-content:space-between;
    color:#94a3b8;
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    margin-bottom:10px;
}

.preview-items .preview-item{
    display:flex;
    justify-content:space-between;
    gap:16px;
    color:#fff;
    padding:10px 0;
    border-bottom:1px solid rgba(255,255,255,.06);
}

.preview-items .preview-item:last-child{
    border-bottom:none;
}

.preview-items .preview-item small{
    display:block;
    color:#94a3b8;
    margin-top:2px;
}

.preview-totals{
    margin-top:6px;
}

.total-row{
    display:flex;
    justify-content:space-between;
    margin-top:12px;
    color:#fff;
    font-size:16px;
}

.tax-row{
    margin-top:16px;
}

.tax-row label{
    display:block;
    color:#7dd3fc;
    font-size:13px;
    font-weight:700;
    margin-bottom:8px;
}

.tax-row input{
    margin-top:0;
    font-size:18px;
    padding:14px;
}

.grand{
    font-size:34px;
    font-weight:800;
    margin-top:18px;
    align-items:flex-end;
}

.deposit-box{
    margin-top:22px;
    padding:18px;
    background:linear-gradient(135deg, rgba(14,165,233,.16), rgba(59,130,246,.10));
    border-radius:16px;
    border:1px solid rgba(14,165,233,.26);
}

.deposit-head{
    display:grid;
    grid-template-columns:110px 1fr;
    gap:14px;
    align-items:end;
    margin-bottom:16px;
}

.deposit-head span{
    color:#7dd3fc;
    font-size:13px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.04em;
}

.deposit-head input{
    margin-top:0;
    font-size:20px;
    font-weight:800;
}

.deposit-split{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

.money-card{
    background:rgba(2,6,23,.45);
    border:1px solid rgba(255,255,255,.06);
    border-radius:14px;
    padding:14px;
}

.money-card small{
    display:block;
    color:#94a3b8;
    margin-bottom:8px;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.05em;
}

.money-card strong{
    font-size:30px;
    color:#fff;
    display:block;
    line-height:1.05;
}

.money-note{
    display:block;
    margin-top:8px;
    color:#7dd3fc;
    font-size:13px;
}

.btn-primary{
    margin-top:22px;
    width:100%;
    padding:17px 18px;
    background:linear-gradient(135deg,#0ea5e9,#2563eb);
    border:none;
    border-radius:14px;
    color:#fff;
    font-weight:800;
    font-size:18px;
    cursor:pointer;
    box-shadow:0 18px 34px rgba(37,99,235,.28);
}

@media(max-width:1100px){
    .grid{grid-template-columns:1fr}
    .preview-card{position:static}
}

@media(max-width:760px){
    .two-col,
    .preview-schedule,
    .deposit-split{grid-template-columns:1fr}
    .thead{display:none}
    .row{
        grid-template-columns:1fr;
        gap:10px;
        padding:14px;
        border:1px solid rgba(255,255,255,.06);
        border-radius:14px;
        background:rgba(255,255,255,.02);
    }
    .line-total,.remove{height:auto}
    .deposit-head{grid-template-columns:1fr}
    .grand{font-size:28px}
}

</style>

<script>
let index = 0;

function addItem(){
    const container = document.getElementById('items');
    const row = document.createElement('div');
    row.className = 'row';

    row.innerHTML = `
        <input name="items[${index}][description]" placeholder="Service">
        <input type="number" name="items[${index}][qty]" value="1" min="0" step="0.01" onchange="calc()" oninput="calc()">
        <input type="number" name="items[${index}][price]" value="0" min="0" step="0.01" onchange="calc()" oninput="calc()">
        <div class="line-total">$0.00</div>
        <button type="button" class="remove" onclick="this.parentNode.remove(); calc()">×</button>
    `;

    container.appendChild(row);
    index++;
    calc();
}

function formatMoney(value){
    return '$' + Number(value).toFixed(2);
}

function formatDateValue(value){
    if(!value) return '—';
    const d = new Date(value + 'T00:00:00');
    if (isNaN(d.getTime())) return value;
    return d.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
}

function calc(){
    const rows = document.querySelectorAll('.row');
    let subtotal = 0;
    let previewHTML = '';

    rows.forEach(r => {
        const desc = r.children[0].value || 'Service';
        const qty = parseFloat(r.children[1].value) || 0;
        const price = parseFloat(r.children[2].value) || 0;
        const total = qty * price;

        r.children[3].innerText = formatMoney(total);
        subtotal += total;

        previewHTML += `
            <div class="preview-item">
                <div>
                    ${desc}
                    <small>${qty} × ${formatMoney(price)}</small>
                </div>
                <strong>${formatMoney(total)}</strong>
            </div>
        `;
    });

    document.getElementById('preview_items').innerHTML = previewHTML || `
        <div class="preview-item">
            <div>
                Service
                <small>No items added yet</small>
            </div>
            <strong>$0.00</strong>
        </div>
    `;

    const taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
    const tax = subtotal * (taxPercent / 100);
    const grand = subtotal + tax;

    const depositPercent = parseFloat(document.getElementById('deposit_percent').value) || 0;
    const deposit = grand * (depositPercent / 100);
    const remaining = grand - deposit;

    document.getElementById('subtotal').innerText = formatMoney(subtotal);
    document.getElementById('tax').innerText = formatMoney(tax);
    document.getElementById('grand').innerText = formatMoney(grand);
    document.getElementById('deposit_amount').innerText = formatMoney(deposit);
    document.getElementById('remaining_amount').innerText = formatMoney(remaining);

    document.getElementById('subtotal_amount_input').value = subtotal.toFixed(2);
    document.getElementById('tax_amount_input').value = tax.toFixed(2);
    document.getElementById('grand_total_input').value = grand.toFixed(2);
    document.getElementById('deposit_amount_input').value = deposit.toFixed(2);
    document.getElementById('remaining_balance_input').value = remaining.toFixed(2);

    const depositDue = document.getElementById('due_date').value;
    const remainingDue = document.getElementById('remaining_due_date').value;

    document.getElementById('preview_due_date').innerText = formatDateValue(depositDue);
    document.getElementById('preview_remaining_due_date').innerText = formatDateValue(remainingDue);
    document.getElementById('deposit_due_note').innerText = 'Due on ' + formatDateValue(depositDue);
    document.getElementById('remaining_due_note').innerText = 'Due on ' + formatDateValue(remainingDue);
}

document.getElementById('customer_id').addEventListener('change', function(){
    const opt = this.options[this.selectedIndex];
    document.getElementById('customer_name').value = opt.dataset.name || '';
    document.getElementById('customer_email').value = opt.dataset.email || '';
    document.getElementById('customer_phone').value = opt.dataset.phone || '';

    document.getElementById('preview_name').innerText = opt.dataset.name || 'Customer';
    document.getElementById('preview_email').innerText = opt.dataset.email || '';
});

document.getElementById('customer_name').addEventListener('input', function(){
    document.getElementById('preview_name').innerText = this.value || 'Customer';
});

document.getElementById('customer_email').addEventListener('input', function(){
    document.getElementById('preview_email').innerText = this.value || '';
});

document.getElementById('tax_percent').addEventListener('input', calc);
document.getElementById('deposit_percent').addEventListener('input', calc);
document.getElementById('due_date').addEventListener('change', calc);
document.getElementById('remaining_due_date').addEventListener('change', calc);

addItem();
calc();
</script>

@endsection
