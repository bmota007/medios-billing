<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class CompanyController extends Controller
{
    public function settings()
    {
        $company = auth()->user()->company;
        return view('company.settings', compact('company'));
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;

        // ===== BASIC INFO =====
        $company->name = $request->name;
        $company->email = $request->email;
        $company->phone = $request->phone;
        $company->address = $request->address;
        $company->website = $request->website;
        $company->primary_color = $request->primary_color;

        // ===== PAYMENT OPTIONS =====
        $company->accept_card = $request->boolean('accept_card');
        $company->accept_check = $request->boolean('accept_check');
        $company->accept_cash = $request->boolean('accept_cash');
        $company->accept_zelle = $request->boolean('accept_zelle');
        $company->accept_venmo = $request->boolean('accept_venmo');

// ===== PAYMENT DETAILS (ZELLE + VENMO) =====
$company->zelle_label = $request->zelle_label ?? 'Zelle';
$company->zelle_value = $request->zelle_value;

$company->venmo_label = $request->venmo_label ?? 'Venmo';
$company->venmo_value = $request->venmo_value;

        // ===== LOGO =====
        if ($request->hasFile('logo')) {
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $company->logo = $path;
            $company->logo_path = $path;
        }

        // ===== CONTRACT NAMES =====
        for ($i = 1; $i <= 4; $i++) {
            $nameField = "contract_{$i}_name";
            $company->$nameField = $request->$nameField ?? $company->$nameField;
        }

        // ===== CONTRACT FILES =====
        for ($i = 1; $i <= 4; $i++) {

            $fileField = "contract_{$i}_file";
            $pathField = "contract_{$i}_path";
            $nameField = "contract_{$i}_name";

            if ($request->hasFile($fileField)) {

                // delete old
                if ($company->$pathField && Storage::disk('public')->exists($company->$pathField)) {
                    Storage::disk('public')->delete($company->$pathField);
                }

                $file = $request->file($fileField);

                // store
                $path = $file->store('contracts', 'public');
                $company->$pathField = $path;

                // auto-name if empty
                if (empty($company->$nameField)) {
                    $company->$nameField = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                }
            }
        }

        $company->save();

        return back()->with('success', 'Settings saved successfully');
    }

// 🔥 STRIPE + BRANDING SAVE (APPEND ONLY)
public function updateBranding(Request $request)
{
    $company = auth()->user()->company;

    // ✅ LOGO UPLOAD
    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('logos', 'public');
        $company->logo_path = $path;
    }

    // ✅ STRIPE MODE
    if ($request->has('stripe_mode')) {
        $company->stripe_mode = $request->stripe_mode;
    }

// ✅ LIVE KEYS
if (!empty($request->stripe_publishable_key)) {
    $company->stripe_publishable_key =
        $request->stripe_publishable_key;
}

if (!empty($request->stripe_secret_key)) {
    $company->stripe_secret_key =
        $request->stripe_secret_key;
}

// ✅ TEST KEYS
if (!empty($request->stripe_test_publishable_key)) {
    $company->stripe_test_publishable_key =
        $request->stripe_test_publishable_key;
}

if (!empty($request->stripe_test_secret_key)) {
    $company->stripe_test_secret_key =
        $request->stripe_test_secret_key;
}

// ✅ WEBHOOK
if (!empty($request->stripe_webhook_secret)) {
    $company->stripe_webhook_secret =
        $request->stripe_webhook_secret;
}

    $company->save();

    return back()->with('success', 'Brand settings saved.');
}

    public function testEmail()
    {
        $company = auth()->user()->company;

        try {
            Config::set('mail.mailers.smtp.host', $company->smtp_host);
            Config::set('mail.mailers.smtp.port', $company->smtp_port);
            Config::set('mail.mailers.smtp.username', $company->smtp_user);
            Config::set('mail.mailers.smtp.password', $company->smtp_pass);

            Config::set('mail.from.address', $company->smtp_from);
            Config::set('mail.from.name', $company->name);

            Mail::raw('SMTP working correctly 🚀', function ($msg) use ($company) {
                $msg->to($company->email)->subject('SMTP Test Successful');
            });

            return back()->with('success', 'SMTP test sent');

        } catch (\Exception $e) {
            return back()->with('error', 'SMTP failed: '.$e->getMessage());
        }
    }
}
