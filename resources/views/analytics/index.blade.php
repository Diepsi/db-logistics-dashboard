<x-app-layout>
    <x-slot name="header">
        Analitik Operasional Lanjutan
    </x-slot>

    <div class="space-y-6">

        <!-- ==================== FILTER ==================== -->
        <div class="card p-5" x-reveal>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark dark:bg-dbl-green/10 dark:text-dbl-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Filter Analitik</h3>
                        <p class="text-[11px] text-gray-400">Analisis historis mendalam sesuai cakupan data</p>
                    </div>
                </div>
                @if(request()->hasAny(['start_date', 'end_date', 'province', 'city_regency', 'vendor_id', 'status', 'sla']))
                    <a href="{{ route('analytics.index') }}" class="btn-ghost !px-3 !py-1.5 !text-xs">Reset Filter</a>
                @endif
            </div>

            <form method="GET" action="{{ route('analytics.index') }}" x-loading class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
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
                    <label class="field-label">Vendor Last Mile</label>
                    <select name="vendor_id" class="field-input">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label">Status Akhir</label>
                    <select name="status" class="field-input">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">Terapkan Filter</button>
                </div>
            </form>
        </div>

        <!-- ==================== RINGKASAN KPI ==================== -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-5" x-reveal>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Pengiriman</p>
                <p class="text-2xl font-black text-gray-800 dark:text-gray-100 tabular-nums mt-1">{{ number_format($kpis['totalShipments']) }}</p>
            </div>
            <div class="card p-5" x-reveal x-reveal.delay>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Kepatuhan SLA</p>
                <p class="text-2xl font-black tabular-nums mt-1 {{ $kpis['slaAchievementRate'] >= 95 ? 'text-emerald-600' : ($kpis['slaAchievementRate'] >= 85 ? 'text-amber-600' : 'text-rose-600') }}">{{ $kpis['slaAchievementRate'] }}%</p>
            </div>
            <div class="card p-5" x-reveal>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Completed</p>
                <p class="text-2xl font-black text-gray-800 dark:text-gray-100 tabular-nums mt-1">{{ number_format($kpis['completed']) }}</p>
            </div>
            <div class="card p-5" x-reveal x-reveal.delay>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Undelivered</p>
                <p class="text-2xl font-black tabular-nums mt-1 {{ $kpis['undelivered'] > 0 ? 'text-rose-600' : 'text-gray-800 dark:text-gray-100' }}">{{ number_format($kpis['undelivered']) }}</p>
            </div>
        </div>

        <!-- ==================== GRAFIK UTAMA (01–07) ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 01 Status Pengiriman -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">01</span>
                        Status Pengiriman
                    </span>
                    <span class="text-xs text-gray-400 font-normal">Distribusi status akhir</span>
                </h4>
                <div class="h-56">
                    <canvas id="statusAkhirChart"></canvas>
                </div>
            </div>

            <!-- 02 SLA Compliance Rate -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">02</span>
                        SLA Compliance Rate
                    </span>
                    <span class="text-xs text-gray-400 font-normal">Target ≥ 95%</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="slaComplianceChart"></canvas>
                </div>
            </div>

            <!-- 03 SLA Middle Mile vs Last Mile -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">03</span>
                        SLA Middle Mile vs Last Mile
                    </span>
                    <span class="text-xs text-gray-400 font-normal">Per Vendor (Top 10)</span>
                </h4>
                <div class="h-72">
                    <canvas id="slaMmVsLmChart"></canvas>
                </div>
            </div>

            <!-- 04 Top 5 Alokasi Provinsi -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">04</span>
                        Top 5 Alokasi Provinsi
                    </span>
                    <span class="text-xs text-gray-400 font-normal">Volume pengiriman</span>
                </h4>
                <div class="h-64">
                    <canvas id="provinceChart"></canvas>
                </div>
            </div>

            <!-- 05 Top Vendor Load -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">05</span>
                        Top Vendor Load
                    </span>
                    <span class="text-xs text-gray-400 font-normal">Volume & Kepatuhan (Top 5)</span>
                </h4>
                <div class="h-64">
                    <canvas id="vendorChart"></canvas>
                </div>
            </div>

            <!-- 06 Status BAST Balik -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">06</span>
                        Status BAST Balik
                    </span>
                    <span class="text-xs text-gray-400 font-normal">{{ number_format($bastFinance['bastTotal']) }} resi</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="bastChart"></canvas>
                </div>
            </div>

            <!-- 07 BAST Handover Finance -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">07</span>
                        BAST Handover Finance
                    </span>
                    <span class="text-xs text-gray-400 font-normal">{{ number_format($bastFinance['financeTotal']) }} resi</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>

        </div>

        <!-- ==================== 08 SLA FUNNEL & AVERAGE LEAD TIME ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 08a Funnel Kepatuhan SLA per Tahap -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-1 flex items-center gap-2">
                    <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">08</span>
                    SLA Funnel — Kepatuhan per Tahap
                </h4>
                <p class="text-xs text-gray-400 mb-4">Dihitung query-time dari kolom result_* sesuai filter saat ini</p>

                @php
                    $target = 95.0;
                    $stagesWithData = collect($slaStageBreakdown)->filter(fn ($s) => $s['total'] > 0);
                    $stageGoalReached = $stagesWithData->count() > 0
                        ? $stagesWithData->every(fn ($s) => $s['rate'] >= $target)
                        : null;
                @endphp

                @foreach($slaStageBreakdown as $stage)
                    @php
                        $barColor = $stage['rate'] >= $target ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : ($stage['rate'] >= 85 ? 'bg-gradient-to-r from-amber-500 to-amber-400' : 'bg-gradient-to-r from-rose-500 to-rose-400');
                        $textColor = $stage['rate'] >= $target ? 'text-emerald-600' : ($stage['rate'] >= 85 ? 'text-amber-600' : 'text-rose-600');
                    @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $stage['label'] }}</span>
                            <span class="text-xs text-gray-400">{{ $stage['within'] }}/{{ $stage['total'] }} resi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2.5 bg-gray-100 dark:bg-gray-700/60 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColor }} rounded-full bar-grow" style="width: {{ $stage['rate'] }}%"></div>
                            </div>
                            <span class="text-sm font-bold {{ $textColor }} w-14 text-right tabular-nums">{{ $stage['rate'] }}%</span>
                        </div>
                    </div>
                @endforeach

                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">
                        Target SLA:
                        <span class="inline-flex items-center gap-1 {{ $stageGoalReached === false ? 'text-rose-600' : 'text-emerald-600' }} font-bold">
                            {{ $stageGoalReached === null ? '—' : ($stageGoalReached ? '✓ tercapai' : '✗ belum tercapai') }}
                        </span>
                    </span>
                    <span class="text-xs text-gray-400">≥ {{ $target }}% per tahap</span>
                </div>
            </div>

            <!-- 08b Rata-rata Lead Time -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">08</span>
                    Average Lead Time (hari)
                </h4>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 rounded-xl p-4 text-center border border-gray-100 dark:border-gray-700/60 transition-all duration-300 hover:border-dbl-green/30 hover:shadow-sm">
                        <div class="text-2xl font-black text-gray-800 dark:text-gray-100 tabular-nums">{{ $leadTimes['ho_to_pickup'] }}</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold mt-1">HO → Pickup</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Pengambilan barang</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 rounded-xl p-4 text-center border border-gray-100 dark:border-gray-700/60 transition-all duration-300 hover:border-dbl-green/30 hover:shadow-sm">
                        <div class="text-2xl font-black text-gray-800 dark:text-gray-100 tabular-nums">{{ $leadTimes['pickup_to_delivery'] }}</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold mt-1">Pickup → Delivery</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Proses antar</div>
                    </div>
                    <div class="bg-gradient-to-br from-dbl-green-light/60 to-white dark:from-dbl-green/10 dark:to-transparent rounded-xl p-4 text-center border border-dbl-green/20 transition-all duration-300 hover:shadow-md">
                        <div class="text-2xl font-black text-dbl-green-dark dark:text-dbl-green tabular-nums">{{ $leadTimes['ho_to_delivery'] }}</div>
                        <div class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold mt-1">HO → Delivery</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">End-to-end</div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">Dihitung dari selisih tanggal antar tahapan (hari) pada resi yang memiliki data tahap lengkap.</p>
            </div>

        </div>

        <!-- ==================== 09 BOTTLENECK: WORST SLA & PENDING REGION ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- 09a Vendor dengan Over-SLA Tertinggi -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-1 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">09</span>
                        Bottleneck: Worst SLA
                    </span>
                    <span class="text-xs bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300 font-bold px-2 py-0.5 rounded-full">min. 10 resi</span>
                </h4>
                <p class="text-xs text-gray-400 mb-4">Prioritas perbaikan kualitas vendor last-mile</p>

                @if($worstVendors->isEmpty())
                    <p class="text-sm text-gray-400 py-6 text-center">Tidak ada data vendor pada filter ini</p>
                @else
                    <div class="space-y-4">
                        @foreach($worstVendors as $index => $vendor)
                            @php
                                $base = request()->except(['vendor_id', 'page']);
                                $link = $vendor->vendor_id
                                    ? route('shipments.index', array_merge($base, ['vendor_id' => $vendor->vendor_id]))
                                    : route('shipments.index', $base);
                                $barColor = $vendor->rate >= 40 ? 'bg-gradient-to-r from-rose-500 to-rose-400' : ($vendor->rate >= 20 ? 'bg-gradient-to-r from-amber-500 to-amber-400' : 'bg-gradient-to-r from-emerald-500 to-emerald-400');
                            @endphp
                            <a href="{{ $link }}" class="block group">
                                <div class="flex items-center justify-between text-sm mb-1.5">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300 group-hover:text-dbl-green-dark dark:group-hover:text-dbl-green transition-colors">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-gray-100 dark:bg-gray-700/60 text-gray-500 dark:text-gray-400 text-[10px] font-bold mr-1.5 align-middle">{{ $index + 1 }}</span>
                                        {{ $vendor->vendor_lm }}
                                    </span>
                                    <span class="text-xs font-bold text-rose-600">{{ $vendor->rate }}% over</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700/60 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full bar-grow" style="width: {{ $vendor->rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 w-16 text-right tabular-nums">{{ $vendor->over_sla }} / {{ $vendor->total }} resi</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- 09b Provinsi & Kota dengan Undelivered Terbanyak -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">09</span>
                    Bottleneck: Pending Region (Top 5)
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="field-label mb-2">Provinsi</p>
                        @if($worstRegions['provinces']->isEmpty())
                            <p class="text-xs text-gray-400 py-3">Tidak ada data</p>
                        @else
                            <div class="space-y-2">
                                @foreach($worstRegions['provinces'] as $row)
                                    @php
                                        $base = request()->except(['province', 'page']);
                                        $link = route('shipments.index', array_merge($base, ['province' => $row->province]));
                                    @endphp
                                    <a href="{{ $link }}" class="flex items-center justify-between text-sm bg-gray-50 dark:bg-gray-800/60 hover:bg-dbl-green-light/40 dark:hover:bg-dbl-green/10 hover:border-dbl-green/20 border border-transparent rounded-lg px-3 py-2 transition-all">
                                        <span class="font-medium text-gray-700 dark:text-gray-300 truncate">{{ $row->province }}</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 shrink-0">
                                            <span class="dot bg-rose-500"></span>{{ $row->undelivered }} resi
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="field-label mb-2">Kabupaten/Kota</p>
                        @if($worstRegions['cities']->isEmpty())
                            <p class="text-xs text-gray-400 py-3">Tidak ada data</p>
                        @else
                            <div class="space-y-2">
                                @foreach($worstRegions['cities'] as $row)
                                    @php
                                        $base = request()->except(['city_regency', 'page']);
                                        $link = route('shipments.index', array_merge($base, ['city_regency' => $row->city_regency]));
                                    @endphp
                                    <a href="{{ $link }}" class="flex items-center justify-between text-sm bg-gray-50 dark:bg-gray-800/60 hover:bg-dbl-green-light/40 dark:hover:bg-dbl-green/10 hover:border-dbl-green/20 border border-transparent rounded-lg px-3 py-2 transition-all">
                                        <span class="font-medium text-gray-700 dark:text-gray-300 truncate">{{ $row->city_regency }}</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 shrink-0">
                                            <span class="dot bg-rose-500"></span>{{ $row->undelivered }} resi
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- ==================== 10 LIVE ALERTS & OPEN ISSUES ==================== -->
        <div class="card p-5" x-reveal>
            <div class="flex items-center justify-between mb-1">
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <span class="text-[10px] font-black tracking-wider text-dbl-green-dark bg-dbl-green-light/60 dark:text-dbl-green dark:bg-dbl-green/10 px-2 py-0.5 rounded-md">10</span>
                    Live Alerts & Open Issues
                </h4>
                <span class="badge bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:border-rose-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-500 opacity-60"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                    {{ count($needsAttention) }} prioritas
                </span>
            </div>
            <p class="text-xs text-gray-400 mb-4">Issue terbuka & pengiriman over SLA / undelivered pada scope filter</p>

            @if(empty($needsAttention))
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="icon-chip bg-emerald-50 text-emerald-500 dark:bg-emerald-500/10 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400 font-medium">Semua aman — tidak ada alert aktif saat ini</p>
                </div>
            @else
                <ul class="grid grid-cols-1 xl:grid-cols-2 gap-2.5">
                    @foreach($needsAttention as $item)
                        <li class="flex items-center gap-3 rounded-xl border border-gray-100 dark:border-gray-700/60 bg-gray-50/60 dark:bg-gray-800/40 hover:border-dbl-green/30 hover:bg-white dark:hover:bg-transparent transition-all duration-200 px-3.5 py-2.5">
                            <span class="icon-chip {{ $item['type'] === 'issue' ? 'bg-rose-50 text-rose-500 dark:bg-rose-500/10' : 'bg-amber-50 text-amber-500 dark:bg-amber-500/10' }} !w-8 !h-8 shrink-0">
                                @if($item['type'] === 'issue')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">{{ $item['waybill_no'] }}</p>
                                    <span class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-md {{ $item['type'] === 'issue' ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' }}">{{ $item['label'] }}</span>
                                </div>
                                <p class="text-[11px] text-gray-400 truncate">
                                    {{ $item['location'] ?: '—' }}@if($item['vendor_lm']) · {{ $item['vendor_lm'] }}@endif · {{ $item['status'] }}
                                </p>
                            </div>
                            <a href="{{ route('shipments.show', $item['shipment_id']) }}" class="btn-ghost !px-2.5 !py-1.5 !text-[11px] shrink-0">
                                Detail
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- ==================== ANALITIK TAMBAHAN ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span>Tren Pengiriman per Bulan</span>
                    <span class="text-xs text-gray-400 font-normal">Agregasi bulanan</span>
                </h4>
                <div class="h-64">
                    <canvas id="trendMonthlyChart"></canvas>
                </div>
            </div>

            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span>Performa Vendor Middle Mile</span>
                    <span class="text-xs text-gray-400 font-normal">Volume & SLA Rate</span>
                </h4>
                <div class="h-72">
                    <canvas id="vendorMmChart"></canvas>
                </div>
            </div>

            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 flex items-center justify-between">
                    <span>Status Inbound First Mile</span>
                    <span class="text-xs text-gray-400 font-normal">Dari FM ke Gudang</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="inboundFmChart"></canvas>
                </div>
            </div>

        </div>

        <!-- ==================== PETA DISTRIBUSI & HUB BOTTLENECK ==================== -->
        <div class="card p-5" x-reveal>
            <h4 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-1 flex items-center justify-between">
                <span>Peta Distribusi &amp; Hub Bottleneck</span>
                <span class="text-xs text-gray-400 font-normal">Volume · SLA Breach · Issue terbuka</span>
            </h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                Ukuran lingkaran = volume pengiriman. Warna merah = memiliki SLA breach atau issue terbuka.
                <span id="map-unmapped-hint" class="hidden font-semibold text-amber-600"></span>
            </p>

            <div class="relative rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 z-0">
                <div id="distribution-map" class="h-[420px] w-full bg-gray-100 dark:bg-gray-800"></div>
                <div id="map-loading" class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-900/60 text-sm font-semibold text-gray-500">
                    Memuat peta...
                </div>
            </div>
        </div>

    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            const map = L.map('distribution-map', { scrollWheelZoom: true }).setView([-2.5, 118.0], 5);

            const tileLayers = {
                light: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19,
                }),
                dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                    subdomains: 'abcd',
                    maxZoom: 19,
                }),
            };

            let currentTiles = document.documentElement.classList.contains('dark')
                ? tileLayers.dark
                : tileLayers.light;
            currentTiles.addTo(map);

            window.addEventListener('theme-changed', (event) => {
                map.removeLayer(currentTiles);
                currentTiles = event.detail.dark ? tileLayers.dark : tileLayers.light;
                currentTiles.addTo(map);
            });

            fetch("{{ route('analytics.map-data') }}", { headers: { Accept: 'application/json' } })
                .then((res) => res.json())
                .then((data) => {
                    document.getElementById('map-loading')?.remove();

                    if (!data.markers || data.markers.length === 0) {
                        document.getElementById('map-unmapped-hint')?.classList.remove('hidden');
                        return;
                    }

                    const maxVolume = Math.max(...data.markers.map((m) => m.total_shipments));

                    data.markers.forEach((marker) => {
                        const radius = 8 + 22 * Math.sqrt(marker.total_shipments / maxVolume);
                        const troubled = marker.sla_breach > 0 || marker.open_issues > 0;

                        L.circleMarker([marker.lat, marker.lng], {
                            radius,
                            color: troubled ? '#EF4444' : '#10B981',
                            weight: 2,
                            fillColor: troubled ? '#F87171' : '#34D399',
                            fillOpacity: 0.45,
                        })
                            .bindPopup(
                                `<strong>${marker.city_regency || marker.province}</strong><br>` +
                                `Provinsi: ${marker.province}<br>` +
                                `Total pengiriman: ${marker.total_shipments.toLocaleString('id-ID')}<br>` +
                                `SLA breach: ${marker.sla_breach.toLocaleString('id-ID')}<br>` +
                                `Issue terbuka: ${marker.open_issues.toLocaleString('id-ID')}`
                            )
                            .addTo(map);
                    });

                    if (data.unmapped_shipments > 0) {
                        const hint = document.getElementById('map-unmapped-hint');
                        if (hint) {
                            hint.textContent =
                                `${data.unmapped_shipments.toLocaleString('id-ID')} pengiriman di lokasi tanpa koordinat tidak ditampilkan.`;
                            hint.classList.remove('hidden');
                        }
                    }
                })
                .catch(() => document.getElementById('map-loading')?.remove());
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // 01 Bar - Status Pengiriman (Status Akhir Detail)
            const saLabels = {!! json_encode($statusAkhirDistribution->pluck('final_status')) !!};
            const saTotals = {!! json_encode($statusAkhirDistribution->pluck('total')) !!};
            const saColors = saLabels.map(s => {
                if (s === 'Completed') return '#10B981';
                if (s === 'On Delivery') return '#3B82F6';
                if (s === 'Undelivered') return '#EF4444';
                return '#6B7280';
            });
            if (saLabels.length > 0) {
                new Chart(document.getElementById('statusAkhirChart'), {
                    type: 'bar',
                    data: {
                        labels: saLabels,
                        datasets: [{
                            label: 'Jumlah Resi',
                            data: saTotals,
                            backgroundColor: saColors.map(c => c + 'CC'),
                            borderColor: saColors,
                            borderWidth: 1,
                            borderRadius: 6,
                            maxBarThickness: 42,
                            hoverBackgroundColor: saColors
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => ` ${ctx.raw.toLocaleString('id-ID')} resi` } } },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 6 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                            y: { grid: { display: false } }
                        }
                    }
                });
            } else {
                document.getElementById('statusAkhirChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Tidak ada data</p>';
            }

            // 02 Donut - SLA Compliance Rate (dengan teks persentase di tengah)
            const slaWithin = {!! json_encode((int) $kpis['withinSla']) !!};
            const slaOver = {!! json_encode((int) $kpis['overSla']) !!};
            const slaRate = {!! json_encode((float) $kpis['slaAchievementRate']) !!};
            if ((slaWithin + slaOver) > 0) {
                const centerText = {
                    id: 'centerText',
                    afterDraw(chart) {
                        const { ctx, chartArea } = chart;
                        if (!chartArea) return;
                        const cx = (chartArea.left + chartArea.right) / 2;
                        const cy = (chartArea.top + chartArea.bottom) / 2;
                        ctx.save();
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.font = "800 26px 'Figtree', ui-sans-serif, system-ui, sans-serif";
                        ctx.fillStyle = slaRate >= 95 ? '#059669' : (slaRate >= 85 ? '#D97706' : '#E11D48');
                        ctx.fillText(slaRate + '%', cx, cy - 8);
                        ctx.font = "600 11px 'Figtree', ui-sans-serif, system-ui, sans-serif";
                        ctx.fillStyle = '#9CA3AF';
                        ctx.fillText('compliance', cx, cy + 14);
                        ctx.restore();
                    }
                };
                new Chart(document.getElementById('slaComplianceChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Within SLA', 'Over SLA'],
                        datasets: [{
                            data: [slaWithin, slaOver],
                            backgroundColor: ['#10B981', '#EF4444'],
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '68%',
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 12 } },
                            tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} resi` } }
                        }
                    },
                    plugins: [centerText]
                });
            } else {
                document.getElementById('slaComplianceChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data SLA pada filter ini</p>';
            }

            // 03 Grouped Bar - SLA MM vs LM per Vendor
            const slaMmLmLabels = {!! json_encode($slaMmVsLm->pluck('vendor')) !!};
            const slaMmLmMmRates = {!! json_encode($slaMmVsLm->pluck('mm_rate')) !!};
            const slaMmLmLmRates = {!! json_encode($slaMmVsLm->pluck('lm_rate')) !!};
            if (slaMmLmLabels.length > 0) {
                new Chart(document.getElementById('slaMmVsLmChart'), {
                    type: 'bar',
                    data: {
                        labels: slaMmLmLabels,
                        datasets: [
                            { label: 'SLA MM (%)', data: slaMmLmMmRates, backgroundColor: 'rgba(99, 102, 241, 0.8)', borderRadius: 5, maxBarThickness: 22, hoverBackgroundColor: '#4F46E5' },
                            { label: 'SLA LM (%)', data: slaMmLmLmRates, backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 5, maxBarThickness: 22, hoverBackgroundColor: '#059669' }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 16 } }
                        },
                        scales: {
                            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%', maxTicksLimit: 6 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                            x: { grid: { display: false }, ticks: { maxRotation: 45 } }
                        }
                    }
                });
            } else {
                document.getElementById('slaMmVsLmChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data SLA MM/LM</p>';
            }

            // 04 Bar - Top 5 Alokasi Provinsi
            new Chart(document.getElementById('provinceChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($provinceData->pluck('province')) !!},
                    datasets: [{
                        label: 'Total Pengiriman',
                        data: {!! json_encode($provinceData->pluck('total')) !!},
                        backgroundColor: (context) => {
                            const { ctx, chartArea } = context.chart;
                            if (!chartArea) return 'rgba(17, 24, 39, 0.8)';
                            const g = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            g.addColorStop(0, 'rgba(17, 24, 39, 0.35)');
                            g.addColorStop(1, 'rgba(17, 24, 39, 0.85)');
                            return g;
                        },
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 42,
                        hoverBackgroundColor: '#059669'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ` ${ctx.raw.toLocaleString('id-ID')} resi` } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 6 }, grid: { color: 'rgba(0, 0, 0, 0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 05 Grouped Bar - Top Vendor Load
            new Chart(document.getElementById('vendorChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($vendorData->pluck('vendor_lm')) !!},
                    datasets: [
                        {
                            label: 'Total Volume',
                            data: {!! json_encode($vendorData->pluck('total')) !!},
                            backgroundColor: 'rgba(55, 65, 81, 0.8)',
                            borderRadius: 5,
                            maxBarThickness: 26,
                            hoverBackgroundColor: '#374151'
                        },
                        {
                            label: 'Within SLA',
                            data: {!! json_encode($vendorData->pluck('on_time')) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderRadius: 5,
                            maxBarThickness: 26,
                            hoverBackgroundColor: '#059669'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 16 } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 6 }, grid: { color: 'rgba(0, 0, 0, 0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 06 Donut - Status BAST Balik
            const bastLabels = {!! json_encode($bastFinance['bastData']->pluck('bast_status')) !!};
            const bastTotals = {!! json_encode($bastFinance['bastData']->pluck('total')) !!};
            const bastColors = ['#6366F1', '#06B6D4', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'];
            if (bastLabels.length > 0) {
                new Chart(document.getElementById('bastChart'), {
                    type: 'doughnut',
                    data: {
                        labels: bastLabels,
                        datasets: [{ data: bastTotals, backgroundColor: bastColors.slice(0, bastLabels.length), borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '62%',
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 12 } },
                            tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} resi` } }
                        }
                    }
                });
            } else {
                document.getElementById('bastChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data BAST balik — grafik otomatis tampil begitu kolom bast_status terisi dari import</p>';
            }

            // 07 Donut - BAST Handover Finance
            const financeLabels = {!! json_encode($bastFinance['financeData']->pluck('finance_status')) !!};
            const financeTotals = {!! json_encode($bastFinance['financeData']->pluck('total')) !!};
            const financeColors = ['#8B5CF6', '#0EA5E9', '#22C55E', '#EAB308', '#F97316', '#EF4444', '#EC4899'];
            if (financeLabels.length > 0) {
                new Chart(document.getElementById('financeChart'), {
                    type: 'doughnut',
                    data: {
                        labels: financeLabels,
                        datasets: [{ data: financeTotals, backgroundColor: financeColors.slice(0, financeLabels.length), borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '62%',
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 12 } },
                            tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} resi` } }
                        }
                    }
                });
            } else {
                document.getElementById('financeChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data keuangan</p>';
            }

            // Tambahan: Bar - Tren Pengiriman per Bulan
            new Chart(document.getElementById('trendMonthlyChart'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($trendMonthly->pluck('month')->map(fn ($m) => \Illuminate\Support\Carbon::createFromFormat('Y-m', $m)->format('M Y'))) !!},
                    datasets: [{
                        label: 'Total Pengiriman',
                        data: {!! json_encode($trendMonthly->pluck('total')) !!},
                        backgroundColor: (context) => {
                            const { ctx, chartArea } = context.chart;
                            if (!chartArea) return 'rgba(5, 150, 105, 0.8)';
                            const g = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                            g.addColorStop(0, 'rgba(16, 185, 129, 0.55)');
                            g.addColorStop(1, 'rgba(5, 150, 105, 0.95)');
                            return g;
                        },
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 42,
                        hoverBackgroundColor: '#047857'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => ` ${ctx.raw.toLocaleString('id-ID')} resi` } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 6 }, grid: { color: 'rgba(0, 0, 0, 0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Tambahan: Grouped Bar - Vendor MM Performance
            const vmmLabels = {!! json_encode($vendorMmPerformance->pluck('vendor')) !!};
            const vmmTotals = {!! json_encode($vendorMmPerformance->pluck('total')) !!};
            const vmmRates = {!! json_encode($vendorMmPerformance->pluck('rate')) !!};
            if (vmmLabels.length > 0) {
                new Chart(document.getElementById('vendorMmChart'), {
                    type: 'bar',
                    data: {
                        labels: vmmLabels,
                        datasets: [
                            { label: 'Volume', data: vmmTotals, backgroundColor: 'rgba(55, 65, 81, 0.8)', borderRadius: 5, maxBarThickness: 22, hoverBackgroundColor: '#374151', yAxisID: 'y' },
                            { label: 'SLA Rate (%)', data: vmmRates, backgroundColor: 'rgba(99, 102, 241, 0.8)', borderRadius: 5, maxBarThickness: 22, hoverBackgroundColor: '#4F46E5', yAxisID: 'y1' }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 16 } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 6 }, grid: { color: 'rgba(0,0,0,0.04)' }, title: { display: true, text: 'Volume', font: { size: 10 } } },
                            y1: { beginAtZero: true, max: 100, position: 'right', ticks: { callback: v => v + '%', maxTicksLimit: 5 }, grid: { drawOnChartArea: false }, title: { display: true, text: 'SLA %', font: { size: 10 } } },
                            x: { grid: { display: false }, ticks: { maxRotation: 45 } }
                        }
                    }
                });
            } else {
                document.getElementById('vendorMmChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data vendor MM</p>';
            }

            // Tambahan: Donut - Inbound First Mile
            const ifmLabels = {!! json_encode($inboundFmMetrics->pluck('status_inbound')) !!};
            const ifmTotals = {!! json_encode($inboundFmMetrics->pluck('total')) !!};
            const ifmColors = ['#14B8A6', '#6366F1', '#F59E0B', '#EF4444', '#06B6D4', '#8B5CF6', '#EC4899'];
            if (ifmLabels.length > 0) {
                new Chart(document.getElementById('inboundFmChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ifmLabels,
                        datasets: [{ data: ifmTotals, backgroundColor: ifmColors.slice(0, ifmLabels.length), borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false, cutout: '62%',
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 12 } },
                            tooltip: { callbacks: { label: (ctx) => ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} resi` } }
                        }
                    }
                });
            } else {
                document.getElementById('inboundFmChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data Inbound FM</p>';
            }

        });
    </script>
</x-app-layout>
