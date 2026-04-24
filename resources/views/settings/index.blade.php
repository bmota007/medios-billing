@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background: #020617; min-height: 100vh;">
    <div class="mb-5">
        <h1 class="text-white fw-900" style="letter-spacing: -2px;">Integrations & <span class="text-info">Legal Library</span></h1>
        <p class="text-secondary small fw-bold">Configure your global payment gateway and manage strategic contract templates.</p>
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- STRIPE PANEL --}}
            <div class="col-md-5">
                <div class="card border-0 rounded-5 p-4 shadow-lg h-100" style="background: #0f172a; border: 1px solid rgba(56, 189, 248, 0.2) !important;">
                    <h4 class="text-white fw-bold mb-4"><i class="fa-brands fa-stripe text-info me-2"></i> Payment Gateway</h4>
                    <div class="mb-3">
                        <label class="text-secondary small fw-bold uppercase">Stripe Public Key</label>
                        <input type="text" name="client_stripe_key" class="form-control premium-input mt-1" value="{{ $company->client_stripe_key }}">
                    </div>
                    <div class="mb-4">
                        <label class="text-secondary small fw-bold uppercase">Stripe Secret Key</label>
                        <input type="password" name="client_stripe_secret" class="form-control premium-input mt-1" value="{{ $company->client_stripe_secret }}">
                    </div>
                    <button type="submit" class="btn btn-info w-100 fw-900 py-3 shadow-lg">SAVE GATEWAY CONFIG</button>
                </div>
            </div>

            {{-- CONTRACT PANEL --}}
            <div class="col-md-7">
                <div class="card border-0 rounded-5 p-4 shadow-lg" style="background: #0f172a; border: 1px solid rgba(245, 158, 11, 0.2) !important;">
                    <h4 class="text-white fw-bold mb-4"><i class="fa-solid fa-file-signature text-warning me-2"></i> Template Library (Max 4)</h4>
                    
                    @for($i = 1; $i <= 4; $i++)
                    @php $nameF = "contract_{$i}_name"; $pathF = "contract_{$i}_path"; @endphp
                    <div class="mb-4 p-3 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <label class="text-secondary small fw-bold uppercase">Template {{ $i }} Name</label>
                                <input type="text" name="contract_{{ $i }}_name" class="form-control premium-input mt-1" value="{{ $company->$nameF }}" placeholder="e.g. Website Design (Spanish)">
                            </div>
                            <div class="col-md-6">
                                <label class="text-secondary small fw-bold uppercase">PDF Attachment</label>
                                <input type="file" name="contract_{{ $i }}_file" class="form-control premium-input mt-1">
                                @if($company->$pathF)
                                    <div class="mt-2 text-success small fw-bold"><i class="fa-solid fa-circle-check"></i> File Active</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endfor
                    
                    <button type="submit" class="btn btn-warning w-100 fw-900 text-dark py-3 shadow-lg mt-2">UPDATE ALL TEMPLATES</button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .fw-900 { font-weight: 900 !important; }
    .premium-input { background: #1e293b !important; border: 1px solid #334155 !important; color: #ffffff !important; border-radius: 12px !important; padding: 12px 15px !important; }
    .premium-input:focus { border-color: #38bdf8 !important; box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1) !important; }
</style>
@endsection
