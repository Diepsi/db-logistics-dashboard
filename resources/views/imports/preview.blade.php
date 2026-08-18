<x-app-layout>
    <x-slot name="header">
        Preview & Konfirmasi Import
    </x-slot>

    <div class="space-y-6">

        <!-- Ringkasan Validasi -->
        <div class="card p-6" x-reveal>
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <span class="icon-chip bg-blue-100 text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Ringkasan Validasi File</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            File berhasil divalidasi. Periksa ringkasan sebelum data dikirim ke database.
                        </p>
                    </div>
                </div>
                <a href="{{ route('imports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-800 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Unggah ulang
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
                <div class="card p-4 border-gray-100 bg-gray-50/60 transition-all duration-300 hover:shadow-lift">
                    <p class="field-label mb-1.5">Total Baris</p>
                    <p class="text-2xl font-black text-gray-900 tabular-nums">{{ number_format($preview['total']) }}</p>
                </div>
                <div class="card p-4 bg-emerald-50 border-emerald-200 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5">
                    <p class="field-label mb-1.5 !text-emerald-700">Valid</p>
                    <p class="text-2xl font-black text-emerald-600 tabular-nums">{{ number_format($preview['valid']) }}</p>
                </div>
                <div class="card p-4 bg-rose-50 border-rose-200 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5">
                    <p class="field-label mb-1.5 !text-rose-700">Tidak Valid</p>
                    <p class="text-2xl font-black text-rose-600 tabular-nums">{{ number_format($preview['invalid']) }}</p>
                </div>
                <div class="card p-4 bg-amber-50 border-amber-200 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5">
                    <p class="field-label mb-1.5 !text-amber-700">Duplikat (dalam file)</p>
                    <p class="text-2xl font-black text-amber-600 tabular-nums">{{ number_format($preview['duplicate']) }}</p>
                </div>
            </div>

            @if(! empty($preview['invalidSamples']))
                <div class="mt-5 p-4 rounded-xl bg-rose-50 border border-rose-100">
                    <p class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span class="dot bg-rose-500"></span>Contoh Baris Tidak Valid
                    </p>
                    <ul class="text-xs text-rose-800 space-y-1">
                        @foreach($preview['invalidSamples'] as $sample)
                            <li>No Resi <strong>{{ $sample['no_resi'] ?? '(kosong)' }}</strong> — {{ $sample['reason'] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Konfirmasi -->
            <form action="{{ route('imports.process') }}" method="POST" class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100 pt-5">
                @csrf
                <input type="hidden" name="token" value="{{ $preview['token'] }}">
                <p class="text-xs text-gray-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Data valid akan disimpan / diperbarui otomatis berdasarkan <code class="bg-gray-100 px-1.5 py-0.5 rounded text-[11px] font-bold">no_resi</code> (upsert anti-duplikat).
                </p>
                <button type="submit" class="btn-primary shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Konfirmasi &amp; Simpan {{ number_format($preview['valid']) }} Baris
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
