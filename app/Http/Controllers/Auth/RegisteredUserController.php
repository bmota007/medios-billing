<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function create(Request $request)
    {
        // Capture plan from URL
        $plan = $request->get('plan');

        return view('auth.register', compact('plan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        // 🔥 CAPTURE PLAN
        $plan = $request->get('plan', 'starter');

        // CREATE COMPANY
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'plan' => $plan,
            'subscription_status' => 'pending_checkout',
            'status' => 'Pending Payment',
        ]);

        // CREATE USER
        $user = User::create([
            'company_id' => $company->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner',
            'is_active' => 1,
            'force_password_change' => 1,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 🔥 REDIRECT TO STRIPE CHECKOUT
        return redirect()->route('checkout.subscribe', ['companyId' => $company->id]);
    }
}
