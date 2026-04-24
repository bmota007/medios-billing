<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class PricingController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active',1)
            ->orderBy('sort_order')
            ->get();

        return view('pricing.index', compact('plans'));
    }
}
