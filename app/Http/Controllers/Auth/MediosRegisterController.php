<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Mail\WelcomeCompanyMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\DB;

class MediosRegisterController extends Controller
{
    public function showRegistrationForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        // 1. PURGE GHOSTS
        $existingUser = DB::table('users')->where('email', $request->email)->first();
        if ($existingUser) {
            $company = DB::table('companies')->where('id', $existingUser->company_id)->first();
            if (!$company || ($company->subscription_status === 'pending' && !$company->is_active)) {
                DB::table('users')->where('email', $request->email)->delete();
                if ($company) { DB::table('companies')->where('id', $company->id)->delete(); }
            }
        }

        // 2. VALIDATE
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'plan' => 'nullable|string'
        ]);

        $priceId = env('STRIPE_PRICE_ID'); 
        $requestedPlan = strtolower($request->plan ?? 'starter');

        // 3. CREATE
        $company = Company::create([
            'name' => $request->company_name,
            'subdomain' => Str::slug($request->company_name),
            'email' => $request->email,
            'plan_name' => ucfirst($requestedPlan),
            'plan' => $requestedPlan,
            'subscription_status' => 'pending', 
            'is_active' => false,
            'trial_ends_at' => Carbon::now()->addDays(7),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        Auth::login($user);

        // 4. STRIPE
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $checkoutSession = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [['price' => $priceId, 'quantity' => 1]],
                'mode' => 'subscription',
                'subscription_data' => ['trial_period_days' => 7],
                'customer_email' => $user->email,
                'success_url' => route('dashboard') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('register'),
            ]);
            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            return redirect()->route('dashboard');
        }
    }

    // Keep showSetupForm and completeSetup below...
    public function showSetupForm($token) {
        $company = Company::where('setup_token', $token)->firstOrFail();
        return view('auth.setup', compact('company', 'token'));
    }

    public function completeSetup(Request $request) {
        $company = Company::where('setup_token', $request->token)->firstOrFail();
        $user = User::where('company_id', $company->id)->firstOrFail();
        $user->update(['password' => Hash::make($request->password), 'email_verified_at' => now()]);
        $company->update(['setup_token' => null, 'subscription_status' => 'trialing', 'is_active' => true]);
        Auth::login($user);
        return redirect()->route('dashboard');
    }
}
