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

class RegisterController extends Controller
{
    public function showRegistrationForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        $company = Company::create(['name' => $request->company_name]);
        $password = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        try {
            Mail::to($user->email)->send(new WelcomeCompanyMail($user, $company, $password));
        } catch (\Exception $e) {
            \Log::error("Registration Mail Failed: " . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Registration successful!');
    }

    /**
     * Show the Onboarding Setup Form
     */
    public function showSetupForm($token)
    {
        try {
            $company = Company::where('setup_token', $token)->first();

            if (!$company) {
                return "Error: Token $token is invalid or has already been used.";
            }

            return view('auth.setup', compact('company', 'token'));
        } catch (\Exception $e) {
            return "Database Error: " . $e->getMessage();
        }
    }

    /**
     * Complete Onboarding
     */
    public function completeSetup(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
            'payment_method' => 'required' 
        ]);

        $company = Company::where('setup_token', $request->token)->firstOrFail();
        $user = User::where('company_id', $company->id)->firstOrFail();

        $user->update([
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        $company->update([
            'setup_token' => null,
            'stripe_payment_method_id' => $request->payment_method,
            'subscription_status' => 'trialing',
            'is_active' => true,
        ]);

        Auth::login($user);
        return redirect()->route('dashboard');
    }
}
