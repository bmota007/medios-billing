<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Helpers\StripeHelper;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'plan'         => ['required', 'in:starter,growth,premium'],
        ]);

        $plan   = strtolower($request->plan);
        $amount = StripeHelper::monthlyAmount($plan);

        /*
        |--------------------------------------------------------------------------
        | CREATE COMPANY (LOCKED UNTIL CARD ADDED)
        |--------------------------------------------------------------------------
        */

        $company = Company::create([
            'name'                    => $request->company_name,
            'email'                   => $request->email,
            'subdomain'               => Str::slug($request->company_name),

            'plan'                    => ucfirst($plan),
            'plan_name'               => ucfirst($plan),
            'monthly_price'           => $amount,
            'billing_cycle'           => 'monthly',

            'subscription_status'     => 'pending_checkout',
            'subscription_started_at' => null,
            'trial_ends_at'           => null,

            'is_active'               => false,
            'status'                  => 'Pending Payment',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE USER
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'company_id' => $company->id,
            'role'       => 'owner',
        ]);

        event(new Registered($user));
        Auth::login($user);

        /*
        |--------------------------------------------------------------------------
        | SEND TO STRIPE CHECKOUT
        |--------------------------------------------------------------------------
        */

        return redirect()->route('checkout.subscribe', $company->id);
    }
}
