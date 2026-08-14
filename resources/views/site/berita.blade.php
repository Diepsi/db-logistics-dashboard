<x-site-layout>
    <x-slot name="title">Berita</x-slot>

    <!-- HEADER -->
    <section class="bg-anl-navy text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-anl-gold font-semibold tracking-widest uppercase text-sm mb-2">Informasi & Update</p>
            <h1 class="text-3xl sm:text-4xl font-extrabold">Berita Terbaru</h1>
            <p class="mt-3 text-gray-300 max-w-2xl">Kabar terbaru seputar layanan, operasional, dan informasi dari Amanah Nusantara Logistik.</p>
        </div>
    </section>

    <!-- DAFTAR BERITA -->
    <section class="py-14 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($posts->isEmpty())
                <p class="text-center text-gray-500 py-16">Belum ada berita.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <a href="{{ route('berita.show', $post->slug) }}"
                           class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-lg transition-shadow group flex flex-col">
                            @if ($post->cover_image)
                                <div class="h-44 overflow-hidden">
                                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="h-44 bg-anl-navy flex items-center justify-center text-anl-gold font-extrabold">ANL</div>
                            @endif
                            <div class="p-5 flex-1">
                                <p class="text-xs text-gray-500 mb-2">{{ $post->published_at?->format('d F Y') }}</p>
                                <h2 class="font-bold text-anl-navy group-hover:text-anl-navy-light">{{ $post->title }}</h2>
                                <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $post->excerpt }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </section>
</x-site-layout>
