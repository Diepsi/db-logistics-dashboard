<?php

namespace App\Services;

use App\Models\ImportBatch;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\Vendor;
use App\Support\StatusNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Query shipment dengan filter global yang dipakai dashboard, tabel data, dan laporan.
     */
    public function shipmentQuery(Request $request): Builder
    {
        return $this->applyFilters(Shipment::query(), $request);
    }

    /**
     * Terapkan filter global (tanggal HO, provinsi, kota, vendor, status, SLA).
     * Kolom diberi prefix "shipments." agar aman dipakai pada query join.
     */
    public function applyFilters(Builder $query, Request $request, ?array $dateRange = null): Builder
    {
        $startDate = $dateRange['start'] ?? null;
        $endDate = $dateRange['end'] ?? null;

        if ($startDate === null && $endDate === null && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        }

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('shipments.ho_date', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        return $query
            ->when($request->filled('province'), fn (Builder $q) => $q->where('shipments.province', $request->province))
            ->when($request->filled('city_regency'), fn (Builder $q) => $q->where('shipments.city_regency', $request->city_regency))
            ->when($request->filled('vendor_id'), fn (Builder $q) => $q->where('shipments.vendor_id', $request->vendor_id))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('shipments.final_status', $request->status))
            ->when($request->filled('sla'), fn (Builder $q) => $q->where('shipments.is_within_sla', $request->sla === 'over' ? 0 : 1));
    }

    /**
     * Hitung KPI utama dari query yang sudah difilter.
     *
     * @return array<string, int|float>
     */
    public function kpis(Builder $query): array
    {
        $total = (clone $query)->count();
        $withinSla = (clone $query)->where('is_within_sla', true)->count();

        return [
            'totalShipments' => $total,
            'completed' => (clone $query)->where('final_status', 'Completed')->count(),
            'onDelivery' => (clone $query)->where('final_status', 'On Delivery')->count(),
            'undelivered' => (clone $query)->where('final_status', 'Undelivered')->count(),
            'withinSla' => $withinSla,
            'overSla' => (clone $query)->where('is_within_sla', false)->count(),
            'slaAchievementRate' => $total > 0 ? round(($withinSla / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Data opsi dropdown filter global (dipakai dashboard, tabel data, dan laporan).
     *
     * @return array<string, mixed>
     */
    public function filterOptions(Request $request): array
    {
        $provinces = collect(
            Cache::remember('dashboard:provinces', 300, fn () =>
                Shipment::select('province')
                    ->whereNotNull('province')
                    ->where('province', '!=', '')
                    ->distinct()
                    ->orderBy('province')
                    ->pluck('province')
                    ->all()
            )
        );

        $cities = $request->filled('province')
            ? Location::where('province', $request->province)->orderBy('city_regency')->pluck('city_regency')
            : Shipment::select('city_regency')
                ->whereNotNull('city_regency')
                ->where('city_regency', '!=', '')
                ->distinct()
                ->orderBy('city_regency')
                ->limit(500)
                ->pluck('city_regency');

        $vendors = Vendor::hydrate(
            Cache::remember('dashboard:vendors', 300, fn () =>
                Vendor::orderBy('name')->get()->toArray()
            )
        );
        $statuses = StatusNormalizer::FINAL_STATUSES;

        return compact('provinces', 'cities', 'vendors', 'statuses');
    }

    /**
     * KPI periode sebelumnya (rentang sama panjang tepat sebelum start_date).
     * Mengembalikan null jika filter tanggal tidak aktif.
     *
     * @return array<string, int>|null
     */
    public function previousPeriodKpis(Request $request): ?array
    {
        if (! $request->filled('start_date') || ! $request->filled('end_date')) {
            return null;
        }

        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = abs($end->diffInDays($start)) + 1;
        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1);

        $query = $this->applyFilters(Shipment::query(), $request, [
            'start' => $prevStart->format('Y-m-d'),
            'end' => $prevEnd->format('Y-m-d'),
        ]);

        $row = (clone $query)->selectRaw(
            'COUNT(*) as total,'.
            "SUM(CASE WHEN final_status = 'Completed' THEN 1 ELSE 0 END) as completed,".
            "SUM(CASE WHEN final_status = 'On Delivery' THEN 1 ELSE 0 END) as on_delivery,".
            "SUM(CASE WHEN final_status = 'Undelivered' THEN 1 ELSE 0 END) as undelivered,".
            'SUM(CASE WHEN is_within_sla = 1 THEN 1 ELSE 0 END) as within_sla'
        )->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'completed' => (int) ($row->completed ?? 0),
            'on_delivery' => (int) ($row->on_delivery ?? 0),
            'undelivered' => (int) ($row->undelivered ?? 0),
            'within_sla' => (int) ($row->within_sla ?? 0),
        ];
    }

    /**
     * Selisih persentase current vs previous. Null jika previous 0.
     */
    public static function delta(int $current, int $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Tren volume pengiriman harian (volume + SLA rate) untuk chart utama dashboard.
     *
     * @return Collection<int, mixed>
     */
    public function dailyTrend(Builder $query): Collection
    {
        return (clone $query)
            ->selectRaw('DATE(ho_date) as date, COUNT(*) as total, SUM(CASE WHEN is_within_sla = 1 THEN 1 ELSE 0 END) as within_sla')
            ->whereNotNull('ho_date')
            ->groupByRaw('DATE(ho_date)')
            ->orderBy('date')
            ->get();
    }

    /**
     * Jumlah issue berstatus open (untuk KPI Active Issues).
     */
    public function openIssueCount(): int
    {
        return ShipmentIssue::query()->where('status', 'open')->count();
    }

    /**
     * Daftar pengiriman yang perlu perhatian: issue terbuka + over SLA / undelivered.
     * Setiap item membawa shipment_id untuk quick-action menuju detail pengiriman.
     *
     * @return array<int, array<string, mixed>>
     */
    public function needsAttention(Request $request, int $limit = 8): array
    {
        $issues = ShipmentIssue::query()
            ->join('shipments', 'shipments.id', '=', 'shipment_issues.shipment_id')
            ->where('shipment_issues.status', 'open')
            ->where(function (Builder $q) use ($request) {
                $this->applyFilters($q, $request);
            })
            ->orderByDesc('shipment_issues.reported_at')
            ->limit($limit)
            ->get([
                'shipment_issues.id',
                'shipment_issues.issue_type',
                'shipment_issues.description',
                'shipment_issues.reported_at',
                'shipments.id as shipment_id',
                'shipments.waybill_no',
                'shipments.province',
                'shipments.city_regency',
                'shipments.vendor_lm',
                'shipments.final_status',
            ])
            ->map(fn ($row) => [
                'type' => 'issue',
                'severity' => 2,
                'shipment_id' => $row->shipment_id,
                'waybill_no' => $row->waybill_no,
                'location' => trim(($row->city_regency ?? '').($row->province ? ', '.$row->province : '')),
                'vendor_lm' => $row->vendor_lm,
                'status' => $row->final_status,
                'label' => $row->issue_type ?: 'Issue',
                'detail' => $row->description,
                'at' => $row->reported_at,
            ]);

        $delays = $this->applyFilters(Shipment::query(), $request)
            ->where(function (Builder $q) {
                $q->where('is_within_sla', false)
                    ->orWhere('final_status', 'Undelivered');
            })
            ->orderByDesc('ho_date')
            ->limit($limit)
            ->get([
                'shipments.id as shipment_id',
                'shipments.waybill_no',
                'shipments.province',
                'shipments.city_regency',
                'shipments.vendor_lm',
                'shipments.final_status',
                'shipments.is_within_sla',
                'shipments.ho_date as at',
            ])
            ->map(fn ($row) => [
                'type' => 'delay',
                'severity' => $row->is_within_sla ? 1 : 2,
                'shipment_id' => $row->shipment_id,
                'waybill_no' => $row->waybill_no,
                'location' => trim(($row->city_regency ?? '').($row->province ? ', '.$row->province : '')),
                'vendor_lm' => $row->vendor_lm,
                'status' => $row->final_status,
                'label' => $row->is_within_sla ? 'Undelivered' : 'Over SLA',
                'detail' => null,
                'at' => $row->at,
            ]);

        return $issues->concat($delays)
            ->sortByDesc('severity')
            ->sortByDesc('at')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * Feed aktivitas terbaru: batch impor + pengiriman yang baru diperbarui.
     *
     * @return array<int, array<string, mixed>>
     */
    public function activityFeed(int $limit = 8): array
    {
        $imports = ImportBatch::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->limit($limit)
            ->get(['id', 'file_name', 'status', 'total_rows', 'uploaded_by', 'created_at'])
            ->map(fn ($batch) => [
                'type' => 'import',
                'title' => $batch->file_name,
                'meta' => 'Import '.($batch->status === 'completed' ? 'selesai' : $batch->status)
                    .' — '.number_format((int) $batch->total_rows).' baris'
                    .($batch->user ? ' oleh '.$batch->user->name : ''),
                'status' => $batch->status,
                'at' => $batch->created_at,
                'link' => route('imports.index'),
            ]);

        $shipments = Shipment::query()
            ->latest('updated_at')
            ->limit($limit)
            ->get(['id', 'waybill_no', 'final_status', 'province', 'city_regency', 'updated_at'])
            ->map(fn ($shipment) => [
                'type' => 'shipment',
                'title' => $shipment->waybill_no,
                'meta' => trim(($shipment->city_regency ?? '').($shipment->province ? ', '.$shipment->province : '')).': '.$shipment->final_status,
                'status' => $shipment->final_status,
                'at' => $shipment->updated_at,
                'link' => route('shipments.show', $shipment->id),
            ]);

        return $imports->concat($shipments)
            ->sortByDesc('at')
            ->take($limit)
            ->values()
            ->all();
    }

    /**
     * Import batch terakhir yang selesai (untuk indikator kesegaran data).
     */
    public function latestImport(): ?ImportBatch
    {
        $row = Cache::remember('dashboard:latest_import', 120, fn () =>
            ImportBatch::query()
                ->where('status', 'completed')
                ->latest('created_at')
                ->first()
                ?->toArray()
        );

        return $row !== null ? ImportBatch::hydrate([$row])->first() : null;
    }
}
