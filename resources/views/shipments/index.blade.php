<x-app-layout>
    <x-slot name="header">
        Data Pengiriman
    </x-slot>

    <div class="space-y-6">

        <!-- Filter & Pencarian -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <form method="GET" action="{{ route('shipments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">

                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No Resi, No Manifest, NPSN, Nama Sekolah..."
                        class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Provinsi</label>
                    <select name="province" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green"
                            onchange="this.form.submit()">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Kabupaten/Kota</label>
                    <select name="city_regency" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city_regency') == $city ? 'selected' : '' }}>{{ $city }}</option>
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

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Kepatuhan SLA</label>
                    <select name="sla" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua</option>
                        <option value="within" {{ request('sla') == 'within' ? 'selected' : '' }}>Within SLA</option>
                        <option value="over" {{ request('sla') == 'over' ? 'selected' : '' }}>Over SLA</option>
                    </select>
                </div>

                <div class="lg:col-span-7 flex items-end justify-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-dbl-dark text-white rounded-lg text-sm font-semibold hover:bg-black transition-colors">
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'start_date', 'end_date', 'province', 'city_regency', 'vendor_id', 'status', 'sla']))
                        <a href="{{ route('shipments.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-sm font-semibold">
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-800">Daftar Pengiriman</h3>
                <span class="text-xs text-gray-400">Menampilkan {{ $shipments->firstItem() ?? 0 }}–{{ $shipments->lastItem() ?? 0 }} dari {{ number_format($shipments->total()) }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3">No Resi</th>
                            <th class="px-6 py-3">No Manifest</th>
                            <th class="px-6 py-3">NPSN</th>
                            <th class="px-6 py-3">Sekolah</th>
                            <th class="px-6 py-3">Provinsi / Kota</th>
                            <th class="px-6 py-3">Tanggal HO</th>
                            <th class="px-6 py-3">Vendor LM</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($shipments as $shipment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-mono font-semibold text-gray-900">{{ $shipment->waybill_no }}</td>
                                <td class="px-6 py-3 font-mono">{{ $shipment->manifest_no ?? '-' }}</td>
                                <td class="px-6 py-3 font-mono">{{ $shipment->npsn ?? '-' }}</td>
                                <td class="px-6 py-3 max-w-[220px] truncate" title="{{ $shipment->school_name }}">{{ $shipment->school_name ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    {{ $shipment->province ?? '-' }}
                                    @if($shipment->city_regency)
                                        <span class="text-gray-400">/ {{ $shipment->city_regency }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-xs">{{ $shipment->ho_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-6 py-3">{{ $shipment->vendor_lm ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    @if($shipment->final_status === 'Completed')
                                        <span class="px-2 py-0.5 text-[11px] font-bold text-emerald-700 bg-emerald-100 rounded-full">✓ Completed</span>
                                    @elseif($shipment->final_status === 'On Delivery')
                                        <span class="px-2 py-0.5 text-[11px] font-bold text-blue-700 bg-blue-100 rounded-full">🚚 On Delivery</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-bold text-rose-700 bg-rose-100 rounded-full">⚠️ Undelivered</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($shipment->is_within_sla)
                                        <span class="px-2 py-0.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 rounded-full">On Time</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-bold text-amber-700 bg-amber-50 rounded-full">Over SLA</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                                    Belum ada data pengiriman. Silakan impor file Excel terlebih dahulu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $shipments->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
