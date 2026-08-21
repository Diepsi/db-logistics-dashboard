<x-app-layout>
    <x-slot name="header">
        Dashboard Analytics Operasional Pengiriman
    </x-slot>

    <div class="space-y-6">

        <!-- ==================== SECTION 1: FILTER MULTI-KRITERIA (FR-09) ==================== -->
        <div class="card p-5" x-reveal>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="icon-chip bg-dbl-green-light/60 text-dbl-green-dark">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-gray-800">Filter Data Analisis</h3>
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
                        start.setDate(end.getDate() - days);
                        document.querySelector('input[name="start_date"]').value = start.toISOString().split('T')[0];
                        document.querySelector('input[name="end_date"]').value = end.toISOString().split('T')[0];
                    }
                    function setMonthThis() {
                        const now = new Date();
                        const first = new Date(now.getFullYear(), now.getMonth(), 1);
                        const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        document.querySelector('input[name="start_date"]').value = first.toISOString().split('T')[0];
                        document.querySelector('input[name="end_date"]').value = last.toISOString().split('T')[0];
                    }
                    function setMonthLast() {
                        const now = new Date();
                        const first = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        const last = new Date(now.getFullYear(), now.getMonth(), 0);
                        document.querySelector('input[name="start_date"]').value = first.toISOString().split('T')[0];
                        document.querySelector('input[name="end_date"]').value = last.toISOString().split('T')[0];
                    }
                </script>

            </form>
        </div>


        <!-- ==================== BANNER KESEGARAN DATA ==================== -->
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 bg-gradient-to-r from-dbl-dark via-gray-800 to-gray-800 text-white px-5 py-3.5 rounded-2xl shadow-card relative overflow-hidden" x-reveal>
            <div class="absolute inset-0 bg-gradient-to-r from-dbl-green/10 via-transparent to-transparent pointer-events-none"></div>
            <div class="flex items-center gap-2.5 relative">
                <span class="icon-chip !w-8 !h-8 bg-white/10 text-dbl-green">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="text-xs font-bold uppercase tracking-wider text-white/80">Kesegaran Data:</span>
            </div>
            @if($latestImport)
                <span class="text-sm font-medium text-white/90 relative">
                    Import terakhir <span class="text-dbl-green-light font-bold">{{ $latestImport->file_name }}</span>
                    · {{ $latestImport->created_at->format('d M Y, H:i') }} · {{ number_format($latestImport->total_rows) }} baris
                </span>
            @else
                <span class="text-sm font-medium text-white/90 relative">Belum ada import data</span>
            @endif
            <span class="hidden md:inline text-white/20">|</span>
            <span class="text-xs text-white/60 relative">
                Cakupan: <span class="font-bold text-white">{{ $kpis['totalShipments'] }} resi</span>
                @if($prevKpis)
                    <span class="text-white/50">(vs {{ number_format($prevKpis['total']) }} resi periode sebelumnya)</span>
                @endif
            </span>
        </div>

        <!-- ==================== SLA ALERT BANNER ==================== -->
        @if($kpis['totalShipments'] > 0 && $kpis['slaAchievementRate'] < 85)
            <div class="flex items-center gap-3 bg-gradient-to-r from-amber-50 to-rose-50 border border-amber-200 rounded-2xl px-5 py-3.5 shadow-card" x-reveal>
                <span class="icon-chip bg-amber-100 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </span>
                <div class="flex-1">
                    <p class="text-sm font-bold text-amber-800">Peringatan: SLA Achievement Rate di bawah target (85%)</p>
                    <p class="text-xs text-amber-600">Tingkat kepatuhan SLA saat ini <span class="font-bold">{{ $kpis['slaAchievementRate'] }}%</span>. Perlu tindakan perbaikan segera.</p>
                </div>
                <span class="text-2xl font-black text-amber-600 tabular-nums">{{ $kpis['slaAchievementRate'] }}%</span>
            </div>
        @endif

        <!-- ==================== SECTION 2: KPI CARDS (7 KPI Utama) ==================== -->
        @php
            $badge = fn ($value, $invert = false) => $value === null
                ? ''
                : '<span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full '.(($value >= 0) xor $invert ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700').'">'.($value >= 0 xor $invert ? '▲' : '▼').' '.abs($value).'%</span>';
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4">

            <!-- Card 1: Total Pengiriman -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-gray-400/80 to-gray-300"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Resi</span>
                    <span class="icon-chip bg-gray-100 text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black text-gray-900 tabular-nums tracking-tight">
                        <span x-counter data-counter-value="{{ $kpis['totalShipments'] }}">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-gray-400">Total shipment terdaftar</p>
                        {!! $badge($deltas['total'] ?? null) !!}
                    </div>
                </div>
            </div>

            <!-- Card 2: Completed -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400 to-emerald-300"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Completed</span>
                    <span class="icon-chip bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black text-emerald-600 tabular-nums tracking-tight">
                        <span x-counter data-counter-value="{{ $kpis['completed'] }}">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <span class="inline-flex items-center text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">
                            {{ $kpis['totalShipments'] > 0 ? round(($kpis['completed'] / $kpis['totalShipments']) * 100, 1) : 0 }}% dari total
                        </span>
                        {!! $badge($deltas['completed'] ?? null) !!}
                    </div>
                </div>
            </div>

            <!-- Card 3: On Delivery -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-400 to-blue-300"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">On Delivery</span>
                    <span class="icon-chip bg-blue-100 text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM5 17h14a1 1 0 001-1V8a1 1 0 00-1-1h-3V5a1 1 0 00-1-1H8a1 1 0 00-1 1v2H4a1 1 0 00-1 1v8a1 1 0 001 1z" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black text-blue-600 tabular-nums tracking-tight">
                        <span x-counter data-counter-value="{{ $kpis['onDelivery'] }}">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-gray-400">Dalam proses pengiriman</p>
                        {!! $badge($deltas['onDelivery'] ?? null) !!}
                    </div>
                </div>
            </div>

            <!-- Card 4: Undelivered -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-400 to-rose-300"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Undelivered</span>
                    <span class="icon-chip bg-rose-100 text-rose-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black text-rose-600 tabular-nums tracking-tight">
                        <span x-counter data-counter-value="{{ $kpis['undelivered'] }}">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-gray-400">Kendala operasional</p>
                        {!! $badge($deltas['undelivered'] ?? null, true) !!}
                    </div>
                </div>
            </div>

            <!-- Card 5: Within SLA -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-teal-400 to-teal-300"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Within SLA</span>
                    <span class="icon-chip bg-teal-100 text-teal-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9a3 3 0 100 6 3 3 0 000-6zm7 1v2m0-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black text-teal-600 tabular-nums tracking-tight">
                        <span x-counter data-counter-value="{{ $kpis['withinSla'] }}">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-gray-400">Tepat waktu</p>
                        {!! $badge($deltas['withinSla'] ?? null) !!}
                    </div>
                </div>
            </div>

            <!-- Card 6: Over SLA -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5" x-reveal x-reveal.delay>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-400 to-amber-300"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Over SLA</span>
                    <span class="icon-chip bg-amber-100 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black text-amber-600 tabular-nums tracking-tight">
                        <span x-counter data-counter-value="{{ $kpis['overSla'] }}">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-amber-700 font-medium">Melewati batas SLA</p>
                        {!! $badge($deltas['overSla'] ?? null, true) !!}
                    </div>
                </div>
            </div>

            <!-- Card 7: SLA Achievement Rate -->
            <div class="card relative overflow-hidden flex flex-col justify-between group transition-all duration-300 hover:shadow-lift hover:-translate-y-0.5 bg-gradient-to-br from-white via-white to-dbl-green-light/30" x-reveal x-reveal.delay>
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-dbl-green to-dbl-green-light"></div>
                <div class="flex items-center justify-between p-4 pb-0">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">SLA Rate</span>
                    <span class="icon-chip bg-dbl-green text-white shadow-glow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </span>
                </div>
                <div class="p-4 pt-3">
                    <h3 class="text-3xl font-black tabular-nums tracking-tight {{ $kpis['slaAchievementRate'] >= 95 ? 'text-emerald-600' : ($kpis['slaAchievementRate'] >= 85 ? 'text-amber-600' : 'text-rose-600') }}">
                        <span x-counter data-counter-value="{{ $kpis['slaAchievementRate'] }}" data-counter-suffix="%">0</span>
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-[11px] text-gray-600 font-medium">Kepatuhan SLA overall</p>
                        {!! $badge($deltas['slaRate'] ?? null) !!}
                    </div>
                </div>
            </div>

        </div>


        <!-- ==================== SECTION 3: VISUALISASI GRAFIK ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart 1: Komposisi Status Pengiriman -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Komposisi Status Pengiriman</span>
                    <span class="text-xs text-gray-400 font-normal">Real-time Status</span>
                </h4>
                <div class="h-64 flex justify-center items-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Performa SLA (Within vs Over) -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Performa Kepatuhan SLA</span>
                    <span class="text-xs text-gray-400 font-normal">Within vs Over SLA</span>
                </h4>
                <div class="h-64 flex justify-center items-center">
                    <canvas id="slaChart"></canvas>
                </div>
            </div>

            <!-- Chart 3: Tren Pengiriman per Hari (+ SLA Rate) -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Tren Pengiriman per Hari</span>
                    <span class="text-xs text-gray-400 font-normal">Volume &amp; SLA Rate harian</span>
                </h4>
                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Chart 6: Tren Pengiriman per Bulan -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Tren Pengiriman per Bulan</span>
                    <span class="text-xs text-gray-400 font-normal">Agregasi bulanan</span>
                </h4>
                <div class="h-64">
                    <canvas id="trendMonthlyChart"></canvas>
                </div>
            </div>

            <!-- Chart 4: Top 5 Volume per Provinsi -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-4">
                    Distribusi Pengiriman per Provinsi (Top 5)
                </h4>
                <div class="h-64">
                    <canvas id="provinceChart"></canvas>
                </div>
            </div>

            <!-- Chart 5: Performa Vendor Last Mile -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4">
                    Top 5 Vendor Last Mile (Volume & Kepatuhan)
                </h4>
                <div class="h-64">
                    <canvas id="vendorChart"></canvas>
                </div>
            </div>

        </div>


        <!-- ==================== SECTION 3b: ANALYTICS LANJUTAN ==================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Chart 7: BAST Pipeline Donut -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Status BAST</span>
                    <span class="text-xs text-gray-400 font-normal">{{ number_format($bastFinance['bastTotal']) }} resi</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="bastChart"></canvas>
                </div>
            </div>

            <!-- Chart 8: Finance Pipeline Donut -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Status Keuangan</span>
                    <span class="text-xs text-gray-400 font-normal">{{ number_format($bastFinance['financeTotal']) }} resi</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>

            <!-- Chart 9: SLA MM vs LM Comparison -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>SLA Middle Mile vs Last Mile</span>
                    <span class="text-xs text-gray-400 font-normal">Per Vendor (Top 10)</span>
                </h4>
                <div class="h-72">
                    <canvas id="slaMmVsLmChart"></canvas>
                </div>
            </div>

            <!-- Chart 10: Vendor MM Performance -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Performa Vendor Middle Mile</span>
                    <span class="text-xs text-gray-400 font-normal">Volume & SLA Rate</span>
                </h4>
                <div class="h-72">
                    <canvas id="vendorMmChart"></canvas>
                </div>
            </div>

            <!-- Chart 11: Inbound First Mile Donut -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Status Inbound First Mile</span>
                    <span class="text-xs text-gray-400 font-normal">Dari FM ke Gudang</span>
                </h4>
                <div class="h-56 flex justify-center items-center">
                    <canvas id="inboundFmChart"></canvas>
                </div>
            </div>

            <!-- Chart 12: Status Akhir Detail -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4 flex items-center justify-between">
                    <span>Distribusi Status Akhir</span>
                    <span class="text-xs text-gray-400 font-normal">Breakdown detail</span>
                </h4>
                <div class="h-56">
                    <canvas id="statusAkhirChart"></canvas>
                </div>
            </div>

        </div>


        <!-- ==================== SECTION 4: PANEL ANALISIS ==================== -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Funnel Kepatuhan SLA per Tahap -->
            <div class="card p-5" x-reveal>
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-base font-bold text-gray-800">Kepatuhan SLA per Tahap</h4>
                    <form method="GET" action="{{ route('dashboard') }}" x-loading class="flex items-center gap-1">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        <input type="hidden" name="province" value="{{ request('province') }}">
                        <input type="hidden" name="city_regency" value="{{ request('city_regency') }}">
                        <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}">
                        <input type="hidden" name="sla" value="{{ request('sla') }}">
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green/30 focus:ring-2 py-1.5">
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
                        $barColor = $stage['rate'] >= $target ? 'bg-gradient-to-r from-emerald-500 to-emerald-400' : ($stage['rate'] >= 85 ? 'bg-gradient-to-r from-amber-500 to-amber-400' : 'bg-gradient-to-r from-rose-500 to-rose-400');
                        $textColor = $stage['rate'] >= $target ? 'text-emerald-600' : ($stage['rate'] >= 85 ? 'text-amber-600' : 'text-rose-600');
                    @endphp
                    <div class="mb-4 last:mb-0">
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-semibold text-gray-700">{{ $stage['label'] }}</span>
                            <span class="text-xs text-gray-400">{{ $stage['within'] }}/{{ $stage['total'] }} resi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $barColor }} rounded-full bar-grow" style="width: {{ $stage['rate'] }}%"></div>
                            </div>
                            <span class="text-sm font-bold {{ $textColor }} w-14 text-right tabular-nums">{{ $stage['rate'] }}%</span>
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
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4">Rata-rata Lead Time (hari)</h4>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100 transition-all duration-300 hover:border-dbl-green/30 hover:shadow-sm">
                        <div class="text-2xl font-black text-gray-800 tabular-nums">{{ $leadTimes['ho_to_pickup'] }}</div>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">HO → Pickup</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Pengambilan barang</div>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100 transition-all duration-300 hover:border-dbl-green/30 hover:shadow-sm">
                        <div class="text-2xl font-black text-gray-800 tabular-nums">{{ $leadTimes['pickup_to_delivery'] }}</div>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">Pickup → Delivery</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">Proses antar</div>
                    </div>
                    <div class="bg-gradient-to-br from-dbl-green-light/60 to-white rounded-xl p-4 text-center border border-dbl-green/20 transition-all duration-300 hover:shadow-md">
                        <div class="text-2xl font-black text-dbl-green-dark tabular-nums">{{ $leadTimes['ho_to_delivery'] }}</div>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">HO → Delivery</div>
                        <div class="text-[10px] text-gray-400 mt-0.5">End-to-end</div>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Dihitung dari selisih tanggal antar tahapan (hari) pada resi yang memiliki data tahap lengkap.
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Vendor dengan Over-SLA Tertinggi -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-1 flex items-center justify-between">
                    <span>Vendor dengan Over-SLA Tertinggi</span>
                    <span class="text-xs bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded-full">min. 10 resi</span>
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
                                $barColor = $vendor->rate >= 40 ? 'bg-gradient-to-r from-rose-500 to-rose-400' : ($vendor->rate >= 20 ? 'bg-gradient-to-r from-amber-500 to-amber-400' : 'bg-gradient-to-r from-emerald-500 to-emerald-400');
                            @endphp
                            <a href="{{ $link }}" class="block group">
                                <div class="flex items-center justify-between text-sm mb-1.5">
                                    <span class="font-semibold text-gray-700 group-hover:text-dbl-green-dark transition-colors">
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-gray-100 text-gray-500 text-[10px] font-bold mr-1.5 align-middle">{{ $index + 1 }}</span>
                                        {{ $vendor->vendor_lm }}
                                    </span>
                                    <span class="text-xs font-bold text-rose-600">{{ $vendor->rate }}% over</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $barColor }} rounded-full bar-grow" style="width: {{ $vendor->rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-400 w-16 text-right tabular-nums">{{ $vendor->over_sla }} / {{ $vendor->total }} resi</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Provinsi & Kota dengan Undelivered Terbanyak -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <h4 class="text-base font-bold text-gray-800 mb-4">Undelivered per Wilayah (Top 5)</h4>

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
                                    <a href="{{ $link }}" class="flex items-center justify-between text-sm bg-gray-50 hover:bg-dbl-green-light/40 hover:border-dbl-green/20 border border-transparent rounded-lg px-3 py-2 transition-all">
                                        <span class="font-medium text-gray-700 truncate">{{ $row->province }}</span>
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
                                    <a href="{{ $link }}" class="flex items-center justify-between text-sm bg-gray-50 hover:bg-dbl-green-light/40 hover:border-dbl-green/20 border border-transparent rounded-lg px-3 py-2 transition-all">
                                        <span class="font-medium text-gray-700 truncate">{{ $row->city_regency }}</span>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Open Issues -->
            <div class="card p-5" x-reveal>
                <h4 class="text-base font-bold text-gray-800 mb-1 flex items-center justify-between">
                    <span>Issue Terbuka</span>
                    <span class="badge bg-rose-50 text-rose-700">
                        <span class="dot bg-rose-500"></span>{{ $issuesTotal }} open
                    </span>
                </h4>
                <p class="text-xs text-gray-400 mb-4">Issue aktif pada scope filter saat ini</p>

                @if($openIssues->isEmpty())
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="icon-chip bg-emerald-50 text-emerald-500 mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-400 font-medium">Tidak ada issue terbuka</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto pr-1 -mr-1">
                        @foreach($openIssues as $issue)
                            <div class="py-3.5 group">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-bold text-gray-800 font-mono">{{ $issue->waybill_no }}</span>
                                    <span class="badge bg-rose-50 text-rose-700 shrink-0">
                                        <span class="dot bg-rose-500"></span>{{ $issue->issue_type }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $issue->description }}</p>
                                <p class="text-[11px] text-gray-400 mt-1.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $issue->province }}{{ $issue->city_regency ? ' · '.$issue->city_regency : '' }}
                                    · {{ $issue->reported_at?->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Dispatch Terbaru (DR-05) -->
            <div class="card p-5" x-reveal x-reveal.delay>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-bold text-gray-800">Dispatch Terbaru</h4>
                    <a href="{{ route('shipments.index', request()->query()) }}"
                       class="inline-flex items-center gap-1 text-xs font-semibold text-dbl-green-dark hover:text-dbl-green transition-colors">
                        Lihat Semua
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>

                @php
                    $statusColor = [
                        'Completed' => 'bg-emerald-50 text-emerald-700',
                        'On Delivery' => 'bg-blue-50 text-blue-700',
                        'Undelivered' => 'bg-rose-50 text-rose-700',
                    ];
                    $statusDot = [
                        'Completed' => 'bg-emerald-500',
                        'On Delivery' => 'bg-blue-500',
                        'Undelivered' => 'bg-rose-500',
                    ];
                @endphp

                @if($recentShipments->isEmpty())
                    <p class="text-sm text-gray-400 py-6 text-center">Belum ada pengiriman pada filter ini</p>
                @else
                    <div class="overflow-x-auto -mx-5 px-5">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-[11px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                    <th class="py-2.5 pr-2 font-bold">Resi</th>
                                    <th class="py-2.5 pr-2 font-bold">Tanggal HO</th>
                                    <th class="py-2.5 pr-2 font-bold">Wilayah</th>
                                    <th class="py-2.5 pr-2 font-bold">Vendor</th>
                                    <th class="py-2.5 pr-2 font-bold">Status</th>
                                    <th class="py-2.5 font-bold">SLA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($recentShipments as $shipment)
                                    <tr class="transition-colors hover:bg-dbl-green-light/20">
                                        <td class="py-3 pr-2 font-mono text-xs font-semibold text-gray-800">{{ $shipment->waybill_no }}</td>
                                        <td class="py-3 pr-2 text-xs text-gray-500">{{ $shipment->ho_date?->format('d M Y') }}</td>
                                        <td class="py-3 pr-2 text-xs text-gray-500">{{ $shipment->city_regency ?? $shipment->province }}</td>
                                        <td class="py-3 pr-2 text-xs text-gray-500">{{ $shipment->vendor_lm ?? '—' }}</td>
                                        <td class="py-3 pr-2">
                                            <span class="badge {{ $statusColor[$shipment->final_status] ?? 'bg-gray-100 text-gray-600' }}">
                                                <span class="dot {{ $statusDot[$shipment->final_status] ?? 'bg-gray-400' }}"></span>
                                                {{ $shipment->final_status ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge {{ $shipment->is_within_sla ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                                <span class="dot {{ $shipment->is_within_sla ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
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

            // Plugin: teks tengah untuk doughnut chart
            const doughnutCenter = {
                id: 'doughnutCenter',
                afterDraw(chart) {
                    const { ctx } = chart;
                    const meta = chart.getDatasetMeta(0);
                    if (!meta || !meta.total) return;

                    const isSla = chart.canvas.id === 'slaChart';
                    const data = chart.data.datasets[0].data;
                    const total = data.reduce((a, b) => a + b, 0);
                    const primary = isSla ? data[0] : total;
                    const pct = total > 0 ? Math.round((primary / total) * 100) : 0;

                    const x = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const y = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;

                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = '800 26px Figtree, ui-sans-serif, sans-serif';
                    ctx.fillStyle = isSla ? '#059669' : '#111827';
                    ctx.fillText(pct + '%', x, y - 8);
                    ctx.font = '600 10px Figtree, ui-sans-serif, sans-serif';
                    ctx.fillStyle = '#9CA3AF';
                    ctx.fillText(isSla ? 'On Time' : 'Total Resi', x, y + 14);
                    ctx.restore();
                }
            };
            Chart.register(doughnutCenter);

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
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} resi (${pct}%)`;
                                }
                            }
                        }
                    }
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
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} resi (${pct}%)`;
                                }
                            }
                        }
                    }
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
                            backgroundColor: (context) => {
                                const { ctx, chartArea } = context.chart;
                                if (!chartArea) return 'rgba(16, 185, 129, 0.15)';
                                const g = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                                g.addColorStop(0, 'rgba(16, 185, 129, 0.02)');
                                g.addColorStop(1, 'rgba(16, 185, 129, 0.25)');
                                return g;
                            },
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#059669',
                            pointBorderWidth: 2,
                            hoverPointRadius: 5,
                            yAxisID: 'y'
                        },
                        {
                            label: 'SLA Rate (%)',
                            data: trendTotalsArr.map((t, i) => t > 0 ? Math.round((trendWithinArr[i] / t) * 1000) / 10 : 0),
                            borderColor: '#111827',
                            backgroundColor: 'transparent',
                            borderDash: [6, 4],
                            borderWidth: 1.5,
                            tension: 0.35,
                            pointRadius: 2,
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
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, maxTicksLimit: 6 },
                            grid: { color: 'rgba(0, 0, 0, 0.04)' }
                        },
                        y1: {
                            beginAtZero: true,
                            max: 100,
                            position: 'right',
                            ticks: { callback: v => v + '%', maxTicksLimit: 5 },
                            grid: { drawOnChartArea: false }
                        },
                        x: { grid: { display: false } }
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
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, maxTicksLimit: 6 },
                            grid: { color: 'rgba(0, 0, 0, 0.04)' }
                        },
                        x: { grid: { display: false } }
                    }
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
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.raw.toLocaleString('id-ID')} resi`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, maxTicksLimit: 6 },
                            grid: { color: 'rgba(0, 0, 0, 0.04)' }
                        },
                        x: { grid: { display: false } }
                    }
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
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, maxTicksLimit: 6 },
                            grid: { color: 'rgba(0, 0, 0, 0.04)' }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 7. Donut Chart - Status BAST
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
                document.getElementById('bastChart').parentElement.innerHTML = '<p class="text-sm text-gray-400 text-center py-8">Belum ada data BAST</p>';
            }

            // 8. Donut Chart - Status Keuangan
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

            // 9. Grouped Bar - SLA MM vs LM per Vendor
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

            // 10. Grouped Bar - Vendor MM Performance
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

            // 11. Donut Chart - Inbound First Mile
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

            // 12. Bar - Status Akhir Detail
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

        });
    </script>
</x-app-layout>
