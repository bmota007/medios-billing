<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'yearly_price' => $request->yearly_price,
            'badge' => $request->badge,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return back()->with('success', 'Plan updated.');
    }
}
