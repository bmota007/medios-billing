<?php

namespace App\Helpers;

use App\Models\Company;

class StripeHelper
{
    public static function billingMode(): string
    {
        return env('APP_BILLING_MODE', 'live');
    }

    public static function planPriceId(string $plan = 'starter'): ?string
    {
        $plan = strtolower(trim($plan));

        if (self::billingMode() === 'test') {

            $map = [
                'starter' => env('STRIPE_TEST_PRICE_STARTER'),
                'growth'  => env('STRIPE_TEST_PRICE_GROWTH'),
                'pro'     => env('STRIPE_TEST_PRICE_PRO'),
                'premium' => env('STRIPE_TEST_PRICE_PREMIUM'),
            ];

        } else {

            $map = [
                'starter' => env('STRIPE_PRICE_STARTER'),
                'growth'  => env('STRIPE_PRICE_GROWTH'),
                'pro'     => env('STRIPE_PRICE_PRO'),
                'premium' => env('STRIPE_PRICE_PREMIUM'),
            ];
        }

        return $map[$plan] ?? $map['starter'];
    }

    public static function monthlyAmount(string $plan='starter'): float
    {
        $map = [
            'starter' => 49,
            'growth'  => 79,
            'pro'     => 129,
            'premium' => 249,
        ];

        return $map[$plan] ?? 49;
    }

    public static function forCompany(?Company $company = null): array
    {
        if (!$company) {
            return self::forSystem();
        }

        $mode = $company->stripe_mode ?? 'test';

        if ($mode === 'live') {
            return [
                'mode'    => 'live',
                'public'  => $company->stripe_publishable_key,
                'secret'  => $company->stripe_secret_key,
                'webhook' => $company->stripe_webhook_secret,
                'price_id'=> null,
            ];
        }

        return [
            'mode'    => 'test',
            'public'  => $company->stripe_test_publishable_key,
            'secret'  => $company->stripe_test_secret_key,
            'webhook' => $company->stripe_webhook_secret,
            'price_id'=> null,
        ];
    }

    public static function forSystem(string $plan='starter'): array
    {
        if (self::billingMode() === 'test') {

            return [
                'mode'    => 'test',
                'public'  => env('STRIPE_TEST_KEY'),
                'secret'  => env('STRIPE_TEST_SECRET'),
                'webhook' => env('STRIPE_WEBHOOK_SECRET'),
                'price_id'=> self::planPriceId($plan),
                'amount'  => self::monthlyAmount($plan),
            ];
        }

        return [
            'mode'    => 'live',
            'public'  => env('STRIPE_KEY'),
            'secret'  => env('STRIPE_SECRET'),
            'webhook' => env('STRIPE_WEBHOOK_SECRET'),
            'price_id'=> self::planPriceId($plan),
            'amount'  => self::monthlyAmount($plan),
        ];
    }
}
