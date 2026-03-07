<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ (isset($is_receipt) && $is_receipt) ? 'Payment Receipt' : 'Invoice' }}</title>
</head>

<body style="font-family:Arial, Helvetica, sans-serif; background:#f4f6f9; padding:30px; margin:0;">

@php
    // SaaS-ready branding with safe fallbacks
    $companyName = $company_name ?? 'McIntosh Cleaning Services';
    $companyLogo = $company_logo ?? 'https://portal.mcintoshcleaningservice.com/images/mcintosh-logo.png';
@endphp

<div style="max-width:600px; margin:auto; background:white; padding:30px; border-radius:8px; border:1px solid #e5e7eb;">

    <!-- COMPANY LOGO (SaaS READY) -->
    <div style="text-align:center; margin-bottom:25px;">
        <img
            src="{{ $companyLogo }}"
            alt="{{ $companyName }} Logo"
            style="max-width:220px; height:auto; display:inline-block;">
    </div>

    <!-- HEADER -->
    <h2 style="margin-top:0; color:#111827;">

        @if(isset($is_receipt) && $is_receipt)

            Receipt #{{ $invoice->invoice_no }}

            <span style="
                background:#16a34a;
                color:white;
                font-size:12px;
                padding:4px 10px;
                border-radius:20px;
                margin-left:10px;
                font-weight:bold;
                display:inline-block;
                vertical-align:middle;
            ">
                PAID
            </span>

        @else

            Invoice #{{ $invoice->invoice_no }}

        @endif

    </h2>

    <p style="font-size:15px; color:#374151; margin:0 0 10px 0;">
        Hello {{ $invoice->customer_name }},
    </p>

    <!-- INVOICE SUMMARY BOX -->
    <div style="
        margin:25px 0;
        border:1px solid #e5e7eb;
        border-radius:6px;
        padding:20px;
        background:#f9fafb;
    ">

        <p style="margin:0 0 10px 0; font-weight:bold; color:#111827;">
            Invoice Summary
        </p>

        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#374151;">

            <tr>
                <td style="padding:6px 0;">Invoice Number</td>
                <td style="text-align:right; padding:6px 0;">
                    <strong>{{ $invoice->invoice_no }}</strong>
                </td>
            </tr>

            <tr>
                <td style="padding:6px 0;">Amount</td>
                <td style="text-align:right; padding:6px 0;">
                    <strong>${{ number_format($invoice->total,2) }}</strong>
                </td>
            </tr>

            <tr>
                <td style="padding:6px 0;">Status</td>
                <td style="text-align:right; padding:6px 0;">
                    @if(isset($is_receipt) && $is_receipt)
                        <span style="color:#16a34a; font-weight:bold;">PAID</span>
                    @else
                        <span style="color:#d97706; font-weight:bold;">PENDING</span>
                    @endif
                </td>
            </tr>

            @if(isset($is_receipt) && $is_receipt && !empty($invoice->paid_at))
                <tr>
                    <td style="padding:6px 0;">Payment Date</td>
                    <td style="text-align:right; padding:6px 0;">
                        {{ \Carbon\Carbon::parse($invoice->paid_at)->format('F j, Y g:i A') }}
                    </td>
                </tr>
            @endif

        </table>

    </div>

    <!-- MESSAGE -->
    @if(isset($is_receipt) && $is_receipt)

        <p style="font-size:15px; color:#374151;">
            We have successfully received your payment of
            <strong>${{ number_format($invoice->total,2) }}</strong>.
        </p>

        @if(!empty($invoice->paid_at))
            <p style="font-size:14px; color:#6b7280; margin-top:-5px;">
                Payment received on
                <strong>{{ \Carbon\Carbon::parse($invoice->paid_at)->format('F j, Y \a\t g:i A') }}</strong>
            </p>
        @endif

        <p style="font-size:15px; color:#374151;">
            Your payment has been processed successfully and your receipt is attached for your records.
        </p>

    @else

        <p style="font-size:15px; color:#374151;">
            Your invoice for
            <strong>${{ number_format($invoice->total,2) }}</strong>
            has been generated.
        </p>

        <p style="font-size:15px; color:#374151;">
            Please review the attached invoice for service details and payment instructions.
        </p>

    @endif

    <!-- PAYMENT BUTTON -->
    @if((!isset($is_receipt) || !$is_receipt) && (($invoice->status ?? null) !== 'paid'))

        <div style="margin:35px 0; text-align:center;">

            <a href="{{ route('invoice.payment.page', ['invoice' => $invoice->id]) }}"
               style="
               display:inline-block;
               background:#2563eb;
               color:white;
               padding:14px 26px;
               text-decoration:none;
               border-radius:6px;
               font-weight:bold;
               font-size:15px;
               ">
                Pay Securely with Credit Card
            </a>

        </div>

        <p style="font-size:14px; color:#374151;">
            You may also pay using <strong>Zelle, Cash, or Check</strong>.
        </p>

    @endif

    <!-- DIVIDER -->
    <hr style="border:none; border-top:1px solid #e5e7eb; margin:30px 0;">

    <!-- FOOTER -->
    <p style="font-size:15px; color:#374151; margin:0 0 8px 0;">
        Thank you for choosing <strong>{{ $companyName }}</strong>.
    </p>

    <p style="font-size:14px; color:#6b7280; margin:0;">
        If you have any questions regarding this invoice or receipt, please reply to this email and our team will be happy to assist you.
    </p>

    <p style="font-size:12px; color:#9ca3af; margin-top:25px;">
        {{ $companyName }}<br>
        Professional Residential &amp; Commercial Cleaning
    </p>

    <p style="font-size:11px; color:#9ca3af; margin-top:15px; text-align:center;">
        Powered by
        <a href="https://www.medioscorporativos.com" style="color:#6b7280; text-decoration:none;">
            MediosCorp Billing System
        </a>
    </p>

</div>

</body>
</html>
