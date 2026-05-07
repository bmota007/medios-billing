<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Invalid webhook signature'
            ], 400);
        }

        $type   = $event->type;
        $object = $event->data->object;

        switch ($type) {

            /*
            |--------------------------------------------------------------------------
            | NEW CHECKOUT COMPLETED
            |--------------------------------------------------------------------------
            */
            case 'checkout.session.completed':

                $companyId = $object->client_reference_id ?? null;

                if ($companyId) {
                    $company = Company::find($companyId);

                    if ($company) {
                        $company->update([
                            'stripe_id'                => $object->customer ?? $company->stripe_id,
                            'subscription_status'     => 'active',
                            'subscription_started_at' => now(),
                            'is_active'               => 1,
                            'status'                  => 'Active',
                        ]);
                    }
                }

            break;

            /*
            |--------------------------------------------------------------------------
            | SUCCESSFUL RECURRING PAYMENT
            |--------------------------------------------------------------------------
            */
            case 'invoice.paid':

                $customerId = $object->customer ?? null;

                if ($customerId) {
                    $company = Company::where('stripe_id', $customerId)->first();

                    if ($company) {
                        $company->update([
                            'subscription_status' => 'active',
                            'is_active'           => 1,
                            'status'              => 'Active',
                        ]);
                    }
                }

            break;

            /*
            |--------------------------------------------------------------------------
            | PAYMENT FAILED
            |--------------------------------------------------------------------------
            */
            case 'invoice.payment_failed':

                $customerId = $object->customer ?? null;

                if ($customerId) {
                    $company = Company::where('stripe_id', $customerId)->first();

                    if ($company) {
                        $company->update([
                            'subscription_status' => 'past_due',
                            'status'              => 'Payment Failed',
                        ]);
                    }
                }

            break;

            /*
            |--------------------------------------------------------------------------
            | SUBSCRIPTION UPDATED
            |--------------------------------------------------------------------------
            */
            case 'customer.subscription.updated':

                $customerId = $object->customer ?? null;

                if ($customerId) {
                    $company = Company::where('stripe_id', $customerId)->first();

                    if ($company) {

                        $cancelAtPeriodEnd = $object->cancel_at_period_end ?? false;

                        $company->update([
                            'subscription_status' => $cancelAtPeriodEnd ? 'cancel_pending' : 'active',
                            'status'              => $cancelAtPeriodEnd ? 'Cancels End Of Cycle' : 'Active',
                            'subscription_ends_at'=> isset($object->current_period_end)
                                ? date('Y-m-d H:i:s', $object->current_period_end)
                                : $company->subscription_ends_at,
                            'is_active'           => 1,
                        ]);
                    }
                }

            break;

            /*
            |--------------------------------------------------------------------------
            | FULLY CANCELLED / ENDED
            |--------------------------------------------------------------------------
            */
            case 'customer.subscription.deleted':

                $customerId = $object->customer ?? null;

                if ($customerId) {
                    $company = Company::where('stripe_id', $customerId)->first();

                    if ($company) {
                        $company->update([
                            'subscription_status' => 'cancelled',
                            'status'              => 'Cancelled',
                            'is_active'           => 0,
                        ]);
                    }
                }

            break;
        }

        return response()->json([
            'received' => true
        ]);
    }
}
