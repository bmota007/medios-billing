<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
</head>
<body style="
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:radial-gradient(circle at top,#0b3a88 0%,#061427 35%,#030b17 100%);
    color:#fff;
">

<div style="
    background:#020617;
    padding:50px;
    border-radius:18px;
    max-width:520px;
    width:100%;
    text-align:center;
    box-shadow:0 20px 60px rgba(0,0,0,.6);
">

    <h1 style="font-size:32px;margin-bottom:15px;">
        🎉 Payment Successful
    </h1>

    <p style="color:#94a3b8;margin-bottom:20px;">
        Your account has been activated successfully.
    </p>

    <p style="color:#22c55e;font-weight:bold;margin-bottom:15px;">
        ✅ Your 5-day free trial has started
    </p>

    <p style="color:#cbd5f5;margin-bottom:25px;">
        📩 Check your email for login details and next steps.
    </p>

    <div style="margin-top:25px;text-align:center;">
        <button onclick="window.location.href='/logout'" style="
            background:#0ea5e9;
            border:none;
            padding:14px 28px;
            border-radius:10px;
            color:#fff;
            font-weight:bold;
            cursor:pointer;
        ">
            Close Window
        </button>

        <p style="margin-top:15px;color:#94a3b8;font-size:14px;">
            Please check your email to access your account.
        </p>
    </div>

</div>

</body>
</html>
