<x-site-layout>
    <x-slot name="title">Tentang Kami</x-slot>

    <!-- HEADER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-anl-navy to-anl-navy-dark text-white">
        <div class="absolute -top-24 right-0 w-96 h-96 rounded-full bg-anl-blue/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-anl-amber font-bold tracking-widest uppercase text-sm mb-3">Profil Perusahaan</p>
            <h1 class="text-4xl font-extrabold tracking-tight">Tentang Kami</h1>
        </div>
    </section>

    <!-- VISI MISI -->
    <section class="py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-1">
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm mb-3">Siapa Kami</p>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">PT. Amanah Nusantara Logistik</h2>
                <p class="mt-5 text-slate-600 leading-relaxed">
                    PT. Amanah Nusantara Logistik atau dikenal dengan brand <span class="font-semibold text-anl-navy">ANL</span>
                    berdiri pada awal tahun 2025, hadir untuk melayani jasa pengiriman domestik darat, laut, dan udara
                    ke seluruh Indonesia.
                </p>
                <p class="mt-4 text-slate-600 leading-relaxed">
                    ANL merupakan pengembangan dari <span class="font-semibold text-anl-navy">PT. Daulay Humala Bersaudara</span>
                    yang telah berdiri sejak awal tahun 2019 dan berfokus pada jasa pengiriman darat trucking ke Pulau Sumatera.
                </p>
            </div>

            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-7">
                    <div class="w-12 h-12 rounded-xl bg-anl-blue-light flex items-center justify-center text-anl-blue mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Visi</h3>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Menjadi perusahaan transportasi, jasa pengiriman, dan logistik yang profesional dan terpercaya,
                        mengutamakan kepuasan pelanggan yang menjadi <span class="font-semibold text-anl-blue">"MITRA UTAMA PELANGGAN"</span>.
                    </p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-7">
                    <div class="w-12 h-12 rounded-xl bg-anl-amber-light flex items-center justify-center text-anl-amber mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Misi</h3>
                    <ol class="text-sm text-slate-600 leading-relaxed space-y-2 list-decimal list-inside marker:text-anl-amber marker:font-bold">
                        <li>Mengoptimalkan armada trucking untuk distribusi yang tepat, cepat, dan aman.</li>
                        <li>Menyiapkan tenaga profesional guna memberikan kinerja terbaik dan membangun kepercayaan pelanggan.</li>
                        <li>Memperkuat kerja sama jaringan distribusi di seluruh Indonesia agar layanan lebih luas dan efisien.</li>
                        <li>Menjunjung etos kerja tinggi untuk memberikan pelayanan prima.</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- NILAI PERUSAHAAN -->
    <section class="py-20 lg:py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">Fundasi Kami</p>
                <h2 class="mt-3 text-3xl font-extrabold text-slate-900 tracking-tight">Nilai-Nilai Kami</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ([
                    ['Amanah', 'Kepercayaan adalah segalanya. Setiap muatan dijaga dan dipertanggungjawabkan hingga tiba di tujuan.'],
                    ['Tepat Waktu', 'Komitmen terhadap waktu yang terukur, dipantau, dan dievaluasi melalui standar SLA.'],
                    ['Transparan', 'Setiap proses dapat dilacak dan dipertanggungjawabkan kepada pelanggan secara terbuka.'],
                ] as [$title, $desc])
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-7 hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $title }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
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
