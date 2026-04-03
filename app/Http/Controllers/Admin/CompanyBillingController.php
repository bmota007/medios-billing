<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyBillingController extends Controller
{
    public function updateBilling(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        $company->update([
            'custom_price' => $request->price, // e.g. 90.00
            'billing_cycle' => $request->cycle, // monthly/yearly
            'trial_ends_at' => $request->trial_ends_at,
            'subscription_status' => $request->status,
        ]);

        return back()->with('success', 'Billing updated for ' . $company->name);
    }
}

