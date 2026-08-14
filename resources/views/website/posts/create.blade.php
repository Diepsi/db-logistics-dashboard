@extends('website.layouts.admin')

@section('title', 'Tulis Berita')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-anl-navy">Tulis Berita Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Artikel akan tampil di halaman publik /berita setelah dipublikasikan.</p>
    </div>

    <form method="POST" action="{{ route('website.posts.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Berita</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Ringkasan (Excerpt)</label>
            <textarea name="excerpt" rows="2" required
                      class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">{{ old('excerpt') }}</textarea>
            @error('excerpt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Isi Berita</label>
            <textarea name="body" rows="12" required
                      class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">{{ old('body') }}</textarea>
            @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar Cover (opsional)</label>
                <input type="file" name="cover_image" accept="image/*"
                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-anl-navy file:text-white file:text-sm file:font-semibold">
                @error('cover_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy">
                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (disimpan, belum tampil)</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (langsung tampil)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('website.posts.index') }}" class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100">Batal</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-anl-navy text-white text-sm font-semibold hover:bg-anl-navy-light transition-colors">
                Simpan Berita
            </button>
        </div>
    </form>
@endsection
