<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }} | Amanah Nusantara Logistik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900">

    <!-- Navbar -->
    <header class="bg-anl-navy sticky top-0 z-50 shadow-md">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-anl-gold flex items-center justify-center text-anl-navy font-extrabold text-lg">
                        ANL
                    </div>
                    <div class="leading-tight">
                        <span class="text-white font-bold tracking-wider block">LOGISTICS</span>
                        <span class="text-[10px] text-anl-gold font-semibold tracking-widest uppercase block">Amanah Nusantara Logistik</span>
                    </div>
                </a>

                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-1">
                    @php
                        $menuItems = [
                            'home' => ['Beranda', route('home')],
                            'about' => ['Tentang', route('about')],
                            'services' => ['Layanan', route('services')],
                            'contact' => ['Kontak', route('contact')],
                        ];
                    @endphp
                    @foreach ($menuItems as $key => [$label, $url])
                        <a href="{{ $url }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs($key) ? 'bg-white/10 text-anl-gold font-semibold' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            {{ $label }}
                        </a>
                    @endforeach

                    @auth
                        <a href="{{ route('dashboard') }}" class="ml-3 px-5 py-2 rounded-lg bg-anl-gold text-anl-navy font-semibold text-sm hover:bg-anl-gold-dark transition-colors">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="ml-3 px-5 py-2 rounded-lg bg-anl-gold text-anl-navy font-semibold text-sm hover:bg-anl-gold-dark transition-colors">
                            Masuk Dashboard
                        </a>
                    @endauth
                </div>

                <!-- Menu Mobile -->
                <div class="md:hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="text-gray-300 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-4 top-16 w-56 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50">
                        @foreach ($menuItems as $key => [$label, $url])
                            <a href="{{ $url }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ $label }}</a>
                        @endforeach
                        @auth
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm font-semibold text-anl-navy hover:bg-gray-50 border-t border-gray-100">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-4 py-2 text-sm font-semibold text-anl-navy hover:bg-gray-50 border-t border-gray-100">Masuk Dashboard</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-anl-navy-dark text-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-9 h-9 rounded-lg bg-anl-gold flex items-center justify-center text-anl-navy font-extrabold text-sm">ANL</div>
                        <span class="text-white font-bold tracking-wider">AMANAH NUSANTARA LOGISTIK</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed">
                        Mitra logistik terpercaya untuk distribusi pengiriman yang aman, tepat waktu, dan dapat dipertanggungjawabkan di seluruh Nusantara.
                    </p>
                </div>

                <div>
                    <h3 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">Navigasi</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-anl-gold transition-colors">Beranda</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-anl-gold transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-anl-gold transition-colors">Layanan</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-anl-gold transition-colors">Kontak</a></li>
                        @guest
                            <li><a href="{{ route('login') }}" class="hover:text-anl-gold transition-colors">Masuk Dashboard</a></li>
                        @endguest
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-semibold text-sm tracking-wider uppercase mb-4">Kontak</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>Jl. Raya Logistik No. 12, Jakarta</li>
                        <li>info@amanahnusantaralogistik.id</li>
                        <li>+62 812-3456-7890</li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-white/10 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Amanah Nusantara Logistik. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <!-- Alpine.js CDN untuk interaktivitas menu -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
