@extends('layouts.blank')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

@php
    $items = json_decode($quote->items ?? '[]', true);
    $subtotal = abs($quote->subtotal ?? $quote->total ?? 0);
    $taxPercent = $quote->tax_percent ?? 0;
    $taxAmount = $subtotal * ($taxPercent / 100);
    $total = $subtotal + $taxAmount;
    $fmtDate = fn($d) => $d ? (is_string($d) ? date('M d, Y', strtotime($d)) : $d->format('M d, Y')) : 'N/A';
@endphp

<style>
    body { background: #f1f5f9; color: #1e293b; font-family: 'Inter', sans-serif; padding: 40px 20px; }
    .contract-shell { background: #fff; max-width: 1100px; margin: auto; border-radius: 28px; box-shadow: 0 40px 100px rgba(0,0,0,0.15); overflow: hidden; border: 1px solid #e2e8f0; }
    
    /* Million Dollar Header */
    .contract-header { background: #0f172a; padding: 50px 60px; color: #fff; display: flex; justify-content: space-between; align-items: flex-end; position: relative; }
    .contract-header::after { content: ""; position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: #38bdf8; }
    
    .contract-content { padding: 60px; }
    
    /* Professional Info Cards */
    .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
    .info-card { background: #f8fafc; padding: 20px; border-radius: 16px; border: 1px solid #edf2f7; }
    .info-label { font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: block; }
    .info-val { font-size: 15px; font-weight: 700; color: #0f172a; }

    /* Service Table */
    .service-table { width: 100%; border-collapse: collapse; margin-bottom: 50px; }
    .service-table th { text-align: left; padding: 15px 20px; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #f1f5f9; }
    .service-table td { padding: 20px; border-bottom: 1px solid #f1f5f9; }

    /* Legal Terms Area */
    .legal-terms { background: #fff; padding: 40px; border: 1px solid #e2e8f0; border-radius: 16px; margin-bottom: 50px; line-height: 1.8; color: #334155; }
    .pdf-embed { width: 100%; height: 600px; border: none; border-radius: 8px; }

    /* Signature Boxes (No Tabs) */
    .signature-section { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 40px; background: #f8fafc; padding: 40px; border-radius: 24px; border: 2px solid #edf2f7; }
    .sig-box { display: flex; flex-direction: column; }
    canvas { background: #fff; border: 1px solid #cbd5e1; border-radius: 12px; width: 100%; height: 180px; cursor: crosshair; }
    .typed-sig { width: 100%; padding: 15px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 32px; font-family: 'Dancing Script', cursive; background: #fff; }

    .btn-confirm { background: #16a34a; color: #fff; width: 100%; padding: 25px; border-radius: 16px; font-size: 22px; font-weight: 900; border: none; cursor: pointer; margin-top: 40px; box-shadow: 0 10px 25px rgba(22, 163, 74, 0.25); }
    .btn-confirm:hover { background: #15803d; transform: translateY(-2px); }
</style>

<div class="contract-shell">
    <div class="contract-header">
        <div>
            <div style="font-size: 32px; font-weight: 900; letter-spacing: -1px;">{{ $company->name }}</div>
            <div style="color: #94a3b8; font-size: 14px; margin-top: 5px;">Service Agreement</div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 12px; text-transform: uppercase; color: #38bdf8; font-weight: 900; letter-spacing: 2px;">Contract ID</div>
            <div style="font-size: 42px; font-weight: 900; line-height: 1;">#{{ $quote->quote_number }}</div>
        </div>
    </div>

    <div class="contract-content">
        @if(session('success')) <div style="background:#f0fdf4; border:1px solid #16a34a; color:#16a34a; padding:20px; border-radius:12px; margin-bottom:30px; font-weight:700; text-align:center;">{{ session('success') }}</div> @endif

{{-- From/To Cards --}}
<div class="info-grid">

    <div class="info-card">
        <span class="info-label">
            From Provider
        </span>

        <div class="info-val">
            {{ $company->name }}
        </div>
    </div>

    <div class="info-card">
        <span class="info-label">
            Prepared For
        </span>

        <div class="info-val">
            {{ $customer->name }}
        </div>
    </div>

    <div class="info-card">
        <span class="info-label">
            Agreement Date
        </span>

        <div class="info-val">
            {{ $fmtDate($quote->quote_date) }}
        </div>
    </div>

    {{-- ✅ DARK TOTAL CARD FIX --}}
    <div class="info-card" style="background:#0f172a;">

        <span
            class="info-label"
            style="color:#38bdf8;"
        >
            Contract Value
        </span>

        <div
            class="info-val"
            style="
                color:#ffffff !important;
                font-size:18px;
                font-weight:800;
            "
        >
            ${{ number_format($total, 2) }}
        </div>

    </div>

</div>

{{-- Service Breakdown --}}
        <h3 style="margin-bottom: 20px; font-weight: 900; color: #0f172a;">I. Project Scope & Pricing</h3>
        <table class="service-table">
            <thead>
                <tr><th>Description</th><th width="80">Qty</th><th width="150" style="text-align:right;">Total</th></tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
<td style="color:#0f172a;">
    <strong>{{ $item['service'] }}</strong>

    @if(!empty($item['description']))
        <div style="font-size:13px; color:#64748b; margin-top:5px;">
            {{ $item['description'] }}
        </div>
    @endif
</td>

<td style="text-align:center; color:#0f172a; font-weight:700;">
    {{ $item['qty'] }}
</td>

<td style="text-align:right; font-weight:800; color:#0f172a;">
    ${{ number_format(($item['qty'] ?? 0) * ($item['price'] ?? 0), 2) }}
</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Legal Agreement Body --}}
        <h3 style="margin-bottom: 20px; font-weight: 900; color: #0f172a;">II. Terms & Conditions</h3>
        <div class="legal-terms">
            @if($contractFileUrl)
                <iframe src="{{ $contractFileUrl }}#toolbar=0" class="pdf-embed"></iframe>
            @else
                <div style="padding: 50px; text-align: center; color: #94a3b8;">Legal template not found.</div>
            @endif
        </div>

        {{-- Signature Boxes --}}
        <form action="{{ route('quotes.sign', $quote->public_token) }}" method="POST" id="sigForm">
            @csrf
            <input type="hidden" name="signature_image" id="signature_image">
            
            <h3 style="margin-bottom: 20px; font-weight: 900; color: #0f172a;">III. Formal Acceptance</h3>
            <div class="signature-section">
                <div class="sig-box">
                    <span class="info-label">Type Your Full Name</span>
                    <input type="text" name="signature_name" class="typed-sig" placeholder="Full Name" required>
                </div>
                <div class="sig-box">
                    <span class="info-label">Draw Your Signature</span>
                    <canvas id="signature-pad"></canvas>
                    <button type="button" onclick="pad.clear()" style="background:none; border:none; color:#ef4444; font-size:11px; font-weight:800; text-align:right; cursor:pointer; margin-top:5px;">Clear Canvas</button>
                </div>
            </div>

            <p style="font-size: 12px; color: #94a3b8; margin-top: 30px; text-align: center; max-width: 600px; margin-left: auto; margin-right: auto;">
                By confirming below, you legally authorize this agreement and project scope. An official copy will be sent to both parties.
            </p>

            <button type="submit" class="btn-confirm">✓ CONFIRM & LEGALLY SIGN AGREEMENT</button>
        </form>
    </div>

    <div style="background: #38bdf8; padding: 25px; text-align: center; color: #fff; font-weight: 800; font-size: 16px; text-transform: uppercase;">
        Questions? Contact {{ $company->name }} Support
    </div>
</div>

<script>
    const canvas = document.getElementById('signature-pad');
    const pad = new SignaturePad(canvas);
    
    document.getElementById('sigForm').onsubmit = function() {
        if (!pad.isEmpty()) {
            document.getElementById('signature_image').value = pad.toDataURL();
        }
    };

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        pad.clear();
    }
    window.onresize = resizeCanvas;
    resizeCanvas();
</script>
@endsection
