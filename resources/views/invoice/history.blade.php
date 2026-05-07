@extends('layouts.admin')

@section('content')

@php
$totalInvoices = $invoices->total();
$paidInvoices = $invoices->getCollection()->where('status','paid')->count();
$openInvoices = $invoices->getCollection()->whereIn('status',['unpaid','pending','sent','viewed'])->count();
$totalAmount = $invoices->getCollection()->sum('total');
$outstanding = $invoices->getCollection()->whereIn('status',['unpaid','pending','sent','viewed'])->sum('total');
@endphp

<style>
:root{
--bg:#020617;
--card:#081226;
--line:rgba(255,255,255,.06);
--blue:#2563eb;
--purple:#7c3aed;
--green:#10b981;
--orange:#f59e0b;
--pink:#ec4899;
--red:#dc2626;
--muted:#94a3b8;
}
body{
background:
radial-gradient(circle at top left,rgba(37,99,235,.08),transparent 28%),
radial-gradient(circle at bottom right,rgba(124,58,237,.08),transparent 28%),
#020617!important;
}
.wrap{max-width:1850px;margin:auto;padding:28px;}
.top{
display:flex;justify-content:space-between;align-items:center;gap:20px;
margin-bottom:24px;flex-wrap:wrap;
}
.title{font-size:54px;font-weight:900;color:#fff;line-height:1;}
.title span{color:#38bdf8;}
.sub{margin-top:8px;color:var(--muted);font-size:18px;}
.newbtn{
background:linear-gradient(135deg,#2563eb,#7c3aed);
color:#fff!important;text-decoration:none;
padding:18px 28px;border-radius:16px;
font-size:26px;font-weight:900;
box-shadow:0 18px 35px rgba(37,99,235,.25);
}
.grid5{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px;}
.kpi{
background:var(--card);border:1px solid var(--line);
border-radius:18px;padding:18px;min-height:135px;
}
.kpi small{display:block;color:var(--muted);font-size:13px;margin-bottom:8px;}
.kpi .num{font-size:46px;font-weight:900;color:#fff;line-height:1;}
.kpi .meta{margin-top:10px;font-weight:800;font-size:18px;}
.box{
background:var(--card);
border:1px solid var(--line);
border-radius:22px;
padding:22px;
}
.searchrow{
display:grid;grid-template-columns:2fr auto;gap:14px;margin-bottom:18px;
}
.input{
background:#0b1730;border:1px solid var(--line);
border-radius:14px;padding:14px 16px;color:#fff;width:100%;
}
.searchbtn{
padding:14px 18px;border:none;border-radius:14px;
background:#0ea5e9;color:#fff;font-weight:900;
}
.rowx{
display:grid;
grid-template-columns:2fr 1fr 1fr 1.5fr;
gap:18px;
align-items:center;
padding:18px;
border-radius:18px;
background:rgba(255,255,255,.02);
border:1px solid rgba(255,255,255,.04);
margin-bottom:12px;
}
.customer{display:flex;gap:14px;align-items:center;}
.avatar{
width:46px;height:46px;border-radius:14px;
display:grid;place-items:center;
background:linear-gradient(135deg,#2563eb,#7c3aed);
font-weight:900;color:#fff;
}
.name{font-weight:900;color:#fff;}
.mail{font-size:12px;color:#94a3b8;}
.amount{font-size:20px;font-weight:900;color:#fff;}
.badge{
padding:8px 12px;border-radius:999px;
font-size:12px;font-weight:900;display:inline-block;
}
.paid{background:rgba(16,185,129,.14);color:#10b981;}
.unpaid{background:rgba(220,38,38,.14);color:#ef4444;}
.pending{background:rgba(245,158,11,.14);color:#f59e0b;}
.actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;}
.btnx{
padding:10px 14px;border-radius:12px;
font-size:13px;font-weight:900;text-decoration:none!important;
color:#fff!important;border:none;
}
.view{background:#2563eb;}
.resend{background:#059669;}
.pdf{background:#7c3aed;}
.mark{background:#0891b2;}
.delete{background:#991b1b;}
.blue{color:#38bdf8}.green{color:#10b981}.orange{color:#f59e0b}.purple{color:#a855f7}.pink{color:#ec4899}

@media(max-width:1450px){
.grid5{grid-template-columns:repeat(2,1fr);}
.rowx{grid-template-columns:1fr;}
.actions{justify-content:flex-start;}
.searchrow{grid-template-columns:1fr;}
}
@media(max-width:768px){
.grid5{grid-template-columns:1fr;}
.title{font-size:38px;}
.newbtn{font-size:18px;padding:14px 18px;}
}
</style>

<div class="wrap">

<div class="top">
<div>
<div class="title">Invoice <span>History</span></div>
<div class="sub">Manage and track all business billing records</div>
</div>

<a href="{{ route('invoice.create') }}" class="newbtn">＋ Create Invoice</a>
</div>

<div class="grid5">
<div class="kpi"><small>Total Invoices</small><div class="num">{{ $totalInvoices }}</div><div class="meta blue">All Records</div></div>
<div class="kpi"><small>Paid</small><div class="num">{{ $paidInvoices }}</div><div class="meta green">Collected</div></div>
<div class="kpi"><small>Open</small><div class="num">{{ $openInvoices }}</div><div class="meta orange">Need Action</div></div>
<div class="kpi"><small>Total Amount</small><div class="num">${{ number_format($totalAmount,0) }}</div><div class="meta purple">Revenue</div></div>
<div class="kpi"><small>Outstanding</small><div class="num">${{ number_format($outstanding,0) }}</div><div class="meta pink">Balance Due</div></div>
</div>

<div class="box">

<form method="GET" action="{{ route('invoice.history') }}" class="searchrow">
<input class="input" type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email or invoice #">
<button class="searchbtn">Search</button>
</form>

@forelse($invoices as $inv)
@php $s=strtolower($inv->status ?? 'unpaid'); @endphp

<div class="rowx">

<div class="customer">
<div class="avatar">{{ strtoupper(substr($inv->customer_name,0,1)) }}</div>
<div>
<div class="name">#{{ $inv->invoice_no }}</div>
<div style="font-weight:700;color:#fff">{{ $inv->customer_name }}</div>
<div class="mail">{{ $inv->customer_email }}</div>
</div>
</div>

<div>
<div class="amount">${{ number_format($inv->total,2) }}</div>
<div class="mail">{{ $inv->created_at->format('M d, Y') }}</div>
</div>

<div>
<span class="badge {{ $s=='paid' ? 'paid' : (in_array($s,['pending']) ? 'pending' : 'unpaid') }}">
{{ strtoupper($s) }}
</span>
</div>

<div class="actions">

<a href="{{ route('invoice.view',$inv->invoice_no) }}" class="btnx view">View</a>

@if(Route::has('invoice.download'))
<a href="{{ route('invoice.download',$inv->id) }}" class="btnx pdf">PDF</a>
@endif

@if(Route::has('invoice.resend'))
<form method="POST" action="{{ route('invoice.resend',$inv->id) }}" style="display:inline;">
@csrf
<button class="btnx resend" type="submit">Resend</button>
</form>
@endif

@if($s!='paid' && Route::has('invoice.markPaid'))
<form method="POST" action="{{ route('invoice.markPaid',$inv->id) }}" style="display:inline;">
@csrf
<button class="btnx mark" type="submit">Mark Paid</button>
</form>
@endif

<form method="POST" action="{{ route('invoice.destroy',$inv->id) }}" style="display:inline;" onsubmit="return confirm('Delete this invoice?')">
@csrf
@method('DELETE')
<button class="btnx delete" type="submit">Delete</button>
</form>

</div>

</div>

@empty
<div style="padding:30px;color:#94a3b8;">No invoices found.</div>
@endforelse

<div class="mt-4">
{{ $invoices->links() }}
</div>

</div>

</div>

@endsection
