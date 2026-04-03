<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // 1. Let Super Admin pass always
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        $company = $user->company;

        // 2. If no card is on file, force them to the setup page
        if (!$company->stripe_payment_method_id && !$request->is('subscribe*') && !$request->is('logout')) {
            return redirect()->route('subscribe')->with('error', 'Please provide a valid payment method to start your 7-day free trial.');
        }

        // 3. If account is locked (Day 3 of failure)
        if (!$company->is_active && !$request->is('billing-locked') && !$request->is('subscribe*')) {
            return redirect()->route('billing.locked');
        }

        return $next($request);
    }
}
