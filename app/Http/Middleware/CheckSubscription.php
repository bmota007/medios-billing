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

        // ✅ Allow guests for public pages
        $allowedRoutes = [
            'login',
            'register',
            'subscription',
            'billing-locked',
            'logout',
        ];

        if (in_array($request->path(), $allowedRoutes)) {
            return $next($request);
        }

        // ✅ If not logged in
        if (!$user) {
            return redirect('/login');
        }

        // ✅ SUPER ADMIN FULL BYPASS (CRITICAL)
        if ($user->role === 'super_admin' || $user->is_admin) {
            return $next($request);
        }

        // ✅ If user has no company → block safely
        if (!$user->company) {
            return redirect('/login');
        }

        $company = $user->company;

        // ✅ Allow active OR trial
        if (
            $company->subscription_status === 'active' ||
            ($company->subscription_ends_at && now()->lt($company->subscription_ends_at))
        ) {
            return $next($request);
        }

        return redirect('/billing-locked');
    }
}
