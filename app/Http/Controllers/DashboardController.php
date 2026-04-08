<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Force Houston Time for the greeting
        date_default_timezone_set('America/Chicago');
        $hour = date('H');
        
        if ($hour < 12) {
            $greeting = 'Good Morning';
        } elseif ($hour < 17) {
            $greeting = 'Good Afternoon';
        } else {
            $greeting = 'Good Evening';
        }

        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect('/login')->with('error', 'Company profile not found.');
        }

        // 1. Stripe Success Logic (Activation)
        if ($request->has('session_id') && $company->subscription_status === 'pending') {
            try {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $session = Session::retrieve($request->get('session_id'));
                if ($session->payment_status === 'paid' || $session->payment_status === 'no_payment_required') {
                    $company->update([
                        'subscription_status' => 'trialing',
                        'is_active' => true,
                        'stripe_customer_id' => $session->customer,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error("Stripe Activation Error: " . $e->getMessage());
            }
        }

        // 2. Stats Calculation
        $stats = [
            'total_revenue' => Invoice::where('company_id', $company->id)->where('status', 'paid')->sum('total'),
            'total_invoices' => Invoice::where('company_id', $company->id)->count(),
            'paid_invoices' => Invoice::where('company_id', $company->id)->where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('company_id', $company->id)->where('status', 'pending')->count(),
        ];

        // 3. Graph Data (Monthly Revenue)
        $chartData = array_fill(0, 12, 0);
        try {
            $monthlyRevenue = Invoice::where('company_id', $company->id)
                ->where('status', 'paid')
                ->whereYear('paid_at', date('Y'))
                ->select(DB::raw('SUM(total) as aggregate'), DB::raw('MONTH(paid_at) as month'))
                ->groupBy('month')
                ->get();

            foreach ($monthlyRevenue as $data) {
                $chartData[$data->month - 1] = (float) $data->aggregate;
            }
        } catch (\Exception $e) {
            \Log::error("Graph Data Error: " . $e->getMessage());
        }

        // 4. Recent Invoices
        $recentInvoices = Invoice::where('company_id', $company->id)
            ->latest()
            ->take(5)
            ->get();

        $currentSecret = $company->stripe_secret_key ?? env('STRIPE_SECRET');
        $stripeStatus = str_contains($currentSecret, 'sk_live') ? 'LIVE' : 'TEST';

        return view('dashboard', compact('stats', 'chartData', 'recentInvoices', 'company', 'stripeStatus', 'greeting'));
    }
}
