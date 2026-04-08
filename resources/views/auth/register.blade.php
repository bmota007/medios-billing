@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background-color: #0f172a;">
    <div class="max-w-md w-full space-y-6 glass-card p-10">
        <div>
            <h2 class="mt-2 text-center text-3xl font-extrabold" style="color: #ffffff;">
                Create your <span style="color: #38bdf8;">Medios</span> account
            </h2>
            <p class="mt-2 text-center text-sm" style="color: #94a3b8;">
                Start managing your billing with precision
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('medios.register') }}" method="POST">
            @csrf

            {{-- ERROR DISPLAY BLOCK --}}
            @if ($errors->any())
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #f87171; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem;">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <input type="hidden" name="plan" id="planInput" value="starter">

            <div id="planBanner" class="plan-banner" style="background-color: rgba(255,255,255,0.1); border: 1px solid rgba(248, 250, 252, 0.5);">
                <div>
                    <p id="planLabel" class="text-label mb-1" style="color: #f8fafc !important;">Selected Plan</p>
                    <h3 id="planName" class="text-xl font-extrabold" style="color: #ffffff; margin: 0;">Starter Tier</h3>
                </div>
                <div class="text-right">
                    <span id="planPrice" class="text-2xl font-black" style="color: #f8fafc;">$35</span>
                    <span class="text-xs" style="color: #94a3b8;">/mo</span>
                </div>
            </div>

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
                <button type="submit" class="submit-btn w-full flex justify-center py-3 px-4 text-sm font-bold rounded-lg transition-all duration-200">
                    Get Started Now
                </button>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-sm" style="color: #cbd5e1;">
                    Already have an account? <a href="{{ route('login') }}" style="color: #38bdf8; font-weight: bold; text-decoration: none;">Sign In</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.replace('#', '').toLowerCase();
        const planInput = document.getElementById('planInput');
        const planBanner = document.getElementById('planBanner');
        const planLabel = document.getElementById('planLabel');
        const planName = document.getElementById('planName');
        const planPrice = document.getElementById('planPrice');

        const plans = {
            'starter': { name: 'Starter Tier', price: '$35', color: '#f8fafc', bg: 'rgba(255,255,255,0.1)' },
            'growth': { name: 'Growth Tier', price: '$79', color: '#38bdf8', bg: 'rgba(56, 189, 248, 0.15)' },
            'elite': { name: 'Elite Tier', price: '$199', color: '#a855f7', bg: 'rgba(168, 85, 247, 0.15)' }
        };

        let selected = plans[hash] ? hash : 'starter';
        let data = plans[selected];

        planInput.value = selected;
        planBanner.style.backgroundColor = data.bg;
        planBanner.style.borderColor = data.color + '50';
        planLabel.style.color = data.color + ' !important';
        planName.innerText = data.name;
        planPrice.innerText = data.price;
        planPrice.style.color = data.color;
    });
</script>

<style>
    .glass-card { background: rgba(30, 41, 59, 0.7) !important; backdrop-filter: blur(16px) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; border-radius: 1.5rem !important; }
    .plan-banner { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-radius: 12px; margin-bottom: 24px; transition: all 0.3s ease; }
    .custom-input { background-color: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; padding: 12px 15px !important; border-radius: 10px !important; outline: none !important; transition: all 0.2s ease-in-out !important; }
    .custom-input::placeholder { color: rgba(255, 255, 255, 0.3) !important; }
    .custom-input:focus { background-color: rgba(255, 255, 255, 0.1) !important; border-color: #38bdf8 !important; box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15) !important; }
    .text-label { color: #94a3b8 !important; font-size: 0.65rem !important; text-transform: uppercase !important; font-weight: 800 !important; letter-spacing: 1.5px !important; margin-left: 4px !important; }
    .submit-btn { background-color: #38bdf8 !important; color: #0f172a !important; border: none !important; }
    .submit-btn:hover { background-color: #7dd3fc !important; transform: translateY(-2px); }
</style>
@endsection
