<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROPOSAL | {{ $quote->company->name ?? 'Medios Billing' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <style>
        body { background: #010409; color: white; font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px 20px; }
        .mesh-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(at 50% 0%, rgba(30, 58, 138, 0.3) 0, transparent 50%); z-index: -1; }
        .contract-box { background: #0d1117; border: 1px solid #30363d; border-radius: 30px; padding: 50px; max-width: 900px; margin: auto; }
        
        .label-caps { color: #8b949e; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 10px; }
        .header-main { font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 30px; }
        
        /* TABLE STYLING */
        .item-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .item-table th { color: #8b949e; font-size: 0.7rem; text-transform: uppercase; border-bottom: 1px solid #30363d; padding-bottom: 10px; }
        .item-table td { padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }

        .price-total { font-size: 3rem; font-weight: 800; color: #38bdf8; text-align: right; }
        
        /* SIGNATURE STYLES */
        .signature-input { background: #161b22 !important; border: 2px solid #30363d !important; color: white !important; font-size: 1.8rem !important; padding: 18px !important; border-radius: 12px !important; font-family: 'Dancing Script', cursive; }
        .sig-canvas { background: white; border-radius: 12px; width: 100%; height: 200px; cursor: crosshair; }
        .btn-activate { background: #38bdf8; color: #000; font-weight: 800; font-size: 1.2rem; padding: 20px; border-radius: 50px; border: none; width: 100%; transition: 0.3s; margin-top: 30px; }
        .btn-activate:hover { background: #fff; transform: translateY(-3px); }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>
    <div class="contract-box shadow-lg">
        <span class="label-caps">Proposal Authorization</span>
        <h1 class="header-main">{{ strtoupper($quote->customer->name) }}</h1>
        
        <table class="item-table">
            <thead>
                <tr><th style="width: 70%;">Service</th><th>Qty</th><th style="text-align: right;">Total</th></tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                <tr>
                    <td style="font-weight: 600;">{{ $item->service_name }}</td>
                    <td style="color: #8b949e;">{{ (int)$item->quantity }}</td>
                    <td style="text-align: right; font-weight: 700;">${{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="price-total">${{ number_format($quote->total, 2) }}</div>

        <hr style="border-color: #30363d; margin: 40px 0;">

        <form method="POST" action="{{ route('quotes.sign.convert', $quote->public_token) }}" id="signForm">
            @csrf
            <input type="hidden" name="signature_data" id="signature_data">
            
            <label class="label-caps">Type Full Legal Name</label>
            <input type="text" name="sign_name" class="form-control signature-input mb-4" placeholder="Full Name" required>
            
            <label class="label-caps">Draw Signature</label>
            <canvas id="signature-pad" class="sig-canvas mb-4"></canvas>
            
            <button type="button" onclick="validateAndSubmit()" class="btn btn-activate shadow-lg">
                SIGN & APPROVE PROPOSAL
            </button>
        </form>
    </div>

    <script>
        const canvas = document.getElementById("signature-pad");
        const signaturePad = new SignaturePad(canvas);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        function validateAndSubmit() {
            if (signaturePad.isEmpty()) {
                alert("Please provide a drawn signature.");
                return;
            }
            document.getElementById('signature_data').value = signaturePad.toDataURL();
            document.getElementById('signForm').submit();
        }
    </script>
</body>
</html>
