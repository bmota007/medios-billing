@extends('layouts.admin')

@section('content')

<div class="page-shell">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h1>Create <span>Quote</span></h1>
            <p>Build a professional proposal with pricing and automation</p>
        </div>

        <div class="header-actions">
            <a href="{{ route('quotes.index') }}" class="btn-secondary">← Back</a>
            <button type="button" class="btn-purple">👁 Preview</button>
        </div>
    </div>

<form method="POST" action="{{ isset($quote) ? route('quotes.update',$quote->id) : route('quotes.store') }}">
@csrf
@if(isset($quote)) @method('PUT') @endif

<div class="builder-grid">

<!-- LEFT SIDE -->
<div>

<!-- STEP 1 -->
<div class="card">
<h3>1. Client & Timeline</h3>

<select name="customer_id" class="input" required>
@foreach($customers as $customer)
<option value="{{ $customer->id }}"
{{ old('customer_id', $quote->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
{{ $customer->name }}
</option>
@endforeach
</select>

<div class="grid-2">
<input type="date" name="quote_date"
value="{{ old('quote_date', $quote->quote_date ?? date('Y-m-d')) }}"
class="input">

<input type="date" name="expiry_date"
value="{{ old('expiry_date', $quote->expiry_date ?? date('Y-m-d', strtotime('+7 days'))) }}"
class="input">
</div>

</div>

<!-- STEP 2 -->
<div class="card">
<div class="card-head">
<h3>2. Service Items</h3>
<button type="button" onclick="addRow()" class="btn-blue">+ Add Item</button>
</div>

<table class="table">
<thead>
<tr>
<th>Service</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th></th>
</tr>
</thead>

<tbody id="items">

@php $i=0; @endphp
@foreach(old('items', $quote->items ?? [['service'=>'','qty'=>1,'price'=>0]]) as $item)

<tr>
<td><input name="items[{{ $i }}][service]" value="{{ $item['service'] ?? '' }}" class="input"></td>
<td><input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? 1 }}" class="input qty"></td>
<td><input type="number" name="items[{{ $i }}][price]" value="{{ $item['price'] ?? 0 }}" class="input price"></td>
<td class="total">$0.00</td>
<td><button type="button" onclick="removeRow(this)" class="btn-danger">X</button></td>
</tr>

@php $i++; @endphp
@endforeach

</tbody>
</table>

</div>

<!-- STEP 3 -->
<div class="card">
<h3>3. Payment Rules</h3>

<div class="grid-2">
<input type="number" name="deposit_value"
value="{{ old('deposit_value',$quote->deposit_value ?? 0) }}"
class="input" placeholder="Deposit">

<select name="deposit_type" class="input">
<option value="none">No Deposit</option>
<option value="percentage">%</option>
<option value="fixed">$</option>
</select>
</div>

</div>

<!-- STEP 4 -->
<div class="card">
<h3>4. Contract & Automation</h3>

<label class="check-line">
<input type="checkbox" name="contract_required" value="1"
{{ old('contract_required',$quote->contract_required ?? false) ? 'checked' : '' }}>
Require Contract Signature
</label>

<label>Legal Template</label>
<select name="selected_contract_id" class="input">
<option value="">Select Template</option>
</select>

<div class="automation-box">
<label><input type="checkbox" checked> Require Signature Before Payment</label>
<label><input type="checkbox" checked> Auto Convert to Invoice</label>
</div>

</div>

<!-- STEP 5 -->
<div class="card">
<h3>5. Notes</h3>

<textarea name="customer_notes" class="input">{{ old('customer_notes',$quote->customer_notes ?? '') }}</textarea>

</div>

</div>

<!-- RIGHT SIDE -->
<div>

<div class="summary-card">
<h3>Quote Summary</h3>

<p>Subtotal: $<span id="sub">0.00</span></p>
<p>Deposit: $<span id="dep">0.00</span></p>
<p>Total: $<span id="grand">0.00</span></p>

<button class="btn-primary">🚀 Publish Quote</button>

</div>

</div>

</div>

</form>
</div>

<!-- STYLE -->
<style>

.page-shell{padding:30px;color:#fff;max-width:1400px}

.page-header{
display:flex;justify-content:space-between;align-items:center;margin-bottom:25px
}

.page-header span{color:#38bdf8}

.header-actions{display:flex;gap:10px}

.builder-grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:25px;
}

.card{
background:#0b1a33;
padding:20px;
border-radius:14px;
margin-bottom:20px;
border:1px solid #1c3760;
}

.card-head{
display:flex;justify-content:space-between;align-items:center
}

.input{
width:100%;
padding:12px;
margin-bottom:12px;
background:#020617;
border:1px solid #1e293b;
color:#fff;
border-radius:10px;
}

.grid-2{
display:grid;
grid-template-columns:1fr 1fr;
gap:10px;
}

.table{
width:100%;
margin-top:10px;
}

.btn-primary{
background:linear-gradient(135deg,#3b82f6,#9333ea);
padding:14px;
border:none;
border-radius:10px;
color:#fff;
width:100%;
font-weight:bold;
}

.btn-blue{
background:#2563eb;
padding:10px;
border:none;
color:#fff;
border-radius:8px;
}

.btn-danger{
background:#ef4444;
border:none;
padding:6px 10px;
border-radius:6px;
color:#fff;
}

.btn-secondary{
background:#334155;
padding:10px;
border-radius:8px;
color:#fff;
text-decoration:none;
}

.btn-purple{
background:#7c3aed;
padding:10px;
border:none;
border-radius:8px;
color:#fff;
}

.summary-card{
background:#020617;
padding:20px;
border-radius:14px;
border:1px solid #1c3760;
}

</style>

<!-- SCRIPT -->
<script>

function addRow(){
let i=document.querySelectorAll('#items tr').length;

document.getElementById('items').insertAdjacentHTML('beforeend',`
<tr>
<td><input name="items[${i}][service]" class="input"></td>
<td><input type="number" name="items[${i}][qty]" class="input qty"></td>
<td><input type="number" name="items[${i}][price]" class="input price"></td>
<td class="total">$0.00</td>
<td><button type="button" onclick="removeRow(this)" class="btn-danger">X</button></td>
</tr>
`);
}

function removeRow(btn){
btn.closest('tr').remove();
calc();
}

document.addEventListener('input',calc);

function calc(){
let sub=0;

document.querySelectorAll('#items tr').forEach(r=>{
let q=r.querySelector('.qty')?.value||0;
let p=r.querySelector('.price')?.value||0;
let t=q*p;

r.querySelector('.total').innerText='$'+t.toFixed(2);
sub+=t;
});

document.getElementById('sub').innerText=sub.toFixed(2);
document.getElementById('grand').innerText=sub.toFixed(2);
}

</script>

@endsection
