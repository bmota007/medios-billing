<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-900 min-h-screen flex items-center justify-center">

<div class="bg-white rounded-3xl shadow-2xl p-10 max-w-lg w-full text-center">

    <div class="text-green-500 text-6xl mb-6">
        ✔
    </div>

    <h1 class="text-3xl font-extrabold text-gray-800 mb-4">
        Payment Successful
    </h1>

    <p class="text-gray-500 mb-6">
        Thank you! Your payment has been processed successfully.
    </p>

    <div class="bg-slate-100 rounded-xl p-4 mb-6">
        <p class="text-sm text-gray-500">Invoice</p>
        <p class="font-bold text-lg">#{{ $invoice->invoice_no }}</p>

        <p class="text-sm text-gray-500 mt-2">Amount Paid</p>
        <p class="text-2xl font-extrabold text-green-600">
            ${{ number_format($invoice->total, 2) }}
        </p>
    </div>

    <a href="{{ route('invoice.public_view', $invoice->invoice_no) }}"
       class="block w-full bg-slate-900 text-white py-3 rounded-xl font-bold mb-3">
       View Invoice
    </a>

</div>

</body>
</html>
