<x-app-layout>
    <x-slot name="header">
        Dashboard Monitoring Operasional
    </x-slot>

    <div class="space-y-6">

        <!-- ==================== FILTER MULTI-KRITERIA ==================== -->
        <div class="card p-5" x-reveal>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">Filter Data Analisis</h3>
                        <p class="text-[11px] text-gray-400">Persempit cakupan berdasarkan kriteria operasional</p>
                    </div>
                </div>
                @if(request()->hasAny(['start_date', 'end_date', 'province', 'city_regency', 'vendor_id', 'status', 'sla']))
                    <a href="{{ route('dashboard') }}" class="btn-ghost !px-3 !py-1.5 !text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Filter
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('dashboard') }}" x-loading class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-4">

                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-8">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Cepat:</span>
                        <button type="button" onclick="setQuickDate(7)" class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-dbl-green-light/60 hover:text-dbl-green-dark transition-colors cursor-pointer">7 Hari</button>
                        <button type="button" onclick="setQuickDate(30)" class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-dbl-green-light/60 hover:text-dbl-green-dark transition-colors cursor-pointer">30 Hari</button>
                        <button type="button" onclick="setQuickDate(90)" class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-dbl-green-light/60 hover:text-dbl-green-dark transition-colors cursor-pointer">90 Hari</button>
                        <button type="button" onclick="setMonthThis()" class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-dbl-green-light/60 hover:text-dbl-green-dark transition-colors cursor-pointer">Bulan Ini</button>
                        <button type="button" onclick="setMonthLast()" class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 hover:bg-dbl-green-light/60 hover:text-dbl-green-dark transition-colors cursor-pointer">Bulan Lalu</button>
                    </div>
                </div>

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
                    <select name="province" class="field-input" onchange="this.form.submit()">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="field-label">Kabupaten/Kota</label>
                    <select name="city_regency" class="field-input" onchange="this.form.submit()">
                        <option value="">Semua Kota</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city_regency') == $city ? 'selected' : '' }}>{{ $city }}</option>
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

                <div>
                    <label class="field-label">Kepatuhan SLA</label>
                    <select name="sla" class="field-input">
                        <option value="">Semua</option>
                        <option value="within" {{ request('sla') == 'within' ? 'selected' : '' }}>Within SLA</option>
                        <option value="over" {{ request('sla') == 'over' ? 'selected' : '' }}>Over SLA</option>
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-4 xl:col-span-8 flex items-end justify-end gap-2">
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Terapkan Filter
                    </button>
                </div>

                <script>
                    function setQuickDate(days) {
                        const end = new Date();
                        const start = new Date();
                        start.setDate(end.getDate() - (days - 1));
                        document.querySelector('[name=start_date]').value = start.toISOString().slice(0, 10);
                        document.querySelector('[name=end_date]').value = end.toISOString().slice(0, 10);
                    }
                    function setMonthThis() {
                        const now = new Date();
                        document.querySelector('[name=start_date]').value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().slice(0, 10);
                        document.querySelector('[name=end_date]').value = new Date(now.getFullYear(), now.getMonth() + 1, 0).toISOString().slice(0, 10);
                    }
                    function setMonthLast() {
                        const now = new Date();
                        document.querySelector('[name=start_date]').value = new Date(now.getFullYear(), now.getMonth() - 1, 1).toISOString().slice(0, 10);
                        document.querySelector('[name=end_date]').value = new Date(now.getFullYear(), now.getMonth(), 0).toISOString().slice(0, 10);
                    }
                </script>
            </form>
        </div>

        <!-- ==================== 4 KPI STAT CARDS ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <!-- Total Shipments -->
            <div class="card card-interactive p-5" x-reveal>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Shipments</p>
                        <h3 class="text-3xl font-black tabular-nums tracking-tight text-gray-900 mt-1.5">
                            <span x-counter data-counter-value="{{ $kpis['totalShipments'] }}">0</span>
                        </h3>
                    </div>
                    <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    @if(!is_null($deltas['total']))
                        <span class="inline-flex items-center gap-0.5 font-bold {{ ($deltas['total'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ ($deltas['total'] ?? 0) >= 0 ? '▲' : '▼' }} {{ abs($deltas['total']) }}%
                        </span>
                        <span class="text-gray-400">vs periode sebelumnya</span>
                    @else
                        <span class="text-gray-400">Atur filter tanggal untuk perbandingan</span>
                    @endif
                </div>
            </div>

            <!-- On-Time Delivery Rate -->
            <div class="card card-interactive p-5" x-reveal x-reveal.delay>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">On-Time Delivery Rate</p>
                        <h3 class="text-3xl font-black tabular-nums tracking-tight mt-1.5 {{ $kpis['slaAchievementRate'] >= 95 ? 'text-emerald-600' : ($kpis['slaAchievementRate'] >= 85 ? 'text-amber-600' : 'text-rose-600') }}">
                            <span x-counter data-counter-value="{{ $kpis['slaAchievementRate'] }}" data-counter-suffix="%">0</span>
                        </h3>
                    </div>
                    <span class="icon-chip bg-emerald-50 text-emerald-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    @if(!is_null($deltas['slaRate']))
                        <span class="inline-flex items-center gap-0.5 font-bold {{ ($deltas['slaRate'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ ($deltas['slaRate'] ?? 0) >= 0 ? '▲' : '▼' }} {{ abs($deltas['slaRate']) }} pp
                        </span>
                        <span class="text-gray-400">{{ number_format($kpis['withinSla']) }}/{{ number_format($kpis['totalShipments']) }} resi within SLA</span>
                    @else
                        <span class="text-gray-400">{{ number_format($kpis['withinSla']) }} dari {{ number_format($kpis['totalShipments']) }} resi</span>
                    @endif
                </div>
            </div>

            <!-- Active Issues / Delays -->
            <div class="card card-interactive p-5" x-reveal>
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Active Issues / Delays</p>
                        <h3 class="text-3xl font-black tabular-nums tracking-tight mt-1.5 {{ $activeIssues > 0 || $kpis['overSla'] > 0 ? 'text-rose-600' : 'text-gray-900' }}">
                            <span x-counter data-counter-value="{{ $activeIssues + $kpis['overSla'] }}">0</span>
                        </h3>
                    </div>
                    <span class="icon-chip bg-rose-50 text-rose-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200"><span class="dot bg-rose-500"></span>{{ $activeIssues }} issue open</span>
                    <span class="text-gray-400">{{ number_format($kpis['overSla']) }} resi over SLA</span>
                </div>
            </div>

            <!-- Latest Import Status -->
            <div class="card card-interactive p-5" x-reveal x-reveal.delay>
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Latest Import</p>
                        @if($latestImport)
                            <h3 class="text-base font-black tracking-tight text-gray-900 mt-1.5 truncate" title="{{ $latestImport->file_name }}">
                                {{ $latestImport->file_name }}
                            </h3>
                        @else
                            <h3 class="text-base font-black tracking-tight text-gray-400 mt-1.5">Belum ada import</h3>
                        @endif
                    </div>
                    <span class="icon-chip {{ $latestImport ? 'bg-dbl-green-light/60 text-dbl-green-dark' : 'bg-gray-100 text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs">
                    @if($latestImport)
                        <x-status-badge :status="$latestImport->status === 'completed' ? 'Completed' : ucfirst($latestImport->status)" />
                        <span class="text-gray-400 truncate">{{ $latestImport->created_at->format('d M Y, H:i') }} · {{ number_format($latestImport->total_rows) }} baris</span>
                    @else
                        <span class="text-gray-400">Upload file Excel di menu Import untuk memulai</span>
                    @endif
                </div>
            </div>

        </div>

        <!-- ==================== RINGKASAN PROPORSI (STACKED PROGRESS BAR) ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="card p-5" x-reveal>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-800">Komposisi Status Pengiriman</h4>
                    <span class="text-xs text-gray-400 font-normal">{{ number_format($kpis['totalShipments']) }} resi</span>
                </div>

                @php
                    $statusTotal = max(collect($statusSummary)->sum('value'), 1);
                @endphp

                <div class="flex h-3 w-full rounded-full overflow-hidden bg-gray-100">
                    @foreach($statusSummary as $seg)
                        @if($seg['value'] > 0)
                            <div class="{{ $seg['color'] }} transition-all duration-700" style="width: {{ round(($seg['value'] / $statusTotal) * 100, 1) }}%"></div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4 grid grid-cols-3 gap-3">
                    @foreach($statusSummary as $seg)
                        <div class="flex items-center gap-2">
                            <span class="dot {{ $seg['color'] }}"></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-600 truncate">{{ $seg['label'] }}</p>
                                <p class="text-sm font-black text-gray-900 tabular-nums">{{ number_format($seg['value']) }} <span class="text-[10px] font-bold text-gray-400">{{ round(($seg['value'] / $statusTotal) * 100, 1) }}%</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-5" x-reveal x-reveal.delay>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-800">Kepatuhan SLA</h4>
                    <span class="text-xs text-gray-400 font-normal">Target ≥ 95%</span>
                </div>

                @php
                    $slaTotal = max(collect($slaSummary)->sum('value'), 1);
                    $slaRate = round(($kpis['withinSla'] / $slaTotal) * 100, 1);
                @endphp

                <div class="flex h-3 w-full rounded-full overflow-hidden bg-gray-100">
                    @foreach($slaSummary as $seg)
                        @if($seg['value'] > 0)
                            <div class="{{ $seg['color'] }} transition-all duration-700" style="width: {{ round(($seg['value'] / $slaTotal) * 100, 1) }}%"></div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-5">
                        @foreach($slaSummary as $seg)
                            <div class="flex items-center gap-2">
                                <span class="dot {{ $seg['color'] }}"></span>
                                <p class="text-xs font-semibold text-gray-600">{{ $seg['label'] }}</p>
                                <p class="text-sm font-black text-gray-900 tabular-nums">{{ number_format($seg['value']) }}</p>
                            </div>
                        @endforeach
                    </div>
                    <span class="text-lg font-black tabular-nums {{ $slaRate >= 95 ? 'text-emerald-600' : ($slaRate >= 85 ? 'text-amber-600' : 'text-rose-600') }}">{{ $slaRate }}%</span>
                </div>
            </div>

        </div>

        <!-- ==================== CHART UTAMA: TREN VOLUME + SLA RATE ==================== -->
        <div class="card p-5" x-reveal>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-base font-bold text-gray-800">Tren Pengiriman Harian</h4>
                    <p class="text-xs text-gray-400">Volume harian & tingkat kepatuhan SLA</p>
                </div>
                <a href="{{ route('analytics.index') }}" class="btn-ghost !px-3 !py-1.5 !text-xs">
                    Analitik Lanjutan
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if($trend->isEmpty())
                <div class="h-64 flex flex-col items-center justify-center text-center">
                    <div class="icon-chip bg-gray-100 text-gray-400 mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400 font-medium">Belum ada data tren pada filter ini</p>
                </div>
                <canvas id="trendChart" class="hidden"></canvas>
            @else
                <div class="h-72">
                    <canvas id="trendChart"></canvas>
                </div>
            @endif
        </div>

        <!-- ==================== NEEDS ATTENTION & ACTIVITY FEED ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Needs Attention / Priority Issues -->
            <div class="card p-5" x-reveal>
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-base font-bold text-gray-800">Needs Attention</h4>
                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="dot bg-rose-500"></span>{{ count($needsAttention) }} prioritas
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-4">Issue terbuka & pengiriman over SLA / undelivered pada scope filter</p>

                @if(empty($needsAttention))
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="icon-chip bg-emerald-50 text-emerald-500 mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">Semua aman — tidak ada prioritas saat ini</p>
                    </div>
                @else
                    <ul class="space-y-2.5">
                        @foreach($needsAttention as $item)
                            <li class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50/60 hover:border-dbl-green/30 hover:bg-white transition-all duration-200 px-3.5 py-2.5">
                                <span class="icon-chip {{ $item['type'] === 'issue' ? 'bg-rose-50 text-rose-500' : 'bg-amber-50 text-amber-500' }} !w-8 !h-8 shrink-0">
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
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $item['waybill_no'] }}</p>
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded-md {{ $item['type'] === 'issue' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">{{ $item['label'] }}</span>
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

            <!-- Recent Activity Feed -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-base font-bold text-gray-800">Recent Activity</h4>
                    <span class="relative flex h-2 w-2" title="Real-time">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-dbl-green opacity-60"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-dbl-green"></span>
                    </span>
                </div>
                <p class="text-xs text-gray-400 mb-4">Pembaruan batch impor & status pengiriman terbaru</p>

                @if(empty($activityFeed))
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="icon-chip bg-gray-100 text-gray-400 mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">Belum ada aktivitas</p>
                    </div>
                @else
                    <ol class="relative border-l-2 border-gray-100 ml-3 space-y-4">
                        @foreach($activityFeed as $item)
                            <li class="ml-5">
                                <span class="absolute -left-[9px] flex items-center justify-center w-4 h-4 rounded-full ring-4 ring-white {{ $item['type'] === 'import' ? 'bg-indigo-500' : 'bg-dbl-green' }}"></span>
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            @if($item['link'])
                                                <a href="{{ $item['link'] }}" class="hover:text-dbl-green-dark transition-colors">{{ $item['title'] }}</a>
                                            @else
                                                {{ $item['title'] }}
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-gray-400 truncate">{{ $item['meta'] }}</p>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-400 whitespace-nowrap shrink-0">{{ $item['at']->diffForHumans() }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Chart Utama: Bar Volume Harian + Line SLA Rate (dual-axis)
            const trendCanvas = document.getElementById('trendChart');
            if (trendCanvas && !trendCanvas.classList.contains('hidden')) {
                const trendLabels = {!! json_encode($trend->pluck('date')->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d M'))) !!};
                const trendTotals = {!! json_encode($trend->pluck('total')) !!};
                const trendWithin = {!! json_encode($trend->pluck('within_sla')) !!};

                new Chart(trendCanvas, {
                    type: 'bar',
                    data: {
                        labels: trendLabels,
                        datasets: [
                            {
                                label: 'Pengiriman',
                                data: trendTotals,
                                backgroundColor: (context) => {
                                    const { ctx, chartArea } = context.chart;
                                    if (!chartArea) return 'rgba(16, 185, 129, 0.75)';
                                    const g = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                    g.addColorStop(0, 'rgba(16, 185, 129, 0.45)');
                                    g.addColorStop(1, 'rgba(5, 150, 105, 0.9)');
                                    return g;
                                },
                                borderRadius: 6,
                                borderSkipped: false,
                                maxBarThickness: 34,
                                yAxisID: 'y'
                            },
                            {
                                label: 'SLA Rate (%)',
                                data: trendTotals.map((t, i) => t > 0 ? Math.round((trendWithin[i] / t) * 1000) / 10 : 0),
                                type: 'line',
                                borderColor: '#111827',
                                backgroundColor: 'transparent',
                                borderDash: [6, 4],
                                borderWidth: 2,
                                tension: 0.35,
                                pointRadius: 2.5,
                                pointBackgroundColor: '#111827',
                                yAxisID: 'y1'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 16 } },
                            tooltip: { callbacks: { label: (ctx) => ctx.dataset.yAxisID === 'y1' ? ` SLA Rate: ${ctx.raw}%` : ` ${ctx.raw.toLocaleString('id-ID')} resi` } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0, maxTicksLimit: 6 }, grid: { color: 'rgba(0, 0, 0, 0.04)' } },
                            y1: { beginAtZero: true, max: 100, position: 'right', ticks: { callback: v => v + '%', maxTicksLimit: 5 }, grid: { drawOnChartArea: false } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

        });
    </script>
</x-app-layout>
