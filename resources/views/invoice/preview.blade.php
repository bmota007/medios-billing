@if (!auth()->check())
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
@endif

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ isset($invoice_no) ? 'Invoice' : 'Invoice Preview' }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
  body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; }
  .topbar {
    background:#0657bd;
    color:#fff;
    padding:16px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
  }
  .actions { display:flex; gap:10px; align-items:center; }
  .btn {
    padding:8px 14px;
    font-size:14px;
    border:none;
    cursor:pointer;
    border-radius:4px;
    text-decoration:none;
    display:inline-block;
  }
  .btn-back { background:#ccc; color:#000; }
  .btn-send { background:#f8d676; color:#000; font-weight:bold; }
  .btn-success { background:#28a745; color:#fff; font-weight:bold; }
  .btn-danger { background:#dc3545; color:#fff; }
  .btn-exit { background:#999; color:#fff; }
  .pdf-wrap { padding:20px; height:calc(100vh - 70px); }
  iframe { width:100%; height:100%; border:none; background:#fff; }
</style>
</head>

<body>

<div class="topbar">
  <h1>
      {{ isset($invoice_no) ? 'Invoice #' . $invoice_no : 'Invoice Preview' }}
  </h1>

  <div class="actions">

    {{-- ===============================
         PREVIEW MODE (NOT SAVED YET)
    =============================== --}}
    @if(!isset($invoiceId))

        {{-- BACK --}}
        <form method="POST" action="{{ route('invoice.form') }}">
            @csrf

            <input type="hidden" name="customer_name" value="{{ $customer_name }}">
            <input type="hidden" name="customer_email" value="{{ $customer_email }}">
            <input type="hidden" name="street_address" value="{{ $street_address }}">
            <input type="hidden" name="city_state_zip" value="{{ $city_state_zip }}">
            <input type="hidden" name="invoice_date" value="{{ $invoice_date }}">
            <input type="hidden" name="due_date" value="{{ $due_date }}">
            <input type="hidden" name="notes" value="{{ $notes ?? '' }}">

            @foreach($items as $i => $it)
                <input type="hidden" name="items[{{ $i }}][desc]" value="{{ $it['desc'] }}">
                <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $it['qty'] }}">
                <input type="hidden" name="items[{{ $i }}][price]" value="{{ $it['price'] }}">
            @endforeach

            <button class="btn btn-back">Back</button>
        </form>

        {{-- SEND --}}
        <form method="POST" action="{{ route('invoice.send') }}">
            @csrf

            <input type="hidden" name="customer_name" value="{{ $customer_name }}">
            <input type="hidden" name="customer_email" value="{{ $customer_email }}">
            <input type="hidden" name="street_address" value="{{ $street_address }}">
            <input type="hidden" name="city_state_zip" value="{{ $city_state_zip }}">
            <input type="hidden" name="invoice_date" value="{{ $invoice_date }}">
            <input type="hidden" name="due_date" value="{{ $due_date }}">
            <input type="hidden" name="notes" value="{{ $notes ?? '' }}">

            @foreach($items as $i => $it)
                <input type="hidden" name="items[{{ $i }}][desc]" value="{{ $it['desc'] }}">
                <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $it['qty'] }}">
                <input type="hidden" name="items[{{ $i }}][price]" value="{{ $it['price'] }}">
            @endforeach

            <button class="btn btn-send">Send Invoice</button>
        </form>

{{-- ===============================
     SAVED INVOICE MODE
=============================== --}}
@else

    {{-- VIEW INVOICE --}}
    @if(isset($invoiceId))
        <a href="{{ route('invoice.view', $invoiceId) }}" class="btn btn-back">
            View Invoice
        </a>
    @endif

    {{-- VIEW RECEIPT (ONLY IF PAID) --}}
    @if(isset($status) && strtolower($status) === 'paid' && isset($invoiceId))
        <a href="{{ route('invoice.receipt', $invoiceId) }}" class="btn btn-success">
            View Receipt
        </a>
    @endif

    {{-- MARK PAID (ONLY IF UNPAID) --}}
    @if(isset($status) && strtolower($status) === 'unpaid' && isset($invoiceId))
        <form method="POST" action="{{ route('invoice.markPaid', $invoiceId) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-success">
                Mark Paid
            </button>
        </form>
    @endif

    {{-- RESEND RECEIPT (ONLY IF PAID) --}}
    @if(isset($status) && strtolower($status) === 'paid' && isset($invoiceId))
        <form method="POST" action="{{ route('invoice.resendReceipt', $invoiceId) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-success">
                Resend Receipt
            </button>
        </form>
    @endif

    {{-- DELETE INVOICE --}}
    @if(isset($invoiceId))
        <form method="POST" action="{{ route('invoice.destroy', $invoiceId) }}"
              style="display:inline;"
              onsubmit="return confirm('Are you sure you want to delete this invoice?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-exit">
                Delete
            </button>
        </form>
    @endif

@endif

{{-- EXIT --}}
<a href="{{ route('invoice.history') }}" class="btn btn-exit">
    Exit
</a>

</div>
</div>

<div class="pdf-wrap">
    <iframe src="data:application/pdf;base64,{{ $pdf_base64 }}"></iframe>
</div>

</body>
</html>
