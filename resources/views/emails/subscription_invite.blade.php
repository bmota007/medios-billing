<div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; max-width: 600px; margin: auto; background: #0f172a; color: #ffffff; border-radius: 20px; padding: 40px; border: 1px solid #1e293b;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #38bdf8; font-size: 28px;">Medios <span style="color: #ffffff;">Billing</span></h1>
    </div>

    <h2 style="text-align: center; font-weight: 300;">
        @if($daysLeft > 0)
            Your trial expires in <span style="color: #38bdf8; font-weight: bold;">{{ $daysLeft }} days</span>
        @else
            Your trial has <span style="color: #f87171; font-weight: bold;">Expired</span>
        @endif
    </h2>

    <p style="text-align: center; color: #94a3b8; line-height: 1.6;">
        "Sometimes Life is hard and unfair, but when you get to the end of the road, use it to gather your thoughts, rest and execute your plan."
    </p>

    <div style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 15px; padding: 25px; margin: 30px 0; text-align: center;">
        <p style="margin: 0; color: #94a3b8; text-transform: uppercase; font-size: 12px; letter-spacing: 1px;">Selected Plan</p>
        <p style="font-size: 32px; color: #ffffff; margin: 10px 0; font-weight: bold;">
            ${{ number_format($company->custom_price ?? 35.00, 2) }} <span style="font-size: 14px; color: #94a3b8;">/ {{ $company->billing_cycle }}</span>
        </p>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <a href="{{ url('/subscribe/' . $company->id) }}" style="background: #38bdf8; color: #ffffff; padding: 16px 40px; text-decoration: none; border-radius: 12px; font-weight: bold; font-size: 18px; display: inline-block;">
            Activate My Subscription
        </a>
    </div>

    <div style="margin-top: 50px; padding-top: 20px; border-top: 1px solid #1e293b; text-align: center; color: #64748b; font-size: 12px;">
        <p>Questions? Contact our IT Support Department</p>
        <p>📧 support@mediosbilling.com | 📞 Your IVR Number</p>
        <p>Have a blessed day, Medios Billing.</p>
    </div>
</div>
