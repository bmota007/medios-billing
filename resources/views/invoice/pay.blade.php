<!DOCTYPE html>
<html>
<head>
    <title>Secure Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 min-h-screen">

<div class="min-h-screen grid lg:grid-cols-2">

    <!-- ============================== -->
    <!-- LEFT SIDE -->
    <!-- ============================== -->
    <div class="flex flex-col justify-center px-8 py-16 sm:px-12 lg:px-20 text-white bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800">

        <div class="max-w-xl">
            <p class="text-sky-400 uppercase tracking-[0.2em] text-xs font-bold mb-4">
                Secure Checkout
            </p>

            <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-6">
                Pay Your Invoice
            </h1>

            <p class="text-slate-300 text-lg leading-8 mb-10">
                Complete your payment securely for the invoice below.
            </p>

            <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-6 sm:p-8 shadow-2xl">
                <p class="text-slate-400 text-sm uppercase tracking-wide mb-2">Invoice Number</p>
                <p class="text-2xl font-bold mb-8">#{{ $invoice->invoice_no }}</p>

                <p class="text-slate-400 text-sm uppercase tracking-wide mb-2">Amount Due</p>
                <p class="text-5xl sm:text-6xl font-extrabold text-green-400 mb-8">
                    ${{ number_format($invoice->total, 2) }}
                </p>

                <div class="grid gap-3 text-sm text-slate-300">
                    <div class="flex items-center gap-3">
                        <span class="text-green-400">✔</span>
                        <span>SSL secure checkout</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-green-400">✔</span>
                        <span>Instant payment confirmation</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-green-400">✔</span>
                        <span>Powered by Medios Billing</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================== -->
    <!-- RIGHT SIDE -->
    <!-- ============================== -->
    <div class="flex items-center justify-center px-6 py-12 sm:px-10 lg:px-16 bg-white">

        <div class="w-full max-w-xl">
            <div class="rounded-3xl border border-slate-200 shadow-2xl p-8 sm:p-10 bg-white">

                <h2 class="text-3xl font-extrabold text-slate-900 mb-3">
                    Complete Payment
                </h2>

                <p class="text-slate-500 leading-7 mb-8">
                    Review the amount and continue to payment.
                </p>

                <div class="rounded-2xl bg-slate-50 border border-slate-200 p-6 mb-8">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-slate-500 font-medium">Invoice</span>
                        <span class="text-slate-900 font-bold">#{{ $invoice->invoice_no }}</span>
                    </div>

                    <div class="flex justify-between items-center mb-3">
                        <span class="text-slate-500 font-medium">Customer</span>
                        <span class="text-slate-900 font-bold">{{ $invoice->customer_name }}</span>
                    </div>

                    <div class="border-t border-slate-200 my-4"></div>

                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-slate-900">Amount Due</span>
                        <span class="text-3xl font-extrabold text-green-600">
                            ${{ number_format($invoice->total, 2) }}
                        </span>
                    </div>
                </div>

                <!-- ============================== -->
                <!-- PAYMENT ACTION -->
                <!-- ============================== -->
                <form method="GET" action="{{ route('invoice.checkout', $invoice->invoice_no) }}">

                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white py-5 rounded-2xl text-xl font-extrabold shadow-xl transition">
                        Pay ${{ number_format($invoice->total, 2) }}
                    </button>
                </form>

                <p class="text-sm text-slate-400 text-center mt-6">
                    Apple Pay / Google Pay / Card will be connected in the next Stripe step.
                </p>

            </div>
        </div>

    </div>

</div>

</body>
</html>
