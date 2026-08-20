<?php

namespace App\Services;

use App\Models\ImportBatch;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\SlaMiddleMile;
use App\Models\SlaLastMile;
use App\Models\SlaAll;
use App\Models\InboundFirstMile;
use App\Models\Vendor;
use App\Support\StatusNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
     * Hitung 7 KPI utama dari query yang sudah difilter.
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
        $provinces = Shipment::select('province')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $cities = $request->filled('province')
            ? Location::where('province', $request->province)->orderBy('city_regency')->pluck('city_regency')
            : Shipment::select('city_regency')
                ->whereNotNull('city_regency')
                ->where('city_regency', '!=', '')
                ->distinct()
                ->orderBy('city_regency')
                ->limit(500)
                ->pluck('city_regency');

        $vendors = Vendor::orderBy('name')->get();
        $statuses = StatusNormalizer::FINAL_STATUSES;

        return compact('provinces', 'cities', 'vendors', 'statuses');
    }

    /**
     * Funnel kepatuhan SLA per tahap (Pickup / Delivery / Last Mile / Vendor),
     * dihitung query-time dari kolom result_* (tanpa kolom baru).
     *
     * @return array<int, array<string, mixed>>
     */
    public function slaStageBreakdown(Builder $query): array
    {
        $stages = [
            ['key' => 'pickup', 'label' => 'Pickup', 'column' => 'pickup_sla_status'],
            ['key' => 'delivery', 'label' => 'Delivery', 'column' => 'delivery_sla_status'],
            ['key' => 'lm', 'label' => 'Last Mile', 'column' => 'lm_sla_status'],
            ['key' => 'vendor', 'label' => 'Vendor', 'column' => 'vendor_sla_status'],
        ];

        $tokens = array_map(
            fn (string $token) => "'".str_replace("'", "''", strtolower(trim($token)))."'",
            StatusNormalizer::slaTokens()
        );
        $inList = implode(',', $tokens);

        $selects = [];
        foreach ($stages as $stage) {
            $column = $stage['column'];
            $selects[] = "SUM(CASE WHEN LOWER(TRIM(COALESCE({$column}, ''))) IN ({$inList}) THEN 1 ELSE 0 END) as {$stage['key']}_within";
            $selects[] = "SUM(CASE WHEN {$column} IS NOT NULL AND TRIM({$column}) <> '' THEN 1 ELSE 0 END) as {$stage['key']}_total";
        }

        $row = (clone $query)->selectRaw(implode(', ', $selects))->first();
        $result = [];

        foreach ($stages as $stage) {
            $total = (int) ($row->{$stage['key'].'_total'} ?? 0);
            $within = (int) ($row->{$stage['key'].'_within'} ?? 0);

            $result[] = [
                'key' => $stage['key'],
                'label' => $stage['label'],
                'total' => $total,
                'within' => $within,
                'rate' => $total > 0 ? round(($within / $total) * 100, 1) : 0,
            ];
        }

        return $result;
    }

    /**
     * Tren volume pengiriman agregasi bulanan (driver-aware: MySQL & SQLite).
     *
     * @return Collection<int, mixed>
     */
    public function monthlyTrend(Builder $query)
    {
        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', ho_date)"
            : "DATE_FORMAT(ho_date, '%Y-%m')";

        return (clone $query)
            ->selectRaw("{$monthExpr} as month, COUNT(*) as total")
            ->whereNotNull('ho_date')
            ->groupByRaw($monthExpr)
            ->orderBy('month')
            ->get();
    }

    /**
     * Rata-rata lead time (hari): HO->Pickup, Pickup->Delivery, HO->Delivery.
     *
     * @return array<string, float>
     */
    public function leadTimes(Builder $query): array
    {
        $diff = fn (string $from, string $to): string => DB::connection()->getDriverName() === 'sqlite'
            ? "CAST((julianday({$to}) - julianday({$from})) AS INTEGER)"
            : "TIMESTAMPDIFF(DAY, {$from}, {$to})";

        $row = (clone $query)->selectRaw(
            'AVG(CASE WHEN ho_date IS NOT NULL AND pickup_eta IS NOT NULL THEN '.$diff('ho_date', 'pickup_eta').' END) as ho_to_pickup,'.
            'AVG(CASE WHEN pickup_eta IS NOT NULL AND delivery_eta IS NOT NULL THEN '.$diff('pickup_eta', 'delivery_eta').' END) as pickup_to_delivery,'.
            'AVG(CASE WHEN ho_date IS NOT NULL AND delivery_eta IS NOT NULL THEN '.$diff('ho_date', 'delivery_eta').' END) as ho_to_delivery'
        )->first();

        return [
            'ho_to_pickup' => round((float) ($row->ho_to_pickup ?? 0), 1),
            'pickup_to_delivery' => round((float) ($row->pickup_to_delivery ?? 0), 1),
            'ho_to_delivery' => round((float) ($row->ho_to_delivery ?? 0), 1),
        ];
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
     * Vendor dengan tingkat over-SLA tertinggi (minimal $minVolume pengiriman).
     *
     * @return Collection<int, mixed>
     */
    public function worstVendors(Builder $query, int $minVolume = 10, int $limit = 5)
    {
        $rows = (clone $query)
            ->select('vendor_lm', DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_within_sla = 1 THEN 0 ELSE 1 END) as over_sla'))
            ->whereNotNull('vendor_lm')
            ->where('vendor_lm', '!=', '')
            ->groupBy('vendor_lm')
            ->havingRaw('COUNT(*) >= ?', [$minVolume])
            ->orderByRaw('SUM(CASE WHEN is_within_sla = 1 THEN 0 ELSE 1 END) * 1.0 / COUNT(*) DESC')
            ->limit($limit)
            ->get();

        $vendorIds = Vendor::whereIn('name', $rows->pluck('vendor_lm')->all())->pluck('id', 'name');

        return $rows->map(function ($row) use ($vendorIds) {
            $row->rate = $row->total > 0 ? round(($row->over_sla / $row->total) * 100, 1) : 0;
            $row->vendor_id = $vendorIds[$row->vendor_lm] ?? null;

            return $row;
        })->values();
    }

    /**
     * Provinsi & kota dengan jumlah Undelivered terbanyak.
     *
     * @return array<string, Collection>
     */
    public function worstRegions(Builder $query, int $limit = 5): array
    {
        $undeliveredExpr = "SUM(CASE WHEN final_status = 'Undelivered' THEN 1 ELSE 0 END)";

        $provinces = (clone $query)
            ->select('province', DB::raw('COUNT(*) as total'), DB::raw("{$undeliveredExpr} as undelivered"))
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->groupBy('province')
            ->orderByDesc('undelivered')
            ->limit($limit)
            ->get()
            ->reject(fn ($row) => (int) $row->undelivered === 0)
            ->values();

        $cities = (clone $query)
            ->select('city_regency', DB::raw('COUNT(*) as total'), DB::raw("{$undeliveredExpr} as undelivered"))
            ->whereNotNull('city_regency')
            ->where('city_regency', '!=', '')
            ->groupBy('city_regency')
            ->orderByDesc('undelivered')
            ->limit($limit)
            ->get()
            ->reject(fn ($row) => (int) $row->undelivered === 0)
            ->values();

        return ['provinces' => $provinces, 'cities' => $cities];
    }

    /**
     * Issue terbuka yang terkait dengan hasil filter saat ini.
     *
     * @return array<string, mixed>
     */
    public function openIssues(Request $request, int $limit = 10): array
    {
        $applyFilters = function (Builder $q) use ($request) {
            $this->applyFilters($q, $request);
        };

        $items = ShipmentIssue::query()
            ->join('shipments', 'shipments.id', '=', 'shipment_issues.shipment_id')
            ->where('shipment_issues.status', 'open')
            ->where(function (Builder $q) use ($applyFilters) {
                $applyFilters($q);
            })
            ->orderByDesc('shipment_issues.reported_at')
            ->limit($limit)
            ->get([
                'shipment_issues.id',
                'shipment_issues.issue_type',
                'shipment_issues.description',
                'shipment_issues.reported_at',
                'shipments.waybill_no',
                'shipments.province',
                'shipments.city_regency',
                'shipments.vendor_lm',
                'shipments.final_status',
            ]);

        $total = ShipmentIssue::query()
            ->join('shipments', 'shipments.id', '=', 'shipment_issues.shipment_id')
            ->where('shipment_issues.status', 'open')
            ->where(function (Builder $q) use ($applyFilters) {
                $applyFilters($q);
            })
            ->count();

        return compact('items', 'total');
    }

    /**
     * Import batch terakhir yang selesai (untuk indikator kesegaran data).
     */
    public function latestImport(): ?ImportBatch
    {
        return ImportBatch::query()
            ->where('status', 'completed')
            ->latest('created_at')
            ->first();
    }

    /**
     * Distribusi status BAST (Belum, Proses, Selesai) dan status Keuangan.
     *
     * @return array{bast: Collection, finance: Collection, bastTotal: int, financeTotal: int}
     */
    public function bastFinanceBreakdown(): array
    {
        $bastData = Shipment::select('bast_status', DB::raw('COUNT(*) as total'))
            ->whereNotNull('bast_status')
            ->where('bast_status', '!=', '')
            ->groupBy('bast_status')
            ->orderByDesc('total')
            ->get();

        $financeData = Shipment::select('finance_status', DB::raw('COUNT(*) as total'))
            ->whereNotNull('finance_status')
            ->where('finance_status', '!=', '')
            ->groupBy('finance_status')
            ->orderByDesc('total')
            ->get();

        $bastTotal = $bastData->sum('total');
        $financeTotal = $financeData->sum('total');

        return compact('bastData', 'financeData', 'bastTotal', 'financeTotal');
    }

    /**
     * Perbandingan SLA MM vs LM per vendor (grouped bar chart).
     * Mengambil vendor_mm dari sla_middle_miles dan vendor_lm dari sla_last_miles.
     *
     * @return Collection<int, mixed>
     */
    public function slaMmVsLmComparison(): Collection
    {
        $mmTokens = StatusNormalizer::slaTokens();
        $mmIn = implode(',', array_map(fn ($t) => "'".str_replace("'", "''", $t)."'", $mmTokens));

        $mm = SlaMiddleMile::select(
            'vendor_mm as vendor',
            DB::raw("SUM(CASE WHEN LOWER(TRIM(COALESCE(result_mm, ''))) IN ({$mmIn}) THEN 1 ELSE 0 END) as within_mm"),
            DB::raw('COUNT(*) as total_mm')
        )
            ->whereNotNull('vendor_mm')
            ->where('vendor_mm', '!=', '')
            ->groupBy('vendor_mm')
            ->get()
            ->keyBy('vendor');

        $lm = SlaLastMile::select(
            'vendor_lm as vendor',
            DB::raw("SUM(CASE WHEN LOWER(TRIM(COALESCE(result_lm, ''))) IN ({$mmIn}) THEN 1 ELSE 0 END) as within_lm"),
            DB::raw('COUNT(*) as total_lm')
        )
            ->whereNotNull('vendor_lm')
            ->where('vendor_lm', '!=', '')
            ->groupBy('vendor_lm')
            ->get()
            ->keyBy('vendor');

        $allVendors = $mm->keys()->merge($lm->keys())->unique()->values();

        return $allVendors->map(function ($vendor) use ($mm, $lm) {
            $mmTotal = $mm[$vendor]->total_mm ?? 0;
            $mmWithin = $mm[$vendor]->within_mm ?? 0;
            $lmTotal = $lm[$vendor]->total_lm ?? 0;
            $lmWithin = $lm[$vendor]->within_lm ?? 0;

            return (object) [
                'vendor' => $vendor,
                'mm_within' => $mmWithin,
                'mm_total' => $mmTotal,
                'mm_rate' => $mmTotal > 0 ? round(($mmWithin / $mmTotal) * 100, 1) : 0,
                'lm_within' => $lmWithin,
                'lm_total' => $lmTotal,
                'lm_rate' => $lmTotal > 0 ? round(($lmWithin / $lmTotal) * 100, 1) : 0,
            ];
        })->sortByDesc('mm_total')->take(10)->values();
    }

    /**
     * Performa vendor Middle Mile: volume, SLA rate, dan rata-rata waktu tempuh.
     *
     * @return Collection<int, mixed>
     */
    public function vendorMmPerformance(int $minVolume = 5, int $limit = 10): Collection
    {
        $tokens = StatusNormalizer::slaTokens();
        $inList = implode(',', array_map(fn ($t) => "'".str_replace("'", "''", $t)."'", $tokens));

        return SlaMiddleMile::select(
            'vendor_mm as vendor',
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN LOWER(TRIM(COALESCE(result_mm, ''))) IN ({$inList}) THEN 1 ELSE 0 END) as within_sla"),
            DB::raw('AVG(TIMESTAMPDIFF(HOUR, eta_mm, tgl_sampai_kota_tujuan)) as avg_hours')
        )
            ->whereNotNull('vendor_mm')
            ->where('vendor_mm', '!=', '')
            ->groupBy('vendor_mm')
            ->havingRaw('COUNT(*) >= ?', [$minVolume])
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->rate = $row->total > 0 ? round(($row->within_sla / $row->total) * 100, 1) : 0;
                $row->avg_hours = round((float) ($row->avg_hours ?? 0), 1);

                return $row;
            });
    }

    /**
     * Distribusi status Inbound First Mile ( dari FM ke gudang pusat).
     *
     * @return Collection<int, mixed>
     */
    public function inboundFirstMileMetrics(): Collection
    {
        return InboundFirstMile::select('status_inbound', DB::raw('COUNT(*) as total'))
            ->whereNotNull('status_inbound')
            ->where('status_inbound', '!=', '')
            ->groupBy('status_inbound')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Distribusi detail Status Akhir (termasuk varian seperti Return to HO, dll).
     *
     * @return Collection<int, mixed>
     */
    public function statusAkhirDistribution(Builder $query): Collection
    {
        return (clone $query)
            ->select('final_status', DB::raw('COUNT(*) as total'))
            ->groupBy('final_status')
            ->orderByDesc('total')
            ->get();
    }
}
