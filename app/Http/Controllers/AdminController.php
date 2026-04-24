<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Models\SubscriptionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Lead;


class AdminController extends Controller
{
    /**
     * Dashboard
     */
public function dashboard()
{
    try {

        // CORE COMPANY METRICS
$totalCompanies = Company::count();

        $activeCompanies = Company::where(function ($q) {
            $q->where('subscription_status', 'active')
              ->orWhere('is_active', 1);
        })->count();
$leads = Lead::latest()->take(5)->get();

$newLeadsCount = Lead::where('status', 'new')->count();

$quotesSentCount = Lead::where('status', 'quote_sent')->count();

$pipelineValue = Lead::whereNotIn('status', ['won', 'lost'])->sum('value');

$followUpsCount = Lead::whereNotNull('follow_up_date')
    ->whereDate('follow_up_date', '<=', now()->toDateString())
    ->count();

        // PLATFORM BILLING ONLY (MEDIOS BILLING TO TENANTS)
        $platformInvoices = SubscriptionInvoice::latest()->take(5)->get();

        $mrr = (float) SubscriptionInvoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $totalRevenue = (float) SubscriptionInvoice::where('status', 'paid')
            ->sum('amount');

        $lastMonthMrr = (float) SubscriptionInvoice::where('status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $growthRate = $lastMonthMrr > 0
            ? (($mrr - $lastMonthMrr) / $lastMonthMrr) * 100
            : 0;

        $totalSubscriptions = Company::whereIn('subscription_status', [
            'active',
            'trialing',
            'past_due',
            'cancelled'
        ])->count();

        $subscriptionStats = [
            'active'    => Company::where('subscription_status', 'active')->count(),
            'past_due'  => Company::where('subscription_status', 'past_due')->count(),
            'trial'     => Company::where('subscription_status', 'trialing')->count(),
            'cancelled' => Company::where('subscription_status', 'cancelled')->count(),
        ];

$platformHealth = $totalCompanies > 0
    ? round(($activeCompanies / max($totalCompanies, 1)) * 100, 0)
    : 100;

        // STRIPE HEALTH
        $healthyStripeCount = Company::where(function ($q) {

            $q->where(function ($x) {
                $x->where('stripe_mode', 'live')
                  ->whereNotNull('stripe_publishable_key')
                  ->whereNotNull('stripe_secret_key')
                  ->whereNotNull('stripe_webhook_secret');
            })

            ->orWhere(function ($x) {
                $x->where('stripe_mode', 'test')
                  ->whereNotNull('stripe_test_publishable_key')
                  ->whereNotNull('stripe_test_secret_key');
            });

        })->count();

        $stripeHealthRows = Company::select(
                'id',
                'name',
                'email',
                'stripe_mode',
                'stripe_publishable_key',
                'stripe_secret_key',
                'stripe_webhook_secret',
                'stripe_test_publishable_key',
                'stripe_test_secret_key',
                'updated_at'
            )
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($company) {

                $mode = $company->stripe_mode ?: 'test';

                $hasKeys = $mode === 'live'
                    ? (!empty($company->stripe_publishable_key) && !empty($company->stripe_secret_key))
                    : (!empty($company->stripe_test_publishable_key) && !empty($company->stripe_test_secret_key));

                $hasWebhook = $mode === 'live'
                    ? !empty($company->stripe_webhook_secret)
                    : true;

                $status = 'Healthy';

                if (!$hasKeys) {
                    $status = 'Missing Keys';
                } elseif (!$hasWebhook) {
                    $status = 'No Webhook';
                } elseif ($mode === 'test') {
                    $status = 'Test Mode';
                }

                return [
                    'id'          => $company->id,
                    'name'        => $company->name,
                    'email'       => $company->email,
                    'mode'        => ucfirst($mode),
                    'webhook'     => $hasWebhook ? 'Connected' : 'Missing',
                    'last_update' => optional($company->updated_at)->format('M d, Y g:i A'),
                    'status'      => $status,
                ];
            });


            // MONTHLY PLATFORM REVENUE CHART
$monthlyRevenueRaw = SubscriptionInvoice::selectRaw('MONTH(invoice_date) as month_num, SUM(amount) as total')
    ->whereYear('invoice_date', now()->year)
    ->where('status', 'paid')
    ->groupBy('month_num')
    ->orderBy('month_num')
    ->pluck('total', 'month_num')
    ->toArray();

$monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

$monthlyRevenue = [];

for ($m = 1; $m <= 12; $m++) {
    $monthlyRevenue[] = (float) ($monthlyRevenueRaw[$m] ?? 0);
}

// RECENT PLATFORM ACTIVITY (NO TENANT CUSTOMER PRIVACY)
$recentCompanies = Company::latest()
    ->take(3)
    ->get()
    ->map(function ($company) {
        return [
            'icon'  => 'building',
            'title' => 'New company "' . $company->name . '" was onboarded',
            'time'  => optional($company->created_at)->diffForHumans(),
        ];
    });

$recentRenewals = SubscriptionInvoice::where('status', 'paid')
    ->latest()
    ->take(3)
    ->get()
    ->map(function ($invoice) {
        return [
            'icon'  => 'receipt',
            'title' => 'Subscription payment received from ' . ($invoice->customer_name ?: 'Tenant Company'),
            'time'  => optional($invoice->created_at)->diffForHumans(),
        ];
    });

$recentActivity = $recentCompanies
    ->merge($recentRenewals)
    ->sortByDesc('time')
    ->take(5)
    ->values();

            // QUICK INSIGHTS
// QUICK INSIGHTS
$mostActiveCompany = Company::where('is_active', true)
    ->latest()
    ->first();

$bestPlan = Company::select('plan_name', DB::raw('COUNT(*) as total'))
    ->whereNotNull('plan_name')
    ->groupBy('plan_name')
    ->orderByDesc('total')
    ->first();

$churnRate  = 2.1;

} catch (\Exception $e) {

    \Log::error('Admin Dashboard Error: ' . $e->getMessage());

    $totalCompanies = 0;
    $activeCompanies = 0;
    $platformInvoices = collect();

    $mrr = 0;
    $totalRevenue = 0;
    $totalSubscriptions = 0;

    $subscriptionStats = [
        'active' => 0,
        'past_due' => 0,
        'trial' => 0,
        'cancelled' => 0,
    ];

    $platformHealth = 100;
    $healthyStripeCount = 0;

    $stripeHealthRows = collect();

    $monthLabels = [
        'Jan','Feb','Mar','Apr','May','Jun',
        'Jul','Aug','Sep','Oct','Nov','Dec'
    ];

    $monthlyRevenue = array_fill(0, 12, 0);

    $recentActivity = collect();

    $mostActiveCompany = null;
    $bestPlan = null;

    $growthRate = 0;
    $churnRate = 0;
}

return view('admin.dashboard', compact(
    'totalCompanies',
    'activeCompanies',
    'platformInvoices',
    'mrr',
    'totalRevenue',
    'totalSubscriptions',
    'subscriptionStats',
    'platformHealth',
    'healthyStripeCount',
    'stripeHealthRows',
    'monthLabels',
    'monthlyRevenue',
    'recentActivity',
    'mostActiveCompany',
'leads',
'newLeadsCount',
'quotesSentCount',
'pipelineValue',
'followUpsCount',
    'bestPlan',
    'growthRate',
    'churnRate'
));
}

/**
 * Companies List
 */

public function storeLead(Request $request)
{
    $validated = $request->validate([
        'business_name'  => 'required|string|max:255',
        'contact_name'   => 'nullable|string|max:255',
        'email'          => 'nullable|email|max:255',
        'phone'          => 'nullable|string|max:255',
        'source'         => 'nullable|string|max:255',
        'notes'          => 'nullable|string',
        'status'         => 'nullable|string|max:50',
        'value'          => 'nullable|numeric|min:0',
        'follow_up_date' => 'nullable|date',
    ]);

    Lead::create([
        'business_name'  => $validated['business_name'],
        'contact_name'   => $validated['contact_name'] ?? null,
        'email'          => $validated['email'] ?? null,
        'phone'          => $validated['phone'] ?? null,
        'source'         => $validated['source'] ?? null,
        'notes'          => $validated['notes'] ?? null,
        'status'         => $validated['status'] ?? 'new',
        'value'          => $validated['value'] ?? 0,
        'follow_up_date' => $validated['follow_up_date'] ?? null,
        'assigned_to'    => auth()->id(),
    ]);

    return redirect()
        ->route('admin.dashboard')
        ->with('success', 'Lead added successfully.');
}

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

    return redirect()->route('admin.companies')
        ->with('success', 'Company + Admin user created.');
}

/**
 * Billing
 */
public function billing()
{
    $stats = [
        'mrr' => SubscriptionInvoice::where('status', 'paid')->sum('amount'),
        'paid_subscriptions' => SubscriptionInvoice::where('status', 'paid')->count(),
        'failed_subscriptions' => SubscriptionInvoice::where('status', 'failed')->count(),
        'active_companies' => Company::where('subscription_status', 'active')->count(),
        'inactive_companies' => Company::where('subscription_status', 'inactive')->count(),
    ];

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

    return redirect('/dashboard')
        ->with('success', 'Now viewing: ' . $company->name);
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

            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Returned to admin.');
        }
    }

    return redirect('/');
}

}
