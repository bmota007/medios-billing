@extends('layouts.app')

@section('content')

<div style="max-width:800px;margin:40px auto;background:white;padding:30px;border-radius:8px;">

<h2>Create Invoice</h2>

<form method="POST" action="{{ route('invoice.preview') }}">
@csrf

<input type="hidden" name="customer_id" value="{{ $customer->id ?? '' }}">

<label>Customer Name</label>
<input type="text" name="customer_name"
       value="{{ $customer->name ?? '' }}"
       style="width:100%;padding:8px;margin-bottom:15px;">

<label>Email</label>
<input type="text" name="customer_email"
       value="{{ $customer->email ?? '' }}"
       style="width:100%;padding:8px;margin-bottom:15px;">

<label>Phone</label>
<input type="text" name="customer_phone"
       value="{{ $customer->phone ?? '' }}"
       style="width:100%;padding:8px;margin-bottom:15px;">

<hr>

<h4>Line Items</h4>

<div id="items">
    <div>
        <input type="text" name="items[0][description]" placeholder="Description">
        <input type="number" name="items[0][amount]" placeholder="Amount">
    </div>
</div>

<br>

<button type="button" onclick="addItem()">+ Add Item</button>

<br><br>

<button type="submit"
        style="background:#16a34a;color:white;padding:12px 20px;border:none;border-radius:6px;">
    Generate Invoice
</button>

</form>

</div>

<script>
let itemIndex = 1;

function addItem() {
    let container = document.getElementById('items');

    let div = document.createElement('div');
    div.innerHTML = `
        <input type="text" name="items[${itemIndex}][description]" placeholder="Description">
        <input type="number" name="items[${itemIndex}][amount]" placeholder="Amount">
    `;

    container.appendChild(div);
    itemIndex++;
}
</script>

@endsection
