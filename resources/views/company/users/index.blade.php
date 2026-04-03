@extends('layouts.admin')

@section('content')
<div class="container-fluid">

<div class="crm-header mb-4">
    <div>
        <h2 class="crm-title">Team <span class="text-sky-400">Members</span></h2>
        <p class="crm-subtitle">Manage employee access and platform roles</p>
    </div>

    <div class="crm-actions">
        <button type="button" class="btn btn-success crm-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
            + Add Member
        </button>
    </div>
</div>

{{-- Notifications --}}
@if(session('success'))
    <div class="alert alert-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400 mb-4">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger bg-red-500/10 border-red-500/20 text-red-400 mb-4">
        <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
    </div>
@endif

<div class="row">
@forelse($users as $user)
<div class="col-12 mb-3">
    <div class="crm-row">
        <div class="crm-left">
            <div class="avatar-circle" style="background: {{ $user->role === 'admin' ? '#0ea5e9' : ($user->role === 'manager' ? '#fbbf24' : '#64748b') }};">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="crm-name">{{ $user->name }}</div>
                <div class="crm-sub">{{ $user->email }}</div>
            </div>
        </div>

        <div class="crm-center">
            <div class="crm-stat">
                <span>Role</span>
                <strong class="{{ $user->role === 'admin' ? 'text-info' : ($user->role === 'manager' ? 'text-warning' : 'text-secondary') }}">
                    {{ strtoupper($user->role) }}
                </strong>
            </div>
            <div class="crm-stat">
                <span>Joined</span>
                <strong>{{ $user->created_at->format('M d, Y') }}</strong>
            </div>
        </div>

        <div class="crm-right">
            <div class="d-flex justify-content-end align-items-center gap-2">
                @if($user->id !== auth()->id())
                    {{-- RESET PASSWORD BUTTON --}}
                    <form action="{{ route('users.reset', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset password and email new credentials?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Reset Password">
                            <i class="fa-solid fa-key"></i>
                        </button>
                    </form>

                    {{-- DELETE BUTTON --}}
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this user?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Remove User">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                @else
                    <span class="badge bg-secondary text-uppercase" style="font-size: 10px; padding: 8px 12px;">You</span>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="glass-card text-center py-5">
        <p class="text-secondary">No team members found.</p>
    </div>
</div>
@endforelse
</div>

{{-- MODAL --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: #1e293b; border-radius: 1.5rem;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white fw-bold">New Team Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Full Name</label>
                        <input type="text" name="name" class="form-control custom-input" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Email Address</label>
                        <input type="email" name="email" class="form-control custom-input" placeholder="name@company.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Password</label>
                        <input type="password" name="password" class="form-control custom-input" placeholder="Min. 8 characters" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 small uppercase fw-bold mb-2 d-block">Role</label>
                        <select name="role" class="form-select custom-input" required>
                            <option value="staff">Staff (Field Tech)</option>
                            <option value="manager">Manager (Operations)</option>
                            <option value="admin" selected>Admin (Full Control)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

</div>

<style>
.custom-input { background: #0f172a !important; border: 1px solid rgba(255,255,255,0.1) !important; color: #ffffff !important; padding: 12px 15px !important; border-radius: 10px !important; }
.crm-header { display: flex; justify-content: space-between; align-items: center; }
.crm-title { font-size: 28px; font-weight: 700; color: white; }
.crm-row { display: flex; justify-content: space-between; align-items: center; background: rgba(15,23,42,0.9); padding: 20px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.05); }
.avatar-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
</style>
@endsection
