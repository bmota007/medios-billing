<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PlatformBranding;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function subscribe($companyId)
    {
        $company = Company::findOrFail($companyId);
        $branding = PlatformBranding::first();

        // 1. Set the API Key (Live or Test based on your dashboard toggle)
        $stripeKey = config('app.env') === 'production' 
            ? $branding->stripe_live_secret_key 
            : $branding->stripe_test_secret_key;

        Stripe::setApiKey($stripeKey);

        // 2. Use Custom Price if set, otherwise default to $35.00
        $amount = ($company->custom_price) ? ($company->custom_price * 100) : 3500;

        // 3. Create Stripe Session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Medios Billing Subscription: ' . $company->name,
                    ],
                    'unit_amount' => $amount,
                    'recurring' => ['interval' => $company->billing_cycle === 'yearly' ? 'year' : 'month'],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => url('/dashboard?payment=success'),
            'cancel_url' => url('/dashboard?payment=cancelled'),
            'client_reference_id' => $company->id,
            'customer_email' => auth()->user()->email,
        ]);

        return redirect($session->url);
    }
}

