<div class="mb-card">
    <div style="font-size:18px;font-weight:800;margin-bottom:15px;">
        Client Messaging
    </div>

    <label class="mb-label">Customer Phone</label>
    <input type="text"
        value="{{ $customer->phone ?? '' }}"
        style="width:100%;background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#fff;border-radius:12px;padding:12px;margin-bottom:15px;">

    <label class="mb-label">SMS Preview</label>
    <textarea rows="3"
        style="width:100%;background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#fff;border-radius:12px;padding:12px;">
Hi {{ $customer->name ?? 'Customer' }}, your quote #{{ $quote->quote_number }} is ready:
{{ url('/quotes/public/'.$quote->id) }}
    </textarea>

    <button class="mb-btn mb-green" style="width:100%;margin-top:15px;">
        Send SMS
    </button>
</div>
