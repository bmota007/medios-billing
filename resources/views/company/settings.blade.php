id="clean_pro_ui" variant="standard"
@extends('layouts.app')

@section('content')

<div class="wrap">

    <div class="header">
        <h1>{{ $company->name }} Settings</h1>
        <p>Manage branding, payments, contract, and system configuration.</p>
    </div>

    <form method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid">

            {{-- LEFT --}}
            <div>

                {{-- BUSINESS --}}
                <div class="card">
                    <h3>Business Profile</h3>

                    <input type="text" name="name" value="{{ $company->name }}" placeholder="Company Name">
                    <input type="email" name="email" value="{{ $company->email }}" placeholder="Company Email">
                    <input type="text" name="phone" value="{{ $company->phone }}" placeholder="Phone">
                    <input type="text" name="address" value="{{ $company->address }}" placeholder="Address">
                    <input type="text" name="website" value="{{ $company->website }}" placeholder="Website">

                    <div class="color-row">
                        <input type="text" name="primary_color" value="{{ $company->primary_color }}">
                        <div class="color-preview" style="background: {{ $company->primary_color }}"></div>
                    </div>
                </div>

                {{-- STRIPE --}}
                <div class="card highlight">
                    <h3>Stripe Payment Integration</h3>

                    <select name="stripe_mode">
                        <option value="live" {{ $company->stripe_mode=='live'?'selected':'' }}>🚀 Live Mode</option>
                        <option value="test" {{ $company->stripe_mode=='test'?'selected':'' }}>🧪 Test Mode</option>
                    </select>

                    <div class="two-col">
                        <input type="text" name="stripe_publishable_key" value="{{ $company->stripe_publishable_key }}" placeholder="Live Publishable Key">
                        <input type="password" name="stripe_secret_key" placeholder="Live Secret Key">
                    </div>

                    <div class="two-col">
                        <input type="text" name="stripe_test_publishable_key" value="{{ $company->stripe_test_publishable_key }}" placeholder="Test Publishable Key">
                        <input type="password" name="stripe_test_secret_key" placeholder="Test Secret Key">
                    </div>

                    <input type="password" name="stripe_webhook_secret" placeholder="Stripe Webhook Secret (WHSEC)">
                </div>

                {{-- CONTRACT --}}
                <div class="card">
                    <h3>Contract Template</h3>

                    @if($company->contract_template_path)
                        <a href="{{ asset('storage/'.$company->contract_template_path) }}" target="_blank" class="btn-small">
                            View Contract
                        </a>
                    @endif

                    <input type="file" name="contract_template">
                </div>

                {{-- PAYMENTS --}}
                <div class="card">
                    <h3>Payment Methods</h3>

                    <div class="toggle-grid">
                        @foreach([
                            'card'=>'Card',
                            'cash'=>'Cash',
                            'venmo'=>'Venmo',
                            'check'=>'Check',
                            'zelle'=>'Zelle'
                        ] as $key=>$label)

                        <label class="toggle">
                            <input type="checkbox" name="accept_{{ $key }}" {{ $company->{'accept_'.$key} ? 'checked':'' }}>
                            <span>{{ $label }}</span>
                        </label>

                        @endforeach
                    </div>
                </div>

                <button class="btn-save">Save Settings</button>

            </div>

            {{-- RIGHT --}}
            <div>

                {{-- PASSWORD --}}
                <div class="card">
                    <h3>Change Password</h3>

                    <input type="password" name="password" placeholder="New Password">
                    <input type="password" name="password_confirmation" placeholder="Confirm Password">

                    <button class="btn-yellow">Update Password</button>
                </div>

                {{-- LOGO --}}
                <div class="card center">
                    <h3>Brand Logo</h3>

                    @if($company->logo_path)
                        <img src="{{ asset('storage/'.$company->logo_path) }}" class="logo-preview">
                    @endif

                    <input type="file" name="logo">
                </div>

                {{-- SMTP --}}
                <div class="card highlight-blue">
                    <h3>SMTP Email Setup</h3>

                    <input type="text" name="smtp_host" value="{{ $company->smtp_host }}" placeholder="SMTP Host">
                    <input type="text" name="smtp_port" value="{{ $company->smtp_port }}" placeholder="Port">
                    <input type="text" name="smtp_user" value="{{ $company->smtp_user }}" placeholder="Email Username">
                    <input type="password" name="smtp_pass" placeholder="Email Password">
                    <input type="text" name="smtp_from" value="{{ $company->smtp_from }}" placeholder="From Email">

                    <small>This allows each business to send invoices from their own email</small>

                    <button type="submit" formaction="{{ route('smtp.test') }}" class="btn-test">
                        Send Test Email
                    </button>
                </div>

            </div>

        </div>

    </form>

</div>

<style>

body{
background: radial-gradient(circle at top, #0f172a, #020617);
}

.wrap{max-width:1300px;margin:auto;padding:30px}

.header h1{color:#fff;font-size:2rem;font-weight:800}
.header p{color:#94a3b8}

.grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:30px;
align-items:start;
}

.card{
background:rgba(15,23,42,.85);
border-radius:18px;
padding:22px;
margin-bottom:22px;
border:1px solid rgba(255,255,255,.08);
transition:.2s;
}

.card:hover{
transform:translateY(-2px);
box-shadow:0 10px 30px rgba(14,165,233,.15);
}

.card h3{
color:#fff;
font-weight:700;
margin-bottom:15px;
}

input,select{
width:100%;
padding:13px;
margin-bottom:12px;
border-radius:10px;
border:none;
background:#e2e8f0;
color:#111;
}

.two-col{
display:grid;
grid-template-columns:1fr 1fr;
gap:12px;
}

.color-row{
display:flex;
gap:10px;
}

.color-preview{
width:50px;
border-radius:10px;
}

.toggle{
display:flex;
justify-content:space-between;
align-items:center;
background:#020617;
padding:12px;
border-radius:10px;
color:#fff;
}

.toggle input{
width:auto;
}

.toggle-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:12px;
}

.btn-yellow{
width:100%;
background:#fbbf24;
padding:12px;
border:none;
border-radius:10px;
font-weight:bold;
color:#000;
}

.btn-save{
width:100%;
padding:16px;
background:linear-gradient(90deg,#0ea5e9,#22c55e);
border:none;
border-radius:12px;
color:#fff;
font-weight:bold;
margin-top:20px;
}

.btn-test{
width:100%;
margin-top:10px;
padding:12px;
background:#22c55e;
border:none;
border-radius:10px;
color:#fff;
font-weight:bold;
}

.logo-preview{
max-width:150px;
margin-bottom:15px;
}

.center{text-align:center}

@media(max-width:900px){
.grid{grid-template-columns:1fr}
.two-col{grid-template-columns:1fr}
}

</style>

@endsection
