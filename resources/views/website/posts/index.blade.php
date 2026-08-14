@extends('website.layouts.admin')

@section('title', 'Kelola Berita')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-anl-navy">Kelola Berita</h1>
            <p class="text-sm text-gray-500 mt-1">Tambah dan kelola artikel/berita website.</p>
        </div>
        <a href="{{ route('website.posts.create') }}"
           class="px-4 py-2 rounded-lg bg-anl-navy text-white text-sm font-semibold hover:bg-anl-navy-light transition-colors">
            + Tulis Berita Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if ($posts->isEmpty())
            <p class="px-6 py-10 text-center text-gray-500 text-sm">Belum ada berita. Klik "Tulis Berita Baru" untuk mulai.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($posts as $post)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-anl-navy">{{ $post->title }}</p>
                                    <p class="text-xs text-gray-500">/berita/{{ $post->slug }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($post->status === 'published')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Published</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Draft</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $post->author?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $post->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('website.posts.edit', $post) }}" class="text-sm font-semibold text-anl-navy hover:text-anl-navy-light">Edit</a>
                                    <form method="POST" action="{{ route('website.posts.destroy', $post) }}" class="inline"
                                          onsubmit="return confirm('Hapus berita ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700 ml-3">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection
