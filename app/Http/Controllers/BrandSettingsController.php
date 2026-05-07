<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandSettingsController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;
        return view('settings.brand', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        // ✅ LOGO UPLOAD
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $company->logo_path = $path;
        }

        // ✅ STRIPE MODE
        $company->stripe_mode = $request->input('stripe_mode', 'live');

        // ✅ LIVE KEYS
        $company->stripe_publishable_key = $request->input('stripe_publishable_key');
        $company->stripe_secret_key = $request->input('stripe_secret_key');

        // ✅ TEST KEYS
        $company->stripe_test_publishable_key = $request->input('stripe_test_publishable_key');
        $company->stripe_test_secret_key = $request->input('stripe_test_secret_key');

        // ✅ WEBHOOK
        $company->stripe_webhook_secret = $request->input('stripe_webhook_secret');

        $company->save();

        return back()->with('success', 'Settings saved successfully.');
    }
}
