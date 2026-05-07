<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Helpers\StripeHelper;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Subscription;

class CheckoutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | START / RESTART SUBSCRIPTION CHECKOUT
    |--------------------------------------------------------------------------
    */
    public function subscribe($companyId)
    {
        $company = Company::findOrFail($companyId);

        if (auth()->check()) {
            $user = auth()->user();

            // Super admin can access any company checkout
            if ($user->role !== 'super_admin' && (int) $user->company_id !== (int) $company->id) {
                abort(403);
            }
        }

        $plan = strtolower(trim($company->plan_name ?: $company->plan ?: 'starter'));

        // normalize aliases
        if ($plan === 'pro') {
            $plan = 'premium';
        }

        $stripe = StripeHelper::forSystem($plan);

        Stripe::setApiKey($stripe['secret']);

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT FIX
        |--------------------------------------------------------------------------
        | Existing customers upgrading should NEVER get trial text.
        | If stripe_id exists OR status already exists => no trial block sent.
        */

        $hasExistingAccount =
            !empty($company->stripe_id) ||
            in_array(strtolower($company->subscription_status ?? ''), [
                'active',
                'trialing',
                'cancel_pending',
                'cancelled',
                'expired',
                'past_due',
                'unpaid'
            ]);

        $subscriptionData = [
            'metadata' => [
                'company_id' => (string) $company->id,
                'plan'       => $plan,
            ],
        ];

        // ONLY brand new clients get free trial
        if (!$hasExistingAccount) {
            $subscriptionData['trial_period_days'] = 5;
        }

        $session = Session::create([
            'mode' => 'subscription',

            'customer_email' => $company->email,

            'client_reference_id' => (string) $company->id,

            'metadata' => [
                'company_id' => (string) $company->id,
                'plan'       => $plan,
            ],

            'line_items' => [[
                'price'    => $stripe['price_id'],
                'quantity' => 1,
            ]],

            'subscription_data' => $subscriptionData,

            'payment_method_collection' => 'always',

            'success_url' => url('/dashboard?billing=trial_started'),

            'cancel_url' => url('/subscription?cancelled=1'),
        ]);

        return redirect($session->url);
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER BILLING PORTAL
    |--------------------------------------------------------------------------
    */
    public function portal()
    {
        $user = auth()->user();

        $company = $user->company;

        if (!$company && $user->role === 'super_admin') {
            $company = \App\Models\Company::first();
        }

        return view('billing.portal', compact('company'));
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL SUBSCRIPTION (AT PERIOD END)
    |--------------------------------------------------------------------------
    */
    public function cancel()
    {
        $user = auth()->user();

        $company = $user->company;

        if (!$company && $user->role === 'super_admin') {
            $company = \App\Models\Company::first();
        }

        if (!$company) {
            return back()->with('error', 'Company not found.');
        }

        try {

            // If no Stripe customer yet, local cancel only
            if (!$company->stripe_id) {

                $company->update([
                    'subscription_status' => 'cancelled',
                    'status'              => 'Cancelled',
                    'is_active'           => 0,
                ]);

                return back()->with('success', 'Subscription cancelled.');
            }

            $stripe = StripeHelper::forSystem();
            Stripe::setApiKey($stripe['secret']);

            $subscriptions = Subscription::all([
                'customer' => $company->stripe_id,
                'limit'    => 1,
                'status'   => 'all',
            ]);

            if (!empty($subscriptions->data)) {

                $sub = $subscriptions->data[0];

                Subscription::update($sub->id, [
                    'cancel_at_period_end' => true,
                ]);

                $periodEnd = !empty($sub->current_period_end)
                    ? date('Y-m-d H:i:s', $sub->current_period_end)
                    : now()->addMonth();

                $company->update([
                    'subscription_status'  => 'cancel_pending',
                    'status'               => 'Cancels End Of Cycle',
                    'subscription_ends_at' => $periodEnd,
                    'is_active'            => 1,
                ]);

                return back()->with(
                    'success',
                    'Subscription scheduled to cancel at end of billing cycle.'
                );
            }

            return back()->with('error', 'No active Stripe subscription found.');

        } catch (\Throwable $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REACTIVATE CANCELLATION
    |--------------------------------------------------------------------------
    */
    public function reactivate()
    {
        $user = auth()->user();

        $company = $user->company;

        if (!$company && $user->role === 'super_admin') {
            $company = \App\Models\Company::first();
        }

        if (!$company) {
            return back()->with('error', 'Company not found.');
        }

        try {

            if (!$company->stripe_id) {
                return back()->with('error', 'No Stripe customer found.');
            }

            $stripe = StripeHelper::forSystem();
            Stripe::setApiKey($stripe['secret']);

            $subscriptions = Subscription::all([
                'customer' => $company->stripe_id,
                'limit'    => 1,
                'status'   => 'all',
            ]);

            if (!empty($subscriptions->data)) {

                $sub = $subscriptions->data[0];

                Subscription::update($sub->id, [
                    'cancel_at_period_end' => false,
                ]);

                $company->update([
                    'subscription_status' => 'active',
                    'status'              => 'Active',
                    'is_active'           => 1,
                ]);

                return back()->with('success', 'Subscription reactivated successfully.');
            }

            return back()->with('error', 'Subscription not found.');

        } catch (\Throwable $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGE PLAN (STARTER / GROWTH / PREMIUM)
    |--------------------------------------------------------------------------
    */
    public function changePlan(Request $request)
    {
        $user = auth()->user();

        $company = $user->company;

        if (!$company && $user->role === 'super_admin') {
            $company = \App\Models\Company::first();
        }

        if (!$company) {
            return back()->with('error', 'Company not found.');
        }

        $request->validate([
            'plan' => 'required|string'
        ]);

        $plan = strtolower(trim($request->plan));

        // alias support from frontend
        if ($plan === 'pro') {
            $plan = 'premium';
        }

        $allowed = ['starter', 'growth', 'premium'];

        if (!in_array($plan, $allowed)) {
            return back()->with('error', 'Invalid plan selected.');
        }

        $company->update([
            'plan_name' => $plan === 'premium' ? 'Pro' : ucfirst($plan),
            'plan'      => $plan === 'premium' ? 'Pro' : ucfirst($plan),
        ]);

        // redirect to checkout so Stripe can switch billing
        return redirect()->route('checkout.subscribe', $company->id);
    }
}
