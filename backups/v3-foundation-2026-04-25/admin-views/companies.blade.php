@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h2 class="text-white fw-bold mb-1">Companies</h2>
            <p class="text-secondary small">Manage and access your organizations</p>
        </div>

        <a href="/admin/companies/create"
           class="btn fw-bold px-4 py-2"
           style="background: linear-gradient(135deg,#fbbf24,#f59e0b); border-radius: 12px; color:#000;">
            + Create Company
        </a>
    </div>

    <!-- GRID -->
    <div class="row g-4">

        @forelse($companies as $company)
        @php
            $isActive = $company->subscription_status === 'active' || ($company->is_active ?? false);
        @endphp

        <div class="col-12 col-md-6 col-lg-4 d-flex justify-content-center">

            <!-- CARD -->
            <div class="company-card">

                <!-- STATUS -->
                <div class="status-badge {{ $isActive ? 'active' : 'inactive' }}">
                    {{ $isActive ? 'ACTIVE' : 'INACTIVE' }}
                </div>

                <!-- TOP -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>

                    <div>
                        <h5 class="mb-0 fw-bold text-dark">
                            {{ $company->name }}
                        </h5>
                        <small class="text-muted">
                            {{ $company->email ?? 'No email' }}
                        </small>
                    </div>
                </div>

                <!-- PLAN -->
                <span class="plan-badge">FREE</span>

                <!-- PRICE -->
                <div class="mb-3 mt-2">
                    <h2 class="fw-bold text-dark mb-0">
                        ${{ number_format($company->monthly_price ?? 0, 2) }}
                        <span class="text-muted fs-6">/mo</span>
                    </h2>
                </div>

                <!-- LOGIN -->
                <a href="{{ route('admin.impersonate', $company->id) }}"
                   class="btn login-btn w-100 mb-3">
                    Login →
                </a>

                <!-- ACTIONS -->
                <div class="d-flex gap-2">

                    <form action="{{ route('admin.toggleStatus', $company->id) }}" method="POST" class="w-100">
                        @csrf
                        <button class="btn action-btn w-100">
                            {{ $isActive ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.companies.destroy', $company->id) }}"
                          method="POST"
                          class="w-100"
                          onsubmit="return confirm('Delete this company?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn delete-btn w-100">
                            Delete
                        </button>
                    </form>

                </div>

            </div>

        </div>

        @empty
            <div class="col-12 text-center text-muted py-5">
                No companies found
            </div>
        @endforelse

    </div>

</div>

<style>

/* CARD */
.company-card {
    position: relative;
    background: #ffffff;
    border-radius: 20px;
    padding: 22px;
    width: 100%;
    max-width: 360px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.25);
    transition: all 0.25s ease;
}

.company-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.35);
}

/* STATUS */
.status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 10px;
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 800;
}

.status-badge.active {
    background: #dcfce7;
    color: #166534;
}

.status-badge.inactive {
    background: #e5e7eb;
    color: #374151;
}

/* AVATAR */
.avatar {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg,#3b82f6,#2563eb);
    color: #fff;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

/* PLAN */
.plan-badge {
    display: inline-block;
    background: #eff6ff;
    color: #2563eb;
    padding: 3px 8px;
    font-size: 10px;
    border-radius: 6px;
    font-weight: 800;
}

/* LOGIN BUTTON */
.login-btn {
    background: linear-gradient(135deg,#fbbf24,#f59e0b);
    border: none;
    border-radius: 10px;
    font-weight: bold;
    color: black;
    transition: 0.2s;
}

.login-btn:hover {
    transform: scale(1.03);
}

/* ACTION BUTTON */
.action-btn {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    background: #fff;
    color: #475569;
}

/* DELETE BUTTON */
.delete-btn {
    border: 1px solid #ef4444;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    background: #fff;
    color: #ef4444;
}

</style>

@endsection
