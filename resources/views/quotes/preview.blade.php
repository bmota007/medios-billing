@extends('layouts.admin')

@section('content')

<style>
    /* Prevent parent containers from clipping our horizontal swipe */
    .main-content, .container-fluid, .glass-card {
        overflow: visible !important;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        .glass-card {
            width: 100% !important;
            padding: 12px !important;
            border-radius: 12px !important;
        }

        h2, h6 { font-size: 1rem !important; }
    }
</style>

<div class="container-fluid py-1">

    {{-- Header --}}
    <div class="mb-2 text-start px-1">
        <h2 class="text-white fw-bold mb-0 h6">Quote Preview</h2>
        <p class="text-secondary small mb-0">{{ $quote->customer_name }}</p>
    </div>

    {{-- Buttons --}}
    <div class="row g-1 mb-3 px-1">
        @php 
        $actions = [
            ['icon' => 'fa-arrow-left', 'btn' => 'btn-secondary', 'url' => url()->previous()],
            ['icon' => 'fa-edit', 'btn' => 'btn-warning text-dark', 'url' => '#'],
            ['icon' => 'fa-rotate', 'btn' => 'btn-success', 'url' => '#'],
            ['icon' => 'fa-file-pdf', 'btn' => 'btn-primary', 'url' => '#'],
            ['icon' => 'fa-print', 'btn' => 'btn-info text-dark', 'url' => '#'],
            ['icon' => 'fa-paper-plane', 'btn' => 'btn-warning text-dark', 'url' => '#', 'style' => 'background:#f59e0b']
        ];
        @endphp
        @foreach($actions as $action)
        <div class="col-4 col-md-auto">
            <a href="{{ $action['url'] }}" class="btn {{ $action['btn'] }} btn-sm w-100 py-2 shadow-sm" style="{{ $action['style'] ?? '' }}">
                <i class="fa-solid {{ $action['icon'] }}"></i>
            </a>
        </div>
        @endforeach
    </div>

    {{-- THE CARD --}}
    <div class="glass-card shadow-lg" style="background: rgba(30, 41, 59, 0.95); border: 1px solid rgba(255,255,255,0.12);">

        {{-- Proposal Meta --}}
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <div>
                <h6 class="text-white fw-bold mb-0 small">PROPOSAL</h6>
                <span class="text-secondary" style="font-size: 0.7rem;">#{{ $quote->quote_no ?? $quote->id }}</span>
            </div>
            <span class="badge bg-success shadow-sm">Approved</span>
        </div>

        {{-- HARD SWIPE TABLE: Guaranteed to show Prices --}}
        <div style="width: 100%; overflow-x: auto !important; -webkit-overflow-scrolling: touch !important; position: relative; z-index: 1; border-radius: 8px;">
            <table style="width: 100%; min-width: 650px; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #edeff2;">
                        <th style="text-align: left; padding: 12px; color: #333; font-size: 0.8rem; text-transform: uppercase; width: auto;">Description</th>
                        <th style="text-align: center; padding: 12px; color: #333; font-size: 0.8rem; text-transform: uppercase; width: 60px;">Qty</th>
                        <th style="text-align: right; padding: 12px; color: #333; font-size: 0.8rem; text-transform: uppercase; white-space: nowrap;">Price</th>
                        <th style="text-align: right; padding: 12px; color: #333; font-size: 0.8rem; text-transform: uppercase; white-space: nowrap;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; color: #000; vertical-align: top; line-height: 1.4; font-size: 0.85rem; font-weight: 600;">{{ $item->description }}</td>
                        <td style="padding: 12px; color: #000; text-align: center; vertical-align: top; font-size: 0.85rem;">{{ $item->qty }}</td>
                        <td style="padding: 12px; color: #000; text-align: right; vertical-align: top; white-space: nowrap; font-size: 0.85rem;">${{ number_format($item->price, 2) }}</td>
                        <td style="padding: 12px; color: #000; text-align: right; vertical-align: top; white-space: nowrap; font-size: 0.85rem; font-weight: 700;">${{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer Totals --}}
        <div class="mt-3 p-3 bg-dark bg-opacity-50 rounded-3 border border-secondary border-opacity-25">
            <div class="d-flex justify-content-between text-white fw-bold">
                <span class="small text-uppercase">Grand Total</span>
                <span class="text-info">${{ number_format($quote->total, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between text-warning mt-1 small">
                <span>Deposit (40%)</span>
                <span>${{ number_format($quote->total * 0.4, 2) }}</span>
            </div>
        </div>

    </div>
</div>

@endsection
