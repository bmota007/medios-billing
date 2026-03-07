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

        // allow access if role is superadmin
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
