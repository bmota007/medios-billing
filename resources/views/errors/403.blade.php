@extends('layouts.admin')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="text-center glass-card p-5 shadow-2xl" style="max-width: 700px; background: rgba(15,23,42,0.9); border-radius: 28px; border: 1px solid rgba(56, 189, 248, 0.2); backdrop-filter: blur(12px);">
        
        {{-- Icon Section --}}
        <div class="mb-5">
            <div class="d-inline-block p-4 rounded-circle mb-3" style="background: rgba(14, 165, 233, 0.1);">
                <i class="fa-solid fa-shield-halved text-sky-400 fa-4x"></i>
            </div>
        </div>
        
        {{-- Your Branded Quote --}}
        <h1 class="text-white fw-bold mb-4" style="font-size: 1.6rem; line-height: 1.6; letter-spacing: -0.5px;">
            "Sometimes Life is hard and unfair, but when you get to the end of the road, use it to gather your thoughts, rest and execute your plan."
        </h1>
        
        <p class="text-sky-400 fw-bold mb-5" style="letter-spacing: 2px; text-transform: uppercase; font-size: 0.9rem;">
            Have a blessed day, Medios Billing.
        </p>

        {{-- Instructions & Role-Based Action --}}
        <div class="pt-4 border-top border-white-10" style="border-top: 1px solid rgba(255,255,255,0.1) !important;">
            <div class="d-flex align-items-center justify-content-center gap-2 text-secondary mb-4">
                <i class="fa-solid fa-lock-keyhole"></i>
                <span>This section is restricted. Please contact your <strong>IT Department</strong> for access.</span>
            </div>
            
            {{-- SMART ROUTING BUTTON --}}
            <a href="{{ in_array(auth()->user()->role, ['super_admin', 'admin']) ? route('admin.dashboard') : route('dashboard') }}" 
               class="btn btn-primary px-5 py-3 fw-bold shadow-lg" 
               style="border-radius: 14px; background: #0ea5e9; border: none;">
                <i class="fa-solid fa-house-chimney me-2"></i> Return to Dashboard
            </a>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #020617 !important;
    }
    
    .glass-card {
        transition: transform 0.3s ease;
    }
    
    .glass-card:hover {
        transform: scale(1.02);
    }

    .border-white-10 {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
</style>
@endsection
