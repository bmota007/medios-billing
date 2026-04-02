<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        // ✅ ALLOW if returning from impersonation
        if (session()->has('impersonator_id')) {
            return $next($request);
        }

        // ✅ NORMAL SUPER ADMIN CHECK
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
