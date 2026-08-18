<x-app-layout>
    <x-slot name="header">
        Laporan & Export
    </x-slot>

    <div class="space-y-6">

        <!-- Filter Laporan -->
        <div class="card p-6" x-reveal>
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Buat Laporan</h3>
                        <p class="text-xs text-gray-500">Terapkan filter terlebih dahulu, lalu unduh laporan dalam format Excel atau PDF.</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('reports.index') }}" x-loading class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="field-label">Tanggal Mulai (HO)</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Tanggal Selesai (HO)</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="field-input">
                </div>
                <div>
                    <label class="field-label">Provinsi</label>
                    <select name="province" class="field-input">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Vendor</label>
                    <select name="vendor_id" class="field-input">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <select name="status" class="field-input">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-5 flex items-end justify-end gap-2">
                    <a href="{{ route('reports.index') }}" class="btn-ghost">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </form>

            <!-- Tombol Export -->
            <div class="flex flex-wrap gap-3 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('reports.export-excel', request()->query()) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-gradient-to-br from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-bold text-sm rounded-lg shadow-md shadow-emerald-600/20 transition-all duration-200 hover:shadow-lg active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel (.xlsx)
                </a>
                <a href="{{ route('reports.export-pdf', request()->query()) }}"
                   class="inline-flex items-center gap-2.5 px-5 py-2.5 bg-gradient-to-br from-rose-600 to-rose-700 hover:from-rose-500 hover:to-rose-600 text-white font-bold text-sm rounded-lg shadow-md shadow-rose-600/20 transition-all duration-200 hover:shadow-lg active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Ringkasan KPI Laporan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="card p-4 flex items-center gap-3.5 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal>
                <span class="icon-chip bg-gray-100 text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Resi</p>
                    <p class="text-2xl font-black text-gray-900 tabular-nums">{{ number_format($kpis['totalShipments']) }}</p>
                </div>
            </div>

            <div class="card p-4 flex items-center gap-3.5 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <span class="icon-chip bg-emerald-100 text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Completed</p>
                    <p class="text-2xl font-black text-emerald-600 tabular-nums">{{ number_format($kpis['completed']) }}</p>
                </div>
            </div>

            <div class="card p-4 flex items-center gap-3.5 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <span class="icon-chip bg-rose-100 text-rose-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Undelivered</p>
                    <p class="text-2xl font-black text-rose-600 tabular-nums">{{ number_format($kpis['undelivered']) }}</p>
                </div>
            </div>

            <div class="card p-4 flex items-center gap-3.5 bg-gradient-to-br from-white to-dbl-green-light/30 transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <span class="icon-chip bg-dbl-green text-white shadow-glow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </span>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">SLA Achievement</p>
                    <p class="text-2xl font-black text-dbl-green-dark tabular-nums">{{ $kpis['slaAchievementRate'] }}%</p>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
