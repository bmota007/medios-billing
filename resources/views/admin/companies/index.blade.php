@extends('layouts.admin')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:30px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <div>
            <h1 style="font-size:22px;font-weight:bold;color:#fff;">Companies</h1>
            <p style="color:#9ca3af;font-size:13px;">Manage and access your organizations</p>
        </div>
        <a href="{{ route('admin.companies.create') }}" style="background:#f59e0b;color:#fff;padding:10px 18px;border-radius:8px;font-weight:bold;text-decoration:none;">
            + Create Company
        </a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:25px;">
        @foreach($companies as $company)
            {{-- ✅ HIDE MEDIOS BILLING FROM CLIENT LIST --}}
            @if($company->name === 'Medios Billing' || $company->plan === 'SYSTEM') @continue @endif

        <div style="background:#ffffff;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.2);display:flex;flex-direction:column;">
            <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                <div style="display:flex;gap:12px;">
                    <div style="width:48px;height:48px;border-radius:50%;background:#3b82f6;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-weight:800;color:#111827;">{{ $company->name }}</div>
                        <div style="font-size:12px;color:#6b7280;">{{ $company->email }}</div>
                    </div>
                </div>
                <div style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px; 
                    {{ $company->subscription_status === 'active' ? 'background:#ecfdf5;color:#059669;' : 'background:#f3f4f6;color:#6b7280;' }}">
                    {{ strtoupper($company->subscription_status ?? 'INACTIVE') }}
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <span style="font-size:26px;font-weight:800;color:#111827;">${{ number_format($company->mrr ?? 0) }}</span>
                <span style="font-size:14px;color:#9ca3af;">/mo</span>
            </div>

            <a href="{{ route('admin.impersonate', $company->id) }}" style="display:block;width:100%;text-align:center;background:#f59e0b;color:#fff;padding:12px;border-radius:10px;font-weight:bold;text-decoration:none;margin-bottom:10px;">
                Login →
            </a>

            <div style="display:flex;gap:10px;">
                {{-- ✅ TOGGLE BUTTON --}}
                <form action="{{ route('admin.companies.toggle', $company->id) }}" method="POST" style="flex:2;">
                    @csrf
                    <button type="submit" style="width:100%; border:1px solid #e5e7eb; padding:8px; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;
                        {{ $company->subscription_status === 'active' ? 'background:#f9fafb;color:#ef4444;' : 'background:#10b981;color:#fff;border-color:#059669;' }}">
                        {{ $company->subscription_status === 'active' ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>

                {{-- ✅ DELETE BUTTON --}}
                <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" onsubmit="return confirm('Delete this company and all its data?')" style="flex:1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width:100%;border:1px solid #fee2e2;background:#fef2f2;color:#ef4444;padding:8px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
