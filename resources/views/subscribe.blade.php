@extends('layouts.app')

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 100vh; background: radial-gradient(circle at top left, #1e293b, #0f172a); padding: 20px;">
    <div class="glass-card shadow-lg" style="max-width: 550px; width: 100%; border-radius: 2rem; background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1); padding: 40px;">
        
        <div class="text-center mb-4">
            <div class="badge bg-info text-dark mb-2 px-3 py-2 rounded-pill fw-bold">7-DAY FREE TRIAL</div>
            <h2 class="fw-bold text-white mb-1">Scale Your <span style="color: #38bdf8;">Business</span></h2>
            <p class="text-secondary small">Join the elite contractors using Medios Billing.</p>
        </div>

        <div class="p-4 mb-4" style="background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 1.2rem;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-white mb-0 fw-bold">Growth Monthly</h5>
                    <ul class="list-unstyled text-secondary mb-0" style="font-size: 0.8rem; margin-top: 5px;">
                        <li><i class="fa-solid fa-check text-info me-2"></i>Unlimited Invoicing</li>
                        <li><i class="fa-solid fa-check text-info me-2"></i>Automation Engine</li>
                    </ul>
                </div>
                <div class="text-end">
                    <h2 class="text-white mb-0 fw-bold">$35</h2>
                    <p class="text-info small mb-0 fw-bold">Free for 7 Days</p>
                </div>
            </div>
        </div>

        <form action="{{ route('subscribe.process') }}" method="POST" id="payment-form">
            @csrf
            <div class="mb-4">
                <label class="small text-uppercase text-secondary fw-bold mb-2 ml-1" style="letter-spacing: 1px;">Secure Credit Card</label>
                <div id="card-element" class="form-control" style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255,255,255,0.1); padding: 15px; border-radius: 0.8rem; color: white;">
                    </div>
                <div id="card-errors" role="alert" class="text-danger small mt-2"></div>
            </div>

            <button type="submit" class="btn btn-info w-100 py-3 fw-bold shadow-lg mb-3" style="border-radius: 1rem; background-color: #38bdf8; border: none; color: #0f172a;">
                START MY FREE TRIAL
            </button>

            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-secondary small text-decoration-none hover-white">
                    <i class="fa-solid fa-arrow-left me-1"></i> Skip for now, let me explore first
                </a>
            </div>
        </form>

        <p class="text-center text-secondary x-small mt-4" style="font-size: 0.7rem;">
            <i class="fa-solid fa-lock me-1"></i> Encrypted by Stripe. You will not be charged until Day 7. Cancel anytime with one click.
        </p>
    </div>
</div>

<style>
    .hover-white:hover { color: white !important; }
    .btn-info:hover { background-color: #7dd3fc !important; transform: translateY(-2px); transition: 0.2s; }
</style>
@endsection
