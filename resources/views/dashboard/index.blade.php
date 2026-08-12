<x-app-layout>
    <x-slot name="header">
        Dashboard Analytics Operasional Pengiriman
    </x-slot>

    <div class="space-y-6">

        <!-- ==================== SECTION 1: FILTER MULTI-KRITERIA (FR-09) ==================== -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Filter Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Mulai (HO)</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                </div>

                <!-- Filter Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Tanggal Selesai (HO)</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                        class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                </div>

                <!-- Filter Provinsi -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Provinsi</label>
                    <select name="province" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov }}" {{ request('province') == $prov ? 'selected' : '' }}>{{ $prov }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Vendor -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Vendor Last Mile</label>
                    <select name="vendor_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status & Tombol Aksi -->
                <div class="flex items-end space-x-2">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Status Akhir</label>
                        <select name="status" class="w-full text-sm rounded-lg border-gray-300 focus:border-dbl-green focus:ring-dbl-green">
                            <option value="">Semua Status</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="On Delivery" {{ request('status') == 'On Delivery' ? 'selected' : '' }}>On Delivery</option>
                            <option value="Undelivered" {{ request('status') == 'Undelivered' ? 'selected' : '' }}>Undelivered</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-dbl-dark text-white rounded-lg text-sm font-semibold hover:bg-black transition-colors flex items-center h-[38px]">
                        Filter
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date', 'province', 'vendor_id', 'status']))
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-sm font-semibold h-[38px] flex items-center">
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>


        <!-- ==================== SECTION 2: KPI CARDS (PRD 9.1) ==================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            
            <!-- Card 1: Total Pengiriman -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Resi</span>
                    <span class="p-2 bg-gray-100 rounded-lg text-gray-700">📦</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-gray-900">{{ number_format($totalShipments) }}</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Total shipment terdaftar</p>
                </div>
            </div>

            <!-- Card 2: Completed -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Completed</span>
                    <span class="p-2 bg-emerald-100 text-emerald-800 rounded-lg font-bold text-xs">✓</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-emerald-600">{{ number_format($completed) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1">
                        {{ $totalShipments > 0 ? round(($completed / $totalShipments) * 100, 1) : 0 }}% dari total
                    </span>
                </div>
            </div>

            <!-- Card 3: On Delivery -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">On Delivery</span>
                    <span class="p-2 bg-blue-100 text-blue-800 rounded-lg font-bold text-xs">🚚</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-blue-600">{{ number_format($onDelivery) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-full mt-1">
                        Proses pengiriman
                    </span>
                </div>
            </div>

            <!-- Card 4: Undelivered -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Undelivered</span>
                    <span class="p-2 bg-rose-100 text-rose-800 rounded-lg font-bold text-xs">⚠️</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-rose-600">{{ number_format($undelivered) }}</h3>
                    <span class="inline-flex items-center text-[11px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full mt-1">
                        Kendala operasional
                    </span>
                </div>
            </div>

            <!-- Card 5: SLA Achievement Rate -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between bg-gradient-to-br from-white to-dbl-green-light/30">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">SLA Rate</span>
                    <span class="p-2 bg-dbl-green text-dbl-dark font-extrabold rounded-lg text-xs">🎯</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-dbl-green-dark">{{ $slaAchievementRate }}%</h3>
                    <p class="text-[11px] text-gray-600 font-medium mt-0.5">Kepatuhan SLA</p>
                </div>
            </div>

            <!-- Card 6: Over SLA -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Over SLA</span>
                    <span class="p-2 bg-amber-100 text-amber-800 rounded-lg font-bold text-xs">⏰</span>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-black text-amber-600">{{ number_format($overSla) }}</h3>
                    <p class="text-[11px] text-amber-700 font-medium mt-0.5">Melewati batas SLA</p>
                </div>
            </div>

        </div>


        <!-- ==================== SECTION 3: VISUALISASI GRAFIK (PRD 9.2) ==================== -->
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

            <!-- Chart 3: Top 5 Volume per Provinsi -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4">
                    Distribusi Pengiriman per Provinsi (Top 5)
                </h4>
                <div class="h-64">
                    <canvas id="provinceChart"></canvas>
                </div>
            </div>

            <!-- Chart 4: Performa Vendor Last Mile -->
            <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
                <h4 class="text-base font-bold text-gray-800 mb-4">
                    Top 5 Vendor Last Mile (Volume & Kepatuhan)
                </h4>
                <div class="h-64">
                    <canvas id="vendorChart"></canvas>
                </div>
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
                            {{ $statusChartData['Completed'] }},
                            {{ $statusChartData['On Delivery'] }},
                            {{ $statusChartData['Undelivered'] }}
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
                            {{ $slaChartData['Within SLA'] }},
                            {{ $slaChartData['Over SLA'] }}
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

            // 3. Bar Chart - Top 5 Provinsi
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
                    scales: { y: { beginAtZero: true } }
                }
            });

            // 4. Grouped Bar Chart - Top 5 Vendor
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
                    scales: { y: { beginAtZero: true } }
                }
            });

        });
    </script>
</x-app-layout>