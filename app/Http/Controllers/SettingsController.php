<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $company = Company::find(auth()->user()->company_id);
        return view('settings.index', compact('company'));
    }

    public function update(Request $request)
    {
        $company = Company::find(auth()->user()->company_id);

        // 1. Stripe Keys
        $company->client_stripe_key = $request->client_stripe_key;
        $company->client_stripe_secret = $request->client_stripe_secret;

        // 2. Loop through 4 Contract slots
        for ($i = 1; $i <= 4; $i++) {
            $nameField = "contract_{$i}_name";
            $fileField = "contract_{$i}_file";
            $pathField = "contract_{$i}_path";

            if ($request->has($nameField)) {
                $company->$nameField = $request->$nameField;
            }

            if ($request->hasFile($fileField)) {
                // Remove old template if replacing
                if ($company->$pathField) { Storage::delete($company->$pathField); }
                $company->$pathField = $request->file($fileField)->store('contracts', 'public');
            }
        }

        $company->save();
        return back()->with('success', 'Platform Intelligence & Contracts Updated!');
    }
}
