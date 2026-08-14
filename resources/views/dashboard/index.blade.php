<x-app-layout>
    <x-slot name="header">
        Dashboard Analytics Operasional Pengiriman
    </x-slot>

    <div class="space-y-6">

        <!-- ==================== SECTION 1: FILTER MULTI-KRITERIA (FR-09) ==================== -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4">

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
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Vendor Last Mile</label>
                    <select name="vendor_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Status Akhir</label>
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

                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-8 flex items-end justify-end space-x-2">
                    <button type="submit" class="px-4 py-2 bg-dbl-dark text-white rounded-lg text-sm font-semibold hover:bg-black transition-colors flex items-center">
                        Filter
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date', 'province', 'city_regency', 'vendor_id', 'status', 'sla']))
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-sm font-semibold">
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>


        <!-- ==================== BANNER KESEGARAN DATA ==================== -->
        <div class="flex flex-wrap items-center gap-3 bg-gradient-to-r from-dbl-dark to-gray-800 text-white px-5 py-3 rounded-2xl shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider">Kesegaran Data:</span>
            @if($latestImport)
                <span class="text-sm font-medium">
                    Import terakhir <span class="text-dbl-green-light font-bold">{{ $latestImport->file_name }}</span>
                    · {{ $latestImport->created_at->format('d M Y, H:i') }} · {{ number_format($latestImport->total_rows) }} baris
                </span>
            @else
                <span class="text-sm font-medium">Belum ada import data</span>
            @endif
            <span class="hidden md:inline text-white/30">|</span>
            <span class="text-xs text-white/70">
                Cakupan: {{ $kpis['totalShipments'] }} resi
                @if($prevKpis)
                    (vs {{ number_format($prevKpis['total']) }} resi pada periode sebelumnya)
                @endif
            </span>
        </div>


        <!-- ==================== SECTION 2: KPI CARDS (7 KPI Utama) ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4">

            @php
                $badge = fn ($value, $invert = false) => $value === null
                    ? ''
                    : '<span class="inline-flex items-center text-[11px] font-bold px-2 py-0.5 rounded-full mt-1 '.($value >= 0 xor $invert ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50').'">'.($value >= 0 xor $invert ? '▲' : '▼').' '.abs($value).'%</span>';
            @endphp

            <!-- Card 1: Total Pengiriman -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Resi</span>
                    <span class="p-2 bg-gray-100 rounded-lg text-gray-700">📦</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-gray-900">{{ number_format($kpis['totalShipments']) }}</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Total shipment terdaftar</p>
                    {!! $badge($deltas['total'] ?? null) !!}
                </div>
            </div>

            <!-- Card 2: Completed -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Completed</span>
                    <span class="p-2 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs">✓</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-emerald-600">{{ number_format($kpis['completed']) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1">
                        {{ $kpis['totalShipments'] > 0 ? round(($kpis['completed'] / $kpis['totalShipments']) * 100, 1) : 0 }}% dari total
                    </span>
                    {!! $badge($deltas['completed'] ?? null) !!}
                </div>
            </div>

            <!-- Card 3: On Delivery -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">On Delivery</span>
                    <span class="p-2 bg-blue-100 text-blue-800 rounded-lg font-bold text-xs">🚚</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-blue-600">{{ number_format($kpis['onDelivery']) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full mt-1">
                        Dalam proses pengiriman
                    </span>
                    {!! $badge($deltas['onDelivery'] ?? null) !!}
                </div>
            </div>

            <!-- Card 4: Undelivered -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Undelivered</span>
                    <span class="p-2 bg-rose-100 text-rose-800 rounded-lg font-bold text-xs">⚠️</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-rose-600">{{ number_format($kpis['undelivered']) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full mt-1">
                        Kendala operasional
                    </span>
                    {!! $badge($deltas['undelivered'] ?? null, true) !!}
                </div>
            </div>

            <!-- Card 5: Within SLA -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Within SLA</span>
                    <span class="p-2 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs">🎯</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-emerald-600">{{ number_format($kpis['withinSla']) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1">
                        Tepat waktu
                    </span>
                    {!! $badge($deltas['withinSla'] ?? null) !!}
                </div>
            </div>

            <!-- Card 6: Over SLA -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Over SLA</span>
                    <span class="p-2 bg-amber-100 text-amber-800 rounded-lg font-bold text-xs">⏰</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-amber-600">{{ number_format($kpis['overSla']) }}</h3>
                    <p class="text-[11px] text-amber-700 font-medium mt-0.5">Melewati batas SLA</p>
                    {!! $badge($deltas['overSla'] ?? null, true) !!}
                </div>
            </div>

            <!-- Card 7: SLA Achievement Rate -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-dbl-green-light/30">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">SLA Rate</span>
                    <span class="p-2 bg-dbl-green text-dbl-dark font-extrabold rounded-lg text-xs">📊</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black {{ $kpis['slaAchievementRate'] >= 95 ? 'text-emerald-600' : ($kpis['slaAchievementRate'] >= 85 ? 'text-amber-600' : 'text-rose-600') }}">
                        {{ $kpis['slaAchievementRate'] }}%
                    </h3>
                    <p class="text-[11px] text-gray-600 font-medium mt-0.5">Kepatuhan SLA overall</p>
                    {!! $badge($deltas['slaRate'] ?? null) !!}
                </div>
            </div>

        </div>


        <!-- ==================== SECTION 3: VISUALISASI GRAFIK ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart 1: Komposisi Status Pengiriman -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Komposisi Status Pengiriman</span>
                    <span class="text-xs text-gray-400 font-normal">Real-time Status</span>
                </h4>
                <div class="h-64 flex justify-center items-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Performa SLA (Within vs Over) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Performa Kepatuhan SLA</span>
                    <span class="text-xs text-gray-400 font-normal">Within vs Over SLA</span>
                </h4>
                <div class="h-64 flex justify-center items-center">
                    <canvas id="slaChart"></canvas>
                </div>
            </div>

            <!-- Chart 3: Tren Pengiriman per Hari (+ SLA Rate) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Tren Pengiriman per Hari</span>
                    <span class="text-xs text-gray-400 font-normal">Volume &amp; SLA Rate harian</span>
                </h4>
                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Chart 6: Tren Pengiriman per Bulan -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Tren Pengiriman per Bulan</span>
                    <span class="text-xs text-gray-400 font-normal">Agregasi bulanan</span>
                </h4>
                <div class="h-64">
                    <canvas id="trendMonthlyChart"></canvas>
                </div>
            </div>

            <!-- Chart 4: Top 5 Volume per Provinsi -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4">
                    Distribusi Pengiriman per Provinsi (Top 5)
                </h4>
                <div class="h-64">
                    <canvas id="provinceChart"></canvas>
                </div>
            </div>

            <!-- Chart 5: Performa Vendor Last Mile -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4">
                    Top 5 Vendor Last Mile (Volume & Kepatuhan)
                </h4>
                <div class="h-64">
                    <canvas id="vendorChart"></canvas>
                </div>
            </div>

        </div>


        <!-- ==================== SECTION 4: PANEL ANALISIS ==================== -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Funnel Kepatuhan SLA per Tahap -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-base font-bold text-gray-800">Kepatuhan SLA per Tahap</h4>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-1">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        <input type="hidden" name="province" value="{{ request('province') }}">
                        <input type="hidden" name="city_regency" value="{{ request('city_regency') }}">
                        <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}">
                        <input type="hidden" name="sla" value="{{ request('sla') }}">
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green py-1">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <p class="text-xs text-gray-400 mb-4">Tingkat kepatuhan dihitung query-time dari kolom result_* sesuai filter saat ini</p>

                @php
                    $target = 95.0;
                    $stageGoalReached = collect($slaStageBreakdown)->filter(fn ($s) => $s['total'] > 0)->count() > 0
                        ? collect($slaStageBreakdown)->filter(fn ($s) => $s['total'] > 0)->every(fn ($s) => $s['rate'] >= $target)
                        : null;
                @endphp

                @foreach($slaStageBreakdown as $stage)
                    @php
                        $barColor = $stage['rate'] >= $target ? 'bg-emerald-500' : ($stage['rate'] >= 85 ? 'bg-amber-500' : 'bg-rose-500');
                        $textColor = $stage['rate'] >= $target ? 'text-emerald-600' : ($stage['rate'] >= 85 ? 'text-amber-600' : 'text-rose-600');
                    @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-semibold text-gray-700">{{ $stage['label'] }}</span>
                            <span class="text-xs text-gray-400">{{ $stage['within'] }}/{{ $stage['total'] }} resi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $stage['rate'] }}%"></div>
                            </div>
                            <span class="text-sm font-bold {{ $textColor }} w-14 text-right">{{ $stage['rate'] }}%</span>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-sm">
                    <span class="text-gray-500 font-medium">
                        Target SLA:
                        <span class="inline-flex items-center gap-1 {{ $stageGoalReached === false ? 'text-rose-600' : 'text-emerald-600' }} font-bold">
                            {{ $stageGoalReached === null ? '—' : ($stageGoalReached ? '✓ tercapai' : '✗ belum tercapai') }}
                        </span>
                    </span>
                    <span class="text-xs text-gray-400">≥ {{ $target }}% per tahap</span>
                </div>
            </div>

            <!-- Rata-rata Lead Time -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4">Rata-rata Lead Time (hari)</h4>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <div class="text-2xl font-black text-gray-800">{{ $leadTimes['ho_to_pickup'] }}</div>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">HO → Pickup</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Pengambilan barang</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <div class="text-2xl font-black text-gray-800">{{ $leadTimes['pickup_to_delivery'] }}</div>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">Pickup → Delivery</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Proses antar</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <div class="text-2xl font-black text-dbl-green-dark">{{ $leadTimes['ho_to_delivery'] }}</div>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">HO → Delivery</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">End-to-end</div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">
                    Dihitung dari selisih tanggal antar tahapan (hari) pada resi yang memiliki data tahap lengkap.
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Vendor dengan Over-SLA Tertinggi -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-1 flex items-center justify-between">
                    <span>Vendor dengan Over-SLA Tertinggi</span>
                    <span class="text-xs text-gray-400 font-normal">min. 10 resi</span>
                </h4>
                <p class="text-xs text-gray-400 mb-4">Prioritas perbaikan kualitas vendor last-mile</p>

                @if($worstVendors->isEmpty())
                    <p class="text-sm text-gray-400 py-6 text-center">Tidak ada data vendor pada filter ini</p>
                @else
                    <div class="space-y-4">
                        @foreach($worstVendors as $index => $vendor)
                            @php
                                $base = request()->except(['vendor_id', 'page']);
                                $link = route('shipments.index', array_merge($base, ['vendor_id' => $vendor->vendor_id]));
                                $barColor = $vendor->rate >= 40 ? 'bg-rose-500' : ($vendor->rate >= 20 ? 'bg-amber-500' : 'bg-emerald-500');
                            @endphp
                            <a href="{{ $link }}" class="block group">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-semibold text-gray-700 group-hover:text-dbl-green-dark transition-colors">
                                        {{ $index + 1 }}. {{ $vendor->vendor_lm }}
                                    </span>
                                    <span class="text-xs font-bold text-rose-600">{{ $vendor->rate }}% over</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full" style="width: {{ $vendor->rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 w-16 text-right">{{ $vendor->over_sla }} / {{ $vendor->total }} resi</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Provinsi & Kota dengan Undelivered Terbanyak -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4">Undelivered per Wilayah (Top 5)</h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Provinsi</p>
                        @if($worstRegions['provinces']->isEmpty())
                            <p class="text-xs text-gray-400 py-3">Tidak ada data</p>
                        @else
                            <div class="space-y-2">
                                @foreach($worstRegions['provinces'] as $row)
                                    @php
                                        $base = request()->except(['province', 'page']);
                                        $link = route('shipments.index', array_merge($base, ['province' => $row->province]));
                                    @endphp
                                    <a href="{{ $link }}" class="flex items-center justify-between text-sm bg-gray-50 hover:bg-gray-100 rounded-lg px-3 py-2 transition-colors">
                                        <span class="font-medium text-gray-700 truncate">{{ $row->province }}</span>
                                        <span class="text-xs font-bold text-rose-600 shrink-0">{{ $row->undelivered }} resi</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kabupaten/Kota</p>
                        @if($worstRegions['cities']->isEmpty())
                            <p class="text-xs text-gray-400 py-3">Tidak ada data</p>
                        @else
                            <div class="space-y-2">
                                @foreach($worstRegions['cities'] as $row)
                                    @php
                                        $base = request()->except(['city_regency', 'page']);
                                        $link = route('shipments.index', array_merge($base, ['city_regency' => $row->city_regency]));
                                    @endphp
                                    <a href="{{ $link }}" class="flex items-center justify-between text-sm bg-gray-50 hover:bg-gray-100 rounded-lg px-3 py-2 transition-colors">
                                        <span class="font-medium text-gray-700 truncate">{{ $row->city_regency }}</span>
                                        <span class="text-xs font-bold text-rose-600 shrink-0">{{ $row->undelivered }} resi</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Open Issues -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-1 flex items-center justify-between">
                    <span>Issue Terbuka</span>
                    <span class="text-xs bg-rose-50 text-rose-700 font-bold px-2 py-0.5 rounded-full">{{ $issuesTotal }} open</span>
                </h4>
                <p class="text-xs text-gray-400 mb-4">Issue aktif pada scope filter saat ini</p>

                @if($openIssues->isEmpty())
                    <p class="text-sm text-gray-400 py-6 text-center">Tidak ada issue terbuka 🎉</p>
                @else
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                        @foreach($openIssues as $issue)
                            <div class="py-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-gray-800">{{ $issue->waybill_no }}</span>
                                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full shrink-0">{{ $issue->issue_type }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $issue->description }}</p>
                                <p class="text-[11px] text-gray-400 mt-1">
                                    {{ $issue->province }}{{ $issue->city_regency ? ' · '.$issue->city_regency : '' }}
                                    · {{ $issue->reported_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Dispatch Terbaru (DR-05) -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-800">Dispatch Terbaru</h4>
                    <a href="{{ route('shipments.index', request()->query()) }}"
                       class="text-xs font-semibold text-dbl-green-dark hover:underline">
                        Lihat Semua →
                    </a>
                </div>

                @php
                    $statusColor = [
                        'Completed' => 'bg-emerald-100 text-emerald-700',
                        'On Delivery' => 'bg-blue-100 text-blue-700',
                        'Undelivered' => 'bg-rose-100 text-rose-700',
                    ];
                @endphp

                @if($recentShipments->isEmpty())
                    <p class="text-sm text-gray-400 py-6 text-center">Belum ada pengiriman pada filter ini</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-[11px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                    <th class="py-2 pr-2 font-semibold">Resi</th>
                                    <th class="py-2 pr-2 font-semibold">Tanggal HO</th>
                                    <th class="py-2 pr-2 font-semibold">Wilayah</th>
                                    <th class="py-2 pr-2 font-semibold">Vendor</th>
                                    <th class="py-2 pr-2 font-semibold">Status</th>
                                    <th class="py-2 font-semibold">SLA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentShipments as $shipment)
                                    <tr>
                                        <td class="py-2.5 pr-2 font-mono text-xs text-gray-700">{{ $shipment->waybill_no }}</td>
                                        <td class="py-2.5 pr-2 text-xs text-gray-500">{{ $shipment->ho_date?->format('d M Y') }}</td>
                                        <td class="py-2.5 pr-2 text-xs text-gray-500">{{ $shipment->city_regency ?? $shipment->province }}</td>
                                        <td class="py-2.5 pr-2 text-xs text-gray-500">{{ $shipment->vendor_lm ?? '—' }}</td>
                                        <td class="py-2.5 pr-2">
                                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $statusColor[$shipment->final_status] ?? 'bg-gray-100 text-gray-600' }}">
                                                {{ $shipment->final_status ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5">
                                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $shipment->is_within_sla ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                {{ $shipment->is_within_sla ? 'Within' : 'Over' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

    </div>

    <!-- ==================== SCRIPT INITIALIZATION CHART.JS ==================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 1. Donut Chart - Status Pengiriman
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'On Delivery', 'Undelivered'],
                    datasets: [{
                        data: [
                            {{ $statusChart['Completed'] }},
                            {{ $statusChart['On Delivery'] }},
                            {{ $statusChart['Undelivered'] }}
                        ],
                        backgroundColor: ['#10B981', '#3B82F6', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // 2. Doughnut Chart - Performa SLA
            new Chart(document.getElementById('slaChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Within SLA (On Time)', 'Over SLA (Late)'],
                    datasets: [{
                        data: [
                            {{ $slaChart['Within SLA'] }},
                            {{ $slaChart['Over SLA'] }}
                        ],
                        backgroundColor: ['#059669', '#F59E0B'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // 3. Line Chart - Tren Pengiriman per Hari (+ SLA Rate)
            const trendLabels = {!! json_encode($trend->pluck('date')->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d M'))) !!};
            const trendTotals = {!! json_encode($trend->pluck('total')) !!};
            const trendTotalsArr = Array.isArray(trendTotals) ? trendTotals : Object.values(trendTotals);
            const trendWithin = {!! json_encode($trend->pluck('within_sla')) !!};
            const trendWithinArr = Array.isArray(trendWithin) ? trendWithin : Object.values(trendWithin);

            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [
                        {
                            label: 'Jumlah Pengiriman',
                            data: trendTotalsArr,
                            borderColor: '#059669',
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3,
                            yAxisID: 'y'
                        },
                        {
                            label: 'SLA Rate (%)',
                            data: trendTotalsArr.map((t, i) => t > 0 ? Math.round((trendWithinArr[i] / t) * 1000) / 10 : 0),
                            borderColor: '#111827',
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            tension: 0.3,
                            pointRadius: 2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        y1: { beginAtZero: true, max: 100, position: 'right', ticks: { callback: v => v + '%' }, grid: { drawOnChartArea: false } }
                    }
                }
            });

            // 6. Bar Chart - Tren Pengiriman per Bulan
            new Chart(document.getElementById('trendMonthlyChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($trendMonthly->pluck('month')->map(fn ($m) => \Illuminate\Support\Carbon::createFromFormat('Y-m', $m)->format('M Y'))) !!},
                    datasets: [{
                        label: 'Total Pengiriman',
                        data: {!! json_encode($trendMonthly->pluck('total')) !!},
                        backgroundColor: 'rgba(17, 24, 39, 0.75)',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // 4. Bar Chart - Top 5 Provinsi
            new Chart(document.getElementById('provinceChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($provinceData->pluck('province')) !!},
                    datasets: [{
                        label: 'Total Pengiriman',
                        data: {!! json_encode($provinceData->pluck('total')) !!},
                        backgroundColor: '#111827',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // 5. Grouped Bar Chart - Top 5 Vendor
            new Chart(document.getElementById('vendorChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($vendorData->pluck('vendor_lm')) !!},
                    datasets: [
                        {
                            label: 'Total Volume',
                            data: {!! json_encode($vendorData->pluck('total')) !!},
                            backgroundColor: '#374151',
                            borderRadius: 4
                        },
                        {
                            label: 'Within SLA',
                            data: {!! json_encode($vendorData->pluck('on_time')) !!},
                            backgroundColor: '#10B981',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

        });
    </script>
</x-app-layout>
