<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Medios Billing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-950 via-blue-900 to-slate-900 px-4">

<div class="w-full max-w-md bg-[#020c1b]/95 backdrop-blur rounded-2xl shadow-2xl border border-slate-800 p-8 text-white">

    <!-- Logo -->
    <div class="text-center mb-8">
        @php
            $company = session('company') ?? null;
        @endphp

        @if($company && $company->logo_path)
            <img src="{{ asset('storage/' . $company->logo_path) }}"
                 alt="Logo"
                 class="h-14 mx-auto">
        @else
            <h2 class="font-bold text-3xl">
                Medios<span class="text-sky-400">Billing</span>
            </h2>
        @endif

        <p class="text-slate-400 text-xs mt-2 uppercase tracking-[0.25em]">
            Secure Client Portal
        </p>
    </div>

    <h1 class="text-xl font-semibold text-center mb-6">
        Welcome Back
    </h1>

    @if ($errors->any())
        <div class="mb-4 text-sm text-red-400 text-center bg-red-950/40 border border-red-800 rounded-lg py-2 px-3">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required
                autofocus
                value="{{ old('email') }}"
                class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 focus:border-sky-400 focus:ring-0 text-white placeholder-slate-500"
            >
        </div>

        <div>
            <input
                type="password"
                name="password"
                placeholder="Password"
                required
                class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 focus:border-sky-400 focus:ring-0 text-white placeholder-slate-500"
            >
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-sm">

            <label class="flex items-center gap-2 text-slate-300">
                <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-800">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sky-400 hover:text-sky-300">
                    Forgot password?
                </a>
            @endif

        </div>

        <button
            type="submit"
            class="w-full py-3 rounded-xl bg-sky-600 hover:bg-sky-500 font-semibold transition"
        >
            Log In
        </button>
    </form>

    <div class="mt-8 text-center text-xs text-slate-500">
        Powered by
        <a href="https://medioscorporativos.com" target="_blank" class="text-sky-400 hover:text-sky-300">
            MediosCorporativos
        </a>
    </div>

</div>

</body>
</html>
