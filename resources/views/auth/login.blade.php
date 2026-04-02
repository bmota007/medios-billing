<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Medios Billing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-blue-700">

<div class="w-full max-w-md bg-[#020c1b] rounded-2xl shadow-xl p-8 text-white">

<!-- 🔥 LOGO FIX -->
<div class="text-center mb-8">
    @php
        $company = session('company') ?? null;
    @endphp

    @if($company && $company->logo_path)
        <img src="{{ asset('storage/' . $company->logo_path) }}"
             alt="Logo"
             style="height:60px; margin:auto;">
    @else
        <h2 class="text-white font-bold text-2xl">
            Medios<span class="text-sky-400">Billing</span>
        </h2>
    @endif

    <p class="text-slate-500 text-xs mt-2 uppercase tracking-widest">
        Secure Client Portal
    </p>
</div>

<h2 class="text-white text-lg font-medium text-center mb-6">Account Login</h2>

@if ($errors->any())
<div class="mb-4 text-sm text-red-400 text-center">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <input type="email" name="email" placeholder="Email" required class="w-full mb-3 px-4 py-2 rounded-full bg-slate-800">
    <input type="password" name="password" placeholder="Password" required class="w-full mb-4 px-4 py-2 rounded-full bg-slate-800">

    <button type="submit" class="w-full py-2 bg-blue-600 rounded-full">
        Log in
    </button>
</form>

</div>
</body>
</html>

