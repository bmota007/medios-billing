<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Basic Stats
        $invoices = Invoice::where('company_id',$companyId)->count();

        $revenue = Invoice::where('company_id',$companyId)
            ->where('status','paid')
            ->sum('total');

        $paidInvoices = Invoice::where('company_id',$companyId)
            ->where('status','paid')
            ->count();

        $pendingInvoices = Invoice::where('company_id',$companyId)
            ->where('status','unpaid')
            ->count();

        $invoicesThisMonth = Invoice::where('company_id',$companyId)
            ->whereMonth('created_at',Carbon::now()->month)
            ->count();

        $customers = Invoice::where('company_id',$companyId)
            ->distinct('customer_email')
            ->count('customer_email');

        $recentInvoices = Invoice::where('company_id',$companyId)
            ->latest()
            ->take(5)
            ->get();

        // Revenue Chart
        $monthlyRevenue = [];

        for($m=1;$m<=12;$m++){
            $monthlyRevenue[] = Invoice::where('company_id',$companyId)
                ->where('status','paid')
                ->whereMonth('created_at',$m)
                ->sum('total');
        }

        // SaaS Metrics (needed by dashboard view)
        $companies = 1;
        $users = User::where('company_id',$companyId)->count();

        $mrr = $revenue;
        $arr = $revenue * 12;
        $activeCompanies = 1;

        // Fake Top Companies (dashboard expects it)
        $topCompanies = collect([
            (object)[
                'name' => $user->company->name ?? 'Your Company',
                'revenue' => $revenue,
                'invoices_count' => $invoices
            ]
        ]);

        return view('admin.dashboard',[
            'companies' => $companies,
            'users' => $users,
            'invoices' => $invoices,
            'revenue' => $revenue,
            'mrr' => $mrr,
            'arr' => $arr,
            'activeCompanies' => $activeCompanies,
            'paidInvoices' => $paidInvoices,
            'pendingInvoices' => $pendingInvoices,
            'invoicesThisMonth' => $invoicesThisMonth,
            'customers' => $customers,
            'monthlyRevenue' => $monthlyRevenue,
            'recentInvoices' => $recentInvoices,
            'topCompanies' => $topCompanies
        ]);
    }
}
