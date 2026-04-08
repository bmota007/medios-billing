@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-5">
        <h2 class="fw-bold text-white">Manual Account Setup</h2>
        <p class="text-secondary">Create a company and assign a subscription plan manually.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card rounded-4 border-0 shadow-lg" style="background: #1e293b;">
                <div class="card-body p-5">
                    <form action="{{ route('admin.sales.onboard_store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-bold text-uppercase">Legal Company Name</label>
                            <input type="text" name="company_name" class="form-control bg-dark border-secondary text-white p-3 shadow-none" placeholder="e.g. Acme Painting Co." required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-secondary small fw-bold text-uppercase">Primary Admin Email</label>
                            <input type="email" name="email" class="form-control bg-dark border-secondary text-white p-3 shadow-none" placeholder="owner@company.com" required>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Select Subscription Plan</label>
                                <select name="plan" class="form-select bg-dark border-secondary text-white p-3 shadow-none">
                                    <option value="starter">Starter Plan ($99/mo)</option>
                                    <option value="growth">Growth Plan ($199/mo)</option>
                                    <option value="elite">Elite Plan ($299/mo)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Special Handling</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_free" id="isFree">
                                    <label class="form-check-label text-white ms-2" for="isFree">Mark as Approved FREE</label>
                                </div>
                            </div>
                        </div>

                        <hr class="border-secondary my-5" style="opacity: 0.1;">
                        
                        <div class="p-3 rounded-3 mb-4" style="background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.2);">
                            <p class="text-info small mb-0"><i class="fa fa-info-circle me-2"></i> The system will generate a secure setup link and email it to the tenant immediately.</p>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-dark text-uppercase shadow-sm" style="letter-spacing: 1px;">Complete Onboarding</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="p-4 rounded-4 border border-secondary" style="border-style: dashed !important; background: rgba(30, 41, 59, 0.5);">
                <h5 class="text-white fw-bold mb-3">Sales Protocol</h5>
                <ul class="text-secondary small ps-3">
                    <li class="mb-2">Verify the customer's email before submitting.</li>
                    <li class="mb-2">"Free" accounts bypass Stripe verification. Use only for approved partners.</li>
                    <li class="mb-2">Tenants will be prompted to connect their own Stripe account during their first login.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
