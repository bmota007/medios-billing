<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckTrialStatus
{
    public function handle($request, Closure $next)
    {
        // 1. 🛡️ SUPPORT MODE BYPASS
        // If an admin is impersonating a user, they MUST be allowed in.
        if (session()->has('impersonator_id')) {
            return $next($request);
        }

        $user = auth()->user();

        // 2. 🛡️ SUPER ADMIN & OWNER BYPASS
        if ($user && ($user->role === 'super_admin' || $user->email === 'ginedy.mcintosh@gmail.com')) {
            return $next($request);
        }

        $company = $user->company;

        if (!$company) {
            return $next($request);
        }

        // 3. 🛡️ STANDARD TENANT LOGIC
        // Check if trial is expired and subscription is not active
        if ($company->trial_ends_at && now()->gt($company->trial_ends_at)) {
            if ($company->subscription_status !== 'active') {
                return redirect()->route('billing.expired');
            }
        }

        return $next($request);
    }
}
