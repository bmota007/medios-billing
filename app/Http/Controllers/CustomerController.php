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
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->latest()->paginate(12)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();

        if (
            $user->role !== 'super_admin' &&
            (int) $customer->company_id !== (int) $user->company_id
        ) {
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
        // CLEAN VALIDATION
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email',
            'phone' => 'required|string|max:50',
        ], [
            'name.required'  => 'Customer name is required.',
            'email.required' => 'Email is required.',
            'email.email'    => 'Please enter a valid email.',
            'email.unique'   => 'This email already exists.',
            'phone.required' => 'Phone number is required.',
        ]);

        $user = auth()->user();
        $companyId = $user->company_id;

        // BACKUP SAFETY
        if (!$companyId) {
            $companyId = Company::where('name', 'Medios Billing')->value('id')
                ?? Company::min('id');
        }

        if (!$companyId) {
            return back()
                ->withInput()
                ->with('error', 'No company found for this account.');
        }

        $street = trim($request->street_address ?? '');
        $city   = trim($request->city ?? '');
        $state  = trim($request->state ?? '');
        $zip    = trim($request->zip ?? '');
        $csz    = trim($request->city_state_zip ?? '');

        Customer::create([
            'company_id'      => $companyId,
            'name'            => trim($request->name),
            'company_name'    => trim($request->company_name ?? $request->name),
            'email'           => strtolower(trim($request->email)),
            'phone'           => trim($request->phone),

            'billing_address' => $street,
            'street_address'  => $street,

            'city'            => $city,
            'state'           => $state,
            'zip'             => $zip,

            'city_state_zip'  => $csz,

            'slug' => Str::slug(trim($request->name) . '-' . time()),
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();

        if (
            $user->role !== 'super_admin' &&
            (int) $customer->company_id !== (int) $user->company_id
        ) {
            abort(403);
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();

        if (
            $user->role !== 'super_admin' &&
            (int) $customer->company_id !== (int) $user->company_id
        ) {
            abort(403);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:50',
        ]);

        $street = trim($request->street_address ?? '');
        $city   = trim($request->city ?? '');
        $state  = trim($request->state ?? '');
        $zip    = trim($request->zip ?? '');
        $csz    = trim($request->city_state_zip ?? '');

        $customer->update([
            'name'            => trim($request->name),
            'company_name'    => trim($request->company_name ?? $request->name),
            'email'           => strtolower(trim($request->email)),
            'phone'           => trim($request->phone),

            'billing_address' => $street,
            'street_address'  => $street,

            'city'            => $city,
            'state'           => $state,
            'zip'             => $zip,

            'city_state_zip'  => $csz,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();

        if (
            (int) $customer->company_id !== (int) $user->company_id &&
            $user->role !== 'super_admin'
        ) {
            abort(403, 'Unauthorized');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
