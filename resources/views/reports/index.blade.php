<x-app-layout>
    <x-slot name="header">
        Laporan & Export
    </x-slot>

    <div class="space-y-6">

        <!-- Filter Laporan -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-1">Buat Laporan</h3>
            <p class="text-xs text-gray-500 mb-4">Terapkan filter terlebih dahulu, lalu unduh laporan dalam format Excel atau PDF.</p>

            <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Mulai (HO)</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Selesai (HO)</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Provinsi</label>
                    <select name="province" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Vendor</label>
                    <select name="vendor_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Status</label>
                    <select name="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-5 flex items-end justify-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-sm font-semibold">
                        Terapkan Filter
                    </button>
                    <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-gray-50 text-gray-400 hover:bg-gray-100 rounded-lg text-sm font-semibold">
                        Reset
                    </a>
                </div>
            </form>

            <!-- Tombol Export -->
            <div class="flex flex-wrap gap-3 mt-6 pt-5 border-t border-gray-100">
                <a href="{{ route('reports.export-excel', request()->query()) }}"
                   class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors flex items-center space-x-2">
                    <span>📗</span>
                    <span>Export Excel (.xlsx)</span>
                </a>
                <a href="{{ route('reports.export-pdf', request()->query()) }}"
                   class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors flex items-center space-x-2">
                    <span>📕</span>
                    <span>Export PDF</span>
                </a>
            </div>
        </div>

        <!-- Ringkasan KPI Laporan -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Resi</p>
                <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($kpis['totalShipments']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Completed</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($kpis['completed']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Undelivered</p>
                <p class="text-2xl font-black text-rose-600 mt-1">{{ number_format($kpis['undelivered']) }}</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">SLA Achievement</p>
                <p class="text-2xl font-black text-dbl-green-dark mt-1">{{ $kpis['slaAchievementRate'] }}%</p>
            </div>
        </div>

    </div>
</x-app-layout>
