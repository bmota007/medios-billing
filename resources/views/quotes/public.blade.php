<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Approval | {{ $quote->company->name ?? 'Medios Billing' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
@php
    $brandColor = $quote->company->primary_color ?? '#0ea5e9';

    $isApproved = ($quote->status === 'approved');

    $nextStepMessage = 'After approval, the next step will be sent by email.';

    if (!empty($quote->contract_required) && $quote->contract_required) {
        $nextStepMessage = 'After approving this quote, the next step will be a contract sent by email for electronic signature.';
    } elseif (!empty($quote->deposit_type) && $quote->deposit_type !== 'none') {
        $nextStepMessage = 'After approving this quote, the next step will be an invoice for the required deposit, sent by email.';
    } else {
        $nextStepMessage = 'After approving this quote, the next step will be a follow-up email with the next instructions from the business.';
    }

    $approvedMessageTitle = 'This quote has already been approved.';
    $approvedMessageBody = 'Thank you. The business has been notified and will contact you with the next steps.';

    if (!empty($quote->contract_required) && $quote->contract_required) {
        $approvedMessageBody = 'Thank you. The business has been notified. The next step is a contract that will be sent to you by email for electronic signature.';
    } elseif (!empty($quote->deposit_type) && $quote->deposit_type !== 'none') {
        $approvedMessageBody = 'Thank you. The business has been notified. The next step is an invoice for the required deposit, which will be sent to you by email.';
    } else {
        $approvedMessageBody = 'Thank you. The business has been notified and will follow up with the next steps soon.';
    }
@endphp
<body class="bg-slate-100 min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-slate-200">
            <div class="px-8 py-8 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #020617 100%);">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div class="flex items-start gap-4">
                        @if(!empty($quote->company->logo_path))
                            <div class="bg-white rounded-2xl p-3 shadow-lg">
                                <img src="{{ asset('storage/' . $quote->company->logo_path) }}" alt="Company Logo" class="h-16 w-auto object-contain">
                            </div>
                        @endif

                        <div>
                            <p class="text-sky-400 uppercase tracking-[0.2em] text-xs font-bold mb-2">Quote Approval</p>
                            <h1 class="text-3xl md:text-4xl font-extrabold">
                                Quote #{{ $quote->quote_number }}
                            </h1>
                            <p class="text-slate-300 mt-2">
                                Please review the details below and approve this quote securely.
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="inline-block px-4 py-2 rounded-full text-sm font-bold
                            @if($quote->status === 'approved')
                                bg-emerald-500/20 text-emerald-300
                            @elseif($quote->status === 'sent')
                                bg-amber-500/20 text-amber-300
                            @else
                                bg-slate-500/20 text-slate-200
                            @endif
                        ">
                            {{ strtoupper($quote->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-8">
                @if(session('success'))
                    <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-700 font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                @if(!$isApproved)
                    <div class="mb-6 rounded-2xl border border-sky-200 bg-sky-50 px-5 py-4">
                        <h2 class="text-slate-900 font-bold text-lg mb-1">What happens after approval?</h2>
                        <p class="text-slate-600 leading-7">
                            {{ $nextStepMessage }}
                        </p>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500 mb-3">From</h2>
                        <p class="text-2xl font-bold text-slate-900">
                            {{ $quote->company->name ?? 'Company' }}
                        </p>

                        @if(!empty($quote->company->email))
                            <p class="text-slate-600 mt-3">{{ $quote->company->email }}</p>
                        @endif

                        @if(!empty($quote->company->phone))
                            <p class="text-slate-600">{{ $quote->company->phone }}</p>
                        @endif

                        @if(!empty($quote->company->address))
                            <p class="text-slate-600">{{ $quote->company->address }}</p>
                        @endif

                        @if(!empty($quote->company->website))
                            <p class="mt-2">
                                <a href="{{ $quote->company->website }}" target="_blank" class="font-medium underline" style="color: {{ $brandColor }};">
                                    {{ $quote->company->website }}
                                </a>
                            </p>
                        @endif
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-slate-500 mb-3">Prepared For</h2>
                        <p class="text-2xl font-bold text-slate-900">{{ $quote->customer->name ?? 'Customer' }}</p>

                        @if(!empty($quote->customer->email))
                            <p class="text-slate-600 mt-3">{{ $quote->customer->email }}</p>
                        @endif

                        @if(!empty($quote->customer->phone))
                            <p class="text-slate-600">{{ $quote->customer->phone }}</p>
                        @endif

                        @if(!empty($quote->customer->address))
                            <p class="text-slate-600">{{ $quote->customer->address }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-2xl p-5 border border-slate-200">
                        <p class="text-sm text-slate-500 font-semibold">Quote Date</p>
                        <p class="text-lg font-bold text-slate-900 mt-1">
                            {{ optional($quote->created_at)->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-slate-200">
                        <p class="text-sm text-slate-500 font-semibold">Valid Until</p>
                        <p class="text-lg font-bold text-slate-900 mt-1">
                            {{ !empty($quote->valid_until) ? \Carbon\Carbon::parse($quote->valid_until)->format('M d, Y') : optional($quote->due_date)->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-slate-200">
                        <p class="text-sm text-slate-500 font-semibold">Total</p>
                        <p class="text-2xl font-extrabold mt-1" style="color: {{ $brandColor }};">
                            ${{ number_format((float) $quote->total, 2) }}
                        </p>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[700px]">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="text-left px-6 py-4 text-sm font-bold text-slate-600">Service Description</th>
                                        <th class="text-center px-6 py-4 text-sm font-bold text-slate-600">Qty</th>
                                        <th class="text-right px-6 py-4 text-sm font-bold text-slate-600">Unit Price</th>
                                        <th class="text-right px-6 py-4 text-sm font-bold text-slate-600">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quote->items as $item)
                                        <tr class="border-t border-slate-100 align-top">
                                            <td class="px-6 py-4">
                                                <p class="font-semibold text-slate-900">{{ $item->service_name ?? 'Service Item' }}</p>
                                                @if(!empty($item->description))
                                                    <p class="text-sm text-slate-500 mt-1 leading-6">{{ $item->description }}</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center text-slate-700">
                                                {{ rtrim(rtrim(number_format((float) ($item->quantity ?? 0), 2), '0'), '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-right text-slate-700">
                                                ${{ number_format((float) ($item->unit_price ?? 0), 2) }}
                                            </td>
                                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                                ${{ number_format((float) ($item->line_total ?? 0), 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                                No quote items found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mb-8">
                    <div class="w-full max-w-md bg-slate-50 rounded-2xl p-6 border border-slate-200">
                        <div class="flex justify-between text-slate-600 mb-2">
                            <span>Subtotal</span>
                            <span>${{ number_format((float) ($quote->subtotal ?? $quote->total), 2) }}</span>
                        </div>

                        @if((float) ($quote->discount ?? 0) > 0)
                            <div class="flex justify-between text-slate-600 mb-2">
                                <span>Discount</span>
                                <span>-${{ number_format((float) ($quote->discount ?? 0), 2) }}</span>
                            </div>
                        @endif

                        @if((float) ($quote->deposit_amount ?? 0) > 0)
                            <div class="flex justify-between text-slate-600 mb-2">
                                <span>Deposit Due</span>
                                <span>${{ number_format((float) ($quote->deposit_amount ?? 0), 2) }}</span>
                            </div>
                        @endif

                        @if((float) ($quote->remaining_amount ?? 0) > 0 && ($quote->deposit_type ?? 'none') !== 'none')
                            <div class="flex justify-between text-slate-600 mb-2">
                                <span>Remaining Balance</span>
                                <span>${{ number_format((float) ($quote->remaining_amount ?? 0), 2) }}</span>
                            </div>
                        @endif

                        @if(!empty($quote->remaining_due_date))
                            <div class="flex justify-between text-slate-600 mb-2">
                                <span>Remaining Due Date</span>
                                <span>{{ \Carbon\Carbon::parse($quote->remaining_due_date)->format('M d, Y') }}</span>
                            </div>
                        @endif

                        <div class="border-t border-slate-300 my-3"></div>

                        <div class="flex justify-between text-xl font-extrabold text-slate-900">
                            <span>Total</span>
                            <span>${{ number_format((float) $quote->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 mb-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Important</h3>
                    <p class="text-slate-600 leading-7">
                        This quote is based on the current project scope and pricing provided by
                        {{ $quote->company->name ?? 'the company' }}.
                        Approval confirms that you would like to move forward with the proposed work.
                    </p>
                </div>

                @if(!$isApproved)
                    <form method="POST" action="{{ route('quotes.approve', $quote->public_token) }}" class="text-center">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-2xl text-white font-bold text-lg px-10 py-4 shadow-lg transition hover:opacity-90"
                            style="background: {{ $brandColor }};">
                            Approve Quote
                        </button>

                        <p class="text-sm text-slate-500 mt-4 max-w-2xl mx-auto leading-6">
                            By approving this quote, you confirm that you would like to proceed with the proposed service.
                            {{ $nextStepMessage }}
                        </p>
                    </form>
                @else
                    <div class="text-center rounded-2xl bg-emerald-50 border border-emerald-200 px-6 py-6">
                        <p class="text-2xl font-bold text-emerald-700">{{ $approvedMessageTitle }}</p>
                        <p class="text-emerald-600 mt-2 max-w-2xl mx-auto leading-7">{{ $approvedMessageBody }}</p>
                    </div>
                @endif
            </div>

            <div class="border-t border-slate-200 px-8 py-6 bg-white">
                <div class="text-center text-slate-500 text-sm">
                    <p class="font-semibold text-base text-slate-700 mb-1">{{ $quote->company->name ?? 'Company' }}</p>

                    @if(!empty($quote->company->address))
                        <p>{{ $quote->company->address }}</p>
                    @endif

                    @if(!empty($quote->company->phone) || !empty($quote->company->email))
                        <p>
                            {{ $quote->company->phone ?? '' }}
                            @if(!empty($quote->company->phone) && !empty($quote->company->email))
                                •
                            @endif
                            {{ $quote->company->email ?? '' }}
                        </p>
                    @endif

                    @if(!empty($quote->company->website))
                        <p>{{ $quote->company->website }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
