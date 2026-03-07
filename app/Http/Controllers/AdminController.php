<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Invoice;
use App\Scopes\CompanyScope;

class AdminController extends Controller
{
    public function dashboard()
    {
        $companies = Company::count();

        $users = User::count();

        // IMPORTANT: Admin must bypass tenant scope
        $invoices = Invoice::withoutGlobalScope(CompanyScope::class)->count();

        $paidInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'paid')
            ->count();

        $pendingInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'pending')
            ->count();

        $overdueInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'overdue')
            ->count();

        $cancelledInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'cancelled')
            ->count();

        $revenue = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'paid')
            ->sum('total');

        $recentInvoices = Invoice::withoutGlobalScope(CompanyScope::class)
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | SaaS Metrics
        |--------------------------------------------------------------------------
        */

        $mrr = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->sum('total');

        $arr = $mrr * 12;

        $invoicesThisMonth = Invoice::withoutGlobalScope(CompanyScope::class)
            ->whereMonth('created_at', now()->month)
            ->count();

        $activeCompanies = Company::count();

        /*
        |--------------------------------------------------------------------------
        | Revenue Trend (Last 12 Months)
        |--------------------------------------------------------------------------
        */

        $revenueTrend = Invoice::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        /*
        |--------------------------------------------------------------------------
        | Top Companies by Revenue
        |--------------------------------------------------------------------------
        */

        $topCompanies = Company::select('companies.id', 'companies.name')
            ->join('invoices', 'companies.id', '=', 'invoices.company_id')
            ->where('invoices.status', 'paid')
            ->selectRaw('SUM(invoices.total) as revenue, COUNT(invoices.id) as invoices_count')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'companies' => $companies,
            'users' => $users,
            'invoices' => $invoices,
            'paidInvoices' => $paidInvoices,
            'pendingInvoices' => $pendingInvoices,
            'overdueInvoices' => $overdueInvoices,
            'cancelledInvoices' => $cancelledInvoices,
            'revenue' => $revenue,
            'recentInvoices' => $recentInvoices,

            // SaaS Metrics
            'mrr' => $mrr,
            'arr' => $arr,
            'invoicesThisMonth' => $invoicesThisMonth,
            'activeCompanies' => $activeCompanies,

            // Chart Data
            'revenueTrend' => $revenueTrend,

            // Leaderboard
            'topCompanies' => $topCompanies,
        ]);
    }

    public function companies()
    {
        $companies = Company::with('users')
            ->withCount(['users', 'invoices'])
            ->latest()
            ->paginate(20);

        return view('admin.companies', compact('companies'));
    }

    public function company($id)
    {
        $company = Company::with(['users', 'invoices'])->findOrFail($id);

        // Total invoices for this company
        $invoiceCount = $company->invoices()->count();

        // Total revenue from paid invoices
        $invoiceTotal = $company->invoices()
            ->where('status', 'paid')
            ->sum('total');

        // Latest invoices
        $recentInvoices = $company->invoices()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.company', compact(
            'company',
            'invoiceCount',
            'invoiceTotal',
            'recentInvoices'
        ));
    }
}
