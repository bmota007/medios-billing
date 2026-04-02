<canvas id="signature-pad" style="border:1px solid #ccc; width:100%; height:200px;"></canvas>

<form method="POST" action="{{ route('quotes.sign.convert', $quote->token) }}">
    @csrf

    <input type="hidden" name="signature" id="signature_input">

    <input type="text" name="name" placeholder="Your Name" required>

    <button type="button" onclick="saveSignature()">Sign & Approve</button>
</form>

<script>
let canvas = document.getElementById('signature-pad');
let ctx = canvas.getContext('2d');
let drawing = false;

canvas.onmousedown = () => drawing = true;
canvas.onmouseup = () => drawing = false;
canvas.onmousemove = (e) => {
    if(!drawing) return;
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
};

function saveSignature(){
    let data = canvas.toDataURL();
    document.getElementById('signature_input').value = data;
    document.querySelector('form').submit();
}
</script>
