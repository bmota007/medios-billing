@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background:linear-gradient(135deg,#020617,#0f172a,#111827);">

    <div class="max-w-md w-full space-y-6 glass-card p-10">

        <div class="text-center">
            <h2 class="mt-2 text-3xl font-extrabold text-white">
                Start Your <span style="color:#38bdf8;">MediosBilling</span> Trial
            </h2>

            <p class="mt-3 text-sm" style="color:#94a3b8;">
                5-Day Free Trial • Card Required • Cancel Anytime During Trial
            </p>
        </div>

<form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div style="background:rgba(239,68,68,.15); border:1px solid #ef4444; color:#fca5a5; padding:14px; border-radius:12px;">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <input type="hidden" name="plan" id="planInput" value="starter">

            <!-- PLAN BANNER -->
            <div id="planBanner" class="plan-banner">

                <div>
                    <p id="planLabel" class="text-label mb-1">Selected Plan</p>
                    <h3 id="planName" class="text-xl font-extrabold text-white">Starter Plan</h3>
                </div>

                <div class="text-right">
                    <span id="planPrice" class="text-3xl font-black text-white">$49</span>
                    <div class="text-xs text-slate-400">per month</div>
                </div>

            </div>

            <!-- FORM -->
            <div class="space-y-4">

                <div>
                    <label class="text-label">Company Name</label>
                    <input type="text" name="company_name" required class="custom-input w-full mt-1" placeholder="Pronto Painting LLC">
                </div>

                <div>
                    <label class="text-label">Your Full Name</label>
                    <input type="text" name="name" required class="custom-input w-full mt-1" placeholder="John Doe">
                </div>

                <div>
                    <label class="text-label">Business Email</label>
                    <input type="email" name="email" required class="custom-input w-full mt-1" placeholder="owner@company.com">
                </div>

                <div>
                    <label class="text-label">Password</label>
                    <input type="password" name="password" required class="custom-input w-full mt-1">
                </div>

                <div>
                    <label class="text-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="custom-input w-full mt-1">
                </div>

            </div>

            <button type="submit" class="submit-btn w-full py-3 px-4 rounded-xl font-bold">
                Continue To Secure Checkout
            </button>

            <div class="text-center mt-4 text-sm text-slate-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-bold text-sky-400 no-underline">
                    Sign In
                </a>
            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const urlParams = new URLSearchParams(window.location.search);
    const queryPlan = (urlParams.get('plan') || '').toLowerCase();
    const hashPlan  = window.location.hash.replace('#','').toLowerCase();

    const selectedPlan = queryPlan || hashPlan || 'starter';

    const planInput  = document.getElementById('planInput');
    const planBanner = document.getElementById('planBanner');
    const planName   = document.getElementById('planName');
    const planPrice  = document.getElementById('planPrice');

    const plans = {
        starter: {
            name:'Starter Plan',
            price:'$49',
            color:'#f8fafc',
            bg:'rgba(255,255,255,.06)'
        },
        growth: {
            name:'Growth Plan',
            price:'$129',
            color:'#38bdf8',
            bg:'rgba(56,189,248,.14)'
        },
        premium: {
            name:'Premium Plan',
            price:'$499',
            color:'#a855f7',
            bg:'rgba(168,85,247,.14)'
        }
    };

    const data = plans[selectedPlan] || plans.starter;

    planInput.value = selectedPlan;
    planBanner.style.background = data.bg;
    planBanner.style.borderColor = data.color + '50';

    planName.innerText = data.name;
    planPrice.innerText = data.price;
    planPrice.style.color = data.color;
});
</script>

<style>
.glass-card{
    background:rgba(15,23,42,.72);
    border:1px solid rgba(255,255,255,.08);
    backdrop-filter:blur(18px);
    border-radius:22px;
    box-shadow:0 25px 70px rgba(0,0,0,.45);
}

.plan-banner{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 20px;
    border-radius:16px;
    border:1px solid rgba(255,255,255,.08);
    transition:.25s;
}

.custom-input{
    background:rgba(255,255,255,.04);
    border:1px solid rgba(255,255,255,.10);
    color:#fff;
    padding:13px 14px;
    border-radius:12px;
    width:100%;
}

.custom-input:focus{
    outline:none;
    border-color:#38bdf8;
    box-shadow:0 0 0 4px rgba(56,189,248,.15);
}

.text-label{
    font-size:.70rem;
    text-transform:uppercase;
    letter-spacing:1.3px;
    color:#94a3b8;
    font-weight:800;
}

.submit-btn{
    background:linear-gradient(135deg,#38bdf8,#2563eb);
    color:white;
    transition:.25s;
}

.submit-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 15px 30px rgba(37,99,235,.35);
}
</style>
@endsection
