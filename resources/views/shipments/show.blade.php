<x-app-layout>
    <x-slot name="header">
        Detail Pengiriman #{{ $shipment->waybill_no }}
    </x-slot>

    <div class="space-y-6">
        <!-- Back Link -->
        <a href="{{ route('shipments.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-dbl-green-dark hover:text-dbl-green transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar
        </a>

        <!-- Status Header -->
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 font-mono">{{ $shipment->waybill_no }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $shipment->school_name ?? '—' }} · NPSN: {{ $shipment->npsn ?? '—' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $statusColor = ['Completed' => 'bg-emerald-50 text-emerald-700', 'On Delivery' => 'bg-blue-50 text-blue-700', 'Undelivered' => 'bg-rose-50 text-rose-700'];
                        $statusDot = ['Completed' => 'bg-emerald-500', 'On Delivery' => 'bg-blue-500', 'Undelivered' => 'bg-rose-500'];
                    @endphp
                    <span class="badge {{ $statusColor[$shipment->final_status] ?? 'bg-gray-100 text-gray-600' }}">
                        <span class="dot {{ $statusDot[$shipment->final_status] ?? 'bg-gray-400' }}"></span>
                        {{ $shipment->final_status }}
                    </span>
                    <span class="badge {{ $shipment->is_within_sla ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                        <span class="dot {{ $shipment->is_within_sla ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $shipment->is_within_sla ? 'Within SLA' : 'Over SLA' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Lifecycle Timeline -->
        <div class="card p-6" x-reveal>
            <div class="flex items-center gap-2.5 mb-5">
                <span class="icon-chip bg-indigo-100 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </span>
                <h3 class="text-base font-bold text-gray-800">Timeline Pengiriman</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-y-6 md:gap-x-3">
                @foreach($timeline as $index => $step)
                    @php
                        $styles = [
                            'done' => ['ring' => 'bg-emerald-50 border-emerald-200 text-emerald-600', 'dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-50 text-emerald-700'],
                            'current' => ['ring' => 'bg-blue-50 border-blue-300 text-blue-600', 'dot' => 'bg-blue-500 animate-pulse-soft', 'badge' => 'bg-blue-50 text-blue-700'],
                            'issue' => ['ring' => 'bg-rose-50 border-rose-300 text-rose-600', 'dot' => 'bg-rose-500', 'badge' => 'bg-rose-50 text-rose-700'],
                            'pending' => ['ring' => 'bg-gray-50 border-gray-200 text-gray-400', 'dot' => 'bg-gray-300', 'badge' => 'bg-gray-100 text-gray-500'],
                        ];
                        $style = $styles[$step['state']];
                        $icons = [
                            'done' => 'M5 13l4 4L19 7',
                            'current' => 'M12 8v4l3 3',
                            'issue' => 'M12 9v2m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z',
                            'pending' => 'M6 18L18 6M6 6l12 12',
                        ];
                    @endphp
                    <div class="relative flex md:flex-col items-start md:items-center gap-3">
                        @if(! $loop->last)
                            <div class="hidden md:block absolute top-5 left-[60%] w-[80%] h-0.5 {{ $step['state'] === 'done' ? 'bg-emerald-200' : 'bg-gray-200' }}"></div>
                        @endif

                        <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center shrink-0 z-10 {{ $style['ring'] }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $icons[$step['state']] }}" />
                            </svg>
                        </div>

                        <div class="md:text-center min-w-0">
                            <p class="text-xs font-bold text-gray-800 truncate">{{ $step['label'] }}</p>
                            <p class="text-[11px] font-semibold tabular-nums mt-0.5 {{ $step['date'] ? 'text-gray-600' : 'text-gray-400' }}">
                                {{ $step['date'] ?? '—' }}
                            </p>
                            <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full font-semibold max-w-full truncate {{ $style['badge'] }}" title="{{ $step['caption'] }}">
                                {{ $step['caption'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Info Utama -->
            <div class="lg:col-span-2 card p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4">Informasi Pengiriman</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="field-label">No Manifest</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->manifest_no ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Tanggal HO</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->ho_date?->format('d M Y, H:i') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Vendor Last Mile</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->vendor_lm ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Provinsi</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->province ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Kota/Kabupaten</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->city_regency ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Status Update</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->status_update ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Status BAST</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->bast_status ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Tanggal BAST</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->bast_date?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="field-label">Status Keuangan</p>
                        <p class="text-sm font-medium text-gray-800">{{ $shipment->finance_status ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- SLA Info -->
            <div class="card p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4">Status SLA</h3>
                <div class="space-y-4">
                    @php
                        $slaStages = [
                            ['label' => 'Pickup', 'eta' => $shipment->pickup_eta, 'status' => $shipment->pickup_sla_status, 'result' => $shipment->pickup_result],
                            ['label' => 'Delivery', 'eta' => $shipment->delivery_eta, 'status' => $shipment->delivery_sla_status, 'result' => $shipment->delivery_result],
                            ['label' => 'Last Mile', 'eta' => null, 'status' => $shipment->lm_sla_status, 'result' => $shipment->lm_result],
                            ['label' => 'Vendor', 'eta' => null, 'status' => $shipment->vendor_sla_status, 'result' => $shipment->vendor_result],
                        ];
                    @endphp
                    @foreach($slaStages as $stage)
                        @php
                            $isWithin = \App\Support\StatusNormalizer::withinSla($stage['status']);
                        @endphp
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-600">{{ $stage['label'] }}</span>
                            @if($stage['status'])
                                <span class="badge {{ $isWithin ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    <span class="dot {{ $isWithin ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    {{ $stage['status'] }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Issues -->
        @if($shipment->issues->count() > 0)
            <div class="card p-6">
                <h3 class="text-base font-bold text-gray-800 mb-4">Issue Terkait ({{ $shipment->issues->count() }})</h3>
                <div class="divide-y divide-gray-100">
                    @foreach($shipment->issues as $issue)
                        <div class="py-4">
                            <div class="flex items-center justify-between">
                                <span class="badge {{ $issue->status === 'open' ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    <span class="dot {{ $issue->status === 'open' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                    {{ $issue->status === 'open' ? 'Open' : 'Resolved' }}
                                </span>
                                <span class="text-xs text-gray-400">{{ $issue->reported_at?->format('d M Y H:i') }}</span>
                            </div>
                            <p class="text-sm font-bold text-gray-800 mt-2">{{ $issue->issue_type }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $issue->description }}</p>
                            @if($issue->resolvedBy)
                                <p class="text-xs text-gray-400 mt-2">Diselesaikan oleh {{ $issue->resolvedBy->name }} · {{ $issue->resolved_at?->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
