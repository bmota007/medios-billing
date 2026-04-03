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
    public function index()
    {
        $user = Auth::user();

        if (!$user->company_id) {
            return redirect()->route('admin.dashboard')
                ->with('error','Super Admin cannot access company employees.');
        }

        $users = User::where('company_id', $user->company_id)->latest()->get();

        return view('company.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'role' => 'required|in:admin,staff'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'company_id' => Auth::user()->company_id,
                'role' => $request->role,
                'is_admin' => ($request->role === 'admin' ? 1 : 0),
                'needs_password_change' => true, // Force change on first login
            ]);

            $details = [
                'name' => $request->name,
                'email' => $request->email,
                'temp_password' => $request->password,
                'company' => Auth::user()->company->name ?? 'Medios Billing'
            ];

            try {
                Mail::to($user->email)->send(new WelcomeStaffMail($details));
            } catch (\Exception $mailError) {
                Log::error("Mail failed to send: " . $mailError->getMessage());
            }

            return redirect()->route('users.index')->with('success','Team member created and welcome email sent.');

        } catch (\Exception $e) {
            Log::error("User Creation Error: " . $e->getMessage());
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    /**
     * Admin Reset Password Feature
     */
    public function resetPassword($id)
    {
        $user = User::where('id', $id)->where('company_id', Auth::user()->company_id)->firstOrFail();
        
        // Generate a random 10-character password
        $newPassword = Str::random(10);
        
        $user->update([
            'password' => Hash::make($newPassword),
            'needs_password_change' => true
        ]);

        $details = [
            'name' => $user->name,
            'email' => $user->email,
            'temp_password' => $newPassword,
            'company' => Auth::user()->company->name
        ];

        try {
            Mail::to($user->email)->send(new WelcomeStaffMail($details));
            return back()->with('success', 'Password reset! New credentials emailed to ' . $user->email);
        } catch (\Exception $e) {
            return back()->with('success', 'Password reset to: ' . $newPassword . ' (Email failed to send)');
        }
    }

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
