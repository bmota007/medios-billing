<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $brandName = $user->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();

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

        return view('customers.index', compact('customers', 'brandName', 'greeting'));
    }

    /**
     * Show a specific customer profile.
     */
    public function show(Customer $customer)
    {
        $user = Auth::user();

        // Security check
        if ($user->role !== 'super_admin' && (int) $customer->company_id !== (int) $user->company_id) {
            abort(403);
        }

        $customer->load(['quotes', 'invoices']);

        // Required variables for layouts.admin
        $brandName = $user->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();

        return view('customers.show', compact('customer', 'brandName', 'greeting'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $user = auth()->user();
        $brandName = $user->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();

        return view('customers.create', compact('brandName', 'greeting'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
        ], [
            'email.unique' => 'This email is already registered in our system.',
        ]);

        $user = auth()->user();
        $companyId = $user->company_id;

        if (!$companyId) {
            $companyId = Company::where('name', 'Medios Billing')->first()?->id ?? Company::first()?->id;
        }

        if (!$companyId) {
            return back()->withInput()->with('error', 'Company context missing.');
        }

        Customer::create([
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
            'slug'            => Str::slug($request->name . '-' . time()),
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Show the form for editing the customer.
     */
    public function edit(Customer $customer)
    {
        $user = Auth::user();

        if ($user->role !== 'super_admin' && (int) $customer->company_id !== (int) $user->company_id) {
            abort(403);
        }

        $brandName = $user->company->name ?? 'Medios Billing';
        $greeting = $this->getGreeting();

        return view('customers.edit', compact('customer', 'brandName', 'greeting'));
    }

    /**
     * Update the customer in storage.
     */
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

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $user = Auth::user();

        if ((int) $customer->company_id !== (int) $user->company_id && $user->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    /**
     * Helper to get greeting based on time of day
     */
    private function getGreeting() 
    {
        $hour = date('H');
        if ($hour < 12) return 'Good Morning';
        if ($hour < 17) return 'Good Afternoon';
        return 'Good Evening';
    }
}
