<x-site-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>

    <!-- HEADER -->
    <section class="bg-anl-navy text-white py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ route('berita') }}" class="text-anl-gold text-sm font-semibold hover:underline">← Kembali ke Berita</a>
            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold leading-tight">{{ $post->title }}</h1>
            <p class="mt-4 text-sm text-gray-300">{{ $post->published_at?->format('d F Y') }}</p>
        </div>
    </section>

    <!-- ISI -->
    <section class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($post->cover_image)
                <div class="rounded-2xl overflow-hidden mb-8">
                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="w-full max-h-96 object-cover">
                </div>
            @endif

            <p class="text-lg text-gray-700 font-medium leading-relaxed mb-6">{{ $post->excerpt }}</p>

            <div class="text-gray-800 leading-relaxed whitespace-pre-line">{{ $post->body }}</div>

            <div class="mt-10 pt-6 border-t border-gray-200 text-sm text-gray-500">
                Ditulis oleh {{ $post->author?->name ?? 'Admin' }}
            </div>
        </div>
    </section>
</x-site-layout>
