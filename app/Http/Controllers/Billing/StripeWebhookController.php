<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PlatformBranding;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $branding = PlatformBranding::first();
        $endpoint_secret = $branding->stripe_webhook_secret;

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $companyId = $session->client_reference_id;

            // Activate the company subscription!
            $company = Company::find($companyId);
            if ($company) {
                $company->update([
                    'subscription_status' => 'active',
                    'stripe_id' => $session->customer,
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
