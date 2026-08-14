@extends('website.layouts.admin')

@section('title', 'Edit Berita')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-anl-navy">Edit Berita</h1>
        <p class="text-sm text-gray-500 mt-1">Perbarui konten berita. Slug akan dibuat ulang dari judul secara otomatis.</p>
    </div>

    <form method="POST" action="{{ route('website.posts.update', $post) }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Berita</label>
            <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Ringkasan (Excerpt)</label>
            <textarea name="excerpt" rows="2" required
                      class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Berita</label>
            <textarea name="body" rows="12" required
                      class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">{{ old('body', $post->body) }}</textarea>
            @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar Cover (kosongkan jika tidak diubah)</label>
                @if ($post->cover_image)
                    <img src="{{ asset('storage/'.$post->cover_image) }}" alt="{{ $post->title }}" class="mb-2 h-28 w-full object-cover rounded-lg border border-gray-200">
                @endif
                <input type="file" name="cover_image" accept="image/*"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-anl-navy file:text-white file:text-sm file:font-semibold">
                @error('cover_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">
                    <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft (disimpan, belum tampil)</option>
                    <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published (langsung tampil)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('website.posts.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100">Batal</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-anl-navy text-white text-sm font-semibold hover:bg-anl-navy-light transition-colors">
                Simpan Perubahan
            </button>
        </div>
    </form>
@endsection
