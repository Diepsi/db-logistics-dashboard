<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Amanah Nusantara Logistik') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-anl-navy min-h-screen relative overflow-x-hidden">
        <!-- Dekorasi latar: blob gradasi lembut -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-[420px] h-[420px] rounded-full bg-anl-amber/10 blur-3xl animate-pulse-soft"></div>
            <div class="absolute -bottom-40 -left-32 w-[480px] h-[480px] rounded-full bg-anl-blue/20 blur-3xl animate-pulse-soft" style="animation-delay: 1.2s"></div>
            <div class="absolute top-1/3 left-1/2 w-72 h-72 rounded-full bg-dbl-green/10 blur-3xl"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center relative py-8 sm:py-0">
            <div class="mb-6 flex flex-col items-center page-enter">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-anl-blue to-anl-navy flex items-center justify-center text-white font-extrabold text-2xl shadow-glow-lg border border-white/10">
                    ANL
                </div>
                <p class="mt-4 text-white font-bold tracking-wider text-lg">AMANAH NUSANTARA LOGISTIK</p>
                <p class="text-[11px] text-anl-amber font-semibold tracking-widest uppercase mt-0.5">Operational Dashboard Access</p>
            </div>

            <div class="w-full sm:max-w-md px-6 sm:px-8 py-7 bg-white/95 backdrop-blur shadow-2xl sm:rounded-2xl page-enter" style="animation-delay: 0.1s">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
