@extends('layouts.app')

@section('content')

<div style="max-width:900px;margin:auto;padding:40px;color:white;">

    <h1>Quote #{{ $quote->quote_number }}</h1>

    <p><strong>Total:</strong> ${{ number_format($quote->total,2) }}</p>

    <form method="POST" action="{{ route('quotes.approve', $quote->public_token)
        @csrf
        <button style="background:#2563eb;color:white;padding:12px 20px;border:none;border-radius:6px;">
            Approve Quote
        </button>
    </form>

</div>

@endsection
