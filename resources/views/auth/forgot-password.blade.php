@extends('layouts.guest')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 20px;">
    
    <div class="glass-card" style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); padding: 40px; border-radius: 24px; width: 100%; max-width: 450px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); position: relative; overflow: hidden;">
        
        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(56, 189, 248, 0.1); filter: blur(40px); border-radius: 50%;"></div>

        <div style="text-align: center; margin-bottom: 30px;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(255,255,255,0.05); border-radius: 16px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.1);">
                <i class="fa-solid fa-key" style="font-size: 24px; color: #38bdf8;"></i>
            </div>
            <h1 style="color: #fff; font-size: 24px; font-weight: 800; margin-bottom: 10px; letter-spacing: -0.5px;">Password Recovery</h1>
            <p style="color: #94a3b8; font-size: 14px; line-height: 1.5;">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
            </p>
        </div>

        @if (session('status'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; text-align: center; font-weight: 600;">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div style="margin-bottom: 25px;">
                <label for="email" style="display: block; color: #cbd5e1; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px;">
                    Registered Email
                </label>
                <div style="position: relative;">
                    <i class="fa-regular fa-envelope" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                        placeholder="you@company.com"
                        style="width: 100%; padding: 12px 12px 12px 45px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; color: #fff; font-size: 15px; outline: none; transition: 0.3s;"
                        onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 4px rgba(56, 189, 248, 0.1)';"
                        onblur="this.style.borderColor='rgba(255, 255, 255, 0.1)'; this.style.boxShadow='none';">
                </div>
                @if($errors->has('email'))
                    <p style="color: #ef4444; font-size: 12px; margin-top: 5px; font-weight: 600;">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <button type="submit" style="width: 100%; background: #38bdf8; color: #0f172a; padding: 14px; border: none; border-radius: 12px; font-weight: 800; font-size: 14px; text-transform: uppercase; cursor: pointer; transition: 0.3s; letter-spacing: 0.5px; box-shadow: 0 10px 15px -3px rgba(56, 189, 248, 0.3);">
                {{ __('Email Password Reset Link') }}
            </button>

            <div style="text-align: center; margin-top: 25px;">
                <a href="{{ route('login') }}" style="color: #94a3b8; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">
                    <i class="fa-solid fa-arrow-left" style="margin-right: 5px;"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
