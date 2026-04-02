{{-- Responsive CSS for Mobile/iPad --}}
<style>
    .contract-container {
        width: 100%;
        max-width: 850px;
        margin: auto;
        padding: 40px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    /* iPad and Tablet Adjustments */
    @media (max-width: 1024px) {
        .contract-container {
            padding: 30px;
            max-width: 95%;
        }
    }

    /* iPhone and Mobile Adjustments */
    @media (max-width: 640px) {
        .contract-container {
            padding: 20px;
            border: none;
            box-shadow: none;
        }
        .payment-table td {
            display: block;
            width: 100% !important;
            border-left: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            margin-bottom: 10px;
            border-right: 1px solid #e2e8f0 !important;
        }
        h1 { font-size: 22px !important; }
        .signature-line { width: 100% !important; }
        .investment-box { padding: 15px !important; }
    }
</style>

<div class="contract-container" style="font-family: 'Inter', -apple-system, sans-serif; color: #1e293b; line-height: 1.8;">
    
    {{-- Header --}}
    <div style="text-align: center; margin-bottom: 40px; border-bottom: 2px solid {{ $quote->company->primary_color ?? '#0ea5e9' }}; padding-bottom: 30px;">
        <h1 style="margin: 0; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; font-weight: 800;">{{ $quote->company->name }}</h1>
        <p style="margin: 8px 0 0; font-size: 14px; color: #64748b;">
            {{ $quote->company->address }}<br>
            PH: {{ $quote->company->phone }} | {{ $quote->company->email }}
        </p>
    </div>

    <h2 style="text-align: center; color: #334155; text-transform: uppercase; font-size: 16px; letter-spacing: 3px; margin-bottom: 40px; font-weight: 600;">General Services Agreement</h2>

    <p style="margin-bottom: 25px; font-size: 15px;">This Agreement is entered into between <strong>{{ $quote->company->name }}</strong> (“Contractor”) and <strong>{{ $quote->customer->name }}</strong> (“Customer”). This document is legally tied to <strong>Quote #{{ $quote->quote_number }}</strong>.</p>

    {{-- SECTION 1: FINANCIALS --}}
    <h3 style="color: {{ $quote->company->primary_color ?? '#0ea5e9' }}; font-size: 14px; letter-spacing: 1px; text-transform: uppercase; margin-top: 40px; margin-bottom: 15px;">01. Investment & Payment Schedule</h3>
    
    <div class="investment-box" style="padding: 25px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; text-align: center;">
        <span style="font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px;">Total Project Investment</span>
        <strong style="font-size: 32px; color: #0f172a;">${{ number_format($quote->total, 2) }}</strong>
    </div>

    <table class="payment-table" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
        <tr>
            <td style="padding: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px 0 0 12px; width: 50%;">
                <strong style="display: block; font-size: 13px; color: #64748b; text-transform: uppercase;">Draw #1 (Deposit)</strong>
                <span style="font-size: 22px; font-weight: 700; color: {{ $quote->company->primary_color ?? '#0ea5e9' }};">${{ number_format($quote->deposit_amount, 2) }}</span>
                <p style="margin: 5px 0 0; font-size: 12px; color: #94a3b8; line-height: 1.4;">Due upon signing to secure materials and scheduling.</p>
            </td>
            <td style="padding: 20px; background: #fff; border: 1px solid #e2e8f0; border-left: none; border-radius: 0 12px 12px 0; width: 50%;">
                <strong style="display: block; font-size: 13px; color: #64748b; text-transform: uppercase;">Draw #2 (Final)</strong>
                <span style="font-size: 22px; font-weight: 700; color: #475569;">${{ number_format($quote->remaining_amount, 2) }}</span>
                <p style="margin: 5px 0 0; font-size: 12px; color: #94a3b8; line-height: 1.4;">Due within 10 days of project completion.</p>
            </td>
        </tr>
    </table>

    {{-- SECTION 2: TERMS --}}
    <h3 style="color: {{ $quote->company->primary_color ?? '#0ea5e9' }}; font-size: 14px; letter-spacing: 1px; text-transform: uppercase; margin-top: 40px; margin-bottom: 15px;">02. General Terms & Conditions</h3>
    <div style="font-size: 14px; color: #475569; line-height: 1.8;">
        <p><strong>Site Access & Utilities:</strong> Customer shall provide reasonable access to the project site and furnish all water and electricity needed for the project at no cost to Contractor. Customer is responsible for identifying all property lines, utilities, or sensitive areas.</p>
        
        <p><strong>Cancellation Policy:</strong> Customer acknowledges that Contractor incurs administrative and scheduling costs upon execution of this agreement. In the event of cancellation by the Customer after signing, a cancellation fee equal to 10% of the total project investment shall be due and payable to the Contractor immediately.</p>
        
        <p><strong>Surface Care & Curing:</strong> Fresh paint requires time to properly cure. Contractor is not responsible for damage to uncured paint after leaving the job site, including damage caused by Customer activity, pets, furniture, or third parties.</p>
        
        <p><strong>Hidden Property Conditions:</strong> Contractor is not liable for pre-existing hidden issues such as water damage, rotten wood, structural problems, or mold discovered beneath surfaces. Repairs for such conditions require a written Change Order.</p>
        
        <p><strong>Change Orders & Scheduling:</strong> Any modifications to scope, cost, or schedule must be approved in writing by both parties. Project dates are estimates and not guaranteed; Contractor is not liable for delays caused by weather, material shortages, or unforeseen structural issues.</p>
        
        <p><strong>Cleanup & Validity:</strong> Contractor will remove job-related debris upon completion. This offer remains valid for 30 calendar days from the date of the quote.</p>
    </div>

    {{-- SECTION 3: SIGNATURE --}}
    <div style="margin-top: 60px; padding: 40px; border: 1px solid #e2e8f0; border-radius: 16px; text-align: center; background: #fcfcfc;">
        <p style="margin-bottom: 15px; font-weight: 600; color: #334155; text-transform: uppercase; font-size: 12px; letter-spacing: 2px;">Customer Acceptance</p>
        <div style="height: 90px; display: flex; align-items: center; justify-content: center;">
             <span style="font-family: 'Brush Script MT', cursive; font-size: 42px; color: {{ $quote->company->primary_color ?? '#0ea5e9' }};">
                {{ $quote->signed_by ?? 'Pending Signature' }}
             </span>
        </div>
        <div class="signature-line" style="border-top: 1px solid #e2e8f0; display: inline-block; width: 80%; padding-top: 15px;">
            <span style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px;">Authorized Digital Signature</span>
        </div>
        <p style="font-size: 11px; margin-top: 15px; color: #cbd5e1;">
            {{ $quote->contract_signed_at ? 'Executed on: ' . $quote->contract_signed_at->format('M d, Y H:i') : 'Awaiting Execution' }}
        </p>
    </div>
</div>
