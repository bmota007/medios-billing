<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\WelcomeStaffMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PLAN LIMIT ENGINE
    |--------------------------------------------------------------------------
    */
    private function getPlanLimit($company)
    {
        $plan = strtolower($company->plan_name ?? $company->plan ?? 'starter');

        if ($plan === 'pro' || $plan === 'premium') {
            return 999999; // unlimited
        }

        if ($plan === 'growth') {
            return 5;
        }

        return 1; // starter
    }

    private function getPlanName($company)
    {
        $plan = strtolower($company->plan_name ?? $company->plan ?? 'starter');

        if ($plan === 'premium') {
            return 'Pro';
        }

        return ucfirst($plan);
    }

    /*
    |--------------------------------------------------------------------------
    | TEAM MEMBERS PAGE
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Super Admin cannot access company employees.');
        }

        $company = $user->company;

        $users = User::where('company_id', $user->company_id)
            ->latest()
            ->get();

        $userCount = $users->count();
        $planLimit = $this->getPlanLimit($company);
        $planName  = $this->getPlanName($company);

        return view('company.users.index', compact(
            'users',
            'company',
            'userCount',
            'planLimit',
            'planName'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE USER
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        try {
            $authUser = Auth::user();

            if (!$authUser->company_id) {
                return back()->with('error', 'No company assigned.');
            }

            $company = $authUser->company;

            /*
            |--------------------------------------------------------------------------
            | PLAN LIMIT CHECK
            |--------------------------------------------------------------------------
            */
            $currentUsers = User::where('company_id', $authUser->company_id)->count();
            $planLimit    = $this->getPlanLimit($company);
            $planName     = $this->getPlanName($company);

            if ($currentUsers >= $planLimit) {

                if ($planName === 'Starter') {
                    return back()->with(
                        'error',
                        'Starter plan allows 1 user only. Upgrade to Growth to add team members.'
                    );
                }

                if ($planName === 'Growth') {
                    return back()->with(
                        'error',
                        'Growth plan allows up to 5 users. Upgrade to Pro for unlimited users.'
                    );
                }

                return back()->with('error', 'User limit reached.');
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8',
            ]);

            $role = $request->input('role', 'staff');

            /*
            |--------------------------------------------------------------------------
            | CREATE USER
            |--------------------------------------------------------------------------
            */
            $user = User::create([
                'name'                  => $request->name,
                'email'                 => $request->email,
                'password'              => Hash::make($request->password),
                'company_id'            => $authUser->company_id,
                'role'                  => $role,
                'is_admin'              => ($role === 'admin' ? 1 : 0),
                'needs_password_change' => true,
            ]);

            /*
            |--------------------------------------------------------------------------
            | SEND WELCOME EMAIL
            |--------------------------------------------------------------------------
            */
            $details = [
                'name'          => $request->name,
                'email'         => $request->email,
                'temp_password' => $request->password,
                'company'       => $company->name ?? 'Medios Billing',
            ];

            try {
                Mail::to($user->email)->send(new WelcomeStaffMail($details));
            } catch (\Exception $mailError) {
                Log::error("Mail failed: " . $mailError->getMessage());
            }

            return redirect()
                ->route('users.index')
                ->with('success', 'Team member created successfully.');

        } catch (\Exception $e) {

            Log::error("User Creation Error: " . $e->getMessage());

            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $errors = implode(' ', \Illuminate\Support\Arr::flatten($e->errors()));

                return back()->with(
                    'error',
                    'Validation Failed: ' . $errors
                );
            }

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESET PASSWORD
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
            'needs_password_change' => true
        ]);

        $details = [
            'name'          => $user->name,
            'email'         => $user->email,
            'temp_password' => $newPassword,
            'company'       => Auth::user()->company->name
        ];

        try {
            Mail::to($user->email)->send(new WelcomeStaffMail($details));

            return back()->with(
                'success',
                'Password reset! New credentials emailed.'
            );

        } catch (\Exception $e) {

            return back()->with(
                'success',
                'Password reset to: ' . $newPassword
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $user = User::where('id', $id)
            ->where('company_id', Auth::user()->company_id)
            ->firstOrFail();

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        return back()->with('success', 'User removed.');
    }
}
