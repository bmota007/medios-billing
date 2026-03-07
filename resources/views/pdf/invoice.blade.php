<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice</title>

<style>
:root{
  --blue:#0657bd;
  --gold:#c5882d;
  --goldSoft:#f8d676;
  --text:#111111;
  --muted:#555555;
}

body{
  font-family: DejaVu Sans, Arial, sans-serif;
  font-size:12px;
  color:var(--text);
  margin:0;
  padding:0;
}

.page{
  margin:0;
  padding:0;
}

/* ================= HEADER ================= */
.header{
  width:100%;
  height:110px;
  background:var(--blue);
  position:relative;
}

.header .logo{
  position:absolute;
  left:32px;
  top:-36px;
  width:180px;
}

.header .header-right{
  position:absolute;
  right:36px;
  top:22px;
  text-align:right;
}

.header .header-right .invoice-label{
  color:var(--goldSoft);
  font-size:20px;
  font-weight:800;
  letter-spacing:1px;
}

.header .header-right .total-label{
  color:#ffffff;
  font-size:12px;
  margin-top:6px;
}

.header .header-right .total-amount{
  color:#ffffff;
  font-size:30px;
  font-weight:900;
  margin-top:2px;
}

/* ================= CONTENT ================= */
.content{
  padding:34px 36px 110px 36px;
}

.top{
  display:flex;
  justify-content:space-between;
  margin-top:14px;
}

.rightbox{
  text-align:right;
  font-size:12px;
}

.muted{ color:var(--muted); }

.invoice-to{
  margin-top:40px;
  width:55%;
}

.label{
  font-weight:700;
  margin-bottom:6px;
}

table{
  width:100%;
  border-collapse:collapse;
  margin-top:36px;
}

thead th{
  text-align:left;
  font-size:11px;
  letter-spacing:.6px;
  color:var(--blue);
  padding-bottom:10px;
  border-bottom:2px solid var(--gold);
}

td{
  padding:10px 6px;
  border-bottom:1px solid #e5e5e5;
}

.num{
  text-align:right;
  white-space:nowrap;
}

/* TOTALS */
.totals{
  width:260px;
  float:right;
  margin-top:30px;
}

.totals-row{
  display:flex;
  justify-content:space-between;
  padding:6px 0;
}

.totals-row.total{
  border-top:2px solid var(--gold);
  margin-top:8px;
  padding-top:8px;
  font-weight:700;
}

/* ================= FOOTER ================= */
.footer{
  position:fixed;
  bottom:0;
  left:0;
  right:0;
  background:var(--blue);
  color:#ffffff;
  text-align:center;
  font-size:13px;
  padding:18px 0;
  line-height:1.6;
}
</style>
</head>

<body>
<div class="page">

<!-- HEADER -->
<div class="header">

@php
  $logoPath = public_path('images/mcintosh-logo.png');
@endphp

@if(file_exists($logoPath))
  <img class="logo" src="file://{{ $logoPath }}">
@endif

<div class="header-right">

  {{-- INVOICE OR RECEIPT --}}
  <div class="invoice-label">
    @if(isset($is_receipt) && $is_receipt)
        RECEIPT
    @else
        INVOICE
    @endif
  </div>

  {{-- TOTAL LABEL --}}
  <div class="total-label">
    @if(isset($is_receipt) && $is_receipt)
        PAID IN FULL
    @else
        TOTAL DUE
    @endif
  </div>

  <div class="total-amount">
    ${{ number_format($grand_total,2) }}
  </div>

</div>
</div>

{{-- ================= GREEN PAID BAR ================= --}}
@if(isset($is_receipt) && $is_receipt)
    <div style="
        background:#6ac259;
        color:#ffffff;
        font-weight:bold;
        padding:10px 36px;
        font-size:16px;
        letter-spacing:1px;
    ">
        ✔ PAID IN FULL
    </div>
@endif

<!-- CONTENT -->
<div class="content">

<div class="top">
  <div></div>
  <div class="rightbox">
    <div class="muted">Date : {{ $invoice_date }}</div>
    <div class="muted">No : {{ $invoice_no }}</div>

    @if(!empty($due_date) && !(isset($is_receipt) && $is_receipt))
      <div class="muted">Due : {{ $due_date }}</div>
    @endif

    @if(isset($is_receipt) && $is_receipt && !empty($paid_at))
      <div class="muted">Paid : {{ \Carbon\Carbon::parse($paid_at)->format('m/d/Y') }}</div>
    @endif
  </div>
</div>

<!-- CUSTOMER INFO -->
<div class="invoice-to">
  <div class="label">
    @if(isset($is_receipt) && $is_receipt)
        RECEIPT TO :
    @else
        INVOICE TO :
    @endif
  </div>

  {{ $customer_name }}<br>
  {{ $customer_email }}<br>

  @if(!empty($street_address))
    {{ $street_address }}<br>
  @endif

  @if(!empty($city_state_zip))
    {{ $city_state_zip }}
  @endif
</div>

<!-- ITEMS -->
<table>
<thead>
<tr>
  <th>PRODUCTS</th>
  <th class="num">QTY</th>
  <th class="num">PRICE</th>
  <th class="num">TOTAL</th>
</tr>
</thead>
<tbody>

@if(!empty($items))
@foreach($items as $item)
<tr>
  <td>{{ $item['desc'] ?? '' }}</td>
  <td class="num">{{ $item['qty'] ?? 0 }}</td>
  <td class="num">${{ number_format($item['price'] ?? 0, 2) }}</td>
  <td class="num">
    ${{ number_format($item['line_total'] ?? (($item['qty'] ?? 0) * ($item['price'] ?? 0)), 2) }}
  </td>
</tr>
@endforeach
@endif

</tbody>
</table>

<!-- TOTALS -->
<div class="totals">

<div class="totals-row">
  <span>Sub-total :</span>
  <span>${{ number_format($sub_total, 2) }}</span>
</div>

<div class="totals-row total">
  <span>Total :</span>
  <span>${{ number_format($grand_total, 2) }}</span>
</div>

</div>

@if (!empty($notes))
<div style="margin-top:20px; padding-top:10px; border-top:1px solid #ccc; font-size:12px;">
  <strong>Notes:</strong><br><br>
  {!! nl2br(e($notes)) !!}
</div>
@endif

<div style="clear:both;"></div>

</div>
</div>

<!-- FOOTER -->
<div class="footer">
sales@mcintoshcleaningservice.com<br>
+1 (281) 572-8322<br>
Houston, Texas
</div>

</body>
</html>
