@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 30px; font-family: sans-serif; color: #333;">
    <h1 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">New Customer Registration</h1>

    {{-- Error Handling: Shows the user what went wrong --}}
    @if ($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customers.store') }}" style="display: flex; flex-direction: column; gap: 15px;">
        @csrf

        {{-- Section: Profile Type --}}
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Customer Type</label>
            <select name="customer_type" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="residential" {{ old('customer_type') == 'residential' ? 'selected' : '' }}>🏠 Residential</option>
                <option value="commercial" {{ old('customer_type') == 'commercial' ? 'selected' : '' }}>🏢 Commercial / Business</option>
            </select>
        </div>

        {{-- Section: Primary Details --}}
        <div>
            <label style="font-weight: 600;">Company Name (Optional)</label>
            <input name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Acme Corp" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="font-weight: 600;">Contact Name *</label>
                <input name="name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 600;">Phone Number</label>
                <input name="phone" value="{{ old('phone') }}" type="tel" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
        </div>

        <div>
            <label style="font-weight: 600;">Email Address</label>
            <input name="email" value="{{ old('email') }}" type="email" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        {{-- Section: Address --}}
        <div style="border-top: 1px solid #eee; padding-top: 15px;">
            <label style="font-weight: 600;">Service Address</label>
            <input name="street_address" value="{{ old('street_address') }}" placeholder="Street Address" style="width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px;">
            <input name="city_state_zip" value="{{ old('city_state_zip') }}" placeholder="City, State, Zip" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        {{-- Section: Administrative --}}
        <div>
            <label style="font-weight: 600;">Internal Notes</label>
            <textarea name="notes" rows="3" placeholder="Special instructions or preferences..." style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">{{ old('notes') }}</textarea>
        </div>

        <button type="submit" style="background: #2563eb; color: white; padding: 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px;">
            💾 Save Customer to Database
        </button>
    </form>
</div>
@endsection
