<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
