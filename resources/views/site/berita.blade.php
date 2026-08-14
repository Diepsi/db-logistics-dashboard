<x-site-layout>
    <x-slot name="title">Berita</x-slot>

    <!-- HEADER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-anl-navy to-anl-navy-dark text-white py-16">
        <div class="absolute -top-24 right-0 w-96 h-96 rounded-full bg-anl-blue/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-anl-amber font-bold tracking-widest uppercase text-sm mb-2">Informasi & Update</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Berita Terbaru</h1>
            <p class="mt-3 text-slate-300 max-w-2xl">Kabar terbaru seputar layanan, operasional, dan informasi dari Amanah Nusantara Logistik.</p>
        </div>
    </section>

    <!-- DAFTAR BERITA -->
    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($posts->isEmpty())
                <p class="text-center text-slate-500 py-16">Belum ada berita.</p>
            @else
                <div x-data="{ q: '' }" class="relative">
                    <div class="mb-10">
                        <label for="berita-search" class="sr-only">Cari berita</label>
                        <div class="relative max-w-md mx-auto">
                            <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input id="berita-search" type="search" x-model="q" placeholder="Cari judul berita..." autocomplete="off"
                                   class="w-full pl-12 pr-12 py-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-anl-blue focus:border-anl-blue transition-all duration-200">
                            <button type="button" @click="q = ''" aria-label="Bersihkan pencarian" x-show="q !== ''" x-cloak
                                    class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    @php
                        $pageSearchable = $posts->map(fn ($p) => $p->title.' '.($p->excerpt ?? ''))->join(' ');
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($posts as $post)
                            <a href="{{ route('berita.show', $post->slug) }}"
                               x-show="!q || {{ json_encode($post->title.' '.($post->excerpt ?? '')) }}.toLowerCase().includes(q.toLowerCase())"
                               x-transition:enter="transition ease-out duration-200"
                               x-transition:enter-start="opacity-0 scale-95"
                               x-transition:enter-end="opacity-100 scale-100"
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
                                    <h2 class="font-bold text-slate-900 group-hover:text-anl-blue transition-colors">{{ $post->title }}</h2>
                                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $post->excerpt }}</p>
                                </div>
                            </a>
                        @endforeach

                        <p x-show="q !== '' && !{{ json_encode($pageSearchable) }}.toLowerCase().includes(q.toLowerCase())"
                           x-cloak class="col-span-full text-center text-slate-500 py-16">
                            Tidak ditemukan berita dengan kata kunci <span class="font-semibold text-slate-700" x-text="q"></span>.
                        </p>
                    </div>
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
</x-site-layout>
