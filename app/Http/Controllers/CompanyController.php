<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;

class CompanyController extends Controller
{

    public function settings()
    {
        $company = Auth::user()->company;

        return view('company.settings', compact('company'));
    }


    public function update(Request $request)
    {

        $company = Auth::user()->company;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048'
        ]);

        $company->name = $request->name;
        $company->email = $request->email;
        $company->phone = $request->phone;
        $company->address = $request->address;

        if ($request->hasFile('logo')) {

            $path = $request->file('logo')->store('logos', 'public');

            $company->logo = '/storage/' . $path;

        }

        $company->save();

        return back()->with('success','Company settings updated successfully.');

    }

}
