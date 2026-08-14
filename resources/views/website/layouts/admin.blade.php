<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kelola Website') | Amanah Nusantara Logistik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen flex flex-col">

    <!-- Topbar -->
    <header class="bg-anl-navy sticky top-0 z-40 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="{{ route('website.posts.index') }}" class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-anl-gold flex items-center justify-center text-anl-navy font-extrabold text-sm">
                        ANL
                    </div>
                    <div class="leading-tight">
                        <span class="text-white font-bold tracking-wider block">KELOLA WEBSITE</span>
                        <span class="text-[10px] text-anl-gold font-semibold tracking-widest uppercase block">Amanah Nusantara Logistik</span>
                    </div>
                </a>

                <div class="flex items-center space-x-1">
                    <a href="{{ route('website.posts.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('website.posts.*') ? 'bg-white/10 text-anl-gold font-semibold' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Kelola Berita
                    </a>
                    <a href="{{ route('home') }}" target="_blank"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-300 hover:text-white hover:bg-white/5 transition-colors">
                        Lihat Website
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-lg text-sm font-medium text-anl-gold border border-anl-gold/40 hover:bg-anl-gold hover:text-anl-navy transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
