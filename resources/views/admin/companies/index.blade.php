@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-white mb-1">Companies</h2>
            <p style="color:#94a3b8 !important;" class="small">Manage and access your organizations</p>
        </div>

        <a href="{{ route('admin.companies.create') }}"
           class="btn fw-bold px-4 shadow-sm"
           style="border-radius: 10px; background: #f59e0b !important; color: #000 !important; border: none;">
            + Create Company
        </a>
    </div>

    {{-- GRID --}}
    <div class="row g-4">

        @forelse($companies as $company)
        @php
            $isActive = $company->subscription_status === 'active' || ($company->is_active ?? false);
        @endphp

        <div class="col-12 col-md-6 col-lg-4">
            {{-- THE CARD --}}
            <div style="
                background: #ffffff !important;
                border-radius: 25px;
                padding: 24px;
                box-shadow: 0 15px 35px rgba(0,0,0,0.3);
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                transition: transform 0.2s;
                border: 1px solid #e2e8f0;
            ">
                
                <div>
                    {{-- TOP HEADER --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div style="width:48px; height:48px; background:#3b82f6 !important; color:#ffffff !important; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800;">
                                {{ strtoupper(substr($company->name, 0, 2)) }}
                            </div>
                            <div class="ms-3">
                                {{-- 🔥 FORCED BLACK TEXT --}}
                                <h5 style="color: #111827 !important; font-weight: 800 !important; margin: 0; font-size: 1.1rem; display: block !important;">
                                    {{ $company->name }}
                                </h5>
                                <small style="color: #6b7280 !important; font-weight: 700 !important; display: block !important;">
                                    {{ $company->industry ?? 'Technology' }}
                                </small>
                            </div>
                        </div>

                        {{-- STATUS BADGE --}}
                        <span style="background: #dcfce7 !important; color: #16a34a !important; padding:4px 10px; border-radius:8px; font-size:10px; font-weight:800;">
                            ACTIVE
                        </span>
                    </div>

                    {{-- BLUE PLAN TAG --}}
                    <span style="background:#eff6ff !important; color:#1d4ed8 !important; padding:3px 8px; font-size:10px; border-radius:6px; font-weight:800; display: inline-block; margin-bottom: 10px;">
                        FREE
                    </span>

                    {{-- PRICE (FORCED BLACK) --}}
                    <div class="mb-2">
                        <h3 style="color: #111827 !important; font-weight: 800 !important; font-size: 1.6rem; margin: 0 !important;">
                            $0.00<span style="color: #94a3b8 !important; font-size:14px; font-weight: 400;">/mo</span>
                        </h3>
                    </div>

                    {{-- EMAIL (FORCED GREY) --}}
                    <p style="color: #4b5563 !important; font-size: 0.85rem; margin-bottom: 15px !important; font-weight: 600 !important; display: block !important;">
                        {{ $company->email }}
                    </p>
                </div>

                {{-- BUTTONS SECTION --}}
                <div class="mt-auto">
                    <a href="{{ route('admin.impersonate', $company->id) }}"
                       class="btn fw-bold w-100 mb-2"
                       style="background:#ffc107 !important; color:#000 !important; border-radius:12px; padding: 10px; font-size: 14px; border: none; display: block; text-align: center; text-decoration: none;">
                        Login →
                    </a>

                    <div class="d-flex gap-2">
                        <button class="btn w-50 fw-bold" style="border: 1px solid #e2e8f0 !important; border-radius: 10px; color: #64748b !important; font-size: 12px; padding: 8px; background: #f8fafc !important;">
                            Deactivate
                        </button>
                        <button class="btn w-50 fw-bold" style="border: 1px solid #fee2e2 !important; border-radius: 10px; color: #ef4444 !important; font-size: 12px; padding: 8px; background: #fff1f2 !important;">
                            Delete
                        </button>
                    </div>
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
@endsection
