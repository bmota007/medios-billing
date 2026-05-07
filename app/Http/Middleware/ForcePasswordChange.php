<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->force_password_change == 1) {
            if ($request->path() != 'force-password-change') {
                return redirect('/force-password-change');
            }
        }

        return $next($request);
    }
}
