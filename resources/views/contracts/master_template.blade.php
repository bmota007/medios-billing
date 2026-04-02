<div style="font-family: 'Inter', sans-serif; color: #1e293b; line-height: 1.5;">
    {{-- Dynamic Branding Header --}}
    <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid {{ $quote->company->primary_color ?? '#0ea5e9' }}; padding-bottom: 20px;">
        <h2 style="margin: 0; color: #0f172a; text-transform: uppercase;">{{ $quote->company->name }}</h2>
        <p style="margin: 5px 0; font-size: 14px;">{{ $quote->company->address }}</p>
        <p style="margin: 5px 0; font-size: 14px;">PH: {{ $quote->company->phone }} | {{ $quote->company->email }}</p>
    </div>

    <h3 style="text-align: center; background: #f1f5f9; padding: 10px; border-radius: 8px;">General Services Agreement</h3>

    <p>This Agreement is made between <strong>{{ $quote->company->name }}</strong> ("Contractor") and <strong>{{ $quote->customer->name }}</strong> ("Customer"). This document is tied specifically to <strong>Quote #{{ $quote->quote_number }}</strong>.</p>

    <h4 style="color: {{ $quote->company->primary_color ?? '#0ea5e9' }}; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">1. Project Scope & Cost</h4>
    <p>Contractor shall furnish labor and materials as set forth in <strong>Quote #{{ $quote->quote_number }}</strong>. The total project cost is <strong>${{ number_format($quote->total, 2) }}</strong>.</p>

    <h4 style="color: {{ $quote->company->primary_color ?? '#0ea5e9' }}; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">2. Payment Schedule</h4>
    <ul style="list-style: none; padding: 0;">
        <li style="margin-bottom: 10px; padding: 10px; background: #f8fafc; border-radius: 6px;">
            <strong>Draw #1 (35% Deposit):</strong> ${{ number_format($quote->total * 0.35, 2) }} due upon execution of this agreement.
        </li>
        <li style="padding: 10px; background: #f8fafc; border-radius: 6px;">
            <strong>Draw #2 (65% Balance):</strong> ${{ number_format($quote->total * 0.65, 2) }} due within 10 days of completion or receipt of invoice.
        </li>
    </ul>

    <h4 style="color: {{ $quote->company->primary_color ?? '#0ea5e9' }}; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px;">3. Key Terms</h4>
    <div style="font-size: 13px;">
        {!! nl2br(e($quote->company->contract_terms ?? "• Customer provides water/electricity.\n• Contractor is not responsible for fresh paint after leaving site.\n• Changes to scope must be in writing.\n• Offer valid for 30 days from date of quote.")) !!}
    </div>

    {{-- E-Signature Block --}}
    <div style="margin-top: 40px; padding: 20px; border: 2px dashed #cbd5e1; border-radius: 12px; text-align: center;">
        <p style="margin-bottom: 10px;"><strong>ACCEPTED BY CUSTOMER:</strong></p>
        <p style="font-size: 20px; font-family: 'Cursive', sans-serif; color: {{ $quote->company->primary_color ?? '#0ea5e9' }};">
            {{ $quote->signed_by ?? 'Pending Signature' }}
        </p>
        <p style="font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; display: inline-block; padding-top: 5px; width: 250px;">
            Customer Signature
        </p>
        <p style="font-size: 12px; margin-top: 5px;">Date: {{ $quote->contract_signed_at ? $quote->contract_signed_at->format('M d, Y') : date('M d, Y') }}</p>
    </div>
</div>
