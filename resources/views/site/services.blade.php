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
                @foreach ($services as $service)
                    <div class="group bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] overflow-hidden hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
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
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $service->description }}</p>
                        </div>
                    </div>
                @endforeach
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
                @foreach ($moda as $item)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-[0_10px_30px_-5px_rgba(15,43,72,0.08)] p-7 hover:-translate-y-2 hover:shadow-xl hover:border-anl-blue/40 transition-all duration-300">
                        <div class="w-12 h-12 rounded-xl bg-anl-blue-light flex items-center justify-center text-anl-blue mb-4 overflow-hidden">
                            @if ($item->icon_image)
                                <img src="{{ asset('storage/'.$item->icon_image) }}" alt="Ikon {{ $item->name }}" class="w-full h-full object-contain">
                            @elseif ($item->icon_svg)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item->icon_svg }}" />
                                </svg>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">{{ $item->name }}</h3>
                        <p class="mt-3 text-sm text-slate-600 leading-relaxed">{{ $item->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 lg:py-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12" x-reveal>
                <p class="text-anl-amber font-bold tracking-widest uppercase text-sm">FAQ</p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Pertanyaan yang Sering Diajukan</h2>
                <p class="mt-4 text-slate-600 leading-relaxed">Temukan jawaban atas pertanyaan umum seputar layanan pengiriman ANL.</p>
            </div>

            <div class="space-y-4" x-data="{ open: null }" x-reveal>
                @foreach ([
                    ['Berapa lama waktu pengiriman?', 'Durasi tergantung rute dan moda. Pengiriman darat antar kota umumnya 1–3 hari kerja, laut 3–7 hari, dan udara 1–2 hari. Estimasi waktu pasti dikonfirmasi tim kami saat penjadwalan.'],
                    ['Bagaimana cara menghitung tarif pengiriman?', 'Tarif dihitung berdasarkan berat/volume barang, jarak, dan moda yang dipilih. Hubungi marketing kami untuk penawaran tercepat melalui WhatsApp, email, atau form kontak.'],
                    ['Apakah barang bisa dijemput di lokasi?', 'Ya, kami menyediakan layanan penjemputan (pickup) ke alamat Anda. Cukup sertakan alamat penjemputan saat menghubungi kami.'],
                    ['Bagaimana cara memantau status pengiriman?', 'Setiap kiriman dilengkapi sistem pemantauan real-time. Petugas kami akan menginformasikan perkembangan status hingga barang tiba di tujuan.'],
                    ['Apakah melayani pengiriman proyek dan alat berat?', 'Ya, divisi Project Logistics menangani muatan berukuran/berat ekstra, alat berat, hingga kebutuhan proyek dengan perencanaan menyeluruh dan armada khusus.'],
                ] as $index => [$q, $a])
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}" :aria-expanded="(open === {{ $index }}).toString()"
                                class="w-full flex items-center justify-between gap-4 px-6 py-5 min-h-[44px] text-left font-bold text-slate-900 hover:bg-anl-blue-light/50 transition-colors duration-200">
                            <span>{{ $q }}</span>
                            <span class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-white transition-colors duration-300"
                                  :class="open === {{ $index }} ? 'bg-anl-blue' : 'bg-slate-300'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </button>
                        <div class="grid transition-all duration-300 ease-in-out" :class="open === {{ $index }} ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0'">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-5 text-sm text-slate-600 leading-relaxed">{{ $a }}</p>
                            </div>
                        </div>
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
                <a href="{{ route('contact') }}" class="px-7 py-3.5 min-h-[44px] rounded-xl bg-anl-amber text-anl-navy font-bold shadow-lg shadow-black/10 hover:bg-anl-amber-dark hover:-translate-y-0.5 transition-all duration-300">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>
</x-site-layout>
