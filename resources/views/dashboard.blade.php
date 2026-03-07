<x-app-layout>

<x-slot name="header">
<h2 class="font-semibold text-2xl text-gray-800 leading-tight">
{{ current_company()->name }} Dashboard
</h2>
</x-slot>


<div class="py-8">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">


<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

<div class="bg-white shadow-lg rounded-xl p-6">
<p class="text-gray-500 text-sm">Total Revenue</p>
<p class="text-3xl font-bold text-green-600">${{ number_format($revenue,2) }}</p>
</div>

<div class="bg-white shadow-lg rounded-xl p-6">
<p class="text-gray-500 text-sm">Invoices</p>
<p class="text-3xl font-bold">{{ $invoices }}</p>
</div>

<div class="bg-white shadow-lg rounded-xl p-6">
<p class="text-gray-500 text-sm">Paid</p>
<p class="text-3xl font-bold text-green-600">{{ $paidInvoices }}</p>
</div>

<div class="bg-white shadow-lg rounded-xl p-6">
<p class="text-gray-500 text-sm">Pending</p>
<p class="text-3xl font-bold text-orange-500">{{ $pendingInvoices }}</p>
</div>

</div>


<div class="bg-white shadow-lg rounded-xl p-6">

<h3 class="text-lg font-semibold mb-4">Recent Invoices</h3>

<table class="w-full text-left">

<thead>

<tr class="border-b">

<th class="py-2">Invoice</th>
<th>Customer</th>
<th>Amount</th>
<th>Status</th>

</tr>

</thead>

<tbody>

@foreach($recentInvoices as $invoice)

<tr class="border-b">

<td>#{{ $invoice->id }}</td>

<td>{{ $invoice->customer_name }}</td>

<td>${{ number_format($invoice->total,2) }}</td>

<td>

@if($invoice->status == 'paid')

<span class="text-green-600 font-semibold">Paid</span>

@else

<span class="text-orange-600 font-semibold">Pending</span>

@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>


</div>
</div>

</x-app-layout>
