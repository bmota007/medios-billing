@extends('layouts.admin')

@section('content')
@php
$defaultItems = !empty($quote->items)
? collect($quote->items)->map(function($item){
return [
'service' => $item->service_name ?? ($item['service_name'] ?? ''),
'description' => $item->description ?? ($item['description'] ?? ''),
'qty' => $item->quantity ?? ($item['quantity'] ?? 1),
'price' => $item->unit_price ?? ($item['unit_price'] ?? 0),
'total' => $item->line_total ?? ($item['line_total'] ?? 0),
];
})->toArray()
: [['service'=>'','description'=>'','qty'=>1,'price'=>0,'total'=>0]];

$oldItems = old('items', $defaultItems);
@endphp

<div class="quote-shell">

<div class="topbar">
    <div>
        <h2>{{ isset($quote) ? 'Update Quote' : 'Create New Quote' }}</h2>
        <p>Build a professional proposal with custom services and payment terms.</p>
        <a href="{{ route('quotes.index') }}" class="back-link">← Back to Quotes</a>
    </div>

    <button type="button" class="preview-btn">
        <i class="fa fa-eye"></i> View Preview
    </button>
</div>

<form action="{{ isset($quote) ? route('quotes.update',$quote->id) : route('quotes.store') }}" method="POST" id="quoteForm">
@csrf
@if(isset($quote))
@method('PUT')
@endif

<div class="builder-grid">

{{-- LEFT SIDE --}}
<div class="left-panel">

{{-- STEP 1 --}}
<div class="card-premium">
<div class="step-title"><span>1</span> Client & Timeline</div>

<div class="row g-3">
<div class="col-md-4">
<label>Customer</label>
<select name="customer_id" class="form-control premium-input" required>
@foreach($customers as $customer)
<option value="{{ $customer->id }}"
{{ old('customer_id',$quote->customer_id ?? '') == $customer->id ? 'selected' : '' }}>
{{ $customer->name }}
</option>
@endforeach
</select>
</div>

<div class="col-md-4">
<label>Quote Date</label>
<input type="date"
name="quote_date"
value="{{ old('quote_date', $quote->quote_date ?? date('Y-m-d')) }}"
class="form-control premium-input">
</div>

<div class="col-md-4">
<label>Expiration Date</label>
<input type="date"
name="expiry_date"
value="{{ old('expiry_date', $quote->expiry_date ?? date('Y-m-d', strtotime('+7 days'))) }}"
class="form-control premium-input">
</div>
</div>
</div>

{{-- STEP 2 --}}
<div class="card-premium">
<div class="step-head">
<div class="step-title m-0"><span>2</span> Service Items & Pricing</div>

<button type="button" onclick="addItemRow()" class="btn-blue">
+ Add Line Item
</button>
</div>

<div class="table-wrap">
<table class="quote-table">
<thead>
<tr>
<th>SERVICE</th>
<th>DESCRIPTION</th>
<th width="90">QTY</th>
<th width="170">UNIT PRICE</th>
<th width="160">LINE TOTAL</th>
<th width="80">REMOVE</th>
</tr>
</thead>

<tbody id="quoteItemsBody">
@foreach($oldItems as $index => $item)
<tr class="item-row">
<td>
<input type="text"
name="items[{{ $index }}][service]"
value="{{ $item['service'] }}"
class="premium-input"
required>
</td>

<td>
<input type="text"
name="items[{{ $index }}][description]"
value="{{ $item['description'] }}"
class="premium-input">
</td>

<td>
<input type="number"
name="items[{{ $index }}][qty]"
value="{{ $item['qty'] }}"
class="premium-input item-qty"
oninput="calculateRow(this)">
</td>

<td>
<div class="money-wrap">
<span>$</span>
<input type="number"
step="0.01"
name="items[{{ $index }}][price]"
value="{{ $item['price'] }}"
class="premium-input item-price"
oninput="calculateRow(this)">
</div>
</td>

<td class="line-box item-total-display">$0.00</td>

<input type="hidden"
name="items[{{ $index }}][total]"
value="{{ $item['total'] }}"
class="item-total-hidden">

<td>
<button type="button" onclick="removeItemRow(this)" class="trash-btn">
🗑
</button>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

{{-- STEP 3 --}}
<div class="card-premium">
<div class="step-title"><span>3</span> Payment Rules</div>

<div class="row g-3 align-items-end">
<div class="col-md-3">
<label>Deposit Amount</label>
<input type="number"
name="deposit_value"
value="{{ old('deposit_value',$quote->deposit_value ?? 0) }}"
class="form-control premium-input"
oninput="calculateTotals()">
</div>

<div class="col-md-3">
<label>Deposit Type</label>
<select name="deposit_type"
class="form-control premium-input"
onchange="calculateTotals()">
<option value="none">No Deposit</option>
<option value="percentage">Percentage %</option>
<option value="fixed">Fixed Dollar $</option>
</select>
</div>

<div class="col-md-6">
<div class="hint-box" id="depositHint">
Deposit will be calculated automatically.
</div>
</div>
</div>
</div>

{{-- STEP 4 --}}
<div class="card-premium">
<div class="step-title"><span>4</span> Contract & Automation</div>

<div class="row g-4">
<div class="col-md-6">

<div class="check-line">
<input type="checkbox" name="contract_required" value="1"
{{ old('contract_required', $quote->contract_required ?? false) ? 'checked' : '' }}>
<label>Require Contract Signature</label>
</div>

<label class="mt-3">Legal Template</label>
<select name="selected_contract_id" class="form-control premium-input">
@for($i=1;$i<=4;$i++)
@php $field="contract_{$i}_name"; @endphp
@if(auth()->user()->company->$field)
<option value="{{ $i }}"
{{ old('selected_contract_id',$quote->selected_contract_id ?? '') == $i ? 'selected' : '' }}>
{{ auth()->user()->company->$field }}
</option>
@endif
@endfor
</select>

</div>

<div class="col-md-6">

<div class="mini-card">
<h6>Automation Options</h6>

<div class="check-line">
<input type="checkbox" checked>
<label>Require Signature Before Payment</label>
</div>

<div class="check-line">
<input type="checkbox" checked>
<label>Auto Convert to Invoice After Deposit</label>
</div>

</div>

</div>
</div>
</div>

{{-- STEP 5 --}}
<div class="card-premium">
<div class="step-title"><span>5</span> Client Notes</div>

<textarea
name="customer_notes"
rows="5"
class="form-control premium-input"
placeholder="Visible to client...">{{ old('customer_notes',$quote->customer_notes ?? '') }}</textarea>

<div class="small-note">These notes will be shown on the proposal and PDF.</div>
</div>

</div>

{{-- RIGHT SIDEBAR --}}
<div class="right-panel">

<div class="summary-card">

<div class="summary-icon">💳</div>
<h4>Quote Summary</h4>

<div class="sum-row">
<span>Subtotal</span>
<strong>$<span id="sub_total_display">0.00</span></strong>
</div>

<div class="sum-row gold">
<span>Deposit Due</span>
<strong>$<span id="dep_total_display">0.00</span></strong>
</div>

<div class="sum-row">
<span>Balance Remaining</span>
<strong>$<span id="bal_total_display">0.00</span></strong>
</div>

<hr>

<div class="grand-row">
<span>Grand Total</span>
<strong>$<span id="grand_total_display">0.00</span></strong>
</div>

<button type="submit" class="publish-btn">
🚀 Publish Quote
</button>

<p>This quote will be saved and ready to send.</p>

</div>

<div class="steps-card">
<h5>What happens next?</h5>

<div class="next-step">1. Publish Quote</div>
<div class="next-step">2. Share with Client</div>
<div class="next-step">3. Client Accepts</div>
<div class="next-step">4. Auto Convert</div>

</div>

</div>

</div>
</form>
</div>

<style>
.quote-shell{
padding:30px;
background:linear-gradient(135deg,#040b16,#07142a,#091c38);
min-height:100vh;
color:#fff;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:flex-start;
margin-bottom:28px;
gap:20px;
}

.topbar h2{
font-size:34px;
font-weight:800;
margin:0 0 6px;
}

.topbar p{
margin:0 0 8px;
color:#95a7c5;
}

.back-link{
color:#fff;
text-decoration:none;
font-size:14px;
}

.preview-btn{
background:transparent;
border:1px solid #4a5f87;
color:#fff;
padding:10px 18px;
border-radius:10px;
}

.builder-grid{
display:grid;
grid-template-columns:2.1fr .9fr;
gap:26px;
align-items:start;
}

.card-premium,.summary-card,.steps-card{
background:rgba(11,26,52,.95);
border:1px solid #1c3760;
border-radius:18px;
padding:24px;
margin-bottom:22px;
box-shadow:0 15px 35px rgba(0,0,0,.35);
}

.step-title{
font-size:22px;
font-weight:700;
display:flex;
align-items:center;
gap:12px;
margin-bottom:20px;
}

.step-title span{
width:34px;
height:34px;
display:flex;
align-items:center;
justify-content:center;
background:#2374ff;
border-radius:10px;
font-size:16px;
font-weight:800;
}

.step-head{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:18px;
}

.btn-blue{
background:#2374ff;
border:none;
color:#fff;
padding:12px 18px;
border-radius:10px;
font-weight:700;
}

label{
font-size:13px;
margin-bottom:7px;
color:#9eb0cc;
display:block;
}

.premium-input{
width:100%;
background:#071222;
border:1px solid #28466f;
color:#fff;
padding:12px 14px;
border-radius:10px;
}

.quote-table{
width:100%;
border-collapse:separate;
border-spacing:0 10px;
}

.quote-table th{
font-size:12px;
color:#8da1c4;
padding-bottom:8px;
}

.quote-table td{
vertical-align:middle;
}

.money-wrap{
display:flex;
align-items:center;
gap:6px;
}

.line-box{
background:#09172a;
border:1px solid #28466f;
padding:12px;
border-radius:10px;
font-weight:700;
}

.trash-btn{
background:#5f1020;
border:none;
color:#ff6f89;
padding:10px 14px;
border-radius:10px;
}

.hint-box,.mini-card{
background:#132949;
border:1px solid #28466f;
padding:18px;
border-radius:14px;
color:#9ec3ff;
}

.check-line{
display:flex;
gap:10px;
margin-bottom:14px;
align-items:center;
}

.small-note{
margin-top:10px;
font-size:13px;
color:#8fa0bc;
}

.summary-card h4{
margin:0 0 18px;
font-size:30px;
font-weight:800;
}

.summary-icon{
font-size:28px;
margin-bottom:12px;
}

.sum-row,.grand-row{
display:flex;
justify-content:space-between;
margin-bottom:16px;
font-size:18px;
}

.gold{
color:#ffd34f;
font-weight:700;
}

.grand-row{
font-size:34px;
font-weight:900;
color:#3e8cff;
margin-top:18px;
}

.publish-btn{
width:100%;
margin-top:18px;
background:#0fba62;
border:none;
padding:16px;
border-radius:12px;
font-size:22px;
font-weight:800;
color:#fff;
}

.summary-card p{
margin-top:12px;
text-align:center;
font-size:13px;
color:#9eb0cc;
}

.steps-card h5{
font-size:24px;
margin-bottom:18px;
}

.next-step{
padding:12px 0;
border-bottom:1px solid #1f3b66;
color:#dce6ff;
}

@media(max-width:1200px){
.builder-grid{
grid-template-columns:1fr;
}
}
</style>

<script>
let itemIdx={{ count($oldItems) }};

function addItemRow(){
const tbody=document.getElementById('quoteItemsBody');

const row=document.createElement('tr');
row.classList.add('item-row');

row.innerHTML=`
<td><input type="text" name="items[${itemIdx}][service]" class="premium-input" required></td>
<td><input type="text" name="items[${itemIdx}][description]" class="premium-input"></td>
<td><input type="number" name="items[${itemIdx}][qty]" class="premium-input item-qty" value="1" oninput="calculateRow(this)"></td>
<td><div class="money-wrap"><span>$</span><input type="number" step="0.01" name="items[${itemIdx}][price]" class="premium-input item-price" value="0" oninput="calculateRow(this)"></div></td>
<td class="line-box item-total-display">$0.00</td>
<input type="hidden" name="items[${itemIdx}][total]" class="item-total-hidden" value="0">
<td><button type="button" onclick="removeItemRow(this)" class="trash-btn">🗑</button></td>
`;

tbody.appendChild(row);
itemIdx++;
}

function removeItemRow(btn){
if(document.querySelectorAll('.item-row').length>1){
btn.closest('tr').remove();
calculateTotals();
}
}

function calculateRow(input){
const row=input.closest('tr');

const qty=parseFloat(row.querySelector('.item-qty').value)||0;
const price=parseFloat(row.querySelector('.item-price').value)||0;
const total=qty*price;

row.querySelector('.item-total-display').innerHTML='$'+total.toLocaleString(undefined,{minimumFractionDigits:2});
row.querySelector('.item-total-hidden').value=total.toFixed(2);

calculateTotals();
}

function calculateTotals(){
let subtotal=0;

document.querySelectorAll('.item-total-hidden').forEach(el=>{
subtotal += parseFloat(el.value)||0;
});

const depType=document.querySelector('[name="deposit_type"]').value;
const depVal=parseFloat(document.querySelector('[name="deposit_value"]').value)||0;

let depTotal=0;

if(depType==='percentage'){
depTotal=subtotal*(depVal/100);
}else if(depType==='fixed'){
depTotal=depVal;
}

const bal=subtotal-depTotal;

document.getElementById('sub_total_display').innerHTML=subtotal.toLocaleString(undefined,{minimumFractionDigits:2});
document.getElementById('dep_total_display').innerHTML=depTotal.toLocaleString(undefined,{minimumFractionDigits:2});
document.getElementById('bal_total_display').innerHTML=bal.toLocaleString(undefined,{minimumFractionDigits:2});
document.getElementById('grand_total_display').innerHTML=subtotal.toLocaleString(undefined,{minimumFractionDigits:2});

document.getElementById('depositHint').innerHTML=
depType==='percentage'
? depVal+'% of $'+subtotal.toFixed(2)+' = $'+depTotal.toFixed(2)
: 'Deposit will be calculated automatically.';
}

document.addEventListener('DOMContentLoaded',()=>{
document.querySelectorAll('.item-row').forEach(row=>{
calculateRow(row.querySelector('.item-qty'));
});
});
</script>

@endsection
