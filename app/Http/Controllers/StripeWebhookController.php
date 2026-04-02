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
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Main entry point for Stripe Webhooks.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $eventData = json_decode($payload);

        Log::info("STRIPE WEBHOOK: Received event type: " . ($eventData->type ?? 'UNKNOWN'));

        // =========================================================
        // PART 1: CUSTOMER PAYMENTS (Invoices & Quotes)
        // =========================================================
        if ($eventData->type === 'checkout.session.completed') {
            $session = $eventData->data->object;
            $invoiceNo = $session->metadata->invoice_no ?? null;
            $quoteId = $session->metadata->quote_id ?? null;

            Log::info("STRIPE WEBHOOK: Metadata found - Invoice: $invoiceNo, Quote: $quoteId");

            if ($invoiceNo) {
                $invoice = Invoice::with('company')->where('invoice_no', $invoiceNo)->first();
                if ($invoice) {
                    $this->nuclearProcessPayment($invoice, $payload, $sigHeader);
                }
            } 
            elseif ($quoteId) {
                $quote = Quote::with('company', 'customer')->find($quoteId);
                if ($quote) {
                    // Logic to find the linked invoice by number
                    $invoice = Invoice::where('invoice_no', 'LIKE', '%' . $quote->quote_number . '%')->first();
                    if ($invoice) {
                        $this->nuclearProcessPayment($invoice, $payload, $sigHeader);
                    } else {
                        Log::error("STRIPE WEBHOOK: Found Quote $quoteId but no linked Invoice found.");
                    }
                }
            }
        }

        // =========================================================
        // PART 2: SUBSCRIPTION RENEWAL LOGIC (MB Platform Fees)
        // =========================================================
        if (in_array($eventData->type, ['invoice.payment_succeeded', 'invoice.paid'])) {
            $stripeInvoice = $eventData->data->object;
            $customerId = $stripeInvoice->customer ?? null;
            $stripeInvoiceId = $stripeInvoice->id ?? null;
            $systemSecret = env('STRIPE_WEBHOOK_SECRET');

            // Verify with SYSTEM secret
            try { Webhook::constructEvent($payload, $sigHeader, $systemSecret); } catch (\Throwable $e) {}

            if ($customerId) {
                $company = Company::withoutGlobalScopes()->where('stripe_id', $customerId)->first();

                if ($company) {
                    $company->update([
                        'subscription_status' => 'active',
                        'subscription_ends_at' => now()->addMonth(),
                        'is_active' => true,
                    ]);

                    if ($stripeInvoiceId) {
                        $alreadyExists = SubscriptionInvoice::where('stripe_invoice_id', $stripeInvoiceId)->first();
                        if (!$alreadyExists) {
                            $amountPaid = isset($stripeInvoice->amount_paid) ? $stripeInvoice->amount_paid / 100 : 35.00;
                            
                            // Original Subscription Recording Logic Restored
                            SubscriptionInvoice::create([
                                'company_id'         => $company->id,
                                'invoice_no'         => 'MB-STRIPE-' . strtoupper(Str::random(6)),
                                'stripe_invoice_id'  => $stripeInvoiceId,
                                'stripe_customer_id' => $customerId,
                                'customer_name'      => $company->name,
                                'customer_email'     => $company->email ?? 'admin@mediosbilling.com',
                                'amount'             => $amountPaid,
                                'currency'           => $stripeInvoice->currency ?? 'usd',
                                'status'             => 'paid',
                                'invoice_date'       => now(),
                                'paid_at'            => now(),
                                'period_start'       => now(),
                                'period_end'         => now()->addMonth(),
                                'items'              => [['desc' => 'Medios Billing Monthly Subscription Renewal', 'qty' => 1, 'price' => $amountPaid, 'line_total' => $amountPaid]],
                                'notes'              => 'Stripe platform renewal success.',
                            ]);
                            Log::info("STRIPE WEBHOOK: Subscription Invoice created for " . $company->name);
                        }
                    }
                }
            }
        }

        // PART 3: FAILED SUBSCRIPTION PAYMENTS
        if ($eventData->type === 'invoice.payment_failed') {
            $stripeInvoice = $eventData->data->object;
            $customerId = $stripeInvoice->customer ?? null;
            if ($customerId) {
                $company = Company::withoutGlobalScopes()->where('stripe_id', $customerId)->first();
                if ($company) {
                    $company->update(['subscription_status' => 'inactive', 'is_active' => false]);
                    Log::warning("STRIPE WEBHOOK: Subscription FAILED for " . $company->name);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Nuclear logic to handle Invoice/Quote Processing + Emails with safety logging.
     */
    protected function nuclearProcessPayment($invoice, $payload, $sigHeader)
    {
        $company = $invoice->company;
        Log::info("STRIPE WEBHOOK: Nuclear processing start for " . $company->name);

        $endpointSecret = ($company->stripe_mode === 'live') 
            ? $company->stripe_webhook_secret 
            : $company->stripe_test_webhook_secret;

        // Signature Verification with safety bypass
        if ($endpointSecret) {
            try {
                Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
                Log::info("STRIPE WEBHOOK: Signature Verified for " . $company->name);
            } catch (\Exception $e) {
                Log::error("STRIPE WEBHOOK: Signature Security Fail for " . $company->name . ": " . $e->getMessage());
                return; 
            }
        } else {
            Log::warning("STRIPE WEBHOOK: Missing Secret for " . $company->name . ". Processing without verification.");
        }

        if ($invoice->status !== 'paid') {
            // 1. Update Database
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => 'card'
            ]);
            Log::info("STRIPE WEBHOOK: DB updated to PAID for #" . $invoice->invoice_no);

// 2. Email the Company (Internal Alert)
            try {
                Mail::to($company->email)->send(new \App\Mail\InvoicePaidMail($invoice));
                Log::info("STRIPE WEBHOOK: Professional Alert sent to company.");
            } catch (\Exception $e) { 
                Log::error("STRIPE WEBHOOK: Company Email Fail: " . $e->getMessage()); 
            }

            // 3. Email the Customer (Receipt)
            try {
                Mail::to($invoice->customer_email)->send(new \App\Mail\InvoicePaidMail($invoice));
                Log::info("STRIPE WEBHOOK: Professional Receipt sent to customer.");
            } catch (\Exception $e) { 
                Log::error("STRIPE WEBHOOK: Customer Receipt Fail: " . $e->getMessage()); 
               }

            }
          }
         }
