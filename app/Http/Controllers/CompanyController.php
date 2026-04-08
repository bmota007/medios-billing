<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class CompanyController extends Controller
{
    /**
     * Show the company/brand settings page.
     */
    public function settings()
    {
        $user = Auth::user();

        // Admin context (Medios Billing global settings)
        if (request()->routeIs('admin.brand') || request()->is('admin/brand*')) {
            $company = Company::where('name', 'LIKE', '%Medios Billing%')->first() ?? Company::find(1);
        } else {
            $company = $user->company;
        }

        // Super admin fallback
        if ($user->role === 'super_admin' && !$company) {
            $company = Company::find(1);
        }

        if (!$company) {
            abort(404, 'Business profile not found.');
        }

        return view('company.settings', compact('company'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            // Resolve company
            if (request()->routeIs('admin.brand.update') || request()->is('admin/brand*')) {
                $company = Company::find(1);
            } else {
                $company = $user->company;
            }

            if (!$company) {
                return back()->with('error', 'Company not found.');
            }

            // =========================
            // BASIC INFO
            // =========================
            $company->name = $request->name;
            $company->email = $request->email;
            $company->phone = $request->phone;
            $company->address = $request->address;
            $company->website = $request->website;
            $company->primary_color = $request->primary_color;

            // =========================
            // PAYMENT METHODS
            // =========================
            $company->accept_card = $request->boolean('accept_card');
            $company->accept_check = $request->boolean('accept_check');
            $company->accept_cash = $request->boolean('accept_cash');
            $company->accept_zelle = $request->boolean('accept_zelle');
            $company->accept_venmo = $request->boolean('accept_venmo');

            $company->zelle_label = $request->zelle_label ?? 'Zelle';
            $company->zelle_value = $request->zelle_value;
            $company->venmo_label = $request->venmo_label ?? 'Venmo';
            $company->venmo_value = $request->venmo_value;

            // =========================
            // STRIPE
            // =========================
            $company->stripe_mode = $request->stripe_mode ?? 'test';
            $company->stripe_test_publishable_key = $request->stripe_test_publishable_key;
            $company->stripe_test_secret_key = $request->stripe_test_secret_key;
            $company->stripe_publishable_key = $request->stripe_publishable_key;
            $company->stripe_secret_key = $request->stripe_secret_key;
            $company->stripe_webhook_secret = $request->stripe_webhook_secret;

            // =========================
            // SMTP 🔥 (NEW)
            // =========================
            $company->smtp_host = $request->smtp_host;
            $company->smtp_port = $request->smtp_port;
            $company->smtp_user = $request->smtp_user;
            $company->smtp_pass = $request->smtp_pass;
            $company->smtp_from = $request->smtp_from;

            // =========================
            // LOGO
            // =========================
            if ($request->hasFile('logo')) {
                if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                    Storage::disk('public')->delete($company->logo_path);
                }

                $file = $request->file('logo');
                $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('logos', $filename, 'public');

                $company->logo = $path;
                $company->logo_path = $path;
            }

            // =========================
            // CONTRACT
            // =========================
            if ($request->hasFile('contract_template')) {
                $file = $request->file('contract_template');
                $path = $file->storeAs(
                    'contracts',
                    'contract_' . time() . '.' . $file->getClientOriginalExtension(),
                    'public'
                );

                $company->contract_template_path = $path;
                $company->contract_template_type = $file->getClientOriginalExtension();
            }

            // SAVE
            $company->save();

            // =========================
            // PASSWORD
            // =========================
            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|min:8|confirmed',
                ]);

                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            return back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            Log::error("Company Settings Error: " . $e->getMessage());
            return back()->with('error', 'Error saving settings.');
        }
    }

    /**
     * TEST SMTP EMAIL 🔥
     */
    public function testEmail()
    {
        $company = auth()->user()->company;

        try {
            // Apply dynamic SMTP
            Config::set('mail.mailers.smtp.host', $company->smtp_host);
            Config::set('mail.mailers.smtp.port', $company->smtp_port);
            Config::set('mail.mailers.smtp.username', $company->smtp_user);
            Config::set('mail.mailers.smtp.password', $company->smtp_pass);

            Config::set('mail.from.address', $company->smtp_from);
            Config::set('mail.from.name', $company->name);

            Mail::raw('SMTP working correctly 🚀', function ($msg) use ($company) {
                $msg->to($company->email)
                    ->subject('SMTP Test Successful');
            });

            return back()->with('success', 'Test email sent successfully!');

        } catch (\Exception $e) {
            Log::error("SMTP Test Failed: " . $e->getMessage());
            return back()->with('error', 'SMTP failed: ' . $e->getMessage());
        }
    }
}
