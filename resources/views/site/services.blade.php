<x-site-layout>
    <x-slot name="title">Layanan</x-slot>

    <!-- HEADER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-anl-navy to-anl-navy-dark text-white">
        <div class="absolute -top-24 right-0 w-96 h-96 rounded-full bg-anl-blue/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-anl-amber font-bold tracking-widest uppercase text-sm mb-3">Apa yang Kami Tawarkan</p>
            <h1 class="text-4xl font-extrabold tracking-tight">Layanan Kami</h1>
            <p class="mt-4 text-slate-300 max-w-2xl leading-relaxed">
                Dalam memberi pelayanan terbaik dan terpercaya, ANL didukung oleh tim profesional berpengalaman
                serta armada yang memadai.
            </p>
        </div>
    </section>

    <!-- DAFTAR LAYANAN -->
    <section class="py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- LTL -->
                <div class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] overflow-hidden hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                    <div class="relative h-44 bg-gradient-to-br from-anl-navy to-[#1E3A5F] flex items-center justify-center overflow-hidden">
                        <svg class="w-28 h-28 text-anl-blue/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-anl-amber text-anl-navy text-xs font-bold tracking-wider uppercase shadow-lg">Retail</span>
                    </div>
                    <div class="p-7">
                        <h3 class="text-xl font-bold text-slate-900">Less Than Truckload <span class="text-anl-blue">(LTL)</span></h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                            Pengiriman Retail / Parsial — menggabungkan muatan beberapa pengirim dalam satu truk sehingga
                            biaya lebih efisien untuk kiriman skala kecil hingga menengah.
                        </p>
                    </div>
                </div>

                <!-- FTL -->
                <div class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] overflow-hidden hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                    <div class="relative h-44 bg-gradient-to-br from-anl-blue to-[#1E40AF] flex items-center justify-center overflow-hidden">
                        <svg class="w-28 h-28 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                        </svg>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-white text-anl-blue text-xs font-bold tracking-wider uppercase shadow-lg">Charter</span>
                    </div>
                    <div class="p-7">
                        <h3 class="text-xl font-bold text-slate-900">Full Truckload <span class="text-anl-blue">(FTL)</span></h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                            Sewa Truk / Charter Penuh — penyewaan armada penuh untuk kebutuhan distribusi dengan volume besar,
                            rute khusus, dan prioritas pengiriman.
                        </p>
                    </div>
                </div>

                <!-- Project Logistics -->
                <div class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] overflow-hidden hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                    <div class="relative h-44 bg-gradient-to-br from-anl-amber to-[#D97706] flex items-center justify-center overflow-hidden">
                        <svg class="w-28 h-28 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.3" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8M9 12h6"/>
                        </svg>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-anl-navy text-anl-amber text-xs font-bold tracking-wider uppercase shadow-lg">Custom Cargo</span>
                    </div>
                    <div class="p-7">
                        <h3 class="text-xl font-bold text-slate-900">Project Logistics</h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                            Pengiriman kargo khusus & project logistics — penanganan muatan berukuran/berat ekstra, alat berat,
                            dan kebutuhan proyek dengan perencanaan menyeluruh.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODA PENGIRIMAN -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Jangkauan</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">3 Moda Pengiriman Domestik</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">Melayani pengiriman ke seluruh Indonesia melalui tiga jalur.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['Darat', 'Armada trucking untuk rute antar kota & antar pulau di Pulau Sumatera dan sekitarnya.', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
                    ['Laut', 'Pengangkutan muatan antar pulau dengan perencanaan logistik matang dan penanganan aman.', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['Udara', 'Pengiriman cepat untuk kebutuhan mendesak dengan prioritas penanganan dan ketepatan waktu.', 'M12 19V9m0 0l-4 4m4-4l4 4M12 3a9 9 0 110 16 9 9 0 010-16z'],
                ] as [$name, $desc, $icon])
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-7 hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl bg-anl-blue-light flex items-center justify-center text-anl-blue mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $name }}</h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-gradient-to-br from-anl-navy to-anl-blue text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h2 class="text-3xl font-extrabold tracking-tight">Siap Bermitra Dengan Kami?</h2>
            <p class="mt-4 text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Hubungi tim kami untuk konsultasi kebutuhan logistik perusahaan Anda secara gratis.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="px-7 py-3.5 min-h-[44px] rounded-xl bg-anl-amber text-anl-navy font-bold shadow-lg shadow-black/10 hover:bg-[#FBBF24] hover:-translate-y-0.5 transition-all duration-300">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>
</x-site-layout>
