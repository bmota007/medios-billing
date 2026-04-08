<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
public function index(Request $request)
{
    $user = auth()->user();

    $query = Customer::query()
        ->where('company_id', $user->company_id)
        ->withCount('invoices')
        ->withSum('invoices', 'total')
        ->withCount([
            'invoices as paid_invoices_count' => function ($q) {
                $q->where('status', 'paid');
            },
            'invoices as unpaid_invoices_count' => function ($q) {
                $q->where('status', '!=', 'paid');
            }
        ]);

    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('phone', 'like', '%' . $search . '%');
        });
    }

    $customers = $query->latest()->paginate(12)->withQueryString();

    return view('customers.index', compact('customers'));
}

    public function show(Customer $customer)
    {
        $user = Auth::user();

        if ($user->role !== 'super_admin' && (int) $customer->company_id !== (int) $user->company_id) {
            abort(403);
        }

        $customer->load(['quotes', 'invoices']);

        return view('customers.show', compact('customer'));
    }

    public function create()
    {
        return view('customers.create');
    }

public function store(Request $request)
{
    // 1. Validation (Prevents 500 error by catching duplicates before save)
    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:customers,email',
    ], [
        'email.unique' => 'This email is already registered in our system. Please use a different one.',
    ]);

    // 2. Safety Check: Ensure we have a company_id
    $user = auth()->user();
    $companyId = $user->company_id;

    // If the user has no company_id (like a broken super admin), 
    // try to find the Medios Billing company or the first available one.
    if (!$companyId) {
        $companyId = \App\Models\Company::where('name', 'Medios Billing')->first()?->id 
                     ?? \App\Models\Company::first()?->id;
    }

    if (!$companyId) {
        return back()->withInput()->with('error', 'No company found for this user. Please ensure Medios Billing company exists.');
    }

    // 3. Create the Customer with ALL fields (Syncing Database + Model)
    \App\Models\Customer::create([
        'company_id'      => $companyId,
        'name'            => $request->name,
        'company_name'    => $request->company_name ?? $request->name,
        'email'           => $request->email,
        'phone'           => $request->phone,
        'billing_address' => $request->billing_address ?? $request->street_address,
        'street_address'  => $request->street_address ?? $request->billing_address,
        'city'            => $request->city,
        'state'           => $request->state,
        'zip'             => $request->zip,
        'city_state_zip'  => $request->city_state_zip ?? trim(($request->city ?? '') . ' ' . ($request->state ?? '') . ' ' . ($request->zip ?? '')),
        'slug'            => \Illuminate\Support\Str::slug($request->name . '-' . time()),
    ]);

    return redirect()
        ->route('customers.index')
        ->with('success', 'Customer created successfully.');
}

    public function edit(Customer $customer)
    {
        $user = Auth::user();

        if ($user->role !== 'super_admin' && (int) $customer->company_id !== (int) $user->company_id) {
            abort(403);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();

        if ($user->role !== 'super_admin' && (int) $customer->company_id !== (int) $user->company_id) {
            abort(403);
        }

        $customer->update([
            'name'            => $request->name,
            'company_name'    => $request->company_name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'billing_address' => $request->street_address,
            'city'            => $request->city_state_zip,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();

        if ((int) $customer->company_id !== (int) $user->company_id && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully');
    }
}
