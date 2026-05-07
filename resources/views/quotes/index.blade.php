@extends('layouts.admin')

@section('content')

@php
$user = auth()->user();
@endphp

<div class="page-shell">

    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h1>Service <span>Quotes</span></h1>
            <p>Track and manage your outgoing estimates</p>
        </div>

        @if($user->company_id)
            <a href="{{ route('quotes.create') }}" class="btn-gradient">
                + Create Quote
            </a>
        @endif
    </div>

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <small>Total Quotes</small>
            <h3>{{ $quotes->count() }}</h3>
        </div>

        <div class="stat-card">
            <small>Draft</small>
            <h3>{{ $quotes->where('status','draft')->count() }}</h3>
        </div>

        <div class="stat-card">
            <small>Sent</small>
            <h3>{{ $quotes->where('status','sent')->count() }}</h3>
        </div>

        <div class="stat-card">
            <small>Approved</small>
            <h3>{{ $quotes->where('status','approved')->count() }}</h3>
        </div>
    </div>

    {{-- LIST --}}
    <div class="list-container">

        @forelse($quotes as $quote)

        @php
            $items = json_decode($quote->items,true) ?? [];
        @endphp

        <div class="list-row">

            {{-- LEFT --}}
            <div class="row-left">
                <div class="icon">📄</div>

                <div>
                    <div class="title">#{{ $quote->quote_number }}</div>
                    <div class="sub">{{ $quote->customer->name ?? 'N/A' }}</div>
                    <div class="date">{{ $quote->created_at->format('M d, Y') }}</div>
                </div>
            </div>

            {{-- CENTER --}}
            <div class="row-center">
                <div class="amount">${{ number_format($quote->total,2) }}</div>
                <div class="items">{{ count($items) }} items</div>
            </div>

            {{-- STATUS --}}
            <div class="row-status">
                <span class="status-badge {{ $quote->status }}">
                    {{ strtoupper($quote->status) }}
                </span>
            </div>

            {{-- ACTIONS --}}
            <div class="row-actions">
                {{-- ✅ ADDED: VIEW BUTTON (Points to the Master Preview) --}}
                <a href="{{ route('quotes.pro_preview', $quote->id) }}" class="btn btn-sky">View</a>

                <a href="{{ route('quotes.edit',$quote->id) }}" class="btn btn-blue">Edit</a>

                <a href="#" class="btn btn-purple">Send</a>

                <a href="#" class="btn btn-green">Convert</a>

<form action="{{ route('quotes.destroy', $quote->id) }}"
      method="POST"
      style="display:inline-block;"
      onsubmit="return confirm('Are you sure you want to delete this quote?');">

    @csrf
    @method('DELETE')

    <button type="submit" class="btn btn-red">
        Delete
    </button>

</form>

            </div>

        </div>

        @empty

        <div class="empty">
            No quotes found.
        </div>

        @endforelse

    </div>

</div>

<style>

.page-shell{
padding:30px;
color:#fff;
}

.page-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
}

.page-header h1{
font-size:32px;
font-weight:800;
}

.page-header span{
color:#38bdf8;
}

/* 🔥 NEW GRADIENT BUTTON (matches invoice) */
.btn-gradient{
background:linear-gradient(135deg,#4f46e5,#9333ea);
padding:14px 28px;
border-radius:14px;
color:#fff;
font-weight:700;
text-decoration:none;
box-shadow:0 10px 25px rgba(0,0,0,.35);
transition:.2s;
}

.btn-gradient:hover{
transform:translateY(-2px);
box-shadow:0 14px 30px rgba(0,0,0,.45);
}

/* STATS */
.stats-grid{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:30px;
}

.stat-card{
background:#0b1a33;
padding:22px;
border-radius:16px;
border:1px solid #1c3760;
box-shadow:0 10px 30px rgba(0,0,0,.25);
}

.stat-card small{
display:block;
font-size:13px;
color:#94a3b8;
margin-bottom:10px;
}

.stat-card h3{
font-size:34px;
font-weight:800;
margin:0;
}

/* Invoice-style colors */
.stat-card:nth-child(1) h3{ color:#38bdf8; }
.stat-card:nth-child(2) h3{ color:#10b981; }
.stat-card:nth-child(3) h3{ color:#f59e0b; }
.stat-card:nth-child(4) h3{ color:#a855f7; }

/* LIST */
.list-container{
display:flex;
flex-direction:column;
gap:15px;
}

.list-row{
display:flex;
justify-content:space-between;
align-items:center;
background:rgba(11,26,52,.9);
border:1px solid #1c3760;
padding:20px;
border-radius:18px;
box-shadow:0 10px 30px rgba(0,0,0,.25);
}

/* LEFT */
.row-left{
display:flex;
gap:15px;
align-items:center;
}

.icon{
background:#7c3aed;
width:42px;
height:42px;
display:flex;
align-items:center;
justify-content:center;
border-radius:50%;
font-size:18px;
}

.title{
font-weight:bold;
}

.sub{
font-size:13px;
color:#94a3b8;
}

.date{
font-size:12px;
color:#64748b;
}

/* CENTER */
.row-center{
text-align:right;
}

.amount{
font-weight:bold;
font-size:16px;
}

.items{
font-size:12px;
color:#94a3b8;
}

/* STATUS */
.status-badge{
padding:6px 12px;
border-radius:999px;
font-size:12px;
font-weight:700;
}

.status-badge.approved{ background:#16a34a; }
.status-badge.sent{ background:#0ea5e9; }
.status-badge.draft{ background:#64748b; }

/* ACTION BUTTONS (🔥 invoice style) */
.row-actions{
display:flex;
gap:10px;
flex-wrap:wrap;
}

.btn{
padding:8px 14px;
border-radius:10px;
font-size:13px;
font-weight:600;
color:#fff;
text-decoration:none;
}

.btn-sky{ background:#0ea5e9; }
.btn-blue{ background:#3b82f6; }
.btn-purple{ background:#7c3aed; }
.btn-green{ background:#10b981; }
.btn-red{ background:#ef4444; }

.empty{
text-align:center;
color:#94a3b8;
}

</style>

@endsection
