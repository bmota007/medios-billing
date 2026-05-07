@extends('layouts.admin')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-white">Quote #{{ $quote->quote_number }}</h4>

        <div class="d-flex gap-2">

            <a href="{{ route('quotes.send', $quote->id) }}" class="btn btn-warning">
                Send
            </a>

            <a href="#" class="btn btn-primary">
                PDF
            </a>

            <a href="#" class="btn btn-success">
                SMS
            </a>

        </div>
    </div>

    <div class="card shadow-lg" style="border-radius:12px;">
        <div class="card-header bg-dark text-white">
            <strong>PROPOSAL</strong>  
            <span class="ms-2">#{{ $quote->id }}</span>

            <span class="float-end badge bg-success">
                {{ ucfirst($quote->status ?? 'draft') }}
            </span>
        </div>

        <div class="card-body p-0">

            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ abs($item->qty) }}</td>
                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                        <td class="text-end">
                            ${{ number_format(abs($item->total), 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        <div class="card-footer text-end">

            <h5>
                Grand Total: 
                <span class="text-primary">
                    ${{ number_format(abs($quote->total), 2) }}
                </span>
            </h5>

            <p class="mb-2">
                Deposit (40%): 
                <strong>${{ number_format(abs($quote->total * 0.4), 2) }}</strong>
            </p>

            {{-- APPROVE BUTTON --}}
            @if(request()->is('q/*'))
                <form method="POST" action="{{ route('quotes.approve', $quote->public_token) }}">
                    @csrf
                    <button class="btn btn-success btn-lg">
                        Approve Quote
                    </button>
                </form>
            @endif

        </div>
    </div>

</div>

@endsection
