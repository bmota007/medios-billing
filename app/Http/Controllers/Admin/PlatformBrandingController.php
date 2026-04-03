<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformBranding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlatformBrandingController extends Controller
{
    public function index()
    {
        $branding = PlatformBranding::first() ?: new PlatformBranding();
        return view('admin.branding', compact('branding'));
    }

    public function update(Request $request)
    {
        $branding = PlatformBranding::first() ?: new PlatformBranding();

        $data = $request->only([
            'platform_name', 
            'stripe_live_pub_key', 
            'stripe_live_secret_key', 
            'stripe_test_pub_key', 
            'stripe_test_secret_key',
            'stripe_webhook_secret' // <--- CRITICAL: ADD THIS LINE
        ]);

        if ($request->hasFile('logo')) {
            if ($branding->logo) {
                Storage::disk('public')->delete($branding->logo);
            }
            $data['logo'] = $request->file('logo')->store('branding', 'public');
        }

        $branding->fill($data);
        $branding->save();

        return back()->with('success', 'Platform settings updated successfully!');
    }
}
