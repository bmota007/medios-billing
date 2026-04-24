<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | NOT LOGGED IN
        |--------------------------------------------------------------------------
        */
        if (!$user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN BYPASS
        |--------------------------------------------------------------------------
        */
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        $company = $user->company;

        /*
        |--------------------------------------------------------------------------
        | NO COMPANY LINKED
        |--------------------------------------------------------------------------
        */
        if (!$company) {
            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | ALLOW CUSTOMER TO ACCESS SUBSCRIPTION PAGE ALWAYS
        |--------------------------------------------------------------------------
        */
        if ($request->routeIs('subscription.portal') ||
            $request->routeIs('subscription.cancel') ||
            $request->routeIs('checkout.subscribe')) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | MUST COMPLETE CHECKOUT FIRST
        |--------------------------------------------------------------------------
        */
        if ($company->subscription_status === 'pending_checkout') {
            return redirect()->route('billing.locked');
        }

        /*
        |--------------------------------------------------------------------------
        | BLOCK INACTIVE ACCOUNT
        |--------------------------------------------------------------------------
        */
        if (!$company->is_active) {
            return redirect()->route('billing.locked');
        }

        /*
        |--------------------------------------------------------------------------
        | BLOCK CANCELLED / EXPIRED / FAILED BILLING
        |--------------------------------------------------------------------------
        */
        if (in_array($company->subscription_status, [
            'cancelled',
            'expired',
            'past_due',
            'unpaid',
            'inactive'
        ])) {
            return redirect()->route('billing.locked');
        }

        /*
        |--------------------------------------------------------------------------
        | ACTIVE USER CONTINUES
        |--------------------------------------------------------------------------
        */
        return $next($request);
    }
}

