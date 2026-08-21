<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100 dark:bg-slate-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Anti-FOUC: terapkan tema tersimpan sebelum CSS dimuat -->
        <script>
            (function () {
                try {
                    var t = localStorage.getItem('theme');
                    if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {}
            })();
        </script>

        <title>{{ config('app.name', 'Amanah Nusantara Logistik') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-gray-900 bg-slate-100 dark:text-gray-100 dark:bg-slate-950 min-h-screen relative overflow-x-hidden">
        <!-- Dekorasi latar: blob gradasi lembut -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-[420px] h-[420px] rounded-full bg-indigo-500/10 blur-3xl animate-pulse-soft"></div>
            <div class="absolute -bottom-40 -left-32 w-[480px] h-[480px] rounded-full bg-indigo-400/15 blur-3xl animate-pulse-soft" style="animation-delay: 1.2s"></div>
            <div class="absolute top-1/3 left-1/2 w-72 h-72 rounded-full bg-dbl-green/10 blur-3xl"></div>
        </div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center relative py-8 sm:py-0">
            <div class="mb-6 flex flex-col items-center page-enter">
                <div class="w-16 h-16 rounded-2xl bg-white dark:bg-white flex items-center justify-center overflow-hidden shadow-md shadow-slate-900/10 ring-1 ring-slate-200/80 dark:ring-slate-700">
                    <img src="{{ asset('images/logo-anl.png') }}" alt="Logo Amanah Nusantara Logistik" class="w-full h-full object-contain p-1" loading="eager">
                </div>
                <p class="mt-4 text-slate-900 dark:text-white font-bold tracking-wide text-lg">Logistics Hub</p>
                <p class="text-[11px] text-indigo-500 dark:text-indigo-400 font-semibold tracking-widest uppercase mt-0.5">Amanah Nusantara Logistik</p>
            </div>

            <div class="w-full sm:max-w-md px-6 sm:px-8 py-7 bg-white/95 backdrop-blur-md shadow-xl sm:rounded-2xl ring-1 ring-slate-200/80 dark:bg-slate-900/95 dark:ring-slate-800 page-enter" style="animation-delay: 0.1s">
                {{ $slot }}
            </div>

            <p class="mt-6 text-[11px] text-slate-400 dark:text-slate-500 font-medium">Operational Dashboard &middot; Monitoring Pengiriman</p>
        </div>
    </body>
</html>
