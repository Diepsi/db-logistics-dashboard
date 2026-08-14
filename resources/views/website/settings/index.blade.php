@extends('website.layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-anl-navy">Pengaturan Website</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola logo, kartu layanan, moda pengiriman, dan logo klien tanpa mengubah kode.</p>
    </div>

    <div x-data="{ tab: 'identitas' }" class="space-y-6">
        <!-- Tabs -->
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="tab = 'identitas'"
                    :class="tab === 'identitas' ? 'bg-anl-navy text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:border-anl-navy'"
                    class="px-5 py-2.5 min-h-[44px] rounded-xl text-sm font-bold transition-all duration-200">
                Identitas
            </button>
            <button type="button" @click="tab = 'layanan'"
                    :class="tab === 'layanan' ? 'bg-anl-navy text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:border-anl-navy'"
                    class="px-5 py-2.5 min-h-[44px] rounded-xl text-sm font-bold transition-all duration-200">
                Layanan & Moda
            </button>
            <button type="button" @click="tab = 'klien'"
                    :class="tab === 'klien' ? 'bg-anl-navy text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:border-anl-navy'"
                    class="px-5 py-2.5 min-h-[44px] rounded-xl text-sm font-bold transition-all duration-200">
                Our Loyal Customers
            </button>
        </div>

        <!-- ============ TAB IDENTITAS ============ -->
        <section x-show="tab === 'identitas'" x-transition.opacity class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-extrabold text-anl-navy">Logo Website</h2>
            <p class="text-sm text-gray-500 mt-1 mb-6">Logo tampil di navbar, footer, dan halaman login CMS.</p>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-48 h-24 rounded-xl border border-gray-200 bg-slate-50 flex items-center justify-center overflow-hidden">
                    <img src="{{ \App\Models\Setting::logoUrl() }}" alt="Logo saat ini" class="max-h-20 w-auto object-contain">
                </div>

                <form method="POST" action="{{ route('website.settings.logo') }}" enctype="multipart/form-data" class="flex-1 space-y-3">
                    @csrf
                    <label class="block text-sm font-semibold text-gray-700">Ganti Logo</label>
                    <input type="file" name="logo" accept="image/*" required
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-anl-navy file:text-white file:text-sm file:font-semibold">
                    @error('logo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-anl-navy text-white text-sm font-bold hover:bg-anl-navy-light transition-colors">
                        Simpan Logo
                    </button>
                </form>
            </div>
        </section>

        <!-- ============ TAB LAYANAN & MODA ============ -->
        <section x-show="tab === 'layanan'" x-transition.opacity>
            <form method="POST" action="{{ route('website.settings.services') }}" enctype="multipart/form-data"
                  class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-8">
                @csrf

                @foreach (['layanan' => 'Kartu Layanan', 'moda' => 'Moda Pengiriman'] as $section => $sectionLabel)
                    <div>
                        <h2 class="text-lg font-extrabold text-anl-navy">{{ $sectionLabel }}</h2>
                        <p class="text-sm text-gray-500 mt-1 mb-5">
                            {{ $section === 'layanan' ? 'Tampil di beranda dan halaman Layanan.' : 'Tampil di halaman Layanan (Darat / Laut / Udara).' }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($services->where('section', $section) as $service)
                                <div class="rounded-xl border border-gray-200 p-5 space-y-4">
                                    <input type="hidden" name="services[{{ $service->id }}][id]" value="{{ $service->id }}">

                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-xl bg-anl-blue-light flex items-center justify-center text-anl-blue overflow-hidden">
                                            @if ($service->icon_image)
                                                <img src="{{ asset('storage/'.$service->icon_image) }}" alt="Ikon {{ $service->name }}" class="w-full h-full object-contain">
                                            @elseif ($service->icon_svg)
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $service->icon_svg }}" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">Ikon saat ini</div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Judul</label>
                                        <input type="text" name="services[{{ $service->id }}][name]" value="{{ old('services.'.$service->id.'.name', $service->name) }}" required
                                               class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy text-sm">
                                    </div>

                                    @if ($section === 'layanan')
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Label (badge)</label>
                                            <input type="text" name="services[{{ $service->id }}][badge]" value="{{ old('services.'.$service->id.'.badge', $service->badge) }}"
                                                   class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy text-sm">
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                                        <textarea name="services[{{ $service->id }}][description]" rows="3"
                                                  class="w-full rounded-lg border-gray-300 focus:border-anl-navy focus:ring-anl-navy text-sm">{{ old('services.'.$service->id.'.description', $service->description) }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Ganti Ikon (opsional — kosongkan jika tidak diubah)</label>
                                        <input type="file" name="services[{{ $service->id }}][icon_image]" accept="image/*"
                                               class="block w-full text-sm text-gray-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-anl-navy file:text-white file:text-xs file:font-semibold">
                                        <label class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600">
                                            <input type="checkbox" name="services[{{ $service->id }}][remove_icon]" value="1"
                                                   class="rounded border-gray-300 text-anl-navy focus:ring-anl-navy">
                                            Kembalikan ke ikon bawaan (hapus gambar ikon)
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-anl-navy text-white text-sm font-bold hover:bg-anl-navy-light transition-colors">
                        Simpan Layanan & Moda
                    </button>
                </div>
            </form>
        </section>

        <!-- ============ TAB KLIEN ============ -->
        <section x-show="tab === 'klien'" x-transition.opacity class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-extrabold text-anl-navy">Tambah Logo Klien</h2>
                <p class="text-sm text-gray-500 mt-1 mb-5">Bisa pilih beberapa file sekaligus.</p>

                <form method="POST" action="{{ route('website.settings.clients.store') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="logos[]" accept="image/*" multiple required
                           class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-anl-navy file:text-white file:text-sm file:font-semibold">
                    @error('logos') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    <button type="submit" class="px-5 py-2.5 rounded-lg bg-anl-navy text-white text-sm font-bold hover:bg-anl-navy-light transition-colors">
                        Tambah Logo
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-extrabold text-anl-navy mb-5">Daftar Logo Klien</h2>

                @if ($clients->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada logo klien. Tambahkan melalui form di atas.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($clients as $client)
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-xl border border-gray-200 p-4">
                                <div class="w-28 h-16 rounded-lg bg-slate-50 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                    <img src="{{ asset('storage/'.$client->image_path) }}" alt="{{ $client->name }}" class="max-h-12 w-auto object-contain">
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-anl-navy truncate">{{ $client->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Urutan {{ $client->sort_order }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($client->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Nonaktif</span>
                                    @endif

                                    <form method="POST" action="{{ route('website.settings.clients.toggle', $client) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-300 text-gray-600 hover:border-anl-navy hover:text-anl-navy transition-colors">
                                            {{ $client->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('website.settings.clients.move', $client) }}">
                                        @csrf
                                        <input type="hidden" name="direction" value="up">
                                        <button type="submit" title="Naik" class="px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-300 text-gray-600 hover:border-anl-navy hover:text-anl-navy transition-colors">↑</button>
                                    </form>
                                    <form method="POST" action="{{ route('website.settings.clients.move', $client) }}">
                                        @csrf
                                        <input type="hidden" name="direction" value="down">
                                        <button type="submit" title="Turun" class="px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-300 text-gray-600 hover:border-anl-navy hover:text-anl-navy transition-colors">↓</button>
                                    </form>

                                    <form method="POST" action="{{ route('website.settings.clients.destroy', $client) }}"
                                          onsubmit="return confirm('Hapus logo klien ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold text-red-600 border border-red-200 hover:bg-red-50 transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
