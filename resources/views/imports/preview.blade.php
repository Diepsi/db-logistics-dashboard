<x-app-layout>
    <x-slot name="header">
        Preview & Konfirmasi Import
    </x-slot>

    <div class="space-y-6">

        <!-- Ringkasan Validasi -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Ringkasan Validasi File</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        File berhasil divalidasi. Periksa ringkasan sebelum data dikirim ke database.
                    </p>
                </div>
                <a href="{{ route('imports.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 flex items-center space-x-1">
                    <span>Unggah ulang</span>
                    <span>↩</span>
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Baris</p>
                    <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($preview['total']) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                    <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Valid</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($preview['valid']) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200">
                    <p class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Tidak Valid</p>
                    <p class="text-2xl font-black text-rose-600 mt-1">{{ number_format($preview['invalid']) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Duplikat (dalam file)</p>
                    <p class="text-2xl font-black text-amber-600 mt-1">{{ number_format($preview['duplicate']) }}</p>
                </div>
            </div>

            @if(! empty($preview['invalidSamples']))
                <div class="mt-5 p-4 rounded-xl bg-rose-50 border border-rose-100">
                    <p class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-2">Contoh Baris Tidak Valid</p>
                    <ul class="text-xs text-rose-800 space-y-1">
                        @foreach($preview['invalidSamples'] as $sample)
                            <li>No Resi <strong>{{ $sample['no_resi'] ?? '(kosong)' }}</strong> — {{ $sample['reason'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Konfirmasi -->
            <form action="{{ route('imports.process') }}" method="POST" class="mt-6 flex items-center justify-between border-t border-gray-100 pt-5">
                @csrf
                <input type="hidden" name="token" value="{{ $preview['token'] }}">
                <p class="text-xs text-gray-500">
                    Data valid akan disimpan / diperbarui otomatis berdasarkan <code class="bg-gray-100 px-1 rounded">no_resi</code> (upsert anti-duplikat).
                </p>
                <button type="submit" class="px-6 py-2.5 bg-dbl-green hover:bg-dbl-green-dark text-dbl-dark font-bold text-sm rounded-lg shadow-md transition-all">
                    Konfirmasi & Simpan {{ number_format($preview['valid']) }} Baris
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
