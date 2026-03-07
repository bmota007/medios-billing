@auth
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ current_company()->name }} – New Invoice</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body { font-family: Arial, sans-serif; margin:40px; }
h1 { font-size:36px; margin-bottom:10px; }
label { display:block; margin-top:14px; font-weight:bold; }
input, select, textarea {
    width:420px;
    padding:8px;
    margin-top:6px;
    font-size:15px;
}
table { width:100%; border-collapse:collapse; margin-top:30px; }
th,td { border:1px solid #ddd; padding:8px; }
th { background:#f5f5f5; }
.right { text-align:right; }
.btn { padding:10px 16px; margin-top:18px; font-size:16px; cursor:pointer; }
.rowbtn { padding:6px 10px; }
.totals { margin-top:20px; font-size:18px; }
.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.logout-btn {
    background:none;
    border:none;
    color:#cc0000;
    font-size:14px;
    cursor:pointer;
    text-decoration:underline;
}
.dashboard-btn {
    display:inline-block;
    background:#2563eb;
    color:#ffffff;
    padding:8px 14px;
    border-radius:6px;
    text-decoration:none;
    font-weight:600;
}
</style>
</head>

<body>

<div class="header">
    <div>
        <a href="{{ route('admin.dashboard') }}" class="dashboard-btn">
            ← Dashboard
        </a>
    <h1>{{ current_company()->name }} — New Invoice</h1>
    </div>

    <div style="display:flex; gap:12px; align-items:center;">
        <a href="{{ route('invoice.history') }}" class="btn">
            Invoice History
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn">Log out</button>
        </form>
    </div>
</div>

<hr style="margin:20px 0;">

@if ($errors->any())
<div style="color:red;">
  <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<form method="POST" action="{{ route('invoice.preview') }}">
@csrf

{{-- SELECT EXISTING CUSTOMER (NOW INSIDE FORM) --}}
<div style="margin-bottom:20px;">
    <label>Select Existing Customer</label>

    <select id="customer_select" name="customer_id">
        <option value="">-- Select Customer --</option>

        @foreach($customers as $customer)
            <option value="{{ $customer->id }}"
                data-name="{{ $customer->name }}"
                data-company="{{ $customer->company_name }}"
                data-email="{{ $customer->email }}"
                data-phone="{{ $customer->phone }}"
                data-street="{{ $customer->billing_address }}"
                data-city="{{ $customer->city }}"
                data-state="{{ $customer->state }}"
                data-zip="{{ $customer->zip }}"
                {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                
                {{ $customer->name }}
                @if($customer->company_name)
                    ({{ $customer->company_name }})
                @endif
            </option>
        @endforeach
    </select>

    <small style="color:#666;">
        Selecting a customer will auto-fill fields below.
    </small>
</div>

<label>Company Name</label>
<input type="text"
       name="company_name"
       id="company_name"
       value="{{ old('company_name') }}">

<label>Customer Name</label>
<input name="customer_name"
       value="{{ old('customer_name') }}"
       required>

<label>Customer Email</label>
<input name="customer_email"
       value="{{ old('customer_email') }}"
       required>

<label>Street Address</label>
<input name="street_address"
       value="{{ old('street_address') }}"
       required>

<label>City, State, ZIP</label>
<input name="city_state_zip"
       value="{{ old('city_state_zip') }}"
       required>

<label>Invoice Date</label>
<input type="date"
       name="invoice_date"
       value="{{ old('invoice_date', date('Y-m-d')) }}"
       required>

<label>Due Date</label>
<input type="date"
       name="due_date"
       value="{{ old('due_date') }}"
       required>

<table id="items">
<thead>
<tr>
  <th>Service</th>
  <th>Qty</th>
  <th>Price</th>
  <th>Total</th>
  <th></th>
</tr>
</thead>
<tbody>

@php
$items = old('items', [['desc'=>'','qty'=>1,'price'=>'']]);
@endphp

@foreach($items as $i => $it)
<tr>
<td><input name="items[{{ $i }}][desc]" value="{{ $it['desc'] }}" required></td>
<td><input class="qty" name="items[{{ $i }}][qty]" value="{{ $it['qty'] }}" required></td>
<td><input class="price" name="items[{{ $i }}][price]" value="{{ $it['price'] }}" required></td>
<td class="right lineTotal">$0.00</td>
<td><button type="button" class="rowbtn" onclick="removeRow(this)">X</button></td>
</tr>
@endforeach

</tbody>
</table>

<button type="button" class="btn" onclick="addRow()">+ Add Line</button>

<label>Notes for Client</label>
<textarea name="notes"
          rows="4"
          style="width:420px;"
          placeholder="Additional notes, payment details, or reminders for the client...">{{ old('notes') }}</textarea>

<div class="totals">
<strong>Subtotal:</strong> $<span id="subTotal">0.00</span><br>
<strong>Total:</strong> $<span id="grandTotal">0.00</span>
</div>

<br>
<button class="btn" type="submit">Generate & Preview Invoice</button>

</form>

<script>
function money(n){ return Number(n).toFixed(2); }

function recalc(){
let rows=document.querySelectorAll('#items tbody tr');
let sub=0;

rows.forEach(r=>{
    let q=parseFloat(r.querySelector('.qty')?.value)||0;
    let p=parseFloat(r.querySelector('.price')?.value)||0;
    let t=q*p;
    r.querySelector('.lineTotal').innerText='$'+money(t);
    sub+=t;
});

document.getElementById('subTotal').innerText=money(sub);
document.getElementById('grandTotal').innerText=money(sub);
}

document.addEventListener('input', recalc);

function addRow(){
let tbody=document.querySelector('#items tbody');
let i=tbody.children.length;
let tr=document.createElement('tr');
tr.innerHTML=`
<td><input name="items[${i}][desc]" required></td>
<td><input class="qty" name="items[${i}][qty]" value="1"></td>
<td><input class="price" name="items[${i}][price]" value="0"></td>
<td class="right lineTotal">$0.00</td>
<td><button type="button" class="rowbtn" onclick="removeRow(this)">X</button></td>
`;
tbody.appendChild(tr);
recalc();
}

function removeRow(btn){
btn.closest('tr').remove();
recalc();
}

document.addEventListener('DOMContentLoaded', function () {
let customerSelect = document.getElementById('customer_select');
if (!customerSelect) return;

function fillCustomerFields(selected) {
    if (!selected) return;

    document.querySelector('[name="customer_name"]').value =
        selected.getAttribute('data-name') || '';

    document.getElementById('company_name').value =
        selected.getAttribute('data-company') || '';

    document.querySelector('[name="customer_email"]').value =
        selected.getAttribute('data-email') || '';

    document.querySelector('[name="street_address"]').value =
        selected.getAttribute('data-street') || '';

    let city  = selected.getAttribute('data-city')  || '';
    let state = selected.getAttribute('data-state') || '';
    let zip   = selected.getAttribute('data-zip')   || '';

    document.querySelector('[name="city_state_zip"]').value =
        city + (city && state ? ', ' : '') + state + (zip ? ' ' + zip : '');
}

customerSelect.addEventListener('change', function () {
    fillCustomerFields(this.options[this.selectedIndex]);
});

if (customerSelect.value) {
    fillCustomerFields(customerSelect.options[customerSelect.selectedIndex]);
}

});
</script>

</body>
</html>
@endauth
