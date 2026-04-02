<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\User;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | 🧠 SUPPORT MODE HANDSHAKE
        | If I am a super_admin but NOT currently impersonating, show admin stats.
        | If I AM impersonating, skip this and show the Company Dashboard below.
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'super_admin' && !session()->has('impersonator_id')) {

            $companies = Company::count();
            $users = User::count();
            $invoices = Invoice::withoutGlobalScope(CompanyScope::class)->count();

            $paidInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
                ->where('status', 'paid')
                ->count();

            $pendingInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
                ->where('status', 'unpaid')
                ->count();

            $revenue = Invoice::withoutGlobalScope(CompanyScope::class)
                ->where('status', 'paid')
                ->sum('total');

            $invoicesThisMonth = Invoice::withoutGlobalScope(CompanyScope::class)
                ->whereMonth('created_at', now()->month)
                ->count();

            $mrr = Company::where('subscription_status', 'active')->sum('monthly_price');
            $arr = $mrr * 12;
            $activeCompanies = Company::where('subscription_status', 'active')->count();

            $recentInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
                ->latest()
                ->take(5)
                ->get();

            $revenueTrendRaw = Invoice::withoutGlobalScope(CompanyScope::class)
                ->where('status', 'paid')
                ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            $revenueTrend = [];
            for ($m = 1; $m <= 12; $m++) {
                $revenueTrend[$m] = (float) ($revenueTrendRaw[$m] ?? 0);
            }

            $topCompanies = Company::select('companies.id', 'companies.name')
                ->join('invoices', 'companies.id', '=', 'invoices.company_id')
                ->where('invoices.status', 'paid')
                ->selectRaw('SUM(invoices.total) as revenue, COUNT(invoices.id) as invoices_count')
                ->groupBy('companies.id', 'companies.name')
                ->orderByDesc('revenue')
                ->limit(5)
                ->get();

            $brandName = 'Medios Billing';
            $brandLogo = null;
            $brandColor = '#6366f1';

            return view('admin.dashboard', compact(
                'companies', 'users', 'invoices', 'paidInvoices', 'pendingInvoices',
                'revenue', 'invoicesThisMonth', 'mrr', 'arr', 'activeCompanies',
                'recentInvoices', 'revenueTrend', 'topCompanies', 'brandName',
                'brandLogo', 'brandColor'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY DASHBOARD (Keeps all your original MRR and Carbon logic)
        |--------------------------------------------------------------------------
        */
        $company = $user->company;

        if (!$company) {
            abort(403, 'No company assigned to this user.');
        }

        $companyId = $company->id;
        $companies = 1;
        $users = User::where('company_id', $companyId)->count();
        $invoices = Invoice::where('company_id', $companyId)->count();

        $paidInvoices = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->count();

        $pendingInvoices = Invoice::where('company_id', $companyId)
            ->where('status', 'unpaid')
            ->count();

        $revenue = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->sum('total');

        $invoicesThisMonth = Invoice::where('company_id', $companyId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $mrr = Invoice::where('company_id', $companyId)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $arr = $mrr * 12;
        $activeCompanies = 1;

        $recentInvoices = Invoice::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();

        $revenueTrend = [];
        for ($m = 1; $m <= 12; $m++) {
            $revenueTrend[$m] = (float) Invoice::where('company_id', $companyId)
                ->where('status', 'paid')
                ->whereMonth('created_at', $m)
                ->sum('total');
        }

        $topCompanies = collect([
            (object) [
                'name' => $company->name,
                'revenue' => (float) $revenue,
                'invoices_count' => $invoices,
            ],
        ]);

        $brandName = $company->name;
        $brandLogo = $company->logo;
        $brandColor = $company->primary_color ?: '#6366f1';

        return view('dashboard', compact(
            'companies', 'users', 'invoices', 'paidInvoices', 'pendingInvoices',
            'revenue', 'invoicesThisMonth', 'mrr', 'arr', 'activeCompanies',
            'recentInvoices', 'revenueTrend', 'topCompanies', 'brandName',
            'brandLogo', 'brandColor'
        ));
    }
}
