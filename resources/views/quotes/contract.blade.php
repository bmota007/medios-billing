<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract | {{ optional($quote->company)->name ?? 'Company' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4">

@php use Illuminate\Support\Str; @endphp

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200">
        
        <div class="px-8 py-8 text-white bg-slate-900">
            <h1 class="text-3xl font-extrabold">Contract Signature</h1>
            <p class="text-slate-300 mt-2">Quote #{{ $quote->quote_number }}</p>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-700 font-medium">
                    {{ session('success') }}
                </div>
            @endif

{{-- CONTRACT DISPLAY (DYNAMIC) --}}
<div class="mb-8">
    <h2 class="text-xl font-bold mb-2">Review Contract Before Signing</h2>
    <p class="text-sm text-slate-500 mb-4">
        Please review the agreement carefully before signing below.
    </p>

    {{-- Since we cleared the path in Tinker, the system will now jump to @else --}}
    @if(!empty($quote->company->contract_template_path))
        <iframe 
            src="https://docs.google.com/viewer?url={{ urlencode(asset('storage/' . $quote->company->contract_template_path)) }}&embedded=true" 
            class="w-full h-[600px] rounded-xl border border-slate-300 shadow-sm">
        </iframe>
    @else
        {{-- LOAD THE NEW DYNAMIC BLADE TEMPLATE --}}
        <div class="p-8 border border-slate-200 rounded-xl bg-white shadow-sm">
            @include('contracts.template', ['quote' => $quote])
        </div>
    @endif
</div>

            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500 mb-3">Company</h3>
                    <p class="text-xl font-bold text-slate-900">{{ optional($quote->company)->name }}</p>
                    <p class="text-slate-600 mt-2">{{ optional($quote->company)->email }}</p>
                    <p class="text-slate-600">{{ optional($quote->company)->phone }}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500 mb-3">Customer</h3>
                    <p class="text-xl font-bold text-slate-900">{{ optional($quote->customer)->name }}</p>
                    <p class="text-slate-600 mt-2">{{ optional($quote->customer)->email }}</p>
                    <p class="text-slate-600">{{ optional($quote->customer)->phone }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 mb-8">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Quote Summary</h3>
                <p class="text-3xl font-extrabold text-sky-600">${{ number_format((float) $quote->total, 2) }}</p>
                @if($quote->deposit_amount > 0)
                    <p class="text-slate-600 mt-3">Deposit Due: <strong>${{ number_format($quote->deposit_amount, 2) }}</strong></p>
                    <p class="text-slate-600">Remaining Balance: <strong>${{ number_format($quote->remaining_amount, 2) }}</strong></p>
                @endif
            </div>

            <form method="POST" action="{{ route('quotes.contract.sign', $quote->public_token) }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Name for Signature</label>
                    <input type="text" name="sign_name" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="Enter full name">
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full md:w-auto rounded-2xl bg-sky-600 text-white font-bold text-lg px-10 py-4 shadow-lg hover:bg-sky-700">
                        Sign Contract & Continue to Invoice
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
</body>
</html>
