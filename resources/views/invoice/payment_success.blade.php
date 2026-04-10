<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Payment Complete</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    background:#020617;
    font-family:'Plus Jakarta Sans',sans-serif;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    height:100vh;
}

.card{
    max-width:500px;
    width:100%;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:24px;
    padding:40px;
    text-align:center;
}

.icon{
    font-size:60px;
    margin-bottom:20px;
}

h1{
    font-size:28px;
    margin-bottom:10px;
}

p{
    color:#cbd5e1;
    line-height:1.6;
    margin-bottom:20px;
}

.btn{
    display:inline-block;
    padding:14px 24px;
    background:linear-gradient(135deg,#2563eb,#60a5fa);
    border-radius:12px;
    color:white;
    text-decoration:none;
    font-weight:700;
}
</style>
</head>

<body>

<div class="card">

    <div class="icon">✅</div>

    <h1>Payment Completed</h1>

    <p>
        Thank you for your payment.<br><br>

        Your transaction has been successfully processed and recorded.
        We truly appreciate your business and the trust you’ve placed in us.
    </p>

    <p>
        A receipt has been sent to your email for your records.<br><br>

        If any additional steps are required, our team will contact you directly.
    </p>

    <a href="#" onclick="window.close()" class="btn">
        Close This Page
    </a>

</div>

</body>
</html>
