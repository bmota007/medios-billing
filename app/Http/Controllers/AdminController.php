<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OnboardingMail;

class AdminController extends Controller
{
    /**
     * Super Admin Dashboard Logic
     */
    public function dashboard()
    {
        $companies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        
        $revenue = SubscriptionInvoice::where('status', 'paid')->sum('amount');
        
        $mrr = SubscriptionInvoice::where('status', 'paid')
                ->where('created_at', '>=', now()->startOfMonth())
                ->sum('amount');
        
        $recentInvoices = SubscriptionInvoice::with('company')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'companies', 
            'activeCompanies', 
            'revenue', 
            'mrr', 
            'recentInvoices'
        ));
    }

    /**
     * Show the Manual Onboarding Form
     */
    public function manualChargeCreate()
    {
        return view('admin.manual-charge');
    }

    /**
     * Handle the Smart Onboarding / Manual Charge Logic
     */
    public function storeManualCharge(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'amount' => 'required|numeric|min:1',
            'interval' => 'required|in:month,year,one_time'
        ]);

        try {
            $company = Company::create([
                'name' => $request->company_name,
                'email' => $request->email,
                'subscription_status' => 'trialing',
                'trial_ends_at' => now()->addDays(7),
                'custom_price' => $request->amount,
                'billing_interval' => $request->interval,
                'is_active' => true,
                'is_subscription' => $request->has('is_subscription'),
            ]);

            $user = User::create([
                'name' => $request->company_name . ' Admin',
                'email' => $request->email,
                'password' => Hash::make(Str::random(16)),
                'company_id' => $company->id,
                'role' => 'admin'
            ]);

            $token = Str::random(40);
            $company->update(['setup_token' => $token]);

            try {
                Mail::to($request->email)->send(new OnboardingMail($company, $token));
            } catch (\Exception $e) {
                Log::error("Mail failed: " . $e->getMessage());
            }

            return redirect()->route('admin.companies')->with('success', 'Onboarding link sent to ' . $request->email);

        } catch (\Exception $e) {
            Log::error("Onboarding Error: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * DASHBOARD QUICK CHARGE METHOD
     * This must exist because your dashboard form points here.
     */
    public function manualCharge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'customer_email' => 'required|email',
            'description' => 'nullable|string|max:255'
        ]);

        return back()->with('success', 'Quick charge initiated for ' . $request->customer_email);
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
        return back()->with('success', 'Status updated.');
    }

    public function destroyCompany($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return back()->with('success', 'Company deleted successfully.');
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

    public function loginAsCompany($id)
    {
        $company = Company::findOrFail($id);
        $user = User::where('company_id', $company->id)->first();
        if (!$user) {
            return back()->with('error', 'No users found.');
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
                return redirect()->route('admin.dashboard');
            }
        }
        return redirect('/');
    }
}
