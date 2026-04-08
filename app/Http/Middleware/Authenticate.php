<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    /**
     * Handle an incoming request.
     * Added safety check for active company status.
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $user = Auth::user();

        // SAFETY CHECK: Only run this for regular admins/users, NOT Super Admins
        if ($user && $user->role !== 'super_admin' && $user->company_id) {
            if ($user->company && !$user->company->is_active && $user->company->subscription_status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support.');
            }
        }

        return $next($request);
    }
}
