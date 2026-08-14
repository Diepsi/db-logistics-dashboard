<x-site-layout>
    <x-slot name="title">Layanan</x-slot>

    <!-- HEADER -->
    <section class="bg-anl-navy text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-anl-gold font-semibold tracking-widest uppercase text-sm mb-3">Apa yang Kami Tawarkan</p>
            <h1 class="text-4xl font-extrabold">Layanan Kami</h1>
            <p class="mt-4 text-gray-300 max-w-2xl">
                Solusi logistik yang lengkap untuk kebutuhan distribusi Anda, didukung pemantauan operasional yang transparan.
            </p>
        </div>
    </section>

    <!-- DAFTAR LAYANAN -->
    <section class="py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ([
                    ['Distribusi Trucking', 'M12 3v1m0 16v1m9-9h1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z', 'Pengiriman barang dalam kota dan antar kota dengan armada yang terpelihara, rute teroptimasi, serta koordinasi vendor last-mile yang andal.'],
                    ['Kargo Antar Pulau', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'Pengangkutan barang skala besar antar pulau dengan perencanaan logistik matang dan penanganan muatan yang aman.'],
                    ['Pengiriman Kilat (Same Day)', 'M13 10V3L4 14h7v7l9-11h-7z', 'Layanan prioritas untuk kebutuhan mendesak dengan penjemputan cepat dan jaminan waktu penyampaian di hari yang sama.'],
                    ['Distribusi Dokumen & Paket', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'Penanganan dokumen dan paket perusahaan dengan pencatatan resi, pelacakan, dan konfirmasi penerimaan.'],
                    ['Manajemen Rute & Vendor', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'Optimalisasi rute distribusi dan pengelolaan vendor last-mile untuk efisiensi biaya dan ketepatan waktu.'],
                    ['Monitoring & Pelaporan', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'Pemantauan KPI pengiriman, kepatuhan SLA, dan pelaporan performa secara berkala untuk pengambilan keputusan.'],
                ] as [$name, $icon, $desc])
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-lg transition-shadow flex space-x-5">
                        <div class="w-12 h-12 rounded-xl bg-anl-navy flex items-center justify-center text-anl-gold shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-anl-navy mb-2">{{ $name }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="bg-anl-navy text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 text-center">
            <h2 class="text-3xl font-extrabold">Siap Bermitra Dengan Kami?</h2>
            <p class="mt-4 text-gray-300 max-w-2xl mx-auto">
                Hubungi tim kami untuk konsultasi kebutuhan logistik perusahaan Anda secara gratis.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('contact') }}" class="px-6 py-3 rounded-lg bg-anl-gold text-anl-navy font-semibold hover:bg-anl-gold-dark transition-colors">
                    Hubungi Kami
                </a>
                @guest
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-lg border border-white/30 font-semibold hover:bg-white/10 transition-colors">
                        Masuk Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>
</x-site-layout>
