<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\WelcomeStaffMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PLAN LIMITS
    |--------------------------------------------------------------------------
    */
    private function getPlanLimit($company)
    {
        $plan = strtolower($company->plan_name ?? $company->plan ?? 'starter');

        if (in_array($plan, ['pro', 'premium'])) {
            return 999999;
        }

        if ($plan === 'growth') {
            return 5;
        }

        return 1;
    }

    private function getPlanName($company)
    {
        $plan = strtolower($company->plan_name ?? $company->plan ?? 'starter');

        if (in_array($plan, ['premium'])) {
            return 'Pro';
        }

        return ucfirst($plan);
    }

    /*
    |--------------------------------------------------------------------------
    | Allowed Roles
    |--------------------------------------------------------------------------
    */
    private function allowedRoles()
    {
        return [
            'owner',
            'regional_director',
            'sales_director',
            'manager',
            'accounting',
            'sales',
            'support',
            'staff',
            'viewer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Team Users Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $auth = Auth::user();

        if (!$auth->company_id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Super Admin cannot access company team users.');
        }

        $company = $auth->company;

        $users = User::where('company_id', $auth->company_id)
            ->latest()
            ->get();

        $userCount = $users->count();
        $planLimit = $this->getPlanLimit($company);
        $planName  = $this->getPlanName($company);
        $roles     = $this->allowedRoles();

        return view('company.users.index', compact(
            'users',
            'company',
            'userCount',
            'planLimit',
            'planName',
            'roles'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Store User
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        try {
            $auth = Auth::user();

            if (!$auth->company_id) {
                return back()->with('error', 'No company assigned.');
            }

            $company = $auth->company;

            $currentUsers = User::where('company_id', $auth->company_id)->count();
            $planLimit    = $this->getPlanLimit($company);
            $planName     = $this->getPlanName($company);

            if ($currentUsers >= $planLimit) {

                if ($planName === 'Starter') {
                    return back()->with(
                        'error',
                        'Starter plan allows 1 user only. Upgrade to Growth.'
                    );
                }

                if ($planName === 'Growth') {
                    return back()->with(
                        'error',
                        'Growth allows 5 users. Upgrade to Pro.'
                    );
                }

                return back()->with('error', 'User limit reached.');
            }

            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'role'     => 'required|string',
            ]);

            $role = strtolower($request->role);

            if (!in_array($role, $this->allowedRoles())) {
                $role = 'staff';
            }

            $user = User::create([
                'name'                  => $request->name,
                'email'                 => $request->email,
                'password'              => Hash::make($request->password),
                'company_id'            => $auth->company_id,
                'role'                  => $role,
                'is_admin'              => in_array($role, [
                    'owner',
                    'regional_director',
                    'sales_director',
                    'manager'
                ]),
                'needs_password_change' => true,
            ]);

            $details = [
                'name'          => $user->name,
                'email'         => $user->email,
                'temp_password' => $request->password,
                'company'       => $company->name,
            ];

            try {
                Mail::to($user->email)->send(new WelcomeStaffMail($details));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            return redirect()
                ->route('company.users')
                ->with('success', 'Team member created successfully.');

        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */
    public function resetPassword($id)
    {
        $user = User::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        $newPassword = Str::random(10);

        $user->update([
            'password'              => Hash::make($newPassword),
            'needs_password_change' => true,
        ]);

        return back()->with(
            'success',
            'Password reset. Temporary password: '.$newPassword
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete User
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $user = User::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User removed.');
    }
}
