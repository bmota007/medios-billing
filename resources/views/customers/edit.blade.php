@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 30px; font-family: sans-serif; color: #333;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; margin-bottom: 20px;">
        <h1 style="padding-bottom: 10px;">Edit Customer</h1>
        <a href="{{ route('customers.index') }}" style="text-decoration: none; color: #666; font-size: 14px;">← Back to List</a>
    </div>

    {{-- Error Handling --}}
    @if ($errors->any())
        <div style="background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customers.update', $customer) }}" style="display: flex; flex-direction: column; gap: 15px;">
        @csrf
        @method('PUT')

        {{-- Section: Profile Type --}}
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px;">Customer Type</label>
            <select name="customer_type" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="residential" {{ old('customer_type', $customer->customer_type) == 'residential' ? 'selected' : '' }}>🏠 Residential</option>
                <option value="commercial" {{ old('customer_type', $customer->customer_type) == 'commercial' ? 'selected' : '' }}>🏢 Commercial / Business</option>
            </select>
        </div>

        {{-- Section: Primary Details --}}
        <div>
            <label style="font-weight: 600;">Company Name (Optional)</label>
            <input name="company_name" value="{{ old('company_name', $customer->company_name) }}" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="display: flex; gap: 15px;">
            <div style="flex: 1;">
                <label style="font-weight: 600;">Contact Name *</label>
                <input name="name" value="{{ old('name', $customer->name) }}" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <div style="flex: 1;">
                <label style="font-weight: 600;">Phone Number</label>
                <input name="phone" value="{{ old('phone', $customer->phone) }}" type="tel" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
            </div>
        </div>

        <div>
            <label style="font-weight: 600;">Email Address</label>
            <input name="email" value="{{ old('email', $customer->email) }}" type="email" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

{{-- =========================
     Section: Address
========================= --}}
<div style="border-top: 1px solid #eee; padding-top: 20px; margin-top:20px;">

    <label style="font-weight: 600; display:block; margin-bottom:6px;">
        Service Address
    </label>

    <!-- Street -->
    <input type="text"
           name="billing_address"
           value="{{ old('billing_address', $customer->billing_address) }}"
           placeholder="Street Address"
           style="width: 100%;
                  padding: 10px;
                  border: 1px solid #ccc;
                  border-radius: 4px;
                  margin-bottom: 12px;">

    <!-- City / State / ZIP -->
    <div style="display:flex; gap:10px;">

        <input type="text"
               name="city"
               placeholder="City"
               value="{{ old('city', $customer->city) }}"
               style="flex:1;
                      padding:10px;
                      border:1px solid #ccc;
                      border-radius:4px;">

        <input type="text"
               name="state"
               placeholder="State"
               value="{{ old('state', $customer->state) }}"
               style="flex:1;
                      padding:10px;
                      border:1px solid #ccc;
                      border-radius:4px;">

        <input type="text"
               name="zip"
               placeholder="ZIP"
               value="{{ old('zip', $customer->zip) }}"
               style="flex:1;
                      padding:10px;
                      border:1px solid #ccc;
                      border-radius:4px;">

    </div>
</div>
        {{-- Section: Administrative --}}
        <div>
            <label style="font-weight: 600;">Internal Notes</label>
            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;">{{ old('notes', $customer->notes) }}</textarea>
        </div>

        <button type="submit" style="background: #10b981; color: white; padding: 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px;">
            ✅ Update Customer Record
        </button>
    </form>
</div>
@endsection
