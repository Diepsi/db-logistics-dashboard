<x-site-layout>
    <x-slot name="title">Beranda</x-slot>

    <!-- HERO -->
    <section class="bg-anl-navy text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="max-w-3xl">
                <p class="text-anl-gold font-semibold tracking-widest uppercase text-sm mb-4">Distribusi & Logistik Nasional</p>
                <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight">
                    Pengiriman Aman & Tepat Waktu<br>
                    di Seluruh Nusantara
                </h1>
                <p class="mt-6 text-lg text-gray-300 leading-relaxed">
                    Amanah Nusantara Logistik adalah mitra terpercaya untuk distribusi barang dan dokumen.
                    Kami menjamin setiap pengiriman ditangani secara profesional, transparan, dan dapat dipantau real-time.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('services') }}" class="px-6 py-3 rounded-lg bg-anl-gold text-anl-navy font-semibold hover:bg-anl-gold-dark transition-colors">
                        Lihat Layanan
                    </a>
                    <a href="{{ route('contact') }}" class="px-6 py-3 rounded-lg border border-white/30 text-white font-semibold hover:bg-white/10 transition-colors">
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- LAYANAN UTAMA -->
    <section class="py-16 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-anl-navy">Layanan Kami</h2>
                <p class="mt-4 text-gray-600">Solusi logistik end-to-end untuk kebutuhan distribusi bisnis Anda.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['Truck', 'M12 3v1m0 16v1m9-9h1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'Distribusi barang dalam kota dan antar kota dengan armada terpelihara serta rute yang teroptimasi.'],
                    ['Kargo', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'Pengangkutan barang skala besar antar pulau dengan koordinasi logistik yang matang dan jaminan keselamatan muatan.'],
                    ['Same Day', 'M13 10V3L4 14h7v7l9-11h-7z', 'Layanan pengiriman kilat untuk kebutuhan mendesak, dengan prioritas penanganan dan pelacakan real-time.'],
                ] as [$name, $icon, $desc])
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 rounded-xl bg-anl-navy flex items-center justify-center text-anl-gold mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-anl-navy mb-2">{{ $name }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('services') }}" class="inline-block text-anl-navy font-semibold underline decoration-anl-gold decoration-2 underline-offset-4 hover:text-anl-navy-light">
                    Selengkapnya tentang layanan →
                </a>
            </div>
        </div>
    </section>

    <!-- STATISTIK -->
    <section class="bg-anl-navy text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="text-4xl font-extrabold text-anl-gold">50+</p>
                <p class="mt-1 text-sm text-gray-300 uppercase tracking-wider">Kota Terjangkau</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-anl-gold">10K+</p>
                <p class="mt-1 text-sm text-gray-300 uppercase tracking-wider">Pengiriman/Bulan</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-anl-gold">98%</p>
                <p class="mt-1 text-sm text-gray-300 uppercase tracking-wider">Tepat Waktu</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-anl-gold">24/7</p>
                <p class="mt-1 text-sm text-gray-300 uppercase tracking-wider">Dukungan</p>
            </div>
        </div>
    </section>

    <!-- ALASAN MEMILIH KAMI -->
    <section class="py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-anl-navy">Mengapa Memilih Kami?</h2>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        Kami membangun kepercayaan melalui komitmen pada setiap proses pengiriman — dari penjemputan
                        hingga barang tiba di tangan penerima dengan kondisi sempurna.
                    </p>
                    <ul class="mt-8 space-y-5">
                        @foreach ([
                            ['Pelacakan Real-Time', 'Pantau status setiap pengiriman secara langsung melalui dashboard operasional.'],
                            ['Jaminan SLA', 'Komitmen waktu pengiriman yang terukur dan dapat dipertanggungjawabkan.'],
                            ['Tim Profesional', 'Ditangani oleh tim logistik berpengalaman dengan standar operasional yang ketat.'],
                            ['Amanah', 'Setiap muatan dijaga dengan prosedur keamanan berlapis hingga sampai tujuan.'],
                        ] as [$title, $desc])
                            <li class="flex space-x-4">
                                <div class="w-10 h-10 rounded-full bg-anl-gold/15 flex items-center justify-center text-anl-gold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-anl-navy">{{ $title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $desc }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Panel Akses Dashboard -->
                <div class="bg-anl-navy rounded-2xl text-white p-8 shadow-xl">
                    <h3 class="text-2xl font-extrabold">Pantau Operasional Secara Langsung</h3>
                    <p class="mt-4 text-gray-300 leading-relaxed">
                        Akses dashboard operasional untuk melihat KPI pengiriman, kepatuhan SLA, dan performa vendor
                        secara real-time — hanya untuk pengguna terdaftar.
                    </p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-6 py-3 rounded-lg bg-anl-gold text-anl-navy font-semibold hover:bg-anl-gold-dark transition-colors">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="mt-6 inline-block px-6 py-3 rounded-lg bg-anl-gold text-anl-navy font-semibold hover:bg-anl-gold-dark transition-colors">
                            Masuk Dashboard
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>
</x-site-layout>
