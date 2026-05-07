<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN NEVER USES TENANT DASHBOARD
        |--------------------------------------------------------------------------
        */
        if ($user && $user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        /*
        |--------------------------------------------------------------------------
        | GREETING
        |--------------------------------------------------------------------------
        */
        date_default_timezone_set('America/Chicago');

        $hour = date('H');

        if ($hour < 12) {
            $greeting = 'Good Morning';
        } elseif ($hour < 17) {
            $greeting = 'Good Afternoon';
        } else {
            $greeting = 'Good Evening';
        }

        /*
        |--------------------------------------------------------------------------
        | COMPANY REQUIRED
        |--------------------------------------------------------------------------
        */
        $company = $user->company;

        if (!$company) {
            return redirect('/login')->with(
                'error',
                'Company profile not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STRIPE RETURN SUCCESS
        |--------------------------------------------------------------------------
        */
        if (
            $request->has('session_id') &&
            $company->subscription_status === 'pending'
        ) {
            try {
                Stripe::setApiKey(env('STRIPE_SECRET'));

                $session = Session::retrieve(
                    $request->get('session_id')
                );

                if (
                    $session->payment_status === 'paid' ||
                    $session->payment_status === 'no_payment_required'
                ) {
                    $company->update([
                        'subscription_status' => 'trialing',
                        'is_active' => true,
                        'stripe_customer_id' => $session->customer,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error(
                    'Stripe Activation Error: '.$e->getMessage()
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE FLAGS
        |--------------------------------------------------------------------------
        */
        $role = $user->role;

        $canCustomers = in_array($role, [
            'owner',
            'admin',
            'regional_director',
            'manager',
            'sales_director',
            'support'
        ]);

        $canQuotes = in_array($role, [
            'owner',
            'admin',
            'regional_director',
            'manager',
            'sales_director'
        ]);

        $canInvoices = in_array($role, [
            'owner',
            'admin',
            'regional_director',
            'manager',
            'accounting'
        ]);

        $canUsers = in_array($role, [
            'owner',
            'admin',
            'regional_director',
            'manager'
        ]);

        $canBrand = in_array($role, [
            'owner',
            'admin',
            'regional_director'
        ]);

        /*
        |--------------------------------------------------------------------------
        | BASE QUERIES
        |--------------------------------------------------------------------------
        */
        $invoiceQuery = Invoice::where('company_id', $company->id);

        /*
        |--------------------------------------------------------------------------
        | ROLE-SPECIFIC DASHBOARD STATS
        |--------------------------------------------------------------------------
        */
        if ($role === 'accounting') {

            $stats = [
                'total_revenue' => (clone $invoiceQuery)
                    ->where('status', 'paid')
                    ->sum('total'),

                'total_invoices' => (clone $invoiceQuery)->count(),

                'paid_invoices' => (clone $invoiceQuery)
                    ->where('status', 'paid')
                    ->count(),

                'pending_invoices' => (clone $invoiceQuery)
                    ->whereIn('status', ['pending', 'sent', 'partial'])
                    ->count(),
            ];

        } elseif (in_array($role, ['sales_director'])) {

            $stats = [
                'total_revenue' => (clone $invoiceQuery)
                    ->where('status', 'paid')
                    ->sum('total'),

                'total_invoices' => (clone $invoiceQuery)->count(),

                'paid_invoices' => (clone $invoiceQuery)
                    ->where('status', 'paid')
                    ->count(),

                'pending_invoices' => (clone $invoiceQuery)
                    ->whereIn('status', ['pending', 'sent', 'partial'])
                    ->count(),
            ];

        } else {

            $stats = [
                'total_revenue' => (clone $invoiceQuery)
                    ->where('status', 'paid')
                    ->sum('total'),

                'total_invoices' => (clone $invoiceQuery)->count(),

                'paid_invoices' => (clone $invoiceQuery)
                    ->where('status', 'paid')
                    ->count(),

                'pending_invoices' => (clone $invoiceQuery)
                    ->whereIn('status', ['pending', 'sent', 'partial'])
                    ->count(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | CHART DATA
        |--------------------------------------------------------------------------
        */
        $chartData = array_fill(0, 12, 0);

        try {
            $monthlyRevenue = Invoice::where('company_id', $company->id)
                ->where('status', 'paid')
                ->whereYear('created_at', now()->year)
                ->select(
                    DB::raw('SUM(total) as aggregate'),
                    DB::raw('MONTH(created_at) as month')
                )
                ->groupBy('month')
                ->get();

            foreach ($monthlyRevenue as $data) {
                $chartData[$data->month - 1] =
                    (float) $data->aggregate;
            }

        } catch (\Exception $e) {
            \Log::error(
                'Graph Data Error: '.$e->getMessage()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RECENT INVOICES
        |--------------------------------------------------------------------------
        */
        $recentInvoices = Invoice::where('company_id', $company->id)
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER COUNTS
        |--------------------------------------------------------------------------
        */
        $customerCount = Customer::where(
            'company_id',
            $company->id
        )->count();

        /*
        |--------------------------------------------------------------------------
        | STRIPE STATUS
        |--------------------------------------------------------------------------
        */
        $currentSecret =
            $company->stripe_secret_key ??
            $company->client_stripe_secret ??
            null;

        if (!$currentSecret) {
            $stripeStatus = 'NOT CONNECTED';
        } elseif (str_contains($currentSecret, 'sk_live')) {
            $stripeStatus = 'LIVE';
        } else {
            $stripeStatus = 'TEST';
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'dashboard',
            compact(
                'stats',
                'chartData',
                'recentInvoices',
                'company',
                'stripeStatus',
                'greeting',
                'role',
                'canCustomers',
                'canQuotes',
                'canInvoices',
                'canUsers',
                'canBrand',
                'customerCount'
            )
        );
    }
}
