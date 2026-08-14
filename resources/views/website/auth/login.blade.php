<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Admin Website | Amanah Nusantara Logistik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-anl-navy min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-anl-gold flex items-center justify-center text-anl-navy font-extrabold text-xl mb-4">
                ANL
            </div>
            <h1 class="text-2xl font-extrabold text-white">Kelola Website</h1>
            <p class="text-sm text-gray-400 mt-1">Login Admin — Amanah Nusantara Logistik</p>
        </div>

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-500/15 border border-red-500/40 text-red-200 px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-500/15 border border-green-500/40 text-green-200 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('website.login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Remember Me -->
                <div class="mt-4 flex items-center">
                    <label class="inline-flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-anl-navy focus:ring-anl-navy">
                        <span class="ml-2">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                        class="mt-6 w-full py-2.5 rounded-lg bg-anl-navy text-white font-semibold hover:bg-anl-navy-light transition-colors">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-sm text-gray-400">
            <a href="{{ route('home') }}" class="hover:text-anl-gold transition-colors">← Kembali ke website</a>
        </p>
    </div>
</body>
</html>
