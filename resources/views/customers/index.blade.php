@extends('layouts.app')

@section('content')
<div style="padding:30px;">
    <h1>Customers</h1>

    <a href="{{ route('customers.create') }}"
       style="background:#10b981;
              color:white;
              padding:8px 14px;
              border-radius:6px;
              text-decoration:none;
              font-weight:500;">
        + New Customer
    </a>

    <br><br>

    <form method="GET" action="{{ route('customers.index') }}">
        <input type="text"
               name="search"
               placeholder="Search..."
               value="{{ request('search') }}"
               style="padding:6px; width:220px;">
        <button type="submit"
                style="padding:6px 10px;">
            Search
        </button>
    </form>

    <br>

    <table width="100%" border="1" cellpadding="10" style="border-collapse:collapse;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th width="220">Actions</th>
            </tr>
        </thead>

        <tbody>
        @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone }}</td>
                <td>

                    <!-- GREEN EDIT BUTTON -->
                    <a href="{{ route('customers.edit', $customer->id) }}"
                       style="background:#10b981;
                              color:white;
                              padding:6px 10px;
                              border-radius:6px;
                              text-decoration:none;
                              font-size:13px;
                              margin-right:8px;">
                        Edit
                    </a>

                    <!-- BLUE CREATE INVOICE BUTTON -->
                    <a href="{{ route('invoice.form', ['customer_id' => $customer->id]) }}"
                       style="background:#2563eb;
                              color:white;
                              padding:6px 10px;
                              border-radius:6px;
                              text-decoration:none;
                              font-size:13px;">
                        Create Invoice
                    </a>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px;">
                    No customers found.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <br>

    {{ $customers->links() }}

</div>
@endsection
