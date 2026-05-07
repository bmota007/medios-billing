@extends('layouts.admin')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div>
            <h1>{{ isset($quote) ? 'Edit' : 'Create' }} <span>Quote</span></h1>
            <p>Build a professional proposal with pricing and automation</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('quotes.index') }}" class="btn-secondary">← Back</a>
        </div>
    </div>

    <form method="POST" action="{{ isset($quote) ? route('quotes.update',$quote->id) : route('quotes.store') }}">
        @csrf
        @if(isset($quote)) @method('PUT') @endif

        <div class="builder-grid">
            <div>
                <div class="card">
                    <h3>1. Client & Project Timeline</h3>
                    <label class="p-label">Quote Title</label>
                    <input type="text" name="title" value="{{ old('title', $quote->title ?? '') }}" class="input" placeholder="e.g. Bathroom Project" required>
                    
                    <label class="p-label">Customer</label>
                    <select name="customer_id" class="input" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $quote->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="grid-2">
                        <div>
                            <label class="p-label">Quote Date</label>
                            <input type="date" name="quote_date" value="{{ old('quote_date', (isset($quote) && $quote->quote_date) ? (is_string($quote->quote_date) ? $quote->quote_date : $quote->quote_date->format('Y-m-d')) : date('Y-m-d')) }}" class="input">
                        </div>
                        <div>
                            <label class="p-label">Expiration Date</label>
                            <input type="date" name="expiry_date" value="{{ old('expiry_date', (isset($quote) && $quote->expiry_date) ? (is_string($quote->expiry_date) ? $quote->expiry_date : $quote->expiry_date->format('Y-m-d')) : date('Y-m-d', strtotime('+7 days'))) }}" class="input">
                        </div>
                    </div>

                    <div class="grid-2" style="margin-top:10px;">
                        <div>
                            <label class="p-label">Deposit Due Date</label>
                            <input type="date" name="deposit_due_date" value="{{ old('deposit_due_date', (isset($quote) && $quote->deposit_due_date) ? (is_string($quote->deposit_due_date) ? $quote->deposit_due_date : $quote->deposit_due_date->format('Y-m-d')) : '') }}" class="input">
                        </div>
                        <div>
                            <label class="p-label">Remaining Due Date (Auto-Charge)</label>
                            <input type="date" name="balance_due_date" value="{{ old('balance_due_date', (isset($quote) && $quote->balance_due_date) ? (is_string($quote->balance_due_date) ? $quote->balance_due_date : $quote->balance_due_date->format('Y-m-d')) : '') }}" class="input">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">
                        <h3>2. Service Items</h3>
                        <button type="button" onclick="addRow()" class="btn-blue">+ Add Item</button>
                    </div>
                    <table class="table">
                        <thead><tr><th>Service & Description</th><th width="100">Qty</th><th width="120">Price</th><th width="120">Total</th><th width="50"></th></tr></thead>
                        <tbody id="items">
                            @php 
                                $i=0; 
                                $loadedItems = old('items', (isset($quote) && $quote->items) ? $quote->items : [['service'=>'','description'=>'','qty'=>1,'price'=>0]]); 
                                if(is_string($loadedItems)) $loadedItems = json_decode($loadedItems, true) ?: []; 
                            @endphp
                            @foreach($loadedItems as $item)
                            <tr>
                                <td>
                                    <input name="items[{{ $i }}][service]" value="{{ $item['service'] ?? '' }}" class="input" placeholder="Title">
                                    <textarea name="items[{{ $i }}][description]" class="input" rows="2" style="font-size:12px; margin-top:5px;" placeholder="Details...">{{ $item['description'] ?? '' }}</textarea>
                                </td>
                                <td><input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? 1 }}" class="input qty"></td>
                                <td><input type="number" name="items[{{ $i }}][price]" value="{{ $item['price'] ?? 0 }}" class="input price"></td>
                                <td class="total" style="font-weight: 800; color: #38bdf8;">$0.00</td>
                                <td><button type="button" onclick="removeRow(this)" class="btn-danger">X</button></td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card">
                    <h3>3. Payment Rules</h3>
                    <div class="grid-2">
                        <div>
                            <label class="p-label">Deposit Amount</label>
                            <input type="number" name="deposit_value" id="deposit_value" value="{{ old('deposit_value', $quote->deposit_value ?? 0) }}" class="input">
                        </div>
                        <div>
                            <label class="p-label">Deposit Type</label>
                            <select name="deposit_type" id="deposit_type" class="input">
                                <option value="none">No Deposit</option>
                                <option value="percentage" {{ (old('deposit_type', $quote->deposit_type ?? '') == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ (old('deposit_type', $quote->deposit_type ?? '') == 'fixed') ? 'selected' : '' }}>Fixed Amount ($)</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top:10px; max-width:200px;">
                        <label class="p-label">Tax (%)</label>
                        <input type="number" name="tax_percent" id="tax_percent" value="{{ old('tax_percent', $quote->tax_percent ?? 0) }}" class="input" step="0.01">
                    </div>
                </div>

                <div class="card">
                    <h3>4. Contract & Automation</h3>
                    <label class="check-line">
                        <input type="checkbox" name="contract_required" value="1" {{ old('contract_required', $quote->contract_required ?? false) ? 'checked' : '' }}> 
                        Require Contract Signature
                    </label>
                    
                    @php $company = $company ?? auth()->user()->company; @endphp
                    <label class="p-label" style="margin-top:15px;">Legal Template</label>
                    <select name="selected_contract_id" class="input">
                        <option value="">-- Select Template --</option>
                        @if($company)
                        <optgroup label="🏠 Residential">
                            @if(!empty($company->contract_1_path)) <option value="1" {{ old('selected_contract_id', $quote->selected_contract_id ?? '') == 1 ? 'selected' : '' }}>🇺🇸 {{ $company->contract_1_name ?? 'RESIDENTIAL CONTRACT ENGLISH' }}</option> @endif
                            @if(!empty($company->contract_2_path)) <option value="2" {{ old('selected_contract_id', $quote->selected_contract_id ?? '') == 2 ? 'selected' : '' }}>🇪🇸 {{ $company->contract_2_name ?? 'RESIDENTIAL CONTRACT (SPANISH)' }}</option> @endif
                        </optgroup>
                        <optgroup label="🏢 Commercial">
                            @if(!empty($company->contract_3_path)) <option value="3" {{ old('selected_contract_id', $quote->selected_contract_id ?? '') == 3 ? 'selected' : '' }}>🇺🇸 {{ $company->contract_3_name ?? 'COMMERCIAL CONTRACT (ENGLISH)' }}</option> @endif
                            @if(!empty($company->contract_4_path)) <option value="4" {{ old('selected_contract_id', $quote->selected_contract_id ?? '') == 4 ? 'selected' : '' }}>🇪🇸 {{ $company->contract_4_name ?? 'COMMERCIAL CONTRACT (SPANISH)' }}</option> @endif
                        </optgroup>
                        @endif
                    </select>

                    <div style="margin-top: 15px; display: flex; gap: 20px; flex-wrap: wrap;">
                        <label class="check-line">
                            <input type="checkbox" name="require_sig_before_pay" value="1" {{ old('require_sig_before_pay', $quote->require_sig_before_pay ?? true) ? 'checked' : '' }}> 
                            Require Signature Before Payment
                        </label>
                        <label class="check-line">
                            <input type="checkbox" name="auto_convert_invoice" value="1" {{ old('auto_convert_invoice', $quote->auto_convert_invoice ?? true) ? 'checked' : '' }}> 
                            Auto Convert to Invoice
                        </label>
                    </div>
                </div>

                <div class="card">
                    <h3>5. Customer Notes</h3>
                    <textarea name="customer_notes" class="input" rows="4">{{ old('customer_notes', $quote->customer_notes ?? '') }}</textarea>
                </div>
            </div>

            <div>
                <div class="summary-card" style="position: sticky; top: 20px;">
                    <h3>Quote Summary</h3>
                    <div style="margin-bottom: 20px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom: 10px;"><span>Subtotal</span><span id="sub">$0.00</span></div>
                        <div style="display:flex; justify-content:space-between; margin-bottom: 10px;"><span>Tax</span><span id="tax_val">$0.00</span></div>
                        <div style="display:flex; justify-content:space-between; border-top: 1px solid #1c3760; padding-top: 10px; font-weight: 900; font-size: 24px; color: #38bdf8;">
                            <span>Total</span><span id="grand">$0.00</span>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:20px; border-top: 1px solid #1c3760; padding-top:20px;">
                        <div style="background:#0b1a33; padding:12px; border-radius:12px; border:1px solid #1c3760;">
                            <div class="p-label" style="font-size:9px; margin-bottom:4px;">Deposit Required</div>
                            <div id="card_dep" style="color:#facc15; font-weight:900; font-size:16px;">$0.00</div>
                        </div>
                        <div style="background:#0b1a33; padding:12px; border-radius:12px; border:1px solid #1c3760;">
                            <div class="p-label" style="font-size:9px; margin-bottom:4px;">Remaining Balance</div>
                            <div id="card_rem" style="color:#10b981; font-weight:900; font-size:16px;">$0.00</div>
                        </div>
                    </div>

                    <p style="font-size: 10px; color: #94a3b8; margin-top: 15px; text-align: center; line-height: 1.4;">
                        <i class="fa-solid fa-circle-info"></i> Balance will be <strong>automatically charged</strong> on the Remaining Due Date.
                    </p>

                    <button class="btn-primary" style="margin-top:15px;">🚀 {{ isset($quote) ? 'Update Quote' : 'Publish Quote' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.page-shell{padding:30px;color:#fff;max-width:1400px}
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px}
.page-header span{color:#38bdf8}
.builder-grid{display:grid;grid-template-columns:2fr 1fr;gap:25px}
.card{background:#0b1a33;padding:24px;border-radius:18px;margin-bottom:20px;border:1px solid #1c3760;box-shadow: 0 10px 30px rgba(0,0,0,0.2);}
.card-head{display:flex;justify-content:space-between;align-items:center}
.input{width:100%;padding:12px;margin-bottom:8px;background:#020617;border:1px solid #1e293b;color:#fff;border-radius:10px}
.p-label{font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 5px; display: block;}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:15px}
.btn-primary{background:linear-gradient(135deg,#3b82f6,#9333ea);padding:16px;border:none;border-radius:12px;color:#fff;width:100%;font-weight:bold;cursor:pointer;}
.btn-blue{background:#2563eb;padding:10px 16px;border:none;color:#fff;border-radius:10px;font-weight: 800;}
.btn-danger{background:#ef4444;border:none;padding:8px 12px;border-radius:8px;color:#fff;}
.btn-secondary{background:#334155;padding:12px 18px;border-radius:10px;color:#fff;text-decoration:none;font-weight: 700;}
.summary-card{background:#020617;padding:25px;border-radius:18px;border:1px solid #1c3760;}
.check-line{display:flex;align-items:center;gap:12px;margin-bottom:12px;cursor:pointer;font-weight: 700;}

/* ✅ FIX: VISIBLE CALENDAR ICONS */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
    opacity: 0.8;
}
input[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}
</style>

<script>
function addRow(){
    let i=document.querySelectorAll('#items tr').length;
    document.getElementById('items').insertAdjacentHTML('beforeend',`
        <tr>
            <td>
                <input name="items[${i}][service]" class="input" placeholder="Title">
                <textarea name="items[${i}][description]" class="input" rows="2" style="font-size:12px; margin-top:5px;" placeholder="Details..."></textarea>
            </td>
            <td><input type="number" name="items[${i}][qty]" class="input qty" value="1"></td>
            <td><input type="number" name="items[${i}][price]" class="input price" value="0"></td>
            <td class="total" style="font-weight: 800; color: #38bdf8;">$0.00</td>
            <td><button type="button" onclick="removeRow(this)" class="btn-danger">X</button></td>
        </tr>
    `);
}
function removeRow(btn){ btn.closest('tr').remove(); calc(); }
document.addEventListener('input',calc);
function calc(){
    let sub=0;
    document.querySelectorAll('#items tr').forEach(r=>{
        let q=parseFloat(r.querySelector('.qty')?.value)||0;
        let p=parseFloat(r.querySelector('.price')?.value)||0;
        let t=q*p;
        r.querySelector('.total').innerText='$'+t.toLocaleString(undefined, {minimumFractionDigits: 2});
        sub+=t;
    });
    let taxP = parseFloat(document.getElementById('tax_percent')?.value) || 0;
    let taxA = sub * (taxP / 100);
    let final = sub + taxA;
    
    let depV = parseFloat(document.getElementById('deposit_value')?.value) || 0;
    let depT = document.getElementById('deposit_type')?.value;
    let depFinal = (depT === 'percentage') ? (final * (depV / 100)) : depV;
    let remain = final - depFinal;

    document.getElementById('sub').innerText='$'+sub.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('tax_val').innerText='$'+taxA.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('grand').innerText='$'+final.toLocaleString(undefined, {minimumFractionDigits: 2});
    
    document.getElementById('card_dep').innerText='$'+depFinal.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('card_rem').innerText='$'+remain.toLocaleString(undefined, {minimumFractionDigits: 2});
}
calc();
</script>
@endsection
