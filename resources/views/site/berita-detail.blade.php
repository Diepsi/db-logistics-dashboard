<x-site-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>

    <!-- HEADER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-anl-navy to-anl-navy-dark text-white py-14">
        <div class="absolute -top-24 right-0 w-96 h-96 rounded-full bg-anl-blue/20 blur-3xl pointer-events-none"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('berita') }}" class="inline-flex items-center gap-1 min-h-[44px] text-anl-amber text-sm font-bold hover:underline">← Kembali ke Berita</a>
            <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold leading-tight tracking-tight">{{ $post->title }}</h1>
            <p class="mt-4 text-sm text-slate-300">{{ $post->published_at?->format('d F Y') }}</p>
        </div>
    </section>

    <!-- ISI -->
    <section class="py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($post->cover_image)
                <div class="rounded-2xl overflow-hidden mb-8 shadow-lg">
                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="w-full max-h-96 object-cover">
                </div>
            @endif

            <p class="text-lg text-slate-700 font-medium leading-relaxed mb-6">{{ $post->excerpt }}</p>

            <div class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $post->body }}</div>

            <div class="mt-10 pt-6 border-t border-slate-200 text-sm text-slate-500">
                Ditulis oleh {{ $post->author?->name ?? 'Admin' }}
            </div>
        </div>
    </section>
</x-site-layout>
