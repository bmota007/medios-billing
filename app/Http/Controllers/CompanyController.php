<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Show the company/brand settings page.
     */
    public function settings()
    {
        $user = Auth::user();

        // Identify context: Force Medios Billing for Admin Brand route
        if (request()->routeIs('admin.brand') || request()->is('admin/brand*')) {
            $company = Company::where('name', 'LIKE', '%Medios Billing%')->first() ?? Company::find(1);
        } else {
            $company = $user->company;
        }

        // Super Admin fallback
        if ($user->role === 'super_admin' && !$company) {
            $company = Company::find(1);
        }

        if (!$company) {
            abort(404, 'Business profile not found.');
        }

        return view('company.settings', compact('company'));
    }

    /**
     * Update the business settings, logo, and contract template.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            // 1. Target correct company (ID 1 for Admin paths, otherwise User's company)
            if (request()->routeIs('admin.brand.update') || request()->is('admin/brand*')) {
                $company = Company::find(1);
            } else {
                $company = $user->company;
            }

            if (!$company) {
                return back()->with('error', 'Update failed: Company record not found.');
            }

            // 2. Map Basic Info & Payment Toggles
            $company->name = $request->name;
            $company->email = $request->email;
            $company->phone = $request->phone;
            $company->address = $request->address;
            $company->website = $request->website;
            $company->primary_color = $request->primary_color;

            $company->accept_card = $request->boolean('accept_card');
            $company->accept_check = $request->boolean('accept_check');
            $company->accept_cash = $request->boolean('accept_cash');
            $company->accept_zelle = $request->boolean('accept_zelle');
            $company->accept_venmo = $request->boolean('accept_venmo');

            $company->zelle_label = $request->zelle_label ?? 'Zelle';
            $company->zelle_value = $request->zelle_value;
            $company->venmo_label = $request->venmo_label ?? 'Venmo';
            $company->venmo_value = $request->venmo_value;

            // 3. Stripe Keys (Webhook Secret Included)
            $company->stripe_mode = $request->stripe_mode ?? 'test';
            $company->stripe_test_publishable_key = $request->stripe_test_publishable_key;
            $company->stripe_test_secret_key = $request->stripe_test_secret_key;
            $company->stripe_publishable_key = $request->stripe_publishable_key;
            $company->stripe_secret_key = $request->stripe_secret_key;
            $company->stripe_webhook_secret = $request->stripe_webhook_secret;

            // 4. Logo Handling (Cleanup and dual-column update)
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                
                $oldPath = $company->logo_path;
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                $filename = 'Logo_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('logos', $filename, 'public');
                
                $company->logo = $path;
                $company->logo_path = $path;
            }

            // 5. Contract Handling
            if ($request->hasFile('contract_template')) {
                $file = $request->file('contract_template');
                $path = $file->storeAs('contracts', 'Contract_'.time().'.'.$file->getClientOriginalExtension(), 'public');
                $company->contract_template_path = $path;
                $company->contract_template_type = $file->getClientOriginalExtension();
            }

            // 6. Save Company Record
            if (!$company->save()) {
                throw new \Exception("Database failed to save the company record.");
            }

            // 7. Password Update (Matches Blade 'password' input name)
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|min:8|confirmed',
                ]);
                $user->update(['password' => Hash::make($request->password)]);
            }

            return back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            Log::error("Critical Update Error: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
