<x-site-layout>
    <x-slot name="title">Beranda</x-slot>

    @php
        $regions = [
            'Sumatera' => ['Aceh', 'Medan', 'Padang', 'Pekanbaru', 'Batam', 'Jambi', 'Palembang', 'Bengkulu', 'Lampung'],
            'Jawa' => ['Cirebon', 'Tegal', 'Semarang', 'Jogjakarta', 'Surabaya'],
            'Kalimantan' => ['Pontianak', 'Balikpapan'],
            'Sulawesi' => ['Makassar', 'Manado', 'Palu', 'Kendari'],
            'Maluku & Papua' => ['Ambon', 'Sorong'],
            'Nusa Tenggara' => ['Denpasar', 'Mataram'],
        ];
    @endphp

    <!-- ============ HERO ============ -->
    <section class="relative overflow-hidden bg-gradient-to-br from-anl-navy via-[#0E2A4A] to-anl-blue text-white">
        <!-- Gradient Orbs -->
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-anl-blue/30 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-0 w-[28rem] h-[28rem] rounded-full bg-anl-amber/10 blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 right-1/4 w-64 h-64 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
                <!-- Kiri: Value Proposition -->
                <div>
                    <span class="x-animate-fade-up inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-anl-amber text-xs font-bold tracking-widest uppercase backdrop-blur-sm" style="animation-delay: 0.05s">
                        Mitra Utama Pelanggan
                    </span>
                    <h1 class="x-animate-fade-up mt-6 text-4xl sm:text-5xl xl:text-6xl font-extrabold leading-[1.15] tracking-tight" style="animation-delay: 0.15s">
                        Solusi Jasa Pengiriman & Logistik Andal ke Seluruh Indonesia
                    </h1>
                    <p class="x-animate-fade-up mt-6 text-lg text-slate-300 leading-relaxed max-w-xl" style="animation-delay: 0.25s">
                        Melayani pengiriman domestik via <span class="text-white font-semibold">Darat</span>,
                        <span class="text-white font-semibold">Laut</span>, dan <span class="text-white font-semibold">Udara</span>
                        dengan tepat, cepat, dan aman.
                    </p>
                    <div class="x-animate-fade-up mt-9 flex flex-wrap gap-4" style="animation-delay: 0.35s">
                        <a href="{{ route('services') }}"
                           class="px-7 py-3.5 min-h-[44px] rounded-xl bg-anl-blue text-white font-bold shadow-lg shadow-anl-blue/30 hover:bg-anl-blue-dark hover:-translate-y-0.5 transition-all duration-300">
                            Layanan Kami
                        </a>
                        <a href="{{ route('contact') }}"
                           class="px-7 py-3.5 min-h-[44px] rounded-xl border border-white/40 text-white font-bold hover:bg-white/10 hover:-translate-y-0.5 transition-all duration-300">
                            Hubungi Kami
                        </a>
                    </div>
                </div>

                <!-- Kanan: Ilustrasi SVG + Floating Badges -->
                <div class="x-animate-fade-up relative" style="animation-delay: 0.45s">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-black/30 border border-white/10">
                        <svg viewBox="0 0 520 440" class="w-full h-auto" role="img" aria-label="Ilustrasi jaringan rute pengiriman ANL">
                            <defs>
                                <linearGradient id="heroBg" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#16345C"/>
                                    <stop offset="100%" stop-color="#0F2B48"/>
                                </linearGradient>
                                <linearGradient id="truckGrad" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#399310"/>
                                    <stop offset="100%" stop-color="#2E6E0F"/>
                                </linearGradient>
                                <radialGradient id="orbBlue" cx="30%" cy="20%" r="80%">
                                    <stop offset="0%" stop-color="#399310" stop-opacity="0.5"/>
                                    <stop offset="100%" stop-color="#399310" stop-opacity="0"/>
                                </radialGradient>
                            </defs>
                            <rect width="520" height="440" fill="url(#heroBg)"/>
                            <rect width="520" height="440" fill="url(#orbBlue)"/>

                            <!-- Grid peta -->
                            <g stroke="#ffffff" stroke-opacity="0.05" stroke-width="1">
                                <path d="M0 80H520M0 160H520M0 240H520M0 320H520M0 400H520M80 0V440M160 0V440M240 0V440M320 0V440M400 0V440M480 0V440"/>
                            </g>

                            <!-- Rute terhubung -->
                            <g stroke="#6CCF2E" stroke-opacity="0.35" stroke-width="1.5" stroke-dasharray="6 6" fill="none">
                                <path d="M80 90 L220 150 L300 90 L420 130"/>
                                <path d="M120 330 L240 260 L360 320 L440 270"/>
                                <path d="M220 150 L240 260 L300 90 L360 320"/>
                                <path d="M80 90 L120 330 L440 270 L420 130"/>
                            </g>

                            <!-- Node kota -->
                            <g fill="#6CCF2E">
                                <circle cx="80" cy="90" r="7"/>
                                <circle cx="120" cy="330" r="7"/>
                                <circle cx="300" cy="90" r="7"/>
                                <circle cx="440" cy="270" r="7"/>
                                <circle cx="360" cy="320" r="7"/>
                            </g>
                            <g fill="#6CCF2E">
                                <circle cx="220" cy="150" r="5"/>
                                <circle cx="240" cy="260" r="5"/>
                                <circle cx="420" cy="130" r="5"/>
                            </g>
                            <g fill="#399310">
                                <circle cx="80" cy="90" r="14" opacity="0.3"/>
                                <circle cx="440" cy="270" r="14" opacity="0.3"/>
                                <circle cx="300" cy="90" r="14" opacity="0.3"/>
                            </g>

                            <!-- Pin kota -->
                            <g fill="#6CCF2E">
                                <path d="M80 70c-5 0-9 4-9 9 0 6.5 9 15 9 15s9-8.5 9-15c0-5-4-9-9-9zm0 13a4 4 0 110-8 4 4 0 010 8z"/>
                                <path d="M440 250c-5 0-9 4-9 9 0 6.5 9 15 9 15s9-8.5 9-15c0-5-4-9-9-9zm0 13a4 4 0 110-8 4 4 0 010 8z"/>
                            </g>

                            <!-- Truk ilustrasi -->
                            <g transform="translate(150 180)">
                                <rect x="0" y="0" width="150" height="78" rx="10" fill="#ffffff" opacity="0.06"/>
                                <g transform="translate(12 20)">
                                    <rect x="0" y="8" width="78" height="38" rx="4" fill="#16345C"/>
                                    <path d="M0 8 V0 H60 L78 8Z" fill="#16345C"/>
                                    <rect x="88" y="18" width="26" height="28" rx="3" fill="#399310"/>
                                    <rect x="70" y="0" width="4" height="58" rx="2" fill="#16345C"/>
                                    <path d="M96 0 h16 v18 h-16z" fill="#2E6E0F"/>
                                    <g fill="#0F2B48">
                                        <circle cx="20" cy="52" r="10"/>
                                        <circle cx="20" cy="52" r="5" fill="#6CCF2E"/>
                                        <circle cx="92" cy="52" r="10"/>
                                        <circle cx="92" cy="52" r="5" fill="#6CCF2E"/>
                                    </g>
                                    <g stroke="#6CCF2E" stroke-opacity="0.5" stroke-width="1.5" stroke-dasharray="4 3">
                                        <line x1="8" y1="20" x2="56" y2="20"/>
                                        <line x1="8" y1="30" x2="56" y2="30"/>
                                        <line x1="8" y1="40" x2="56" y2="40"/>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>

                    <!-- Floating Badges -->
                    <div class="absolute -top-5 -right-3 sm:right-6 rounded-2xl bg-white/90 backdrop-blur-md border border-white/40 shadow-xl px-5 py-4 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-anl-blue-light flex items-center justify-center text-anl-blue">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-anl-navy leading-none">24+</p>
                            <p class="text-xs font-semibold text-slate-500 mt-1">Cabang Perwakilan</p>
                        </div>
                    </div>

                    <div class="absolute -bottom-6 -left-3 sm:left-6 rounded-2xl bg-white/90 backdrop-blur-md border border-white/40 shadow-xl px-5 py-4 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-anl-navy leading-none">98%</p>
                            <p class="text-xs font-semibold text-slate-500 mt-1">On-Time Delivery</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ LAYANAN UTAMA ============ -->
    <section class="py-20 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14" x-reveal>
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Apa yang Kami Tawarkan</p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Layanan Kami</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">
                    Dalam memberi pelayanan terbaik dan terpercaya, ANL didukung oleh tim profesional
                    berpengalaman serta armada yang memadai.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($services as $i => $service)
                    <div x-reveal {{ $i > 0 ? 'x-reveal.delay' : '' }} class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] overflow-hidden hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                        <div class="relative h-44 bg-gradient-to-br from-anl-navy to-anl-blue flex items-center justify-center overflow-hidden">
                            @if ($service->icon_image)
                                <img src="{{ asset('storage/'.$service->icon_image) }}" alt="Ikon {{ $service->name }}" class="w-28 h-28 object-contain">
                            @elseif ($service->icon_svg)
                                <svg class="w-28 h-28 text-anl-blue/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="{{ $service->icon_svg }}" />
                                </svg>
                            @endif
                            @if ($service->badge)
                                <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-anl-amber text-anl-navy text-xs font-bold tracking-wider uppercase shadow-lg">{{ $service->badge }}</span>
                            @endif
                        </div>
                        <div class="p-7">
                            <h3 class="text-xl font-bold text-slate-900">{{ $service->name }}</h3>
                            <p class="mt-3 text-slate-600 leading-relaxed text-sm">{{ $service->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('services') }}" class="inline-flex items-center gap-2 min-h-[44px] px-6 py-3 rounded-xl bg-anl-blue text-white font-bold shadow-md shadow-anl-blue/20 hover:bg-anl-blue-dark transition-all duration-300">
                    Selengkapnya tentang layanan
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ============ STATISTIK ============ -->
    <section class="bg-gradient-to-br from-anl-navy to-anl-navy-dark text-white py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-10 text-center" x-reveal>
            <div>
                <p class="text-4xl font-extrabold text-anl-amber" x-counter data-counter-value="24" data-counter-duration="1500" data-counter-suffix="+">0+</p>
                <p class="mt-2 text-sm text-slate-300 uppercase tracking-wider">Cabang Perwakilan</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-anl-amber" x-counter data-counter-value="3" data-counter-duration="1500" data-counter-suffix=" Moda">0</p>
                <p class="mt-2 text-sm text-slate-300 uppercase tracking-wider">Darat · Laut · Udara</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-anl-amber" x-counter data-counter-value="98" data-counter-duration="1800" data-counter-suffix="%">0%</p>
                <p class="mt-2 text-sm text-slate-300 uppercase tracking-wider">Tepat Waktu</p>
            </div>
            <div>
                <p class="text-4xl font-extrabold text-anl-amber" x-counter data-counter-value="24" data-counter-duration="1500" data-counter-suffix="/7">0/7</p>
                <p class="mt-2 text-sm text-slate-300 uppercase tracking-wider">Dukungan</p>
            </div>
        </div>
    </section>

    <!-- ============ WHY CHOOSE US ============ -->
    <section class="py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14" x-reveal>
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Keunggulan Kami</p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Mengapa Memilih Kami?</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">
                    Kami membangun kepercayaan melalui komitmen pada setiap proses pengiriman — dari penjemputan
                    hingga barang tiba di tangan penerima dengan kondisi sempurna.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    ['Reliable & On-Time Deliveries', 'Pengiriman tepat waktu dan terpercaya dengan standar SLA yang terukur dan dapat dipertanggungjawabkan.', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'blue'],
                    ['Customized Logistics Solutions', 'Solusi logistik yang dirancang sesuai kebutuhan bisnis Anda — dari volume kecil hingga project berskala besar.', 'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z', 'amber'],
                    ['Experienced Logistics Experts', 'Didukung tenaga ahli logistik berpengalaman dengan standar operasional yang profesional dan ketat.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'blue'],
                    ['Real-Time Tracking Systems', 'Sistem pemantauan pengiriman terkini — pantau status setiap kiriman secara real-time hingga tiba di tujuan.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'amber'],
                ] as $i => [$title, $desc, $icon, $tone])
                    <div x-reveal style="transition-delay: {{ $i * 0.1 }}s" class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-7 hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/60 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-all duration-300 {{ $tone === 'blue' ? 'bg-anl-blue-light text-anl-blue group-hover:bg-anl-blue group-hover:text-white' : 'bg-anl-amber-light text-anl-amber group-hover:bg-anl-amber group-hover:text-white' }}">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ============ JARINGAN CABANG ============ -->
    <section id="jaringan" class="py-20 lg:py-24 bg-slate-50 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12" x-reveal>
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Network & Coverage</p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Jaringan Cabang Perwakilan</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">
                    24 cabang perwakilan tersebar di seluruh Indonesia untuk layanan distribusi yang lebih luas dan efisien.
                </p>
            </div>

            <div x-data="{ region: 'Semua' }" class="max-w-4xl mx-auto">
                <!-- Tab Filter -->
                <div class="flex flex-wrap justify-center gap-2 mb-10">
                    @foreach (array_merge(['Semua'], array_keys($regions)) as $key)
                        <button type="button" @click="region = '{{ $key }}'"
                                class="px-4 py-2 min-h-[44px] rounded-full text-sm font-semibold border transition-all duration-300"
                                :class="region === '{{ $key }}' ? 'bg-anl-blue text-white border-anl-blue shadow-md shadow-anl-blue/20' : 'bg-white text-slate-600 border-slate-200 hover:border-anl-blue hover:text-anl-blue'">
                            {{ $key }}
                        </button>
                    @endforeach
                </div>

                <!-- Grid Kota -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach ($regions as $regionName => $cities)
                        @foreach ($cities as $city)
                            <div x-show="region === 'Semua' || region === '{{ $regionName }}'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-90"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="flex items-center gap-2 bg-white rounded-xl border border-slate-200 px-4 py-3 shadow-sm hover:border-anl-blue hover:shadow-md transition-all duration-300">
                                <svg class="w-4 h-4 text-anl-amber shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-semibold text-slate-700">{{ $city }}</span>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ============ CLIENTS / PARTNERS ============ -->
    @if (!empty($clients))
    <section class="py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12" x-reveal>
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Kepercayaan</p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Our Loyal Customers</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">
                    Dipercaya oleh klien & mitra bisnis dari berbagai sektor di seluruh Indonesia.
                </p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 items-center" x-reveal>
                @foreach ($clients as $logo)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center justify-center h-24 grayscale hover:grayscale-0 hover:border-anl-blue/40 hover:shadow-md transition-all duration-300">
                        <img src="{{ $logo['url'] }}" alt="{{ $logo['name'] }}" class="max-h-12 w-auto object-contain" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ============ BERITA TERBARU ============ -->
    @if ($latestPosts->isNotEmpty())
    <section class="py-20 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-12" x-reveal>
                <div>
                    <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Informasi & Update</p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Berita Terbaru</h2>
                </div>
                <a href="{{ route('berita') }}" class="hidden sm:inline-flex items-center gap-1 min-h-[44px] px-4 py-2 text-anl-blue font-bold text-sm hover:text-anl-blue-dark transition-colors">
                    Lihat semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8" x-reveal>
                @foreach ($latestPosts as $post)
                    <a href="{{ route('berita.show', $post->slug) }}"
                       class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] overflow-hidden hover:-translate-y-2 hover:shadow-xl transition-all duration-300 flex flex-col">
                        @if ($post->cover_image)
                            <div class="h-44 overflow-hidden">
                                <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div class="h-44 bg-gradient-to-br from-anl-navy to-anl-blue flex items-center justify-center text-white font-extrabold tracking-widest text-lg">ANL</div>
                        @endif
                        <div class="p-6 flex-1">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">{{ $post->published_at?->format('d F Y') }}</p>
                            <h3 class="font-bold text-slate-900 group-hover:text-anl-blue transition-colors">{{ $post->title }}</h3>
                            <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $post->excerpt }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-10 sm:hidden">
                <a href="{{ route('berita') }}" class="inline-block min-h-[44px] px-6 py-3 rounded-xl bg-anl-blue text-white font-bold shadow-md shadow-anl-blue/20 hover:bg-anl-blue-dark transition-all duration-300">
                    Lihat semua berita
                </a>
            </div>
        </div>
    </section>
    @endif
</x-site-layout>
