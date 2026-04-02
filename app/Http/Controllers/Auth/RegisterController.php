<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        // 1. Create the Company
        $company = Company::create(['name' => $request->company_name]);

        // 2. Generate Temp Password
        $password = Str::random(12);

        // 3. Create the Admin User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        // 4. Send Welcome Email
        Mail::to($user->email)->send(new WelcomeCompanyMail($user, $company, $password));

        return redirect()->route('login')->with('success', 'Registration successful! Check your email for login details.');
    }
}
