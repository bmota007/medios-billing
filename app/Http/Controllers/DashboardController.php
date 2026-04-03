<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Quote;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isImpersonating = Session::has('impersonator_id');

        /*
        |--------------------------------------------------------------------------
        | 🔒 THE HANDSHAKE
        |--------------------------------------------------------------------------
        */
        if ($user->role !== 'super_admin' && !$isImpersonating) {
            // 1. Force Legal NDA
            if (!$user->legal_accepted_at) {
                return view('company.onboarding.welcome');
            }
            // 2. Force Password Change
            if ($user->needs_password_change) {
                return redirect()->route('profile.edit')->with('info', 'Security update required.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 🌍 DYNAMIC GREETING (Houston Time)
        |--------------------------------------------------------------------------
        */
        $hour = Carbon::now('America/Chicago')->hour;
        if ($hour < 12) { $greeting = 'Good Morning'; }
        elseif ($hour < 17) { $greeting = 'Good Afternoon'; }
        else { $greeting = 'Good Evening'; }

        /*
        |--------------------------------------------------------------------------
        | 🧠 PLATFORM LEVEL (Super Admin)
        |--------------------------------------------------------------------------
        */
        if (in_array($user->role, ['super_admin', 'admin']) && $user->company_id === null && !$isImpersonating) {
            $companies = Company::count();
            $revenue = Invoice::withoutGlobalScope(CompanyScope::class)->where('status', 'paid')->sum('total');
            $activeCompanies = Company::where('subscription_status', 'active')->count();
            $recentInvoices = Invoice::withoutGlobalScope(CompanyScope::class)->latest()->take(5)->get();
            $mrr = Company::where('subscription_status', 'active')->sum('monthly_price');

            return view('admin.dashboard', compact('companies', 'revenue', 'activeCompanies', 'recentInvoices', 'greeting', 'mrr'));
        }

        /*
        |--------------------------------------------------------------------------
        | 🏢 TENANT LEVEL (MCS, PP, etc.)
        |--------------------------------------------------------------------------
        */
        $company = $user->company;
        if (!$company) { abort(403, 'Company not found.'); }

        // --- ROLE-BASED DASHBOARD SPLIT ---
        
        if ($user->role === 'staff') {
            // Patty (Staff) lands here. NO REVENUE DATA.
            $recentQuotes = Quote::where('company_id', $company->id)->latest()->take(5)->get();
            return view('company.dashboards.staff', compact('greeting', 'company', 'recentQuotes'));
        }

        // Admins & Managers land here. FULL REVENUE DATA.
        $revenue = Invoice::where('company_id', $company->id)->where('status', 'paid')->sum('total');
        $invoicesCount = Invoice::where('company_id', $company->id)->count();
        $paidInvoices = Invoice::where('company_id', $company->id)->where('status', 'paid')->count();
        $pendingInvoices = Invoice::where('company_id', $company->id)->where('status', 'unpaid')->count();
        $recentInvoices = Invoice::where('company_id', $company->id)->latest()->take(5)->get();
        
        $brandName = $company->name;
        $revenueTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueTrend[$m] = (float) Invoice::where('company_id', $company->id)
                ->where('status', 'paid')
                ->whereMonth('created_at', $m)
                ->sum('total');
        }

        return view('dashboard', compact(
            'revenue', 'invoicesCount', 'paidInvoices', 'pendingInvoices', 
            'recentInvoices', 'brandName', 'greeting', 'revenueTrend'
        ));
    }

    public function acceptLegal()
    {
        $user = Auth::user();
        $user->update(['legal_accepted_at' => now()]);
        return redirect()->route('dashboard');
    }
}
