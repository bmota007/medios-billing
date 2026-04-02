@extends('layouts.admin')

@section('content')
<div class="container-fluid mb-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-white font-bold mb-1">Company <span class="text-sky-400">Control Panel</span></h2>
            <p class="text-secondary small">Management portal for <strong>{{ $company->name }}</strong></p>
        </div>
        <a href="{{ route('admin.companies') }}" class="btn btn-outline-secondary border-white/10 text-white px-4 font-bold">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Companies
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-emerald-500/10 border-emerald-500/20 text-emerald-400 mb-4">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="glass-card h-100">
                <h5 class="text-white mb-4"><i class="fa-solid fa-user-tie text-sky-400 mr-2"></i> Owner Information</h5>
                @foreach($company->users->where('role', 'admin') as $admin)
                    <div class="p-3 rounded bg-white/5 border border-white/5 mb-2">
                        <h6 class="text-white font-bold mb-0">{{ $admin->name }}</h6>
                        <p class="text-secondary small mb-0">{{ $admin->email }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-6">
            <div class="glass-card h-100">
                <h5 class="text-white mb-4"><i class="fa-solid fa-chart-line text-sky-400 mr-2"></i> Company Statistics</h5>
                <div class="row text-center">
                    <div class="col-4 border-end border-white/10">
                        <div class="text-white h4 font-bold mb-0">{{ $company->users->count() }}</div>
                        <div class="text-secondary small uppercase">Users</div>
                    </div>
                    <div class="col-4 border-end border-white/10">
                        <div class="text-white h4 font-bold mb-0">{{ $invoiceCount ?? 0 }}</div>
                        <div class="text-secondary small uppercase">Invoices</div>
                    </div>
                    <div class="col-4">
                        <div class="text-emerald-400 h4 font-bold mb-0">${{ number_format($invoiceTotal ?? 0, 2) }}</div>
                        <div class="text-secondary small uppercase">Revenue</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="glass-card">
                <h5 class="text-white mb-4"><i class="fa-solid fa-shield-check text-sky-400 mr-2"></i> Admin Tools</h5>
                <div class="d-flex flex-wrap gap-3">
                    
                    <form action="{{ route('admin.company.resetPassword', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning font-bold px-3 py-2 text-dark shadow-lg" onclick="return confirm('Reset password and show temporary key?')">
                            <i class="fa-solid fa-key mr-1"></i> Reset Password
                        </button>
                    </form>

                    <form action="{{ route('admin.company.resendWelcome', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-info font-bold px-3 py-2 text-white shadow-lg">
                            <i class="fa-solid fa-envelope mr-1"></i> Resend Welcome
                        </button>
                    </form>

                    <form action="{{ route('admin.impersonate', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary font-bold px-3 py-2 shadow-lg">
                            <i class="fa-solid fa-user-secret mr-1"></i> Login As Company
                        </button>
                    </form>

                    <form action="{{ route('admin.company.suspend', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $company->is_active ? 'btn-danger' : 'btn-success' }} font-bold px-3 py-2 shadow-lg">
                            <i class="fa-solid {{ $company->is_active ? 'fa-ban' : 'fa-check' }} mr-1"></i> 
                            {{ $company->is_active ? 'Suspend Company' : 'Activate Company' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.company.delete', $company->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger font-bold px-3 py-2 shadow-lg" onclick="return confirm('EXTREME DANGER: This will delete ALL data for this company. Continue?')">
                            <i class="fa-solid fa-trash-can mr-1"></i> Delete Company
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="glass-card">
                <h5 class="text-white mb-4"><i class="fa-solid fa-file-invoice mr-2"></i> Recent Invoices</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead class="text-secondary small uppercase">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentInvoices as $inv)
                                <tr>
                                    <td class="text-sky-400">#{{ $inv->id }}</td>
                                    <td>{{ $inv->customer_name }}</td>
                                    <td class="font-bold">${{ number_format($inv->total, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $inv->status == 'paid' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-warning/10 text-warning' }} border px-2 py-1">
                                            {{ strtoupper($inv->status) }}
                                        </span>
                                    </td>
                                    <td class="text-secondary">{{ $inv->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-secondary">No invoices found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-bold { font-weight: 700; }
    .uppercase { text-transform: uppercase; letter-spacing: 1px; }
    .glass-card {
        background: rgba(30, 41, 59, 0.4);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1rem;
        padding: 2rem;
    }
</style>
@endsection
