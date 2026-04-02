@extends('layouts.app')

@section('content')

<style>

@media print {

.sidebar {
display:none;
}

.main-content{
margin-left:0 !important;
}

}

</style>

<div class="card">


<head>

<title>Print Quote</title>

<style>

body{
font-family: Arial, sans-serif;
margin:40px;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th{
background:#f3f4f6;
padding:10px;
text-align:left;
}

td{
padding:10px;
border-bottom:1px solid #ddd;
}

.total{
text-align:right;
margin-top:20px;
font-size:18px;
font-weight:bold;
}

</style>

</head>

<body onload="window.print()">

<h2>{{ $quote->company->name }}</h2>

<p>
Quote #: {{ $quote->quote_number }} <br>
Customer: {{ $quote->customer->name }}
</p>

<hr>

<table>

<tr>
<th>Service</th>
<th width="80">Qty</th>
<th width="120">Price</th>
<th width="120">Total</th>
</tr>

@foreach($quote->items as $item)

<tr>
<td>{{ $item->service_name }}</td>
<td>{{ $item->quantity }}</td>
<td>${{ number_format($item->unit_price,2) }}</td>
<td>${{ number_format($item->line_total,2) }}</td>
</tr>

@endforeach

</table>

<div class="total">

Total: ${{ number_format($quote->total,2) }}

</div>

</div>

@endsection
