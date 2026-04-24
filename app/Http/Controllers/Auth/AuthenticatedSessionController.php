<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show login page.
     */
    public function create()
    {
        // If already logged in, redirect intelligently
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role === 'super_admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Logout user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
