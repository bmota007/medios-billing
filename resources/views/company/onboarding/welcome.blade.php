@extends('layouts.admin')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="glass-card p-5 text-center" style="max-width: 600px; background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 1.5rem;">
        <i class="fa-solid fa-shield-halved text-info fa-3x mb-4"></i>
        <h2 class="text-white fw-bold mb-3">Security Handshake</h2>
        <p class="text-secondary mb-4">
            Welcome to the team! Before accessing <strong>{{ auth()->user()->company->name }}</strong> private data, you must acknowledge our confidentiality agreement.
        </p>

        <div class="bg-dark p-4 rounded text-start mb-4 border border-secondary" style="font-size: 13px; max-height: 200px; overflow-y: auto; color: #94a3b8; background-color: #0f172a !important;">
            <strong>NON-DISCLOSURE & CONFIDENTIALITY AGREEMENT</strong><br><br>
            By proceeding, I acknowledge that I will have access to sensitive company information, including client lists, pricing strategies, and intellectual property. I agree that:
            <br>1. I will not share login credentials with anyone.
            <br>2. I will not download or distribute company data for personal use.
            <br>3. I understand that {{ auth()->user()->company->name }} reserves the right to pursue legal action for any breach of this agreement.
        </div>

        <form action="{{ route('legal.accept') }}" method="POST">
            @csrf
            <div class="form-check text-start mb-4">
                <input class="form-check-input" type="checkbox" id="legalCheck" required>
                <label class="form-check-label text-white small" for="legalCheck">
                    I have read and agree to the confidentiality terms above.
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">Enter Dashboard</button>
        </form>
    </div>
</div>
@endsection
