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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($posts as $post)
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
                                <h2 class="font-bold text-slate-900 group-hover:text-anl-blue transition-colors">{{ $post->title }}</h2>
                                <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $post->excerpt }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
</x-site-layout>
