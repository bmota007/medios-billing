<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; color: #1e293b; line-height: 1.5;">
    <div style="max-width: 600px; margin: auto; border: 1px solid #e2e8f0; padding: 30px; border-radius: 12px;">
        <h2 style="color: #10b981; margin-top: 0;">Good news! 💰</h2>
        <p>Your customer, <strong>{{ $invoice->customer_name }}</strong>, just paid their invoice online.</p>
        
        <div style="background: #f1f5f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;"><strong>Invoice:</strong> #{{ $invoice->invoice_no }}</p>
            <p style="margin: 0 0 10px 0;"><strong>Amount:</strong> ${{ number_format($invoice->total, 2) }}</p>
            <p style="margin: 0;"><strong>Status:</strong> <span style="color: #059669; font-weight: bold;">PAID IN FULL</span></p>
        </div>

        <p>The system has automatically updated the record in your dashboard and sent a PDF receipt to the customer's email ({{ $invoice->customer_email }}).</p>
        
        <br>
        <p style="font-size: 12px; color: #94a3b8;">This is an automated notification from your Medios Billing Portal.</p>
    </div>
</body>
</html>
