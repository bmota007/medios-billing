@extends('layouts.app')

@section('content')

<div class="plans-wrapper">

    <div class="topbar">
        <div>
            <h1>Plans Management</h1>
            <p>Control pricing, visibility, and positioning for your SaaS plans.</p>
        </div>

        <div class="top-actions">
            <a href="{{ route('admin.dashboard') }}" class="ghost-btn">Dashboard</a>
            <a href="#" class="primary-btn">+ Create New Plan</a>
        </div>
    </div>

    @if(session('success'))
        <div class="success-box">
            {{ session('success') }}
        </div>
    @endif

    <div class="plans-grid">

        @foreach($plans as $plan)

        <div class="plan-card">

            <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
                @csrf

                <div class="card-head">

                    <div>
                        <span class="badge-pill">{{ $plan->badge ?: 'Plan' }}</span>
                        <h2>{{ $plan->name }}</h2>
                        <small>Slug: {{ $plan->slug }}</small>
                    </div>

                    <label class="switch">
                        <input type="checkbox" name="is_active" {{ $plan->is_active ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>

                </div>

                <div class="form-grid">

                    <div>
                        <label>Plan Name</label>
                        <input type="text" name="name" value="{{ $plan->name }}">
                    </div>

                    <div>
                        <label>Badge</label>
                        <input type="text" name="badge" value="{{ $plan->badge }}">
                    </div>

                    <div>
                        <label>Monthly Price</label>
                        <input type="number" step="0.01" name="price" value="{{ $plan->price }}">
                    </div>

                    <div>
                        <label>Yearly Price</label>
                        <input type="number" step="0.01" name="yearly_price" value="{{ $plan->yearly_price }}">
                    </div>

                </div>

                <div class="bottom-row">

                    <div class="stats">
                        <span>${{ number_format($plan->price,2) }}/mo</span>
                        <span>${{ number_format($plan->yearly_price,2) }}/yr</span>
                    </div>

                    <button type="submit" class="save-btn">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

        @endforeach

    </div>

</div>

<style>

body{
    background:linear-gradient(135deg,#07111f,#08192d,#06101d);
}

.plans-wrapper{
    padding:40px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:30px;
    flex-wrap:wrap;
}

.topbar h1{
    color:#fff;
    font-size:42px;
    font-weight:800;
    margin:0;
}

.topbar p{
    color:#94a3b8;
    margin:5px 0 0;
}

.top-actions{
    display:flex;
    gap:12px;
}

.ghost-btn,.primary-btn{
    padding:14px 22px;
    border-radius:14px;
    text-decoration:none;
    font-weight:700;
}

.ghost-btn{
    color:#fff;
    border:1px solid rgba(255,255,255,.08);
}

.primary-btn{
    background:#2563eb;
    color:#fff;
}

.success-box{
    background:#16a34a;
    color:#fff;
    padding:15px 20px;
    border-radius:14px;
    margin-bottom:25px;
}

.plans-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:24px;
}

.plan-card{
    background:linear-gradient(180deg,#081222,#09182b);
    border:1px solid rgba(255,255,255,.06);
    border-radius:24px;
    padding:28px;
    box-shadow:0 25px 50px rgba(0,0,0,.35);
}

.card-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:25px;
}

.badge-pill{
    display:inline-block;
    padding:8px 16px;
    border-radius:999px;
    background:rgba(37,99,235,.18);
    color:#93c5fd;
    font-size:13px;
    font-weight:700;
}

.card-head h2{
    color:#fff;
    margin:16px 0 4px;
    font-size:34px;
    font-weight:800;
}

.card-head small{
    color:#64748b;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:16px;
}

.form-grid label{
    display:block;
    color:#cbd5e1;
    margin-bottom:8px;
    font-size:14px;
}

.form-grid input{
    width:100%;
    background:rgba(255,255,255,.03);
    border:1px solid rgba(255,255,255,.07);
    color:#fff;
    padding:14px;
    border-radius:14px;
}

.bottom-row{
    margin-top:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
}

.stats{
    display:flex;
    gap:15px;
    color:#94a3b8;
    font-weight:700;
}

.save-btn{
    background:#2563eb;
    color:#fff;
    border:none;
    padding:14px 22px;
    border-radius:14px;
    font-weight:700;
}

.switch{
    position:relative;
    width:54px;
    height:28px;
}

.switch input{
    opacity:0;
}

.slider{
    position:absolute;
    inset:0;
    background:#334155;
    border-radius:999px;
}

.slider:before{
    content:'';
    position:absolute;
    width:22px;
    height:22px;
    left:3px;
    top:3px;
    background:#fff;
    border-radius:50%;
    transition:.2s;
}

.switch input:checked + .slider{
    background:#22c55e;
}

.switch input:checked + .slider:before{
    transform:translateX(26px);
}

@media(max-width:1100px){
    .plans-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){

.plans-wrapper{
    padding:20px;
}

.topbar h1{
    font-size:30px;
}

.form-grid{
    grid-template-columns:1fr;
}

}

</style>

@endsection
