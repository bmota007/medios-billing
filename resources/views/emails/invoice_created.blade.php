<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice Ready</title>
</head>

<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.08);">

<!-- HEADER -->
<tr>
<td style="background:#3b82f6;padding:20px;text-align:center;">
<h2 style="color:#ffffff;margin:0;">Medios Billing</h2>
</td>
</tr>

<!-- BODY -->
<tr>
<td style="padding:30px;">

<h1 style="margin-top:0;color:#111;font-size:24px;">Invoice Ready</h1>

<p style="color:#555;font-size:15px;">
Hi {{ $invoice->customer_name }},
</p>

<p style="color:#555;font-size:15px;">
Your invoice <strong>#{{ $invoice->invoice_no }}</strong> is ready.
</p>

<!-- CARD -->
<div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:20px;margin:20px 0;">

<p style="margin:8px 0;font-size:15px;">
<strong>Total:</strong> ${{ number_format($invoice->total, 2) }}
</p>

<p style="margin:8px 0;font-size:15px;">
<strong>Deposit:</strong> ${{ number_format($invoice->deposit_amount, 2) }}
</p>

<p style="margin:8px 0;font-size:15px;">
<strong>Remaining:</strong> ${{ number_format($invoice->remaining_balance, 2) }}
</p>

</div>

<!-- BUTTON -->
<div style="text-align:center;margin-top:30px;">
<a href="{{ route('invoice.public_view', $invoice->invoice_no) }}"
style="background:#3b82f6;color:#fff;text-decoration:none;padding:14px 28px;border-radius:8px;font-weight:bold;display:inline-block;">
View & Pay Invoice
</a>
</div>

</td>
</tr>

<!-- FOOTER -->
<tr>
<td style="padding:20px;text-align:center;font-size:12px;color:#888;">
Powered by Medios Billing
</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
