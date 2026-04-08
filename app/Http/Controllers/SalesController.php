<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function overview()
    {
        try {
            $stats = [
                'total_mrr' => Company::where('subscription_status', 'active')->sum('monthly_price') ?? 0,
                'active_tenants' => Company::where('is_active', true)->count(),
                'new_leads' => Company::where('subscription_status', 'pending')->count(),
                'total_revenue_collected' => Invoice::where('status', 'paid')->sum('total') ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error("Sales Overview Error: " . $e->getMessage());
            $stats = [
                'total_mrr' => 0,
                'active_tenants' => 0,
                'new_leads' => 0,
                'total_revenue_collected' => 0
            ];
        }

        return view('admin.sales.overview', compact('stats'));
    }

    public function subscriptions()
    {
        $companies = Company::latest()->get();
        return view('admin.sales.subscriptions', compact('companies'));
    }

    public function onboarding()
    {
        return view('admin.sales.onboarding');
    }

    public function promos()
    {
        return view('admin.sales.promos');
    }
}
