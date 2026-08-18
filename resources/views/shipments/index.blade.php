<x-app-layout>
    <x-slot name="header">
        Data Pengiriman
    </x-slot>

    <div class="space-y-6">

        <!-- Filter & Pencarian -->
        <div class="card p-5" x-reveal>
            <form method="GET" action="{{ route('shipments.index') }}" x-loading class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">

                <div class="lg:col-span-2">
                    <label class="field-label">Pencarian</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="No Resi, No Manifest, NPSN, Nama Sekolah..."
                            class="field-input !pl-9">
                    </div>
                </div>

                <div>
                    <label class="field-label">Provinsi</label>
                    <select name="province" class="field-input" onchange="this.form.submit()">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label">Kabupaten/Kota</label>
                    <select name="city_regency" class="field-input">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city_regency') == $city ? 'selected' : '' }}>{{ $city }}</option>
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

                <div>
                    <label class="field-label">Kepatuhan SLA</label>
                    <select name="sla" class="field-input">
                        <option value="">Semua</option>
                        <option value="within" {{ request('sla') == 'within' ? 'selected' : '' }}>Within SLA</option>
                        <option value="over" {{ request('sla') == 'over' ? 'selected' : '' }}>Over SLA</option>
                    </select>
                </div>

                <div class="lg:col-span-7 flex items-end justify-end gap-2">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'start_date', 'end_date', 'province', 'city_regency', 'vendor_id', 'status', 'sla']))
                        <a href="{{ route('shipments.index') }}" class="btn-ghost">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>

        <!-- Tabel Data -->
        <div class="card overflow-hidden" x-reveal>
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip !w-8 !h-8 bg-dbl-green-light/60 text-dbl-green-dark">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    <h3 class="text-base font-bold text-gray-800">Daftar Pengiriman</h3>
                </div>
                <span class="text-xs text-gray-400 bg-white border border-gray-200 rounded-full px-3 py-1">Menampilkan {{ $shipments->firstItem() ?? 0 }}–{{ $shipments->lastItem() ?? 0 }} dari {{ number_format($shipments->total()) }} data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 font-bold">No Resi</th>
                            <th class="px-6 py-3 font-bold">No Manifest</th>
                            <th class="px-6 py-3 font-bold">NPSN</th>
                            <th class="px-6 py-3 font-bold">Sekolah</th>
                            <th class="px-6 py-3 font-bold">Provinsi / Kota</th>
                            <th class="px-6 py-3 font-bold">Tanggal HO</th>
                            <th class="px-6 py-3 font-bold">Vendor LM</th>
                            <th class="px-6 py-3 font-bold">Status</th>
                            <th class="px-6 py-3 font-bold">SLA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($shipments as $shipment)
                            <tr class="hover:bg-dbl-green-light/20 transition-colors">
                                <td class="px-6 py-3.5 font-mono font-bold text-gray-900">{{ $shipment->waybill_no }}</td>
                                <td class="px-6 py-3.5 font-mono text-xs text-gray-500">{{ $shipment->manifest_no ?? '-' }}</td>
                                <td class="px-6 py-3.5 font-mono text-xs">{{ $shipment->npsn ?? '-' }}</td>
                                <td class="px-6 py-3.5 max-w-[220px] truncate" title="{{ $shipment->school_name }}">{{ $shipment->school_name ?? '-' }}</td>
                                <td class="px-6 py-3.5">
                                    {{ $shipment->province ?? '-' }}
                                    @if($shipment->city_regency)
                                        <span class="text-gray-400">/ {{ $shipment->city_regency }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 text-xs">{{ $shipment->ho_date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-6 py-3.5">{{ $shipment->vendor_lm ?? '-' }}</td>
                                <td class="px-6 py-3.5">
                                    @if($shipment->final_status === 'Completed')
                                        <span class="badge bg-emerald-50 text-emerald-700"><span class="dot bg-emerald-500"></span>Completed</span>
                                    @elseif($shipment->final_status === 'On Delivery')
                                        <span class="badge bg-blue-50 text-blue-700"><span class="dot bg-blue-500"></span>On Delivery</span>
                                    @else
                                        <span class="badge bg-rose-50 text-rose-700"><span class="dot bg-rose-500"></span>Undelivered</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5">
                                    @if($shipment->is_within_sla)
                                        <span class="badge bg-emerald-50 text-emerald-600"><span class="dot bg-emerald-500"></span>On Time</span>
                                    @else
                                        <span class="badge bg-amber-50 text-amber-700"><span class="dot bg-amber-500"></span>Over SLA</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="icon-chip bg-gray-100 text-gray-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500">Belum ada data pengiriman</p>
                                        <p class="text-xs text-gray-400 mt-1">Silakan impor file Excel terlebih dahulu pada menu Import Data Excel.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $shipments->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
