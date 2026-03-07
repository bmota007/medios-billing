<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | McIntosh Cleaning Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-blue-700">

<div class="w-full max-w-md bg-[#020c1b] rounded-2xl shadow-xl p-8 text-white">

    <!-- LOGO -->
    <div class="flex justify-center mb-6">
        <img
            src="{{ asset('images/mcintosh-logo.png') }}"
            alt="McIntosh Cleaning Service"
            class="h-24 object-contain"
        >
    </div>

    <h2 class="text-2xl text-center mb-6">Log in</h2>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-4 text-sm text-red-400 text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-4">
            <input
                type="email"
                name="email"
                placeholder="Email"
                value="{{ old('email') }}"
                required
                autofocus
                class="w-full px-4 py-2 rounded-full bg-slate-800 border border-slate-600 focus:outline-none focus:ring focus:ring-blue-500"
            >
        </div>

        <div class="mb-4">
            <input
                type="password"
                name="password"
                placeholder="Password"
                required
                class="w-full px-4 py-2 rounded-full bg-slate-800 border border-slate-600 focus:outline-none focus:ring focus:ring-blue-500"
            >
        </div>

        <div class="flex justify-between items-center text-sm mb-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember">
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-blue-400 hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        <button
            type="submit"
            class="w-full py-2 rounded-full bg-blue-600 hover:bg-blue-700 transition font-semibold"
        >
            Log in
        </button>
    </form>

    <div class="text-center text-sm mt-6">
        or
        <a href="{{ route('register') }}" class="text-blue-400 hover:underline">
            Sign up
        </a>
    </div>

    <!-- POWERED BY -->
    <div class="text-center text-xs text-gray-400 mt-6">
        Powered by
        <a
            href="https://medioscorporativos.com/"
            target="_blank"
            class="text-blue-400 hover:underline"
        >
            MediosCorporativos
        </a>
    </div>

</div>

</body>
</html>
