<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->status === 'paid' ? 'Receipt' : 'Invoice' }} #{{ $invoice->invoice_no }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="py-12 px-4">

<div class="max-w-4xl mx-auto">
    <div class="glass rounded-3xl shadow-2xl overflow-hidden">
        
        <div class="p-8 border-b border-slate-700 flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-black tracking-tighter uppercase">
                    {{ $invoice->status === 'paid' ? 'Receipt' : 'Invoice' }}
                </h1>
                <p class="text-slate-400 mt-1">#{{ $invoice->invoice_no }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-400 uppercase tracking-widest">
                    {{ $invoice->status === 'paid' ? 'Amount Paid' : 'Amount Due' }}
                </p>
                <p class="text-4xl font-bold text-sky-400">${{ number_format($invoice->total, 2) }}</p>
            </div>
        </div>

        <div class="p-8">
            <div class="grid md:grid-cols-2 gap-8 mb-12">
                <div class="glass p-6 rounded-2xl">
                    <p class="text-xs font-bold text-sky-400 uppercase tracking-widest mb-2">Billed To</p>
                    <h2 class="text-2xl font-bold">{{ $invoice->customer_name }}</h2>
                    <p class="text-slate-400">{{ $invoice->customer_email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-slate-400">Date: <strong>{{ $invoice->invoice_date->format('M d, Y') }}</strong></p>
                    <div class="mt-4">
                        <span class="px-4 py-1 rounded-full {{ $invoice->status === 'paid' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-orange-500/20 text-orange-400 border-orange-500/30' }} text-xs font-bold uppercase border">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-400 text-xs uppercase tracking-widest border-b border-slate-700">
                            <th class="py-4 px-2">Description</th>
                            <th class="py-4 px-2 text-center">Qty</th>
                            <th class="py-4 px-2 text-right">Price</th>
                            <th class="py-4 px-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @php 
                            $items = is_string($invoice->items) ? json_decode($invoice->items, true) : $invoice->items;
                        @endphp
                        
                        @foreach($items as $item)
                        <tr class="text-slate-300">
                            <td class="py-6 px-2 font-medium">{{ $item['service_name'] ?? ($item['desc'] ?? 'Service Item') }}</td>
                            <td class="py-6 px-2 text-center">{{ $item['quantity'] ?? ($item['qty'] ?? 1) }}</td>
                            <td class="py-6 px-2 text-right">${{ number_format($item['unit_price'] ?? ($item['price'] ?? 0), 2) }}</td>
                            <td class="py-6 px-2 text-right font-bold text-white">${{ number_format($item['line_total'] ?? ($item['total'] ?? 0), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-12 flex justify-end">
                <div class="glass p-8 rounded-3xl w-full md:w-96">
                    <div class="flex justify-between text-slate-400 mb-2">
                        <span>Project Total</span>
                        <span>${{ number_format($invoice->subtotal, 2) }}</span>
                    </div>

                    @if($invoice->status === 'paid')
                        <div class="flex justify-between text-emerald-400 mb-2 font-bold">
                            <span>Total Paid</span>
                            <span>${{ number_format($invoice->total, 2) }}</span>
                        </div>
                        
                        @php $balance = $invoice->subtotal - $invoice->total; @endphp
                        
                        <div class="flex justify-between items-center pt-4 border-t border-slate-700">
                            <span class="text-xl font-bold">Remaining Balance</span>
                            <span class="text-2xl font-black {{ $balance <= 0 ? 'text-emerald-400' : 'text-white' }}">
                                {{ $balance <= 0 ? 'PAID IN FULL' : '$' . number_format($balance, 2) }}
                            </span>
                        </div>
                    @else
                        <div class="flex justify-between items-center pt-4 border-t border-slate-700">
                            <span class="text-xl font-bold">Due Now</span>
                            <span class="text-3xl font-black text-white">${{ number_format($invoice->total, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($invoice->status !== 'paid')
            <div class="mt-12">
                <a href="{{ route('invoice.pay', $invoice->invoice_no) }}" class="block w-full bg-sky-500 hover:bg-sky-400 text-white font-black text-center py-5 rounded-2xl shadow-lg transition-all uppercase tracking-widest flex items-center justify-center gap-3">
                    Pay Now
                </a>
            </div>
            @else
            <div class="mt-12 text-center">
                <p class="text-slate-500 text-sm italic">Thank you for your business! A copy of this receipt has been sent to your email.</p>
            </div>
            @endif
        </div>
    </div>
</div>

</body>
</html>
