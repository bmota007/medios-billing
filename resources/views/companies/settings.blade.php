@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="text-white fw-bold">{{ $company->name }} Settings</h2>
        <p class="text-secondary small">Update your business profile, branding, contract, and payment methods in one place.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-green-500/10 border-green-500/20 text-green-500 mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger bg-red-500/10 border-red-500/20 text-red-500 mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- DYNAMIC ROUTE FIX: If we are on admin/brand, use admin update route, else use standard --}}
    <form method="POST" action="{{ request()->routeIs('admin.brand') ? route('admin.brand.update') : route('company.update') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-7">
                <div class="glass-card mb-4">
                    <h5 class="text-white mb-4">Business Profile</h5>
                    
                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Company Name</label>
                        <input type="text" name="name" value="{{ $company->name }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Company Email</label>
                        <input type="email" name="email" value="{{ $company->email }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Phone</label>
                        <input type="text" name="phone" value="{{ $company->phone }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Business Address</label>
                        <input type="text" name="address" value="{{ $company->address }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Website</label>
                        <input type="text" name="website" value="{{ $company->website }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Primary Brand Color</label>
                        <input type="text" name="primary_color" value="{{ $company->primary_color }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>

                    <div class="mb-3">
                        <label class="text-secondary small uppercase d-block">Logo</label>
                        @if($company->logo || $company->logo_path)
                            <img src="{{ asset('storage/'.($company->logo ?? $company->logo_path)) }}" class="h-12 mb-2" style="max-height: 50px;">
                        @endif
                        <input type="file" name="logo" class="form-control bg-transparent border-slate-700 text-white">
                        <small class="text-secondary small">Accepted: JPG, JPEG, PNG, SVG, WEBP (Max 2MB)</small>
                    </div>
                </div>

                <div class="glass-card mb-4">
                    <h5 class="text-white mb-3">Contract Template</h5>
                    @if($company->contract_template_path)
                        <a href="{{ Storage::url($company->contract_template_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mb-3">View Current Contract</a>
                    @endif
                    <input type="file" name="contract_template" class="form-control bg-transparent border-slate-700 text-white">
                    <small class="text-secondary small">Accepted: PDF, DOC, DOCX, TXT (Max 10MB)</small>
                </div>

                <div class="glass-card mb-4">
                    <h5 class="text-white mb-3">Accepted Payment Methods</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="accept_card" value="1" {{ $company->accept_card ? 'checked' : '' }}><label class="form-check-label text-white small">Accept Card</label></div>
                            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="accept_cash" value="1" {{ $company->accept_cash ? 'checked' : '' }}><label class="form-check-label text-white small">Accept Cash</label></div>
                            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="accept_venmo" value="1" {{ $company->accept_venmo ? 'checked' : '' }}><label class="form-check-label text-white small">Accept Venmo</label></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="accept_check" value="1" {{ $company->accept_check ? 'checked' : '' }}><label class="form-check-label text-white small">Accept Check</label></div>
                            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="accept_zelle" value="1" {{ $company->accept_zelle ? 'checked' : '' }}><label class="form-check-label text-white small">Accept Zelle</label></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-secondary small uppercase">Zelle Label</label>
                        <input type="text" name="zelle_label" value="{{ $company->zelle_label }}" class="form-control bg-transparent border-slate-700 text-white mb-2">
                        <label class="text-secondary small uppercase">Zelle Value</label>
                        <input type="text" name="zelle_value" value="{{ $company->zelle_value }}" class="form-control bg-transparent border-slate-700 text-white mb-3">
                        
                        <label class="text-secondary small uppercase">Venmo Label</label>
                        <input type="text" name="venmo_label" value="{{ $company->venmo_label }}" class="form-control bg-transparent border-slate-700 text-white mb-2">
                        <label class="text-secondary small uppercase">Venmo Value</label>
                        <input type="text" name="venmo_value" value="{{ $company->venmo_value }}" class="form-control bg-transparent border-slate-700 text-white">
                    </div>
                </div>

                <div class="glass-card mb-4 border border-slate-700/50">
                    <h5 class="text-white mb-2"><i class="fa-solid fa-credit-card mr-2"></i> Stripe Payment Integration</h5>
                    <div class="mb-4">
                        <label class="text-secondary small uppercase">Transaction Mode</label>
                        <select name="stripe_mode" class="form-control bg-transparent border-slate-700 text-white">
                            <option value="test" {{ $company->stripe_mode == 'test' ? 'selected' : '' }} class="bg-slate-900">🛠 Test Mode</option>
                            <option value="live" {{ $company->stripe_mode == 'live' ? 'selected' : '' }} class="bg-slate-900">🚀 Live Mode</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-warning small uppercase font-bold">Live Publishable Key</label>
                            <input type="text" name="stripe_publishable_key" value="{{ $company->stripe_publishable_key }}" class="form-control bg-transparent border-slate-700 text-white">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-warning small uppercase font-bold">Live Secret Key</label>
                            <input type="password" name="stripe_secret_key" value="{{ $company->stripe_secret_key }}" class="form-control bg-transparent border-slate-700 text-white">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-secondary small uppercase">Test Publishable Key</label>
                            <input type="text" name="stripe_test_publishable_key" value="{{ $company->stripe_test_publishable_key }}" class="form-control bg-transparent border-slate-700 text-white">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-secondary small uppercase">Test Secret Key</label>
                            <input type="password" name="stripe_test_secret_key" value="{{ $company->stripe_test_secret_key }}" class="form-control bg-transparent border-slate-700 text-white">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm px-4">Save Business Settings</button>
            </div>

            <div class="col-md-5">
                <div class="glass-card h-100">
                    <h5 class="text-white mb-4">Change Password</h5>
                    <div class="mb-3">
                        <label class="text-secondary small uppercase">New Password</label>
                        {{-- FIX: name changed to new_password to match Controller --}}
                        <input type="password" name="new_password" class="form-control bg-transparent border-slate-700 text-white" placeholder="New password">
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small uppercase">Confirm New Password</label>
                        {{-- FIX: name changed to new_password_confirmation for automatic Laravel validation --}}
                        <input type="password" name="new_password_confirmation" class="form-control bg-transparent border-slate-700 text-white" placeholder="Confirm password">
                    </div>
                    <button type="submit" class="btn btn-warning w-100 font-bold uppercase">Update Profile & Password</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
