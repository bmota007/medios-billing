<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_companies'      => Company::count(),
            'active_companies'     => Company::where('subscription_status', 'active')->count(),
            'inactive_companies'   => Company::where('subscription_status', 'inactive')->count(),
            'total_invoices'       => Invoice::count(),
            'total_revenue'        => Invoice::where('status', 'paid')->sum('total'),
            'subscription_revenue' => SubscriptionInvoice::where('status', 'paid')->sum('amount'),
            'recent_invoices'      => Invoice::with('company')->latest()->take(10)->get(),
            'recent_subscriptions'=> SubscriptionInvoice::with('company')->latest()->take(10)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function companies(Request $request)
    {
        $query = Company::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('subscription_status', strtolower($request->status));
        }

        $companies = $query->withCount(['users', 'invoices'])
                           ->latest()
                           ->paginate(9)
                           ->withQueryString();

        return view('admin.companies.index', compact('companies'));
    }

    public function toggleStatus($id)
    {
        $company = Company::findOrFail($id);
        $isActiveNow = ($company->subscription_status === 'active');

        $company->subscription_status = $isActiveNow ? 'inactive' : 'active';
        $company->is_active = !$isActiveNow;

        if (!$isActiveNow) {
            $company->subscription_ends_at = now()->addMonth();
        }

        $company->save();

        return back()->with('success', 'Status updated for ' . $company->name);
    }

    public function destroyCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return back()->with('success', 'Company deleted successfully.');
    }

    public function createCompany()
    {
        return view('admin.companies.create');
    }

    public function storeCompany(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:companies,email',
            'phone'    => 'nullable|string',
            'plan'     => 'required|string',
            'mrr'      => 'nullable|numeric',
            'industry' => 'nullable|string',
        ]);

        $company = Company::create($validated);

        User::create([
            'name'       => $company->name . ' Admin',
            'email'      => 'admin@' . strtolower(str_replace(' ', '', $company->name)) . '.com',
            'password'   => bcrypt('password123'),
            'company_id' => $company->id,
            'role'       => 'admin',
        ]);

        return redirect()->route('admin.companies')->with('success', 'Company + Admin user created.');
    }

    public function billing()
    {
        $stats = [
            'mrr'                  => SubscriptionInvoice::where('status', 'paid')->sum('amount'),
            'paid_subscriptions'   => SubscriptionInvoice::where('status', 'paid')->count(),
            'failed_subscriptions' => SubscriptionInvoice::where('status', 'failed')->count(),
            'active_companies'     => Company::where('subscription_status', 'active')->count(),
            'inactive_companies'   => Company::where('subscription_status', 'inactive')->count(),
        ];

        $subscriptionInvoices = SubscriptionInvoice::with('company')->latest()->paginate(20);

        return view('admin.billing', compact('stats', 'subscriptionInvoices'));
    }

    public function brand()
    {
        $user = auth()->user();
        return view('admin.brand', compact('user'));
    }

    public function updateBrand(Request $request)
    {
        $user = auth()->user();

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $user->logo = $path;
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:6']);
            $user->password = bcrypt($request->password);
        }

        $user->save();
        return back()->with('success', 'Platform updated successfully');
    }

    public function loginAsCompany($id)
    {
        $company = Company::findOrFail($id);
        $user = User::where('company_id', $company->id)->first();

        if (!$user) {
            return back()->with('error', 'No users found for this company.');
        }

        session(['impersonator_id' => auth()->id()]);
        auth()->login($user);
        request()->session()->regenerate();

        return redirect('/dashboard')->with('success', 'Now viewing: ' . $company->name);
    }

    public function stopImpersonating()
    {
        $adminId = session('impersonator_id');
        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin) {
                Auth::login($admin);
                session()->forget('impersonator_id');
                session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Returned to admin.');
            }
        }
        return redirect('/');
    }
}
