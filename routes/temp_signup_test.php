<?php

use Illuminate\Support\Facades\Route;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/admin/quick-premium-test', function () {

    $email = 'premiumlive@mediosbilling.com';

    $existing = User::where('email', $email)->first();

    if (!$existing) {

        $company = Company::create([
            'name' => 'Premium Live Demo LLC',
            'email' => $email,
            'plan_name' => 'Pro',
            'plan' => 'Pro',
            'subscription_status' => 'pending',
            'status' => 'Pending Payment',
            'is_active' => 1,
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'Premium Owner',
            'email' => $email,
            'password' => Hash::make('Temp1234!'),
            'role' => 'owner',
            'is_active' => 1,
            'must_change_password' => 1,
            'needs_password_change' => 1,
        ]);
    }

    $company = Company::where('email', $email)->first();

    return redirect('/checkout/subscribe/'.$company->id);
});
