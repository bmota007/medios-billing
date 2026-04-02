<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

public function index()
{

    $user = Auth::user();

    if (!$user->company_id) {
        return redirect()->route('admin.dashboard')
            ->with('error','Super Admin cannot access company employees.');
    }

    $company = $user->company;

    $users = User::where('company_id', $company->id)->get();

    return view('company.users.index', compact('users'));

}


    public function create()
    {
        return view('company.users.create');
    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => Auth::user()->company_id,
            'is_admin' => 0
        ]);

        return redirect()->route('company.users')->with('success','Employee created.');
    }


    public function edit(User $user)
    {
        $company = Auth::user()->company;

        if ($user->company_id != $company->id) {
            abort(403);
        }

        return view('company.users.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {

        $company = Auth::user()->company;

        if ($user->company_id != $company->id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('company.users')->with('success','User updated.');
    }


    public function destroy(User $user)
    {
        $company = Auth::user()->company;

        if ($user->company_id != $company->id) {
            abort(403);
        }

        $user->delete();

        return back()->with('success','User removed.');
    }

}
