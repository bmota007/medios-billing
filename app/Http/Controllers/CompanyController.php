<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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

        return view('companies.settings', compact('company'));
    }

    /**
     * Update the business settings, logo, and contract template.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            // 1. HARD TARGET: If we are on the admin branding path, we MUST use ID 1
            if (request()->routeIs('admin.brand.update') || request()->is('admin/brand*')) {
                $company = Company::find(1);
            } else {
                $company = $user->company;
            }

            if (!$company) {
                return back()->with('error', 'Update failed: Company record not found in database.');
            }

            // 2. Map Basic Info & Payment Toggles (All original logic preserved)
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

            // 3. Stripe Keys (Kitchen logic intact)
            $company->stripe_mode = $request->stripe_mode ?? 'test';
            $company->stripe_test_publishable_key = $request->stripe_test_publishable_key;
            $company->stripe_test_secret_key = $request->stripe_test_secret_key;
            $company->stripe_publishable_key = $request->stripe_publishable_key;
            $company->stripe_secret_key = $request->stripe_secret_key;
            $company->stripe_webhook_secret = $request->stripe_webhook_secret;

            // 4. THE LOGO FIX: Explicitly saving to both columns
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                
                // Cleanup old file from disk
                $oldPath = $company->logo ?? $company->logo_path;
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }

                $filename = 'Logo_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('logos', $filename, 'public');
                
                $company->logo = $path;
                $company->logo_path = $path;
                
                Log::info("Logo saved for Company ID {$company->id}: " . $path);
            }

            // 5. Handle Contract
            if ($request->hasFile('contract_template')) {
                $file = $request->file('contract_template');
                $path = $file->storeAs('contracts', 'Contract_'.time().'.'.$file->getClientOriginalExtension(), 'public');
                $company->contract_template_path = $path;
                $company->contract_template_type = $file->getClientOriginalExtension();
            }

            // 6. SAVE AND VERIFY
            if (!$company->save()) {
                throw new \Exception("Database failed to save the company record.");
            }

            // 7. Handle Password
            if ($request->filled('new_password')) {
                $user->update(['password' => Hash::make($request->new_password)]);
            }

            return back()->with('success', 'Branding and Settings updated successfully!');

        } catch (\Exception $e) {
            Log::error("Critical Update Error: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
