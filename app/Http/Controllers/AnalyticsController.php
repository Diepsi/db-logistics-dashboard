<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Halaman analitik historis mendalam (chart kompleks dipindah dari dashboard).
     */
    public function __construct(
        private readonly AnalyticsService $analytics,
        private readonly DashboardService $dashboard
    ) {}

    public function index(Request $request)
    {
        $query = $this->dashboard->shipmentQuery($request);
        $kpis = $this->dashboard->kpis($query);
        $filterOptions = $this->dashboard->filterOptions($request);

        $trendMonthly = $this->analytics->monthlyTrend($query);
        $provinceData = $this->analytics->provinceDistribution($query);
        $vendorData = $this->analytics->vendorDistribution($query);
        $slaStageBreakdown = $this->analytics->slaStageBreakdown($query);
        $leadTimes = $this->analytics->leadTimes($query);
        $worstVendors = $this->analytics->worstVendors($query);
        $worstRegions = $this->analytics->worstRegions($query);
        $bastFinance = $this->analytics->bastFinanceBreakdown();
        $slaMmVsLm = $this->analytics->slaMmVsLmComparison();
        $vendorMmPerformance = $this->analytics->vendorMmPerformance();
        $inboundFmMetrics = $this->analytics->inboundFirstMileMetrics();
        $statusAkhirDistribution = $this->analytics->statusAkhirDistribution($query);
        $needsAttention = $this->dashboard->needsAttention($request);

        return view('analytics.index', array_merge(compact(
            'kpis',
            'trendMonthly',
            'provinceData',
            'vendorData',
            'slaStageBreakdown',
            'leadTimes',
            'worstVendors',
            'worstRegions',
            'bastFinance',
            'slaMmVsLm',
            'vendorMmPerformance',
            'inboundFmMetrics',
            'statusAkhirDistribution',
            'needsAttention',
        ), $filterOptions));
    }

    /**
     * Endpoint JSON peta distribusi: agregasi volume, SLA breach,
     * dan issue terbuka per lokasi berkoordinat.
     */
    public function mapData()
    {
        $data = Cache::remember('analytics:map_data', 300, fn () => $this->buildMapData());

        return response()->json($data);
    }

    /**
     * @return array{markers: array<int, array<string, mixed>>, unmapped_shipments: int}
     */
    private function buildMapData(): array
    {
        $rows = DB::table('shipments')
            ->join('locations', 'locations.id', '=', 'shipments.location_id')
            ->whereNotNull('locations.latitude')
            ->groupBy('locations.id', 'locations.province', 'locations.city_regency', 'locations.latitude', 'locations.longitude')
            ->selectRaw(implode(', ', [
                'locations.id as location_id',
                'locations.province',
                'locations.city_regency',
                'locations.latitude',
                'locations.longitude',
                'COUNT(*) as total_shipments',
                'SUM(CASE WHEN shipments.is_within_sla = 0 THEN 1 ELSE 0 END) as sla_breach',
            ]))
            ->get();

        $openIssues = DB::table('shipment_issues')
            ->join('shipments', 'shipments.id', '=', 'shipment_issues.shipment_id')
            ->where('shipment_issues.status', 'open')
            ->groupBy('shipments.location_id')
            ->selectRaw('shipments.location_id, COUNT(*) as open_issues')
            ->pluck('open_issues', 'location_id');

        $unmapped = DB::table('shipments')
            ->join('locations', 'locations.id', '=', 'shipments.location_id')
            ->whereNull('locations.latitude')
            ->count();

        $markers = $rows->map(function ($row) use ($openIssues): array {
            return [
                'id' => (int) $row->location_id,
                'province' => $row->province,
                'city_regency' => $row->city_regency,
                'lat' => (float) $row->latitude,
                'lng' => (float) $row->longitude,
                'total_shipments' => (int) $row->total_shipments,
                'sla_breach' => (int) $row->sla_breach,
                'open_issues' => (int) ($openIssues[$row->location_id] ?? 0),
            ];
        })->values()->all();

        return [
            'markers' => $markers,
            'unmapped_shipments' => (int) $unmapped,
        ];
    }
}
