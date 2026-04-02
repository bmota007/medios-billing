@extends('layouts.app')

@section('content')

<div class="container py-5">

    <div class="card shadow-lg rounded-4 p-4">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h2 class="fw-bold">
                    {{ $invoice->company->name ?? 'Company' }}
                </h2>
                <p class="mb-0">Invoice #: {{ $invoice->invoice_no }}</p>
                <p class="mb-0">Date: {{ $invoice->invoice_date }}</p>
            </div>

            <div class="text-end">
                <h4 class="text-success fw-bold">
                    ${{ number_format($invoice->total ?? 0, 2) }}
                </h4>
                <p>Status: {{ strtoupper($invoice->status) }}</p>
            </div>
        </div>

        <hr>

        <h5 class="mb-3">Bill To:</h5>
        <p class="mb-0">{{ $invoice->customer_name }}</p>
        <p class="mb-0">{{ $invoice->customer_email }}</p>
        <p class="mb-0">{{ $invoice->customer_phone }}</p>

        <hr>

        <h5 class="mb-3">Items</h5>

        @php
            $items = [];

            if (!empty($invoice->items)) {
                $decoded = json_decode($invoice->items, true);
                if (is_array($decoded)) {
                    $items = $decoded;
                }
            }
        @endphp

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>

                @if(count($items) > 0)
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item['desc'] ?? $item['description'] ?? 'Service' }}</td>
                            <td>{{ $item['qty'] ?? 0 }}</td>
                            <td>${{ number_format($item['price'] ?? 0, 2) }}</td>
                            <td>
                                ${{ number_format(
                                    ($item['total'] ?? (($item['qty'] ?? 0) * ($item['price'] ?? 0))), 
                                    2
                                ) }}
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">No items available</td>
                    </tr>
                @endif

            </tbody>
        </table>

        <div class="text-end mt-4">
            <h4>Total: ${{ number_format($invoice->total ?? 0, 2) }}</h4>
        </div>

        <div class="mt-4 text-center">
            @if($invoice->status !== 'paid')
                <a href="{{ route('invoice.pay', $invoice->invoice_no) }}" class="btn btn-primary btn-lg">
                    Pay Now
                </a>
            @else
                <span class="badge bg-success p-3">PAID</span>
            @endif
        </div>

    </div>

</div>

@endsection
