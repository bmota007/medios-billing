<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Models\User;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (\Exception $e) {
            Log::error("Stripe Webhook Signature Error: " . $e->getMessage());
            return response('Invalid signature', 400);
        }

        $type = $event->type ?? 'unknown';

        Log::info("STRIPE WEBHOOK RECEIVED: {$type}");

        /*
        |--------------------------------------------------------------------------
        | CHECKOUT COMPLETED
        |--------------------------------------------------------------------------
        */
        if ($type === 'checkout.session.completed') {

            $session = $event->data->object ?? null;
/*
|--------------------------------------------------------------------------
| INVOICE PAYMENT WEBHOOK
|--------------------------------------------------------------------------
*/

if (
    isset($session->metadata->invoice_no)
) {

    $invoiceNo =
        $session->metadata->invoice_no;

    $invoice =
        \App\Models\Invoice::where(
            'invoice_no',
            $invoiceNo
        )->first();

    if ($invoice) {

        $invoice->status = 'paid';

        $invoice->paid_at = now();

        $invoice->payment_reference =
            $session->payment_intent ?? null;

        $invoice->stripe_customer_id =
            $session->customer ?? null;

        $invoice->save();

        Log::info(
            "✅ Invoice payment webhook updated: {$invoiceNo}"
        );
    }
}
            $companyId = $session->client_reference_id ?? null;

            if ($companyId) {

                $company = Company::find($companyId);

                if ($company) {

                    $company->update([
                        'stripe_id'                => $session->customer ?? $company->stripe_id,
                        'subscription_status'     => 'active',
                        'subscription_started_at' => now(),
                        'subscription_ends_at'    => now()->addDays(5),
                        'is_active'               => 1,
                        'status'                  => 'Active',
                    ]);

                    Log::info("✅ Subscription ACTIVATED for company ID: {$company->id}");

                    /*
                    |--------------------------------------------------------------------------
                    | SEND WELCOME EMAIL
                    |--------------------------------------------------------------------------
                    */
                    try {
                        $user = User::where('company_id', $company->id)->first();

                        if ($user) {

                            $loginUrl = url('/login');

                            Mail::send('emails.welcome_subscription', [
                                'user' => $user,
                                'company' => $company,
                                'loginUrl' => $loginUrl,
                            ], function ($m) use ($user) {
                                $m->to($user->email)
                                  ->subject('🎉 🚀 Welcome to MediosBilling — Your 5-Day Free Trial Has Started!');
                            });

                            Log::info("✅ Welcome email sent to {$user->email}");
                        }

                    } catch (\Exception $e) {
                        Log::error("❌ Email send failed: " . $e->getMessage());
                    }

                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
