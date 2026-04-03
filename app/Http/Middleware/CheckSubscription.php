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

        /**
         * 2. SOFTEN THE BOUNCER
         * We REMOVE the "force to subscribe" if no card is on file.
         * Now, they can access the Dashboard, but we still protect 
         * sensitive routes like 'invoices/create' if you want.
         */
        
        // Example: Only force subscription if they try to CREATE an invoice
        /*
        if (!$company->stripe_payment_method_id && $request->is('invoices/create')) {
             return redirect()->route('subscribe')->with('info', 'Please activate your 7-day trial to create invoices.');
        }
        */

        // 3. Keep the "Hard Lock" for non-payment (After the 7 days/failed bill)
        if (!$company->is_active && !$request->is('billing-locked') && !$request->is('subscribe*') && !$request->is('logout')) {
            return redirect()->route('billing.locked');
        }

        return $next($request);
    }
}
