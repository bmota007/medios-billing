@extends('layouts.admin')

@section('content')

<div style="max-width:600px;margin:0 auto;padding:30px;font-family:sans-serif;color:#333;">

    <h1 style="border-bottom:2px solid #eee;padding-bottom:10px;margin-bottom:20px;">
        New Customer Registration
    </h1>

    {{-- GENERAL ERROR DISPLAY --}}
    @if ($errors->any())
    <div style="background:#fee2e2;color:#b91c1c;padding:15px;border-radius:8px;margin-bottom:20px;border: 1px solid #f87171;">
        <p style="margin:0 0 10px 0; font-weight:bold;">Please fix the following errors:</p>
        <ul style="margin:0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('customers.store') }}" style="display:flex;flex-direction:column;gap:15px;">
        @csrf

        {{-- CUSTOMER TYPE --}}
        <div style="background:#f3f4f6;padding:15px;border-radius:8px;border:1px solid #e5e7eb;">
            <label style="display:block;font-weight:bold;margin-bottom:8px;">
                Customer Type
            </label>
            <select name="customer_type" style="width:100%;padding:10px;border:1px solid #ccc;border-radius:6px;">
                <option value="residential" {{ old('customer_type') == 'residential' ? 'selected' : '' }}>🏠 Residential</option>
                <option value="commercial" {{ old('customer_type') == 'commercial' ? 'selected' : '' }}>🏢 Commercial</option>
            </select>
        </div>

        {{-- COMPANY NAME --}}
        <div>
            <label style="font-weight:600;">Company Name (Optional)</label>
            <input name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Acme Corp" style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;">
        </div>

        {{-- CONTACT + PHONE --}}
        <div style="display:flex;gap:15px;">
            <div style="flex:1;">
                <label style="font-weight:600;">Contact Name *</label>
                <input name="name" value="{{ old('name') }}" required style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;">
            </div>
            <div style="flex:1;">
                <label style="font-weight:600;">Phone Number</label>
                <input name="phone" value="{{ old('phone') }}" type="tel" style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;">
            </div>
        </div>

        {{-- EMAIL --}}
        <div>
            <label style="font-weight:600; color: {{ $errors->has('email') ? '#b91c1c' : '#333' }};">
                Email Address *
            </label>
            <input 
                name="email" 
                value="{{ old('email') }}" 
                type="email" 
                required
                style="width:100%;padding:10px;margin-top:5px;border:1px solid {{ $errors->has('email') ? '#f87171' : '#ccc' }};border-radius:6px; background: {{ $errors->has('email') ? '#fff1f2' : '#fff' }};"
            >
            @error('email')
                <small style="color:#b91c1c; font-weight:bold; margin-top:5px; display:block;">{{ $message }}</small>
            @enderror
        </div>

        {{-- ADDRESS --}}
        <div style="border-top:1px solid #eee;padding-top:15px;">
            <label style="font-weight:600;">Service Address</label>
            <input name="street_address" value="{{ old('street_address') }}" placeholder="Street Address" style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;">
            <input name="city_state_zip" value="{{ old('city_state_zip') }}" placeholder="City, State, Zip" style="width:100%;padding:10px;margin-top:10px;border:1px solid #ccc;border-radius:6px;">
        </div>

        {{-- NOTES --}}
        <div>
            <label style="font-weight:600;">Internal Notes</label>
            <textarea name="notes" rows="3" placeholder="Special instructions or preferences..." style="width:100%;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:6px;">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" style="background:#2563eb;color:white;padding:14px;border:none;border-radius:6px;font-weight:bold;cursor:pointer; transition: background 0.2s;">
            💾 Save Customer to Database
        </button>
    </form>
</div>

@endsection
