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
    /**
     * Dashboard
     */
    public function dashboard()
    {
        try {
            $companies = Company::count();
            $activeCompanies = Company::where('is_active', true)->count();

            $revenue = Invoice::where('status', 'paid')->sum('total') ?? 0;
            $mrr = Company::where('subscription_status', 'active')->sum('monthly_price') ?? 0;

            $recentInvoices = Invoice::latest()->take(5)->get();
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());

            $companies = 0;
            $activeCompanies = 0;
            $revenue = 0;
            $mrr = 0;
            $recentInvoices = collect();
        }

        return view('admin.dashboard', compact(
            'companies',
            'activeCompanies',
            'revenue',
            'mrr',
            'recentInvoices'
        ));
    }

    /**
     * Companies List
     */
    public function companies()
    {
        try {
            $companies = Company::latest()->get();
        } catch (\Exception $e) {
            \Log::error('Companies Page Error: ' . $e->getMessage());
            $companies = collect();
        }
return view('admin.companies.index', compact('companies'));
    }

    /**
     * Toggle Company Status
     */
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

    /**
     * Delete Company
     */
    public function destroyCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();

        return back()->with('success', 'Company deleted successfully.');
    }

    /**
     * Create Company View
     */
    public function createCompany()
    {
        return view('admin.companies.create');
    }

    /**
     * Store Company
     */
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

    /**
     * Billing
     */
    public function billing()
    {
        $stats = [
            'mrr'                  => SubscriptionInvoice::where('status', 'paid')->sum('amount'),
            'paid_subscriptions'   => SubscriptionInvoice::where('status', 'paid')->count(),
            'failed_subscriptions' => SubscriptionInvoice::where('status', 'failed')->count(),
            'active_companies'     => Company::where('subscription_status', 'active')->count(),
            'inactive_companies'   => Company::where('subscription_status', 'inactive')->count(),
        ];

        // ⚠️ REMOVED relationship to avoid crash
        $subscriptionInvoices = SubscriptionInvoice::latest()->get();

        return view('admin.billing', compact('stats', 'subscriptionInvoices'));
    }

    /**
     * Impersonate
     */
    public function loginAsCompany($id)
    {
        $company = Company::findOrFail($id);
        $user = User::where('company_id', $company->id)
            ->where('role', '!=', 'super_admin')
            ->first();

        if (!$user) {
            return back()->with('error', 'No users found for this company.');
        }

        session(['impersonator_id' => auth()->id()]);
        Auth::login($user);
        request()->session()->regenerate();

        return redirect('/dashboard')->with('success', 'Now viewing: ' . $company->name);
    }

    /**
     * Stop Impersonation
     */
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
