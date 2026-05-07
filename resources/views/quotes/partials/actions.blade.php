<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:10px;">

    <div>
        <a href="{{ route('quotes.index') }}" style="color:white;font-weight:700;margin-right:15px;text-decoration:none;">
            ← Back
        </a>

        <a href="{{ route('quotes.edit', $quote->id) }}" style="color:white;font-weight:700;text-decoration:none;">
            ✏️ Edit Quote
        </a>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;">

        <button style="background:#0ea5e9;padding:10px 16px;border:none;border-radius:10px;color:white;font-weight:800;">
            Send Quote
        </button>

        <button style="background:#4f46e5;padding:10px 16px;border:none;border-radius:10px;color:white;font-weight:800;">
            Download PDF
        </button>

        <button style="background:#334155;padding:10px 16px;border:none;border-radius:10px;color:white;font-weight:800;">
            Copy Link
        </button>

        <button style="background:#14b8a6;padding:10px 16px;border:none;border-radius:10px;color:white;font-weight:800;">
            Send SMS
        </button>

    </div>

</div>
