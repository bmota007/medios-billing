@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">
                Team <span class="text-sky-400">Users</span>
            </h1>
            <p class="text-slate-400 mt-1">
                Manage staff access, roles, and permissions
            </p>
        </div>

        <button onclick="document.getElementById('userModal').classList.remove('hidden')"
            class="px-5 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold">
            + Add Team Member
        </button>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-500/10 border border-red-500/20 px-4 py-3 text-red-300">
            {{ session('error') }}
        </div>
    @endif

    {{-- Plan Usage --}}
    <div class="mb-6 rounded-2xl bg-slate-900/70 border border-white/5 p-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-white font-bold text-lg">
                    {{ $planName }} Plan
                </div>
                <div class="text-slate-400 text-sm">
                    {{ $userCount }} / {{ $planLimit >= 999999 ? 'Unlimited' : $planLimit }} Users Used
                </div>
            </div>

            @if($planLimit < 999999 && $userCount >= $planLimit)
                <a href="{{ route('subscription.portal') }}"
                   class="px-4 py-2 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30">
                    Upgrade Plan
                </a>
            @endif
        </div>
    </div>

    {{-- Users --}}
    <div class="space-y-4">

        @forelse($users as $user)

        <div class="rounded-2xl bg-slate-900/70 border border-white/5 p-5 flex items-center justify-between">

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-sky-500 flex items-center justify-center font-bold text-white">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>

                <div>
                    <div class="text-white font-semibold">
                        {{ $user->name }}
                    </div>

                    <div class="text-slate-400 text-sm">
                        {{ $user->email }}
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-6">

                <div class="text-right">
                    <div class="text-slate-400 text-xs uppercase">
                        Role
                    </div>

                    <div class="text-white font-semibold">
                        {{ ucwords(str_replace('_',' ',$user->role_name)) }}
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-slate-400 text-xs uppercase">
                        Joined
                    </div>

                    <div class="text-white">
                        {{ $user->created_at->format('M d, Y') }}
                    </div>
                </div>

                @if($user->id !== auth()->id())

                <div class="flex gap-2">

                    <form method="POST" action="{{ url('/company/users/reset/'.$user->id) }}">
                        @csrf
                        <button class="px-3 py-2 rounded-xl bg-amber-500/20 text-amber-300 hover:bg-amber-500/30">
                            Reset
                        </button>
                    </form>

                    <form method="POST" action="{{ url('/company/users/delete/'.$user->id) }}">
                        @csrf
                        @method('DELETE')

                        <button onclick="return confirm('Delete user?')"
                            class="px-3 py-2 rounded-xl bg-red-500/20 text-red-300 hover:bg-red-500/30">
                            Delete
                        </button>
                    </form>

                </div>

                @else

                <span class="px-3 py-2 rounded-xl bg-slate-700 text-slate-300 text-sm">
                    You
                </span>

                @endif

            </div>

        </div>

        @empty

        <div class="rounded-2xl bg-slate-900/70 border border-white/5 p-10 text-center text-slate-400">
            No team members found.
        </div>

        @endforelse

    </div>

</div>

{{-- Modal --}}
<div id="userModal" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center">

    <div class="w-full max-w-xl rounded-2xl bg-slate-900 border border-white/10 p-6">

        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-bold text-white">Create Team User</h2>

            <button onclick="document.getElementById('userModal').classList.add('hidden')"
                class="text-slate-400 hover:text-white">✕</button>
        </div>

        <form method="POST" action="{{ route('company.users.store') }}">
            @csrf

            <div class="space-y-4">

                <input type="text" name="name" placeholder="Full Name"
                    class="w-full rounded-xl bg-slate-800 border border-white/10 text-white px-4 py-3">

                <input type="email" name="email" placeholder="Email Address"
                    class="w-full rounded-xl bg-slate-800 border border-white/10 text-white px-4 py-3">

                <input type="password" name="password" placeholder="Temporary Password"
                    class="w-full rounded-xl bg-slate-800 border border-white/10 text-white px-4 py-3">

                <select name="role"
                    class="w-full rounded-xl bg-slate-800 border border-white/10 text-white px-4 py-3">

                    <option value="owner">Owner</option>
                    <option value="regional_director">Regional Director</option>
                    <option value="sales_director">Sales Director</option>
                    <option value="manager">Manager</option>
                    <option value="accounting">Accounting</option>
                    <option value="sales">Sales</option>
                    <option value="support">Support</option>
                    <option value="staff" selected>Staff</option>
                    <option value="viewer">Viewer</option>

                </select>

            </div>

            <div class="mt-6 flex justify-end gap-3">

                <button type="button"
                    onclick="document.getElementById('userModal').classList.add('hidden')"
                    class="px-4 py-3 rounded-xl bg-slate-700 text-white">
                    Cancel
                </button>

                <button type="submit"
                    class="px-5 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold">
                    Create User
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
