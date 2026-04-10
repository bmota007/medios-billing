<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #1e293b; line-height: 1.5;">
    <div style="max-width: 600px; margin: auto; border: 1px solid #e2e8f0; padding: 30px; border-radius: 12px;">
        
        <h2 style="color: #10b981; margin-top: 0;">💰 Payment Received</h2>

        <p><strong>{{ $invoice->customer_name }}</strong> has made a payment.</p>

        <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
            
            <p><strong>Invoice:</strong> #{{ $invoice->invoice_no }}</p>

            {{-- ✅ CORRECT PAYMENT LOGIC --}}
            <p><strong>Amount Paid:</strong> ${{ number_format($invoice->amount_paid ?? 0, 2) }}</p>

            @if($invoice->status === 'partial')
                <p><strong>Status:</strong> PARTIAL PAYMENT RECEIVED</p>
                <p><strong>Remaining Balance:</strong> ${{ number_format($invoice->remaining_balance ?? 0, 2) }}</p>
            @else
                <p><strong>Status:</strong> PAID IN FULL</p>
            @endif

            <p><strong>Payment Method:</strong> {{ ucfirst($invoice->payment_method ?? 'N/A') }}</p>

        </div>

        <p>You can review this invoice in your dashboard for full details.</p>

        <br>

        <p style="font-size: 12px; color: #94a3b8;">
            Medios Billing Notification System
        </p>

    </div>
</body>
</html>
