<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function subscribe($companyId)
    {
        $company = Company::findOrFail($companyId);

        $mode = env('APP_BILLING_MODE', 'test');

        Stripe::setApiKey($mode === 'live'
            ? env('STRIPE_SECRET')
            : env('STRIPE_TEST_SECRET')
        );

        $plan = $company->plan ?? 'starter';

        $priceMap = [
            'starter' => env($mode === 'live' ? 'STRIPE_PRICE_STARTER' : 'STRIPE_TEST_PRICE_STARTER'),
            'growth'  => env($mode === 'live' ? 'STRIPE_PRICE_GROWTH' : 'STRIPE_TEST_PRICE_GROWTH'),
            'pro'     => env($mode === 'live' ? 'STRIPE_PRICE_PRO' : 'STRIPE_TEST_PRICE_PRO'),
            'premium' => env($mode === 'live' ? 'STRIPE_PRICE_PREMIUM' : 'STRIPE_TEST_PRICE_PREMIUM'),
        ];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $priceMap[$plan],
                'quantity' => 1,
            ]],
            'success_url' => url('/billing/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/subscription'),
            'metadata' => [
                'company_id' => $company->id,
                'plan' => $plan,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect('/dashboard');
        }

        $mode = env('APP_BILLING_MODE', 'test');

        Stripe::setApiKey($mode === 'live'
            ? env('STRIPE_SECRET')
            : env('STRIPE_TEST_SECRET')
        );

        $session = Session::retrieve($sessionId);

        $companyId = $session->metadata->company_id ?? null;
        $plan = $session->metadata->plan ?? 'starter';

        $company = Company::find($companyId);

        if ($company) {
            $company->plan = $plan;
            $company->subscription_status = 'active';
            $company->status = 'Active';
            $company->save();

            $user = $company->users()->first();

            if ($user) {
                Mail::send('emails.welcome_subscription', [
                    'user' => $user,
                    'company' => $company
                ], function ($m) use ($user) {
                    $m->to($user->email)->subject('Welcome to Medios Billing 🚀');
                });
            }
        }

        return view('billing-success');
    }

    public function portal()
    {
        $user = auth()->user();

        if (!$user) return redirect('/login');

        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        $company = $user->company;

        if (!$company) {
            return redirect('/dashboard')->with('error', 'No company found.');
        }

        return view('billing.portal', compact('company'));
    }
}
