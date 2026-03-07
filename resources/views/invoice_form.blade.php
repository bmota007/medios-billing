@if (!auth()->check())
    <script>window.location.href = "{{ route('login') }}";</script>
@endif
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice | McIntosh Cleaning</title>
</head>
<body style="font-family: sans-serif; background-color: #f4f7f6; padding: 40px; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h2 style="text-align: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 10px;">McIntosh Cleaning Service</h2>

        {{-- STEP 1: CUSTOMER SELECTION DROPDOWN --}}
        <div style="margin: 20px 0; padding: 15px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
            <label style="display:block; font-weight:700; margin-bottom:8px;">Select Existing Customer</label>

            <select id="customer_id" name="customer_id" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                <option value="">-- Choose a customer --</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}"
                        data-name="{{ e($c->name) }}"
                        data-email="{{ e($c->email ?? '') }}"
                        data-phone="{{ e($c->phone ?? '') }}"
                        data-company="{{ e($c->company_name ?? '') }}"
                        data-city_state_zip="{{ e($c->city_state_zip ?? '') }}"
                        @selected(isset($selectedCustomer) && $selectedCustomer->id === $c->id)
                    >
                        {{ $c->name }}@if($c->phone) ({{ $c->phone }})@endif
                    </option>
                @endforeach
            </select>

            <div style="font-size:12px; color:#6b7280; margin-top:8px;">
                Selecting a customer will auto-fill fields below. You can still edit them manually.
            </div>
        </div>

        {{-- STEP 2: INVOICE FORM --}}
        <form action="{{ route('invoice.send') }}" method="POST">
            @csrf

            <label style="font-weight:600;">Company Name:</label>
            <input id="company_name" name="company_name" type="text" style="width:100%; padding:10px; margin: 5px 0 15px 0; border-radius:6px; border:1px solid #ccc; box-sizing: border-box;">

            <label style="font-weight:600;">Customer Name *:</label>
            <input id="customer_name" name="customer_name" type="text" required style="width:100%; padding:10px; margin: 5px 0 15px 0; border-radius:6px; border:1px solid #ccc; box-sizing: border-box;">

            <label style="font-weight:600;">Customer Email *:</label>
            <input id="customer_email" name="customer_email" type="email" required style="width:100%; padding:10px; margin: 5px 0 15px 0; border-radius:6px; border:1px solid #ccc; box-sizing: border-box;">

            <label style="font-weight:600;">Customer Phone:</label>
            <input id="customer_phone" name="customer_phone" type="text" style="width:100%; padding:10px; margin: 5px 0 15px 0; border-radius:6px; border:1px solid #ccc; box-sizing: border-box;">

            <label style="font-weight:600;">Street Address:</label>
            <input id="street_address" name="street_address" type="text" style="width:100%; padding:10px; margin: 5px 0 15px 0; border-radius:6px; border:1px solid #ccc; box-sizing: border-box;">

            <label style="font-weight:600;">City, State, Zip:</label>
            <input id="city_state_zip" name="city_state_zip" type="text" style="width:100%; padding:10px; margin: 5px 0 15px 0; border-radius:6px; border:1px solid #ccc; box-sizing: border-box;">

            <div style="background: #fffbe6; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #ffe58f;">
                <p style="margin:0; font-size: 14px;"><strong>Standard Items:</strong> Stove Cleaning ($84), Refrigerator ($60), Tax ($55)</p>
            </div>

            <button type="submit" style="width:100%; background:#2563eb; color:white; padding:16px; border-radius:6px; border:none; font-weight:600; font-size: 16px; cursor: pointer;">
                Generate & Email PDF
            </button>
        </form>
    </div>

    {{-- STEP 3: AUTO-FILL SCRIPT --}}
    <script>
    (function () {
        const sel = document.getElementById('customer_id');
        if (!sel) return;

        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el && (val !== undefined) && (val !== null)) el.value = val;
        };

        const apply = () => {
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !sel.value) return;

            setVal('customer_name', opt.dataset.name || '');
            setVal('customer_email', opt.dataset.email || '');
            setVal('customer_phone', opt.dataset.phone || '');
            setVal('company_name', opt.dataset.company || '');
            setVal('city_state_zip', opt.dataset.city_state_zip || '');
        };

        sel.addEventListener('change', apply);

        // Auto-apply if a customer is already selected (e.g., from the redirect)
        if (sel.value) apply();
    })();
    </script>

</body>
</html>
