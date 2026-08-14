<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Amanah Nusantara Logistik') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-anl-navy">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6 flex flex-col items-center">
                <div class="w-16 h-16 rounded-xl bg-anl-gold flex items-center justify-center text-anl-navy font-extrabold text-2xl shadow-lg">
                    ANL
                </div>
                <p class="mt-3 text-white font-bold tracking-wider text-lg">AMANAH NUSANTARA LOGISTIK</p>
                <p class="text-[11px] text-anl-gold font-semibold tracking-widest uppercase">Operational Dashboard Access</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <div class="mt-6">
                <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-anl-gold transition-colors">
                    &larr; Kembali ke Website
                </a>
            </div>
        </div>
    </body>
</html>
