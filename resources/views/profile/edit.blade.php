@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="crm-header mb-4">
        <div>
            <h2 class="crm-title">Account <span class="text-sky-400">Settings</span></h2>
            <p class="crm-subtitle">Logged in as <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->company->name ?? 'System' }})</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            {{-- Notification Status --}}
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400 mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i> Profile updated successfully.
                </div>
            @endif

            {{-- Update Info Section --}}
            <div class="dashboard-card p-4 mb-4">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password Section --}}
            <div class="dashboard-card p-4">
                <div class="max-w-xl text-white">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .crm-title { font-size: 28px; font-weight: 700; color: white; }
    .crm-subtitle { font-size: 14px; color: #94a3b8; }
    .dashboard-card { background: rgba(15,23,42,0.9); padding: 20px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
    
    /* Input Styling for Profile Forms */
    .max-w-xl h2, .max-w-xl p, .max-w-xl label { color: white !important; }
    .max-w-xl p { color: #94a3b8 !important; font-size: 0.9rem; }
    .max-w-xl input { 
        background: #0f172a !important; 
        border: 1px solid rgba(255,255,255,0.1) !important; 
        color: white !important;
        border-radius: 8px !important;
        padding: 10px !important;
    }
    .max-w-xl button {
        background: #0ea5e9 !important;
        border: none !important;
        padding: 10px 20px !important;
        border-radius: 8px !important;
        font-weight: bold !important;
        color: white !important;
        margin-top: 15px;
    }
</style>
@endsection
