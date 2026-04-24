@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0" style="max-width: 950px; margin: auto; background: #fff; color: #333; border-radius: 20px; overflow: hidden;">

        {{-- Header --}}
        <div class="p-5 text-center" style="background: #0f172a; color: #fff;">
            <h2 class="fw-bold mb-1">{{ strtoupper($quote->company->name) }}</h2>
            <p class="mb-0 opacity-75">{{ $quote->company->address }}</p>
            <p class="mb-0 opacity-75">Document #{{ $quote->quote_number }}</p>
            <h3 class="mt-3 fw-light">
                {{ $selectedContractName ?: 'GENERAL SERVICES AGREEMENT' }}
            </h3>
        </div>

        <div class="card-body p-5">

            {{-- If uploaded contract PDF exists, show it --}}
            @if(!empty($selectedContractUrl))
                <h5 class="fw-bold border-bottom pb-2 text-primary text-uppercase small">Selected Contract</h5>
                <div class="mb-5 mt-3">
                    <iframe
                        src="{{ $selectedContractUrl }}"
                        style="width: 100%; height: 900px; border: 1px solid #cbd5e1; border-radius: 16px; background: #fff;">
                    </iframe>
                </div>
            @else
                {{-- Legacy fallback HTML contract --}}
                <h5 class="fw-bold border-bottom pb-2 text-primary text-uppercase small">Project Financials</h5>
                <div class="bg-light p-4 rounded-4 mb-4">
                    <table class="table table-borderless m-0">
                        <thead>
                            <tr class="small text-muted text-uppercase">
                                <th>Service</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr class="border-bottom">
                                <td class="py-3 fw-bold">{{ $item->service_name }}</td>
                                <td class="py-3 text-center">{{ (int)$item->quantity }}</td>
                                <td class="py-3 text-end fw-bold">${{ number_format($item->line_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-end mt-3">
                        <span class="text-muted uppercase small">Total Agreement Amount:</span>
                        <h2 class="fw-900 text-dark">${{ number_format($quote->total, 2) }}</h2>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="p-4 border border-info rounded-4 bg-white shadow-sm">
                            <p class="mb-1 text-uppercase small fw-bold text-info">Draw #1 – Deposit</p>
                            <h3 class="fw-bold text-dark">${{ number_format($quote->deposit_amount, 2) }}</h3>
                            <p class="small text-muted mb-0">Required for activation.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded-4 bg-light shadow-sm">
                            <p class="mb-1 text-uppercase small fw-bold text-muted">Draw #2 – Final Balance</p>
                            <h3 class="fw-bold text-dark">${{ number_format($quote->total - $quote->deposit_amount, 2) }}</h3>
                            <p class="small text-muted mb-0">Due upon completion.</p>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold border-bottom pb-2 text-primary text-uppercase small">Terms & Conditions</h5>
                <div class="mt-3 small text-muted" style="line-height: 1.7; max-height: 400px; overflow-y: auto; padding: 20px; border: 1px solid #f1f5f9; background: #fafafa; border-radius: 15px;">
                    <p><strong>CANCELLATION FEE:</strong> If the Customer decides to cancel this Agreement after signing, a <strong>cancellation fee of 10%</strong> will be applied.</p>
                    <p><strong>ACCEPTANCE:</strong> Digital signature constitutes a binding contract and authorization to begin work.</p>
                    <p>{!! nl2br(e($quote->customer_notes)) !!}</p>
                </div>
            @endif

            {{-- Signature section always stays --}}
            <div class="mt-5 pt-4 border-top">
                <form action="{{ route('quotes.contract.sign', $quote->public_token) }}" method="POST" id="duoSignForm">
                    @csrf
                    <input type="hidden" name="signature_data" id="signature_data">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="fw-bold text-dark text-uppercase small d-block mb-2">Step 1: Type Legal Name</label>
                            <input type="text" name="sign_name" class="form-control form-control-lg bg-light border-0" style="font-family: 'Dancing Script', cursive; font-size: 24px;" placeholder="Full Name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold text-dark text-uppercase small d-flex justify-content-between mb-2">
                                Step 2: Draw Signature
                                <a href="javascript:void(0)" onclick="signaturePad.clear()" class="text-danger small">Clear</a>
                            </label>
                            <canvas id="signature-pad" style="border:1px solid #cbd5e1; border-radius:10px; width:100%; height:120px; background: #fdfdfd; cursor: crosshair;"></canvas>
                        </div>
                    </div>

                    <button type="button" onclick="submitDuo()" class="btn btn-primary btn-lg w-100 mt-5 py-3 fw-bold shadow-lg">
                        AUTHORIZE & ACTIVATE PROPOSAL
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.getElementById("signature-pad");
    const signaturePad = new SignaturePad(canvas);

    function submitDuo() {
        if (signaturePad.isEmpty()) {
            alert("Please draw your signature in Step 2.");
            return;
        }
        document.getElementById('signature_data').value = signaturePad.toDataURL();
        document.getElementById('duoSignForm').submit();
    }

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
    }

    window.onresize = resizeCanvas;
    resizeCanvas();
</script>

<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
@endsection
