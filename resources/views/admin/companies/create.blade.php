@extends('layouts.app')

@section('content')
<div style="display:flex; justify-content:center; align-items:center; min-height:80vh; padding:20px;">
    <div style="background:rgba(30, 41, 59, 0.7); backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.1); padding:40px; border-radius:24px; width:100%; max-width:500px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);">
        
        <div style="text-align:center; margin-bottom:30px;">
            <h1 style="color:#fff; font-size:24px; font-weight:800; margin-bottom:8px;">Provision New Node</h1>
            <p style="color:#94a3b8; font-size:14px;">Onboard a new entity to the Medios ecosystem</p>
        </div>

        <form action="{{ route('admin.companies.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:20px;">
                <label style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; text-transform:uppercase; margin-bottom:8px; letter-spacing:1px;">Business Name</label>
                <input type="text" name="name" required placeholder="e.g. McIntosh Cleaning" style="width:100%; padding:12px; background:rgba(15, 23, 42, 0.6); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#fff; outline:none;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; text-transform:uppercase; margin-bottom:8px; letter-spacing:1px;">Admin Email</label>
                <input type="email" name="email" required placeholder="owner@company.com" style="width:100%; padding:12px; background:rgba(15, 23, 42, 0.6); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#fff; outline:none;">
            </div>

            <div style="margin-bottom:30px;">
                <label style="display:block; color:#cbd5e1; font-size:12px; font-weight:700; text-transform:uppercase; margin-bottom:8px; letter-spacing:1px;">Subscription Plan</label>
                <select name="plan" style="width:100%; padding:12px; background:rgba(15, 23, 42, 0.6); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#fff; outline:none;">
                    <option value="PRO">Professional Plan</option>
                    <option value="ENTERPRISE">Enterprise Plan</option>
                    <option value="FREE">Free Trial</option>
                </select>
            </div>

            <button type="submit" style="width:100%; background:#f59e0b; color:#fff; padding:14px; border:none; border-radius:12px; font-weight:800; cursor:pointer; transition:0.3s; box-shadow:0 10px 15px -3px rgba(245, 158, 11, 0.3);">
                CREATE ENTITY →
            </button>
            
            <a href="{{ route('admin.companies') }}" style="display:block; text-align:center; margin-top:20px; color:#94a3b8; text-decoration:none; font-size:13px; font-weight:600;">Cancel and Return</a>
        </form>
    </div>
</div>
@endsection
