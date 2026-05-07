@php $hideSidebar = true; $isPdf = $isPdf ?? false; @endphp
@extends($isPdf ? 'layouts.blank' : 'layouts.app')

@php
    $customer = \App\Models\Customer::find($quote->customer_id);
    $company = $quote->company ?? auth()->user()->company ?? null;
    $items = $items ?? json_decode($quote->items ?? '[]', true);
    $subtotal = abs($quote->subtotal ?? $quote->total ?? 0);
    $taxPercent = $quote->tax_percent ?? 0;
    $taxAmount = $subtotal * ($taxPercent / 100);
    $total = $subtotal + $taxAmount;
    $rawDeposit = abs($quote->deposit_value ?? 0);
    $deposit = ($quote->deposit_type === 'percentage') ? ($total * ($rawDeposit / 100)) : $rawDeposit;
    $remaining = $total - $deposit;
    $publicUrl = $quote->public_token ? url('/q/' . $quote->public_token) : '#';
    $fmtDate = fn($d) => $d ? (is_string($d) ? date('M d, Y', strtotime($d)) : $d->format('M d, Y')) : 'N/A';
    
    // Find the generated invoice for payment
    $invoice = \App\Models\Invoice::where('quote_id', $quote->id)->first();
@endphp

@push('page_styles')
<style>
    @auth
    body { 
        background: radial-gradient(circle at top, #0f172a, #020617); 
        color: #fff; 
    }

    .mb-shell { 
        max-width: 1350px; 
        margin: auto; 
        padding: 30px; 
        border-radius: 28px; 
        background: linear-gradient(180deg, rgba(2,6,23,.96), rgba(6,18,42,.96)); 
        border: 1px solid rgba(56,189,248,.18); 
        box-shadow: 0 25px 60px rgba(0,0,0,.28); 
        position: relative; 
    }

    .mb-layout { 
        display: grid; 
        grid-template-columns: 1fr 420px; 
        gap: 30px; 
    }

    .mb-card { 
        background: rgba(15,23,42,.65); 
        padding: 24px; 
        border-radius: 20px; 
        border: 1px solid rgba(255,255,255,0.03); 
        margin-bottom: 20px; 
    }

    .p-label { 
        font-size: 10px; 
        font-weight: 800; 
        color: #94a3b8; 
        text-transform: uppercase; 
        margin-bottom: 8px; 
    }

    .btn-action { 
        width: 100%; 
        padding: 18px; 
        border: none; 
        border-radius: 15px; 
        color: #fff; 
        font-weight: 900; 
        font-size: 18px; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        gap: 10px; 
    }

    input, textarea { 
        background: #020617 !important; 
        color: #fff !important; 
        border: 1px solid rgba(255,255,255,0.1) !important; 
        width: 100%; 
        border-radius: 10px; 
        padding: 12px; 
    }

    .btn-header { 
        color:#fff; 
        text-decoration:none; 
        font-weight:800; 
        padding:14px 24px; 
        border-radius:12px; 
        font-size:15px; 
        display:flex; 
        align-items:center; 
        gap:10px; 
        border:none; 
        cursor:pointer; 
    }

    .alert-success { 
        background: rgba(16, 185, 129, 0.15); 
        border: 1px solid #10b981; 
        color: #10b981; 
        padding: 15px; 
        border-radius: 12px; 
        margin-bottom: 25px; 
        text-align: center; 
        font-weight: 800; 
    }
    @endauth


    @guest
    body { 
        background: #f1f5f9; 
        color: #1e293b; 
        font-family: 'Inter', sans-serif; 
        margin: 0; 
        padding: 20px; 
    }

    .guest-shell { 
        background: #fff; 
        max-width: 1100px; 
        margin: 20px auto; 
        border-radius: 24px; 
        overflow: hidden; 
        box-shadow: 0 40px 100px rgba(0,0,0,0.12); 
        border: 1px solid #e2e8f0; 
    }

    .guest-header { 
        background: #0f172a; 
        padding: 60px; 
        color: #fff; 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        position: relative; 
    }

    .guest-header::after { 
        content: ""; 
        position: absolute; 
        bottom: 0; 
        left: 0; 
        right: 0; 
        height: 4px; 
        background: #38bdf8; 
    }

    .guest-content { 
        padding: 60px; 
    }

    .card-grid { 
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        gap: 20px; 
        margin-bottom: 50px; 
    }

    .info-card { 
        background: #f8fafc; 
        padding: 20px; 
        border-radius: 16px; 
        border: 1px solid #edf2f7; 
    }

    .info-label { 
        font-size: 10px; 
        font-weight: 900; 
        color: #64748b; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
        margin-bottom: 10px; 
        display: block; 
    }

    .info-value { 
        font-size: 16px; 
        font-weight: 700; 
        color: #0f172a; 
    }

    .investment-card { 
        background: #0f172a; 
        color: #fff; 
        border: none; 
    }

    .investment-card .info-label { 
        color: #38bdf8; 
    }

    .guest-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-bottom: 40px; 
    }

    .guest-table th { 
        text-align: left; 
        padding: 20px; 
        color: #64748b; 
        font-size: 12px; 
        text-transform: uppercase; 
        border-bottom: 2px solid #f1f5f9; 
    }

    .guest-table td { 
        padding: 25px 20px; 
        border-bottom: 1px solid #f1f5f9; 
    }

    .financial-footer { 
        display: grid; 
        grid-template-columns: 1.5fr 1fr; 
        gap: 40px; 
        margin-top: 40px; 
        padding-top: 40px; 
        border-top: 2px solid #f1f5f9; 
    }

    .btn-pay { 
        background: #3b82f6; 
        color: #fff; 
        width: 100%; 
        padding: 25px; 
        border-radius: 16px; 
        font-size: 22px; 
        font-weight: 900; 
        border: none; 
        cursor: pointer; 
        text-decoration: none; 
        display: block; 
        text-align: center; 
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2); 
    }

    .btn-approve { 
        background: #16a34a; 
        color: #fff; 
        width: 100%; 
        padding: 25px; 
        border-radius: 16px; 
        font-size: 22px; 
        font-weight: 900; 
        border: none; 
        cursor: pointer; 
        text-decoration: none; 
        display: block; 
        text-align: center; 
    }

    .guest-footer-bar { 
        background: #38bdf8; 
        padding: 25px; 
        text-align: center; 
        color: #fff; 
        font-weight: 800; 
        font-size: 16px; 
        text-transform: uppercase; 
    }
    @endguest
</style>
@endpush

@section('content')
@auth
<div class="mb-shell">
    @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
    <div style="display:flex; justify-content:space-between; margin-bottom:30px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:20px;">
        <div style="display:flex; gap:20px; align-items:center;">
            <a href="{{ route('quotes.index') }}" class="btn-header" style="background:rgba(255,255,255,0.08);">← Back</a>
            <a href="{{ route('quotes.edit', $quote->id) }}" class="btn-header" style="background:rgba(255,255,255,0.15);"><i class="fa-solid fa-pen-to-square"></i> Edit Quote</a>
        </div>
        <div style="display:flex; gap:15px;">
            <form action="{{ route('quotes.send', $quote->id) }}" method="POST">@csrf<button type="submit" class="btn-header" style="background:#f59e0b; color:#000;">Resend</button></form>
            <a href="{{ route('quotes.download', $quote->id) }}" class="btn-header" style="background:#6366f1;">Download PDF</a>
            <button onclick="window.print()" class="btn-header" style="background:#475569;">Print</button>
            <button onclick="navigator.clipboard.writeText('{{ $publicUrl }}'); alert('Link Copied!')" class="btn-header" style="background:#0ea5e9;">Copy Link</button>
            <button class="btn-header" style="background:#10b981;">SMS</button>
        </div>
    </div>
    <div class="mb-layout">
        <div>
            <div class="mb-card"><div class="p-label">Billed To</div><div style="font-size:28px; font-weight:900;">{{ $customer->name }}</div><div style="color:#94a3b8;">{{ $customer->email }}</div></div>
            <div style="display:flex; gap:15px; margin-bottom:20px;">
                <div class="mini-stat-card"><div class="p-label">Quote Date</div><div style="font-weight:800;">{{ $fmtDate($quote->quote_date) }}</div></div>
                <div class="mini-stat-card"><div class="p-label">Deposit Due</div><div style="font-weight:800; color:#facc15;">{{ $fmtDate($quote->deposit_due_date) }}</div></div>
                <div class="mini-stat-card"><div class="p-label">Remaining</div><div style="font-weight:800; color:#38bdf8;">{{ $fmtDate($quote->balance_due_date) }}</div></div>
            </div>
            <div class="mb-card">
                <div style="font-weight:900; margin-bottom: 20px; font-size:18px; display:flex; align-items:center; gap:10px;"><i class="fa-solid fa-list-ul"></i> Services & Items</div>
                @foreach($items as $item)
                <div class="mb-row">
                    <div><strong>{{ $item['service'] }}</strong>@if(!empty($item['description']))<div style="font-size:12px; color:#94a3b8; margin-top:4px;">{{ $item['description'] }}</div>@endif</div>
                    <div style="text-align:center;">{{ $item['qty'] }}</div>
                    <div style="text-align:right;">${{ number_format($item['price'],2) }}</div>
                    <div style="text-align:right; color:#38bdf8; font-weight: 900;">${{ number_format($item['qty'] * $item['price'],2) }}</div>
                </div>
                @endforeach
            </div>
            <div class="mb-card">
                <div style="font-weight:900; margin-bottom:10px; font-size:18px;">Client Messaging</div>
                <form action="{{ route('quotes.send_sms', $quote->id) }}" method="POST">@csrf
                    <input type="text" name="phone" value="{{ $customer->phone ?? '' }}" style="margin-bottom:15px;">
                    <textarea name="message" rows="3">Hi {{ $customer->name }}, your quote is ready: {{ $publicUrl }}</textarea>
                    <button type="submit" class="btn-action" style="background:#10b981; margin-top:15px; font-size:14px; padding:12px;">Send SMS</button>
                </form>
            </div>
            <div class="mb-card">
                <div style="font-weight:900; margin-bottom:10px; font-size:18px;">Send Test Email</div>
                <form action="{{ route('quotes.send', $quote->id) }}" method="POST">@csrf
                    <input type="email" name="test_email" placeholder="you@example.com" class="input" style="margin-bottom:15px;">
                    <button type="submit" class="btn-action" style="background:#6366f1; font-size:14px; padding:12px;">Send Test</button>
                </form>
            </div>
        </div>
        <div>
            <div class="mb-card" style="background:rgba(56,189,248,0.1); border:1px solid #38bdf8; text-align:right;"><div class="p-label">Project Total</div><div style="font-size:48px; font-weight:900; color:#38bdf8;">${{ number_format($total,2) }}</div></div>
            <div class="mb-card">
                <div style="display:flex; justify-content:space-between; margin-bottom:10px; color:#94a3b8;"><span>Subtotal</span><span>${{ number_format($subtotal,2) }}</span></div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px; color:#94a3b8;"><span>Tax ({{ $taxPercent }}%)</span><span>${{ number_format($taxAmount,2) }}</span></div>
                <div style="display:flex; justify-content:space-between; border-top:1px solid rgba(255,255,255,0.1); padding-top:12px; font-weight:900; font-size:22px;"><span>Total</span><span style="color:#38bdf8;">${{ number_format($total,2) }}</span></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
                <div class="mb-card" style="margin:0; text-align:center;"><div class="p-label">Deposit Required</div><div style="font-size:22px; font-weight:900; color:#facc15;">${{ number_format($deposit,2) }}</div></div>
                <div class="mb-card" style="margin:0; text-align:center;"><div class="p-label">Remaining Balance</div><div style="font-size:22px; font-weight:900; color:#10b981;">${{ number_format($remaining,2) }}</div></div>
            </div>
            <div class="mb-card">
                <div class="p-label">Payment Flow</div>
                <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px;">
                    <label style="display:flex; align-items:center; gap:12px; cursor:pointer;"><input type="radio" checked> Customer will pay themselves</label>
                    <label style="display:flex; align-items:center; gap:12px; cursor:pointer;"><input type="radio"> I am charging customer manually</label>
                </div>
                <form action="{{ route('quotes.markPaid', $quote->id) }}" method="POST" id="paidForm">@csrf</form>
                <form action="{{ route('quotes.markDeposit', $quote->id) }}" method="POST" id="depositForm">@csrf</form>
                <div style="display:flex; gap:12px;"><button onclick="document.getElementById('paidForm').submit()" style="flex:1; background:transparent; border:1px solid #3b82f6; color:#fff; padding:12px; border-radius:12px; font-weight:800;">Pay Now</button><button onclick="document.getElementById('depositForm').submit()" style="flex:1; background:transparent; border:1px solid #f59e0b; color:#fff; padding:12px; border-radius:12px; font-weight:800;">Deposit</button></div>
            </div>
            <form method="POST" action="{{ route('quotes.send', $quote->id) }}">@csrf<button type="submit" class="btn-action" style="background:#38bdf8; color:#000;">Send Quote</button></form>
        </div>
    </div>
</div>
@endauth

@guest
<div class="guest-shell">
    @if(session('success')) <div style="background:#f0fdf4; color:#16a34a; padding:20px; border-radius:12px; margin:20px 60px; font-weight:800; text-align:center;">{{ session('success') }}</div> @endif
    <div class="guest-header">
        <div><div style="font-size: 32px; font-weight: 900;">{{ $company->name }}</div><div style="color: #94a3b8; font-size: 14px; margin-top: 5px;">{{ $company->email }}</div></div>
        <div style="text-align: right;"><div style="font-size: 12px; color: #38bdf8; font-weight: 900;">Project Proposal</div><div style="font-size: 42px; font-weight: 900;">#{{ $quote->quote_number }}</div></div>
    </div>
    <div class="guest-content">
        <div class="card-grid">
            <div class="info-card"><span class="info-label">Prepared For</span><div class="info-value">{{ $customer->name }}</div></div>
            <div class="info-card"><span class="info-label">Quote Date</span><div class="info-value">{{ $fmtDate($quote->quote_date) }}</div></div>
            <div class="info-card"><span class="info-label">Valid Until</span><div class="info-value">{{ $fmtDate($quote->expiry_date) }}</div></div>
            <div class="info-card investment-card"><span class="info-label">Total Investment</span><div class="info-value">${{ number_format($total, 2) }}</div></div>
        </div>
        <table class="guest-table">
            <thead><tr><th>Description</th><th width="80" style="text-align: center;">Qty</th><th width="150" style="text-align: right;">Line Total</th></tr></thead>
            <tbody>
                @foreach($items as $item)
                <tr><td><strong>{{ $item['service'] }}</strong>@if(!empty($item['description']))<div style="color: #64748b; font-size: 14px;">{{ $item['description'] }}</div>@endif</td><td style="text-align:center;">{{ $item['qty'] }}</td><td style="text-align:right; font-weight:800; font-size:18px;">${{ number_format($item['qty'] * $item['price'], 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
        <div class="financial-footer">
            <div style="background: #f8fafc; padding: 30px; border-radius: 20px;">
                <div class="info-label">Payment Schedule</div>
                <div style="display:flex; justify-content:space-between; padding:10px 0;"><span>Initial Deposit</span><span style="font-weight:800; color:#16a34a;">${{ number_format($deposit, 2) }}</span></div>
                <div style="display:flex; justify-content:space-between; padding:10px 0;"><span>Remaining Balance</span><span style="font-weight:800;">${{ number_format($remaining, 2) }}</span></div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:16px; margin-bottom:5px;">Subtotal: ${{ number_format($subtotal, 2) }}</div>
                <div style="font-size:16px; margin-bottom:20px;">Tax ({{ $taxPercent }}%): ${{ number_format($taxAmount, 2) }}</div>
                <div style="font-size:32px; font-weight:900; color:#0f172a;">Total: ${{ number_format($total, 2) }}</div>
            </div>
        </div>
        <div style="margin-top: 40px;">
            {{-- ✅ WAKE UP WIN: DETECTION OF NEW INVOICE --}}
            @if($invoice)
                <a href="{{ route('invoices.pay', $invoice->id) }}" class="btn-pay">💳 PAY WITH CARD (STRIPE)</a>
            @elseif($quote->status !== 'approved' && $quote->status !== 'signed')
                <form action="{{ route('quotes.approve', $quote->public_token) }}" method="POST">@csrf<button type="submit" class="btn-approve">✓ APPROVE PROPOSAL</button></form>
            @else
                <div style="background:#f0fdf4; color:#16a34a; padding:25px; border-radius:16px; text-align:center; font-weight:900; font-size:24px; border:2px solid #16a34a;">✓ SIGNED & APPROVED</div>
            @endif
        </div>
    </div>
    <div class="guest-footer-bar">Questions? Contact {{ $company->name }} Support</div>
</div>
@endguest
@endsection
