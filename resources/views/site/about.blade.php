<x-site-layout>
    <x-slot name="title">Tentang Kami</x-slot>

    <!-- HEADER -->
    <section class="bg-anl-navy text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-anl-gold font-semibold tracking-widest uppercase text-sm mb-3">Profil Perusahaan</p>
            <h1 class="text-4xl font-extrabold">Tentang Kami</h1>
        </div>
    </section>

    <!-- VISI MISI -->
    <section class="py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <h2 class="text-2xl font-extrabold text-anl-navy mb-6">Siapa Kami</h2>
                <p class="text-gray-600 leading-relaxed">
                    Amanah Nusantara Logistik adalah perusahaan yang bergerak di bidang jasa distribusi dan
                    pengiriman. Berdiri dengan semangat pelayanan yang amanah, kami melayani kebutuhan logistik
                    perusahaan maupun perorangan di berbagai kota di Indonesia.
                </p>
                <p class="mt-4 text-gray-600 leading-relaxed">
                    Setiap pengiriman dikelola melalui sistem operasional yang terukur — mulai dari penjadwalan,
                    pemilihan vendor last-mile, hingga evaluasi kepatuhan SLA — sehingga kualitas layanan selalu
                    dapat dipantau dan ditingkatkan.
                </p>
            </div>

            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
                    <div class="w-12 h-12 rounded-xl bg-anl-navy flex items-center justify-center text-anl-gold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-anl-navy mb-2">Visi</h3>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Menjadi perusahaan logistik nasional yang paling dipercaya, menghadirkan layanan
                        pengiriman yang aman, cepat, dan tepat waktu di seluruh Indonesia.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
                    <div class="w-12 h-12 rounded-xl bg-anl-navy flex items-center justify-center text-anl-gold mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-anl-navy mb-2">Misi</h3>
                    <ul class="text-sm text-gray-600 leading-relaxed space-y-1.5 list-disc list-inside">
                        <li>Menyediakan layanan logistik yang amanah dan profesional.</li>
                        <li>Mengoptimalkan setiap rute dan proses pengiriman.</li>
                        <li>Menjaga komitmen waktu sesuai standar layanan (SLA).</li>
                        <li>Membangun kemitraan jangka panjang dengan pelanggan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- NILAI PERUSAHAAN -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-extrabold text-anl-navy">Nilai-Nilai Kami</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ([
                    ['Amanah', 'Kepercayaan adalah segalanya. Setiap muatan dijaga dan dipertanggungjawabkan hingga tiba di tujuan.'],
                    ['Tepat Waktu', 'Komitmen terhadap waktu yang terukur, dipantau, dan dievaluasi melalui standar SLA.'],
                    ['Transparan', 'Setiap proses dapat dilacak dan dipertanggungjawabkan kepada pelanggan secara terbuka.'],
                ] as [$title, $desc])
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-anl-navy mb-2">{{ $title }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-site-layout>
