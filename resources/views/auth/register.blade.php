<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | McIntosh Cleaning Service</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-blue-700">

<div class="w-full max-w-md bg-[#020c1b] rounded-2xl shadow-xl p-8 text-white">
    <h2 class="text-2xl text-center mb-6">Sign up</h2>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-4 text-sm text-red-400 text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <input
                type="text"
                name="name"
                placeholder="Full name"
                value="{{ old('name') }}"
                required
                class="w-full px-4 py-2 rounded-full bg-slate-800 border border-slate-600 focus:outline-none focus:ring focus:ring-blue-500"
            >
        </div>

        <div class="mb-4">
            <input
                type="email"
                name="email"
                placeholder="Email"
                value="{{ old('email') }}"
                required
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

        <div class="mb-6">
            <input
                type="password"
                name="password_confirmation"
                placeholder="Confirm password"
                required
                class="w-full px-4 py-2 rounded-full bg-slate-800 border border-slate-600 focus:outline-none focus:ring focus:ring-blue-500"
            >
        </div>

        <button
            type="submit"
            class="w-full py-2 rounded-full bg-blue-600 hover:bg-blue-700 transition font-semibold"
        >
            Create account
        </button>
    </form>

    <div class="text-center text-sm mt-6">
        or
        <a href="{{ route('login') }}" class="text-blue-400 hover:underline">
            Log in
        </a>
    </div>
</div>

</body>
</html>
