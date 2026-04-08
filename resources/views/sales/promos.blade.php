@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-5">
        <h2 class="fw-bold text-white">Promos & Credits</h2>
        <p class="text-secondary">Manage special pricing, discounts, and approved free accounts.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card rounded-4 border-0 shadow-lg" style="background: #1e293b;">
                <div class="card-header bg-transparent border-secondary p-4">
                    <h5 class="text-white fw-bold mb-0"><i class="fa fa-star text-warning me-2"></i>Approved Free Accounts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr class="text-secondary small text-uppercase">
                                    <th class="px-4">Company</th>
                                    <th>Plan Type</th>
                                    <th>Approved Date</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-secondary">
                                    <td class="px-4 py-3">
                                        <div class="fw-bold text-white">Example Partnership</div>
                                        <div class="small text-secondary">partner@example.com</div>
                                    </td>
                                    <td><span class="badge bg-info">Growth (Free)</span></td>
                                    <td>04/01/2026</td>
                                    <td class="text-end px-4">
                                        <button class="btn btn-sm btn-outline-danger">Revoke</button>
                                    </td>
                                </tr>
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card rounded-4 border-0 shadow-lg" style="background: #1e293b; border: 1px dashed #475569 !important;">
                <div class="card-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fa fa-gift text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="text-white fw-bold">Issue Credit</h5>
                    <p class="text-secondary small">Apply a one-time credit or permanent discount to a tenant's billing profile.</p>
                    <button class="btn btn-info w-100 fw-bold text-dark mt-2">Generate Promo Code</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
