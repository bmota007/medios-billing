<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\SubscriptionInvoice;
use App\Helpers\StripeHelper;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;

class BillingController extends Controller
{
    public function expired()
    {
        return view('billing.expired');
    }

    public function processSubscription(Request $request)
    {
        // SYSTEM BILLING = MEDIOS BILLING PLATFORM BILLING
        $stripe = StripeHelper::forSystem();
        Stripe::setApiKey($stripe['secret']);

        try {
            $user = Auth::user();
            $company = $user->company;

            if (!$company) {
                return back()->with('error', 'No company found for this user.');
            }

            if (!$request->filled('stripeToken')) {
                return back()->with('error', 'Payment token missing. Please try again.');
            }

            $priceId = $stripe['price_id'];

            if (!$priceId) {
                return back()->with('error', 'Stripe price ID is missing.');
            }

            // Create or reuse Stripe customer
            if (!$company->stripe_id) {
                $customer = Customer::create([
                    'email'  => $user->email,
                    'name'   => $company->name,
                    'source' => $request->stripeToken,
                ]);

                $company->stripe_id = $customer->id;
                $company->save();
            } else {
                $customer = Customer::retrieve($company->stripe_id);

                Customer::update($customer->id, [
                    'source' => $request->stripeToken,
                ]);
            }

            // Prevent duplicate active subscriptions
            $existingSubscriptions = Subscription::all([
                'customer' => $customer->id,
                'status'   => 'all',
                'limit'    => 20,
            ]);

            foreach ($existingSubscriptions->data as $existingSubscription) {
                if (in_array($existingSubscription->status, ['active', 'trialing', 'past_due'])) {
                    return redirect()->route('dashboard')
                        ->with('success', 'This company already has an active subscription.');
                }
            }

            // Create Stripe subscription
            $subscription = Subscription::create([
                'customer' => $customer->id,
                'items' => [[
                    'price' => $priceId,
                ]],
            ]);

            // Activate company
            $company->update([
                'subscription_status'     => 'active',
                'subscription_started_at' => now(),
                'subscription_ends_at'    => now()->addMonth(),
                'is_active'               => true,
            ]);

            // Internal SaaS billing record
            SubscriptionInvoice::create([
                'company_id'         => $company->id,
                'invoice_no'         => 'MB-' . strtoupper(Str::random(6)),
                'stripe_invoice_id'  => null,
                'stripe_customer_id' => $company->stripe_id,
                'customer_name'      => $company->name,
                'customer_email'     => $user->email,
                'amount'             => 35.00,
                'currency'           => 'usd',
                'status'             => 'paid',
                'invoice_date'       => now(),
                'paid_at'            => now(),
                'period_start'       => now(),
                'period_end'         => now()->addMonth(),
                'items'              => [
                    [
                        'desc'       => 'Medios Billing Monthly Subscription',
                        'qty'        => 1,
                        'price'      => 35.00,
                        'line_total' => 35.00,
                    ]
                ],
                'notes'              => 'Stripe subscription created: ' . ($subscription->id ?? 'N/A'),
            ]);

            return redirect()->route('dashboard')->with('success', 'Subscription Activated!');
        } catch (\Exception $e) {
            return back()->with('error', 'Payment Failed: ' . $e->getMessage());
        }
    }
}
