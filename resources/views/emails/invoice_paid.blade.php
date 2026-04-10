<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #1e293b; line-height: 1.5; background:#f8fafc; padding:20px;">
    <div style="max-width: 600px; margin: auto; border: 1px solid #e2e8f0; padding: 30px; border-radius: 12px; background:white;">

        <h2 style="color: #10b981; margin-top: 0;">Payment Received ✅</h2>

        <p>Hello <strong>{{ $invoice->customer_name }}</strong>,</p>

        <p>We’ve successfully received your payment. Thank you for your business!</p>

        <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p><strong>Receipt #:</strong> {{ $invoice->invoice_no }}</p>

            <p><strong>Amount Paid:</strong> 
                ${{ number_format($invoice->amount_paid ?? 0, 2) }}
            </p>

            <p><strong>Status:</strong> 
                @if($invoice->status === 'paid')
                    <span style="color:#059669;font-weight:bold;">PAID IN FULL</span>
                @elseif($invoice->status === 'partial')
                    <span style="color:#d97706;font-weight:bold;">PARTIAL PAYMENT RECEIVED</span>
                @else
                    <span style="color:#dc2626;font-weight:bold;">PENDING</span>
                @endif
            </p>

            @if($invoice->status === 'partial')
            <p>
                <strong>Remaining Balance:</strong> 
                ${{ number_format($invoice->remaining_balance, 2) }}
            </p>
            @endif
        </div>

        <p>This email serves as your official receipt.</p>

        <br>

        <p>
            <strong>{{ $invoice->company->name ?? 'Medios Billing' }}</strong><br>
            {{ $invoice->company->email ?? '' }}
        </p>

        <br>

        <p style="font-size: 12px; color: #94a3b8;">
            If you have any questions, feel free to contact us.
        </p>

    </div>
</body>
</html>
