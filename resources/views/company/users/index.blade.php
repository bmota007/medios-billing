@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-white font-bold mb-1">Company <span class="text-sky-400">Users</span></h2>
            <p class="text-secondary small">Manage your team members and their access levels</p>
        </div>
        <a href="{{ route('company.users.create') }}" class="btn btn-primary px-4 shadow-lg">
            <i class="fa-solid fa-user-plus mr-2"></i> Add Employee
        </a>
    </div>

    <div class="glass-card">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="text-secondary small uppercase tracking-wider">
                    <tr>
                        <th class="border-0 pb-3">Name</th>
                        <th class="border-0 pb-3">Email Address</th>
                        <th class="border-0 pb-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @foreach($users as $user)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                        <td class="py-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-sky-500/10 text-sky-400 rounded-circle d-flex align-items-center justify-content-center mr-3 font-bold" 
                                     style="width: 38px; height: 38px; margin-right: 12px; border: 1px solid rgba(56, 189, 248, 0.2); font-size: 0.8rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="text-white font-bold">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-secondary">
                            {{ $user->email }}
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('company.users.edit', $user->id) }}" 
                                   class="btn btn-sm btn-outline-primary border-opacity-20 px-3">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                </a>

                                <form method="POST" action="{{ route('company.users.delete', $user->id) }}" 
                                      style="display:inline;" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-opacity-20 px-3">
                                        <i class="fa-solid fa-trash-can mr-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .table-dark { --bs-table-bg: transparent; }
    .glass-card {
        background: rgba(30, 41, 59, 0.4);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        padding: 1.5rem;
    }
    /* Buttons Hover Effects */
    .btn-outline-primary:hover { background: #3b82f6; color: white; }
    .btn-outline-danger:hover { background: #ef4444; color: white; }
</style>
@endsection
