<?php

namespace App\Services;

use App\Models\InboundFirstMile;
use App\Models\SlaLastMile;
use App\Models\SlaMiddleMile;
use App\Models\Shipment;
use App\Models\Vendor;
use App\Support\StatusNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
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

        $inList = StatusNormalizer::sqlInList();

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
     * Distribusi status BAST (Belum, Proses, Selesai) dan status Keuangan.
     *
     * @return array{bast: Collection, finance: Collection, bastTotal: int, financeTotal: int}
     */
    public function bastFinanceBreakdown(): array
    {
        $cached = Cache::remember('dashboard:bast_finance', 300, function () {
            $bastData = Shipment::select('bast_status', DB::raw('COUNT(*) as total'))
                ->whereNotNull('bast_status')
                ->where('bast_status', '!=', '')
                ->groupBy('bast_status')
                ->orderByDesc('total')
                ->get()
                ->toArray();

            $financeData = Shipment::select('finance_status', DB::raw('COUNT(*) as total'))
                ->whereNotNull('finance_status')
                ->where('finance_status', '!=', '')
                ->groupBy('finance_status')
                ->orderByDesc('total')
                ->get()
                ->toArray();

            return [
                'bastData' => $bastData,
                'financeData' => $financeData,
                'bastTotal' => collect($bastData)->sum('total'),
                'financeTotal' => collect($financeData)->sum('total'),
            ];
        });

        $cached['bastData'] = Shipment::hydrate($cached['bastData']);
        $cached['financeData'] = Shipment::hydrate($cached['financeData']);

        return $cached;
    }

    /**
     * Perbandingan SLA MM vs LM per vendor (grouped bar chart).
     * Mengambil vendor_mm dari sla_middle_miles dan vendor_lm dari sla_last_miles.
     *
     * @return Collection<int, mixed>
     */
    public function slaMmVsLmComparison(): Collection
    {
        $inList = StatusNormalizer::sqlInList();

        return collect(Cache::remember('dashboard:sla_mm_lm', 300, function () use ($inList) {
            $mm = SlaMiddleMile::select(
                'vendor_mm as vendor',
                DB::raw("SUM(CASE WHEN LOWER(TRIM(COALESCE(result_mm, ''))) IN ({$inList}) THEN 1 ELSE 0 END) as within_mm"),
                DB::raw('COUNT(*) as total_mm')
            )
                ->whereNotNull('vendor_mm')
                ->where('vendor_mm', '!=', '')
                ->groupBy('vendor_mm')
                ->get()
                ->keyBy('vendor');

            $lm = SlaLastMile::select(
                'vendor_lm as vendor',
                DB::raw("SUM(CASE WHEN LOWER(TRIM(COALESCE(result_lm, ''))) IN ({$inList}) THEN 1 ELSE 0 END) as within_lm"),
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

                return [
                    'vendor' => $vendor,
                    'mm_within' => $mmWithin,
                    'mm_total' => $mmTotal,
                    'mm_rate' => $mmTotal > 0 ? round(($mmWithin / $mmTotal) * 100, 1) : 0,
                    'lm_within' => $lmWithin,
                    'lm_total' => $lmTotal,
                    'lm_rate' => $lmTotal > 0 ? round(($lmWithin / $lmTotal) * 100, 1) : 0,
                ];
            })->sortByDesc('mm_total')->take(10)->values()->all();
        }));
    }

    /**
     * Performa vendor Middle Mile: volume, SLA rate, dan rata-rata waktu tempuh.
     *
     * @return Collection<int, mixed>
     */
    public function vendorMmPerformance(int $minVolume = 5, int $limit = 10): Collection
    {
        $inList = StatusNormalizer::sqlInList();

        return collect(Cache::remember('dashboard:vendor_mm', 300, function () use ($inList, $minVolume, $limit) {
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
                ->map(fn ($row) => [
                    'vendor' => $row->vendor,
                    'total' => (int) $row->total,
                    'within_sla' => (int) $row->within_sla,
                    'rate' => $row->total > 0 ? round(($row->within_sla / $row->total) * 100, 1) : 0,
                    'avg_hours' => round((float) ($row->avg_hours ?? 0), 1),
                ])
                ->all();
        }));
    }

    /**
     * Distribusi status Inbound First Mile ( dari FM ke gudang pusat).
     *
     * @return Collection<int, mixed>
     */
    public function inboundFirstMileMetrics(): Collection
    {
        return collect(Cache::remember('dashboard:inbound_fm', 300, fn () =>
            InboundFirstMile::select('status_inbound', DB::raw('COUNT(*) as total'))
                ->whereNotNull('status_inbound')
                ->where('status_inbound', '!=', '')
                ->groupBy('status_inbound')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($row) => [
                    'status_inbound' => $row->status_inbound,
                    'total' => (int) $row->total,
                ])
                ->all()
        ));
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

    /**
     * Top N provinsi berdasarkan volume pengiriman.
     *
     * @return Collection<int, mixed>
     */
    public function provinceDistribution(Builder $query, int $limit = 5): Collection
    {
        return (clone $query)
            ->select('province', DB::raw('count(*) as total'))
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    /**
     * Top N vendor berdasarkan volume & SLA rate.
     *
     * @return Collection<int, mixed>
     */
    public function vendorDistribution(Builder $query, int $limit = 5): Collection
    {
        return (clone $query)
            ->select(
                'vendor_lm',
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN is_within_sla = 1 THEN 1 ELSE 0 END) as on_time')
            )
            ->whereNotNull('vendor_lm')
            ->where('vendor_lm', '!=', '')
            ->groupBy('vendor_lm')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }
}
