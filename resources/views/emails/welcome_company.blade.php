<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f7f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #0284c7; padding: 40px; text-align: center; color: white; }
        .content { padding: 40px; color: #334155; line-height: 1.6; }
        .button { display: inline-block; padding: 14px 28px; background-color: #0284c7; color: white !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
        .credentials { background-color: #f1f5f9; padding: 20px; border-radius: 6px; margin-top: 20px; border-left: 4px solid #0284c7; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Medios Billing</h1>
        </div>
        <div class="content">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            <p>Your professional billing portal is ready. We've set up <strong>{{ $company->name }}</strong> on our premium tier. You can now start creating quotes and managing your customers.</p>

            <div class="credentials">
                <p style="margin: 0;"><strong>Login Email:</strong> {{ $user->email }}</p>
                <p style="margin: 5px 0 0 0;"><strong>Temporary Password:</strong> {{ $password }}</p>
            </div>

            <center>
                <a href="{{ route('login') }}" class="button">Log In to Your Portal</a>
            </center>

            <p style="margin-top: 30px;">For security, please change your password immediately after logging in.</p>
            <p>Best regards,<br>The Medios Billing Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} All rights reserved.
        </div>
    </div>
</body>
</html>
