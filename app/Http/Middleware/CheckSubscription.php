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

        // 1. Bypass for Super Admins
        if ($user && $user->role === 'super_admin') {
            return $next($request);
        }

        if (!$user || !$user->company) {
            return redirect()->route('login');
        }

        $company = $user->company;

        // 2. Bypass for System Accounts (Medios Billing)
        if ($company->plan === 'SYSTEM') {
            return $next($request);
        }

        // Settings, Profile, and Logout are always allowed
        if ($request->is('company/settings*') || $request->is('logout') || $request->is('profile*')) {
            return $next($request);
        }

        // 3. Active check for standard Tenants
        if (!$company->is_active && $company->subscription_status !== 'trialing') {
            return redirect()->route('company.settings')->with('warning', 'Please activate your plan.');
        }

        return $next($request);
    }
}
