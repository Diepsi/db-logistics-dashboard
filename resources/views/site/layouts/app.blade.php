<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }} | Amanah Nusantara Logistik</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-root antialiased bg-slate-50 text-slate-600">

    <!-- Top Utility Bar -->
    <div class="bg-anl-navy text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-9 text-xs">
                <p class="font-bold tracking-[0.2em] text-anl-amber uppercase">Mitra Utama Pelanggan</p>
                <div class="hidden md:flex items-center gap-6">
                    <a href="mailto:office@dblogistics.co.id" class="flex items-center gap-1.5 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        office@dblogistics.co.id
                    </a>
                    <a href="https://wa.me/6281362323510" target="_blank" rel="noopener" class="flex items-center gap-1.5 hover:text-white transition-colors">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        0813-6232-3510
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <header class="sticky top-0 z-50" x-data="{ scrolled: false, open: false }" @scroll.window="scrolled = window.scrollY > 8">
        <nav class="bg-white/85 backdrop-blur-md border-b border-slate-200 transition-shadow duration-300" :class="scrolled ? 'shadow-lg shadow-slate-900/10' : 'shadow-sm'">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center py-2 shrink-0" aria-label="Beranda">
                        <img src="{{ asset('images/logo-anl.jpg') }}" alt="Amanah Nusantara Logistik" class="h-10 w-auto">
                    </a>

                    <!-- Menu Desktop -->
                    <div class="hidden lg:flex items-center justify-center flex-1">
                        <div class="flex items-center gap-1">
                            @php
                                $menuItems = [
                                    ['home', 'Beranda', route('home'), 'home'],
                                    ['about', 'Tentang Kami', route('about'), 'about'],
                                    ['services', 'Layanan', route('services'), 'services'],
                                    ['jaringan', 'Jaringan Cabang', route('home').'#jaringan', 'home'],
                                    ['berita', 'Berita', route('berita'), 'berita'],
                                    ['contact', 'Kontak', route('contact'), 'contact'],
                                ];
                            @endphp
                            @foreach ($menuItems as [$key, $label, $url, $activeOn])
                                @php
                                    $active = $key === 'jaringan'
                                        ? str_contains(request()->fullUrl(), '#jaringan')
                                        : request()->routeIs($activeOn);
                                @endphp
                                <a href="{{ $url }}"
                                   class="px-4 py-2 min-h-[44px] flex items-center rounded-full text-sm font-semibold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-anl-blue/50 {{ $active ? 'bg-anl-blue-light text-anl-blue' : 'text-slate-600 hover:bg-slate-100 hover:text-anl-blue' }}"
                                   @if ($active) aria-current="page" @endif>
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- CTA Kanan -->
                    <div class="hidden lg:flex items-center gap-3 shrink-0">
                        <a href="https://wa.me/6281362323510" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
                           class="w-11 h-11 rounded-full bg-green-500/10 text-green-600 flex items-center justify-center hover:bg-green-500 hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                        <a href="{{ route('contact') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 min-h-[44px] rounded-xl bg-anl-blue text-white text-sm font-bold shadow-md shadow-anl-blue/25 hover:bg-anl-blue-dark hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            Hubungi Kami
                        </a>
                    </div>

                    <!-- Menu Mobile -->
                    <div class="lg:hidden flex items-center gap-2">
                        <a href="https://wa.me/6281362323510" target="_blank" rel="noopener" aria-label="Chat WhatsApp"
                           class="w-11 h-11 rounded-full bg-green-500 text-white flex items-center justify-center shadow-md shadow-green-500/30">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                        <button @click="open = !open" :aria-expanded="open.toString()" aria-controls="mobile-menu" aria-label="Menu navigasi"
                                class="w-11 h-11 rounded-lg flex items-center justify-center text-anl-navy hover:bg-slate-100 hover:text-anl-blue transition-colors duration-200">
                            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Panel -->
            <div id="mobile-menu" x-show="open" x-cloak @click.away="open = false" @keydown.escape.window="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="lg:hidden bg-white border-t border-slate-100 px-4 sm:px-6 pb-4 pt-3">
                <div class="flex items-center gap-2 pb-3 mb-1 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-anl-blue to-anl-navy flex items-center justify-center text-white font-extrabold text-sm">ANL</div>
                    <div class="leading-tight">
                        <span class="text-anl-navy font-extrabold text-xs tracking-tight block">AMANAH NUSANTARA</span>
                        <span class="text-[10px] font-bold tracking-[0.2em] text-slate-500 block">LOGISTIK<span class="text-anl-amber">.</span></span>
                    </div>
                </div>
                <nav aria-label="Navigasi mobile" class="pt-2">
                    @foreach ($menuItems as [$key, $label, $url, $activeOn])
                        @php
                            $active = $key === 'jaringan'
                                ? str_contains(request()->fullUrl(), '#jaringan')
                                : request()->routeIs($activeOn);
                        @endphp
                        <a href="{{ $url }}"
                           class="block px-4 py-2.5 min-h-[44px] flex items-center rounded-xl text-sm font-semibold transition-colors duration-200 {{ $active ? 'bg-anl-blue-light text-anl-blue' : 'text-slate-700 hover:bg-slate-100 hover:text-anl-blue' }}"
                           @if ($active) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
                <div class="mt-3 pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <a href="{{ route('contact') }}" class="block text-center py-3 min-h-[44px] rounded-xl bg-anl-blue text-white text-sm font-bold shadow-md shadow-anl-blue/20 hover:bg-anl-blue-dark transition-colors duration-200">
                        Cek Tarif / Hubungi Kami
                    </a>
                    <a href="https://wa.me/6281362323510" target="_blank" rel="noopener" class="block text-center py-3 min-h-[44px] rounded-xl border border-green-500/40 text-green-600 text-sm font-bold hover:bg-green-50 transition-colors duration-200">
                        WhatsApp 0813-6232-3510
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-anl-navy-dark text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="lg:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="{{ asset('images/logo-anl.jpg') }}" alt="Amanah Nusantara Logistik" class="h-9 w-auto">
                        <div class="leading-tight">
                            <span class="text-xs text-anl-amber font-bold tracking-widest uppercase">Mitra Utama Pelanggan</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md">
                        Solusi jasa pengiriman & logistik darat, laut, dan udara ke seluruh Indonesia.
                        Layanan yang andal, aman, dan tepat waktu — didukung jaringan 24 cabang perwakilan.
                    </p>
                </div>

                <div>
                    <h3 class="text-white font-bold text-sm tracking-wider uppercase mb-4">Navigasi</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-anl-amber transition-colors">Beranda</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-anl-amber transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-anl-amber transition-colors">Layanan</a></li>
                        <li><a href="{{ route('home') }}#jaringan" class="hover:text-anl-amber transition-colors">Jaringan Cabang</a></li>
                        <li><a href="{{ route('berita') }}" class="hover:text-anl-amber transition-colors">Berita</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-anl-amber transition-colors">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold text-sm tracking-wider uppercase mb-4">Kontak</h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex gap-2">
                            <span class="shrink-0">Jl. Letda Natsir No.10A, Bojong Kulur,</span>
                        </li>
                        <li class="flex gap-2">
                            <span>Kec. Gn. Putri, Kab. Bogor, Jawa Barat 16968</span>
                        </li>
                        <li><a href="https://wa.me/6281362323510" target="_blank" rel="noopener" class="hover:text-anl-amber transition-colors">WhatsApp: 0813-6232-3510</a></li>
                        <li><a href="mailto:marketing@amanahlogistik.co.id" class="hover:text-anl-amber transition-colors">marketing@amanahlogistik.co.id</a></li>
                        <li><a href="mailto:office@dblogistics.co.id" class="hover:text-anl-amber transition-colors">office@dblogistics.co.id</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-white/10 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} PT. Amanah Nusantara Logistik. Seluruh hak cipta dilindungi.
            </div>
        </div>
    </footer>

    <!-- Floating Action Button WhatsApp -->
    <a href="https://wa.me/6281362323510?text=Halo%20Amanah%20Nusantara%20Logistik,%20saya%20ingin%20menanyakan%20layanan%20pengiriman."
       target="_blank" rel="noopener" aria-label="Chat WhatsApp"
       class="fixed bottom-6 right-6 z-50 w-14 h-14 min-w-[44px] min-h-[44px] flex items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-green-500/30 hover:scale-110 transition-all duration-300">
        <span class="absolute inline-flex h-full w-full rounded-full bg-[#25D366] opacity-60 animate-ping"></span>
        <svg class="w-7 h-7 relative" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    <!-- Alpine.js CDN untuk interaktivitas menu -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
