<?php

namespace App\Helpers;

use App\Models\Company;

class StripeHelper
{
    /*
    |--------------------------------------------------------------------------
    | PLAN PRICE IDS (LIVE MODE)
    |--------------------------------------------------------------------------
    */

    public static function planPriceId(string $plan = 'starter'): ?string
    {
        $plan = strtolower(trim($plan));

        // alias support
        if ($plan === 'pro') {
            $plan = 'premium';
        }

        $map = [
            'starter' => env('STRIPE_PRICE_STARTER'),
            'growth'  => env('STRIPE_PRICE_GROWTH'),
            'premium' => env('STRIPE_PRICE_PREMIUM'),
        ];

        return $map[$plan] ?? $map['starter'];
    }

    /*
    |--------------------------------------------------------------------------
    | PLAN MONTHLY PRICE (DISPLAY / INTERNAL)
    |--------------------------------------------------------------------------
    */

    public static function monthlyAmount(string $plan = 'starter'): float
    {
        $plan = strtolower(trim($plan));

        // alias support
        if ($plan === 'pro') {
            $plan = 'premium';
        }

        $map = [
            'starter' => 49.00,
            'growth'  => 99.00,
            'premium' => 179.00,
        ];

        return $map[$plan] ?? 49.00;
    }

    /*
    |--------------------------------------------------------------------------
    | COMPANY STRIPE KEYS (TENANT PAYMENTS)
    |--------------------------------------------------------------------------
    */

    public static function forCompany(?Company $company = null): array
    {
        if (!$company) {
            return self::forSystem();
        }

        $mode = $company->stripe_mode ?? 'test';

        if ($mode === 'live') {
            return [
                'mode'     => 'live',
                'public'   => $company->stripe_publishable_key ?: env('STRIPE_KEY'),
                'secret'   => $company->stripe_secret_key ?: env('STRIPE_SECRET'),
                'webhook'  => $company->stripe_webhook_secret ?: env('STRIPE_WEBHOOK_SECRET'),
                'price_id' => null,
            ];
        }

        return [
            'mode'     => 'test',
            'public'   => $company->stripe_test_publishable_key ?: env('STRIPE_KEY'),
            'secret'   => $company->stripe_test_secret_key ?: env('STRIPE_SECRET'),
            'webhook'  => $company->stripe_test_webhook_secret ?: env('STRIPE_WEBHOOK_SECRET'),
            'price_id' => null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SYSTEM BILLING (MEDIOS BILLING SaaS)
    |--------------------------------------------------------------------------
    */

    public static function forSystem(string $plan = 'starter'): array
    {
        return [
            'mode'     => 'system',
            'public'   => env('STRIPE_KEY'),
            'secret'   => env('STRIPE_SECRET'),
            'webhook'  => env('STRIPE_WEBHOOK_SECRET'),
            'price_id' => self::planPriceId($plan),
            'amount'   => self::monthlyAmount($plan),
        ];
    }
}
