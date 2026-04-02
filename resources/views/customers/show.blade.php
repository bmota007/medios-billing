@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-8">

    <!-- HEADER -->
    <div class="bg-white shadow-md border border-gray-200 rounded-xl p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $customer->name }}
                </h1>

                @if($customer->company_name)
                    <p class="text-gray-500 mt-1">{{ $customer->company_name }}</p>
                @endif

                @if($customer->email)
                    <p class="text-gray-600 mt-3">{{ $customer->email }}</p>
                @endif

                @if($customer->phone)
                    <p class="text-gray-600">{{ $customer->phone }}</p>
                @endif
            </div>

            <div class="flex gap-3">
                <a href="{{ route('customers.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-lg font-semibold">
                    Back
                </a>

                <a href="{{ route('customers.edit', $customer->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg shadow font-semibold">
                    Edit Customer
                </a>
            </div>
        </div>
    </div>

    <!-- TAB NAVIGATION -->
    <div style="margin-top:25px;border-bottom:1px solid #ddd;padding-bottom:10px;">

        <a href="#summary" class="tab-link active" onclick="openTab(event,'summary')">
            Summary
        </a>

        <a href="#quotes" class="tab-link" onclick="openTab(event,'quotes')">
            Quotes
        </a>

        <a href="#invoices" class="tab-link" onclick="openTab(event,'invoices')">
            Invoices
        </a>

        <a href="#emails" class="tab-link" onclick="openTab(event,'emails')">
            Emails
        </a>

        <a href="#notes" class="tab-link" onclick="openTab(event,'notes')">
            Notes
        </a>

        <a href="#activity" class="tab-link" onclick="openTab(event,'activity')">
            Activity
        </a>

    </div>

<style>

.tab-link{
padding:10px 16px;
margin-right:6px;
background:#f3f4f6;
border-radius:6px;
text-decoration:none;
color:#374151;
font-weight:600;
cursor:pointer;
display:inline-block;
}

.tab-link.active{
background:#2563eb;
color:white;
}

.tab-content{
display:none;
margin-top:20px;
}

.tab-content.active{
display:block;
}

</style>

<!-- SUMMARY TAB -->
<div id="summary" class="tab-content active">

<div class="grid grid-cols-3 gap-6 mb-6 mt-6">

    <div class="bg-white shadow-md border border-gray-200 rounded-xl p-6">
        <p class="text-gray-500 text-sm">Quotes</p>
        <p class="text-3xl font-bold text-blue-700">
            {{ $customer->quotes->count() }}
        </p>
    </div>

    <div class="bg-white shadow-md border border-gray-200 rounded-xl p-6">
        <p class="text-gray-500 text-sm">Invoices</p>
        <p class="text-3xl font-bold text-green-700">
            {{ $customer->invoices->count() }}
        </p>
    </div>

    <div class="bg-white shadow-md border border-gray-200 rounded-xl p-6">
        <p class="text-gray-500 text-sm">Emails</p>
        <p class="text-3xl font-bold text-purple-700">0</p>
    </div>

</div>

</div>

<!-- QUOTES TAB -->
<div id="quotes" class="tab-content">

<h3 class="text-xl font-semibold mt-6 mb-4">Quotes</h3>

<a href="{{ route('quotes.create',['customer_id'=>$customer->id]) }}"
style="background:#2563eb;color:white;padding:8px 12px;border-radius:6px;text-decoration:none;">
+ New Quote
</a>

<br><br>

<table width="100%" border="1" cellpadding="10" style="border-collapse:collapse;">

<tr>
<th>Quote</th>
<th>Total</th>
<th>Status</th>
<th>Date</th>
<th>Action</th>
</tr>

@forelse($customer->quotes as $quote)

<tr>

<td>#{{ $quote->id }}</td>

<td>${{ number_format($quote->total,2) }}</td>

<td>{{ ucfirst($quote->status) }}</td>

<td>{{ $quote->created_at->format('M d, Y') }}</td>

<td>

<a href="{{ route('quotes.show',$quote->id) }}"
style="background:#2563eb;color:white;padding:6px 10px;border-radius:6px;text-decoration:none;">
View
</a>

</td>

</tr>

@empty

<tr>
<td colspan="5">No quotes yet.</td>
</tr>

@endforelse

</table>

</div>

<!-- INVOICES TAB -->
<div id="invoices" class="tab-content">

<h3 class="text-xl font-semibold mt-6 mb-4">Invoices</h3>

<table width="100%" border="1" cellpadding="10" style="border-collapse:collapse;">

<tr>
<th>Invoice</th>
<th>Total</th>
<th>Status</th>
<th>Date</th>
<th>Action</th>
</tr>

@forelse($customer->invoices as $invoice)

<tr>

<td>#{{ $invoice->id }}</td>

<td>${{ number_format($invoice->total,2) }}</td>

<td>{{ ucfirst($invoice->status) }}</td>

<td>{{ $invoice->created_at->format('M d, Y') }}</td>

<td>

<a href="{{ route('invoice.view',$invoice->id) }}"
style="background:#2563eb;color:white;padding:6px 10px;border-radius:6px;text-decoration:none;">
View
</a>

</td>

</tr>

@empty

<tr>
<td colspan="5">No invoices yet.</td>
</tr>

@endforelse

</table>

</div>

<!-- EMAILS TAB -->
<div id="emails" class="tab-content">

<h3 class="text-xl font-semibold mt-6 mb-4">Emails</h3>

<p>Email history will appear here.</p>

</div>

<!-- NOTES TAB -->
<div id="notes" class="tab-content">

<h3 class="text-xl font-semibold mt-6 mb-4">Notes</h3>

<p>Internal notes about the customer.</p>

</div>

<!-- ACTIVITY TAB -->
<div id="activity" class="tab-content">

<h3 class="text-xl font-semibold mt-6 mb-4">Activity</h3>

<p>Customer activity log will appear here.</p>

</div>

</div>

<script>

function openTab(evt, tabName) {

var tabs = document.getElementsByClassName("tab-content");

for (var i = 0; i < tabs.length; i++) {
tabs[i].classList.remove("active");
}

var links = document.getElementsByClassName("tab-link");

for (var i = 0; i < links.length; i++) {
links[i].classList.remove("active");
}

document.getElementById(tabName).classList.add("active");

evt.currentTarget.classList.add("active");

}

</script>

@endsection
