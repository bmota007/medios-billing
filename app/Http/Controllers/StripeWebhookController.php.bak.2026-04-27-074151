<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\SubscriptionInvoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use App\Helpers\StripeHelper;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $eventData = json_decode($payload);

        Log::info("STRIPE WEBHOOK RECEIVED: " . ($eventData->type ?? 'unknown'));

        /*
        |--------------------------------------------------------------------------
        | PART 1 — TENANT CUSTOMER PAYMENTS
        |--------------------------------------------------------------------------
        */
        if (($eventData->type ?? '') === 'checkout.session.completed') {

            $session = $eventData->data->object ?? null;

            $invoiceNo = $session->metadata->invoice_no ?? null;
            $quoteId   = $session->metadata->quote_id ?? null;

            if ($invoiceNo) {
                $invoice = Invoice::with('company')
                    ->where('invoice_no', $invoiceNo)
                    ->first();

                if ($invoice) {
                    $this->processTenantPayment($invoice, $payload, $sigHeader);
                }
            }

            if ($quoteId) {
                $quote = Quote::find($quoteId);

                if ($quote) {
                    $invoice = Invoice::where('company_id', $quote->company_id)
                        ->latest()
                        ->first();

                    if ($invoice) {
                        $this->processTenantPayment($invoice, $payload, $sigHeader);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PART 2 — MEDIOS BILLING SUBSCRIPTIONS
        |--------------------------------------------------------------------------
        */
        if (in_array(($eventData->type ?? ''), ['invoice.payment_succeeded', 'invoice.paid'])) {

            $stripeInvoice = $eventData->data->object ?? null;

            $customerId      = $stripeInvoice->customer ?? null;
            $stripeInvoiceId = $stripeInvoice->id ?? null;

            $system = StripeHelper::forSystem();

            try {
                Webhook::constructEvent($payload, $sigHeader, $system['webhook']);
            } catch (\Throwable $e) {
                Log::warning("System webhook verification skipped/fail.");
            }

            if ($customerId) {

                $company = Company::where('stripe_id', $customerId)->first();

                if ($company) {

                    /*
                    |--------------------------------------------------------------------------
                    | ACTIVATE COMPANY
                    |--------------------------------------------------------------------------
                    */
                    $wasInactive = $company->subscription_status !== 'active';

                    $company->update([
                        'subscription_status' => 'active',
                        'subscription_ends_at' => now()->addMonth(),
                        'is_active' => true,
                        'status' => 'Active',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | SEND PREMIUM WELCOME EMAIL (FIRST ACTIVATION ONLY)
                    |--------------------------------------------------------------------------
                    */
                    if ($wasInactive && $company->email) {
                        try {
                            Mail::to($company->email)
                                ->send(new \App\Mail\WelcomeOnboardMail($company));

                            Log::info("WelcomeOnboardMail sent to {$company->email}");
                        } catch (\Throwable $e) {
                            Log::error("Welcome email failed: " . $e->getMessage());
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE SUBSCRIPTION INVOICE RECORD
                    |--------------------------------------------------------------------------
                    */
                    $exists = SubscriptionInvoice::where('stripe_invoice_id', $stripeInvoiceId)->first();

                    if (!$exists) {

                        $amountPaid = isset($stripeInvoice->amount_paid)
                            ? $stripeInvoice->amount_paid / 100
                            : 35.00;

                        SubscriptionInvoice::create([
                            'company_id'         => $company->id,
                            'invoice_no'         => 'MB-' . strtoupper(Str::random(6)),
                            'stripe_invoice_id'  => $stripeInvoiceId,
                            'stripe_customer_id' => $customerId,
                            'customer_name'      => $company->name,
                            'customer_email'     => $company->email,
                            'amount'             => $amountPaid,
                            'currency'           => 'usd',
                            'status'             => 'paid',
                            'invoice_date'       => now(),
                            'paid_at'            => now(),
                            'period_start'       => now(),
                            'period_end'         => now()->addMonth(),
                            'items'              => [
                                [
                                    'desc'       => 'Medios Billing Subscription Renewal',
                                    'qty'        => 1,
                                    'price'      => $amountPaid,
                                    'line_total' => $amountPaid,
                                ]
                            ],
                            'notes' => 'Stripe renewal webhook success',
                        ]);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PART 3 — FAILED SUBSCRIPTIONS
        |--------------------------------------------------------------------------
        */
        if (($eventData->type ?? '') === 'invoice.payment_failed') {

            $stripeInvoice = $eventData->data->object ?? null;
            $customerId    = $stripeInvoice->customer ?? null;

            if ($customerId) {

                $company = Company::where('stripe_id', $customerId)->first();

                if ($company) {
                    $company->update([
                        'subscription_status' => 'inactive',
                        'is_active' => false,
                        'status' => 'Payment Failed',
                    ]);
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /*
    |--------------------------------------------------------------------------
    | TENANT PAYMENT ENGINE
    |--------------------------------------------------------------------------
    */
    protected function processTenantPayment($invoice, $payload, $sigHeader)
    {
        $company = $invoice->company;

        $stripe = StripeHelper::forCompany($company);

        if ($stripe['webhook']) {
            try {
                Webhook::constructEvent($payload, $sigHeader, $stripe['webhook']);
            } catch (\Throwable $e) {
                Log::warning("Tenant webhook verify failed for {$company->name}");
            }
        }

        if ($invoice->status === 'paid') {
            return;
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => 'card',
            'remaining_balance' => 0,
            'amount_paid' => $invoice->total,
        ]);

        try {

            if ($invoice->customer_email) {
                Mail::to($invoice->customer_email)
                    ->send(new \App\Mail\InvoicePaidMail($invoice));
            }

            if ($company->email) {
                Mail::to($company->email)
                    ->send(new \App\Mail\InvoicePaidAdminMail($invoice));
            }

        } catch (\Throwable $e) {
            Log::error("Webhook email fail: " . $e->getMessage());
        }
    }
}
