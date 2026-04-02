@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#0f172a] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 glass-card p-10">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
                Create your <span class="text-sky-400">Medios</span> account
            </h2>
            <p class="mt-2 text-center text-sm text-slate-400">
                Start managing your billing with precision
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label class="text-label">Company Name</label>
                    <input type="text" name="company_name" required class="custom-input w-full mt-1" placeholder="Solaris Tech">
                </div>

                <div class="mb-4">
                    <label class="text-label">Your Full Name</label>
                    <input type="text" name="name" required class="custom-input w-full mt-1" placeholder="John Doe">
                </div>

                <div class="mb-4">
                    <label class="text-label">Work Email Address</label>
                    <input type="email" name="email" required class="custom-input w-full mt-1" placeholder="name@company.com">
                </div>

                <div class="mb-4">
                    <label class="text-label">Password</label>
                    <input type="password" name="password" required class="custom-input w-full mt-1" placeholder="••••••••">
                </div>

                <div class="mb-4">
                    <label class="text-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="custom-input w-full mt-1" placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-sky-500 hover:bg-sky-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-all duration-200 shadow-lg shadow-sky-500/20">
                    Get Started Now
                </button>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-xs text-slate-500">
                    Already have an account? <a href="{{ route('login') }}" class="text-sky-400 hover:text-sky-300 font-bold">Sign In</a>
                </p>
            </div>
        </form>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1.5rem;
    }
    .custom-input {
        background-color: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        padding: 12px 15px !important;
        border-radius: 10px !important;
        outline: none !important;
    }
    .custom-input:focus {
        border-color: #38bdf8 !important;
        box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1) !important;
    }
    .text-label {
        color: #94a3b8;
        font-size: 0.65rem;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 1.5px;
    }
</style>
@endsection
