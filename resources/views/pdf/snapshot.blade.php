<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        Receipt {{ $snapshot->invoice_no }}
    </title>

    <style>

@page{
    margin:22px;
}

body{
    font-family: DejaVu Sans, sans-serif;
    background:#ffffff;
    color:#0f172a;
    margin:0;
    padding:12px;
    font-size:12px;
}

.header{
    width:100%;
    margin-bottom:18px;
}

        .company{
            float:right;
            text-align:right;
        }

.company h1{
    margin:0;
    font-size:30px;
    font-weight:800;
    color:#0f172a;
    letter-spacing:-1px;
}

        .company p{
            margin:4px 0;
            color:#475569;
            font-size:13px;
        }

        .clear{
            clear:both;
        }

        .badge{
            display:inline-block;
            padding:10px 18px;
            border-radius:30px;
            background:#2563eb;
            color:white;
            font-weight:bold;
            font-size:12px;
            letter-spacing:1px;
            margin-bottom:25px;
        }

        .invoice-title{
            font-size:30px;
            font-weight:700;
            margin-bottom:6px;
        }

        .muted{
            color:#64748b;
            font-size:13px;
        }

.panel{
    border:1px solid #dbe3ee;
    border-radius:16px;
    padding:18px;
    margin-top:14px;
    background:#ffffff;
}

        .grid{
            width:100%;
        }

        .left{
            width:60%;
            float:left;
        }

        .right{
            width:35%;
            float:right;
            text-align:right;
        }

.total{
    font-size:48px;
    font-weight:900;
    color:#0284c7;
    margin-top:6px;
    letter-spacing:-2px;
}

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

th{
    background:#f1f5f9;
    text-align:left;
    padding:10px;
            border-bottom:1px solid #e2e8f0;
            font-size:13px;
        }

        td{
            padding:10px;
            border-bottom:1px solid #e2e8f0;
            font-size:13px;
        }

        .stats{
            margin-top:14px;
        }

.stat{
    width:22%;
    float:left;
    margin-right:2%;
    border:1px solid #dbe3ee;
    border-radius:14px;
    padding:14px;
    min-height:58px;
    background:#f8fafc;
}

        .stat:last-child{
            margin-right:0;
        }

        .label{
            color:#64748b;
            font-size:11px;
            text-transform:uppercase;
            margin-bottom:10px;
        }

        .value{
            font-size:18px;
            font-weight:700;
            color:#0f172a;
        }

        .footer{
            margin-top:24px;
            text-align:center;
            color:#94a3b8;
            font-size:11px;
        }

    </style>
</head>
<body>

@php

    $invoiceData = $data['invoice'] ?? [];

    $invoice = (object) $invoiceData;

    $companyData =
        $invoiceData['company'] ?? null;

    $company = $companyData
        ? (object) $companyData
        : null;

    $items = [];

    if (isset($data['items'])) {

        $items = $data['items'];

    } elseif (isset($invoice->items)) {

        $items = json_decode(
            $invoice->items,
            true
        );
    }

@endphp

<div class="header">

    <div class="company">

        <h1>
            {{ $company->name ?? 'Company' }}
        </h1>

        <p>
            {{ $company->email ?? '' }}
        </p>

        <p>
            {{ $company->phone ?? '' }}
        </p>

    </div>

    <div class="clear"></div>

</div>

<div style="
    height:10px;
    background:linear-gradient(
        90deg,
        #0ea5e9,
        #2563eb,
        #1e3a8a
    );
    border-radius:12px;
    margin-bottom:20px;
"></div>

<div class="badge">
    {{ strtoupper(str_replace('_',' ',$snapshot->snapshot_type)) }}
</div>

<div class="invoice-title">
    Invoice #{{ $snapshot->invoice_no }}
</div>

<div class="muted">
    Archived:
    {{ \Carbon\Carbon::parse($snapshot->snapshot_created_at)->format('M d, Y h:i A') }}
</div>

<div class="panel">

    <div class="grid">

        <div class="left">

            <strong>BILLED TO</strong><br><br>

            {{ $invoice->customer_name ?? '' }}<br>

            {{ $invoice->customer_email ?? '' }}<br>

            {{ $invoice->customer_phone ?? '' }}

        </div>

        <div class="right">

            <div class="label">
                TOTAL
            </div>

            <div class="total">
                ${{ number_format($invoice->total ?? 0,2) }}
            </div>

        </div>

        <div class="clear"></div>

    </div>

</div>

<div class="panel">

    <strong>
        Services & Items
    </strong>

    <table>

        <thead>
            <tr>
                <th>Description</th>
                <th width="80">Qty</th>
                <th width="120">Price</th>
                <th width="120">Total</th>
            </tr>
        </thead>

        <tbody>

        @foreach($items as $item)

            <tr>

                <td>
                    {{ $item['desc'] ?? '' }}
                </td>

                <td>
                    {{ $item['qty'] ?? 1 }}
                </td>

                <td>
                    ${{ number_format($item['price'] ?? 0,2) }}
                </td>

                <td>
                    ${{ number_format($item['line_total'] ?? 0,2) }}
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

<div class="stats">

    <div class="stat">

        <div class="label">
            Subtotal
        </div>

        <div class="value">
            ${{ number_format($invoice->subtotal ?? 0,2) }}
        </div>

    </div>

    <div class="stat">

        <div class="label">
            Deposit
        </div>

        <div class="value">
            ${{ number_format($invoice->deposit_amount ?? 0,2) }}
        </div>

    </div>

    <div class="stat">

        <div class="label">
            Remaining
        </div>

        <div class="value">
            ${{ number_format($invoice->remaining_balance ?? 0,2) }}
        </div>

    </div>

    <div class="stat">

        <div class="label">
            Status
        </div>

        <div class="value">
            {{ strtoupper($invoice->status ?? 'UNPAID') }}
        </div>

    </div>

    <div class="clear"></div>

</div>

@if(!empty($invoice->payment_method))

<div class="panel">

    <strong>
        Payment Information
    </strong>

    <br><br>

    Payment Method:
    <strong>
        {{ strtoupper($invoice->payment_method) }}
    </strong>

    <br><br>

    @if(!empty($invoice->payment_reference))

        Reference:
        {{ $invoice->payment_reference }}

        <br><br>

    @endif

    @if(!empty($invoice->paid_at))

        Paid At:
        {{ \Carbon\Carbon::parse($invoice->paid_at)->format('M d, Y h:i A') }}

    @endif

</div>

@endif

<div class="footer">

    Generated by Medios Billing™<br>
    Enterprise Billing & Receipt System

</div>

</body>
</html>
