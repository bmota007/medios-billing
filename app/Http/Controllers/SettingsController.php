<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class SettingsController extends Controller
{
    public function index()
    {
        // Fetch the current user's company
        $company = Company::find(auth()->user()->company_id);
        return view('settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        // Validate that keys are provided
        $request->validate([
            'client_stripe_key' => 'required|string',
            'client_stripe_secret' => 'required|string',
        ]);

        // Find the specific company for the logged-in user
        $company = Company::find(auth()->user()->company_id);

        if ($company) {
            $company->update([
                'client_stripe_key' => $request->client_stripe_key,
                'client_stripe_secret' => $request->client_stripe_secret,
            ]);

            return back()->with('success', 'Stripe Configuration Saved Successfully!');
        }

        return back()->with('error', 'Company not found.');
    }
}
