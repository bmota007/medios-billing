// Inside a middleware or even in the LoginController
if (Auth::check() && !Auth::user()->company->is_active) {
    Auth::logout();
    return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact support.');
}
