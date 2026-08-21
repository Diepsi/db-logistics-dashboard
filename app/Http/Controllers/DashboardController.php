<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service
    ) {}

    public function index(Request $request)
    {
        $query = $this->service->shipmentQuery($request);

        $kpis = $this->service->kpis($query);
        $prevKpis = $this->service->previousPeriodKpis($request);
        $deltas = $prevKpis ? [
            'total' => DashboardService::delta($kpis['totalShipments'], $prevKpis['total']),
            'completed' => DashboardService::delta($kpis['completed'], $prevKpis['completed']),
            'onDelivery' => DashboardService::delta($kpis['onDelivery'], $prevKpis['on_delivery']),
            'undelivered' => DashboardService::delta($kpis['undelivered'], $prevKpis['undelivered']),
            'withinSla' => DashboardService::delta($kpis['withinSla'], $prevKpis['within_sla']),
            'overSla' => DashboardService::delta($kpis['overSla'], $prevKpis['total'] - $prevKpis['within_sla']),
            'slaRate' => $kpis['slaAchievementRate'] > 0 && $prevKpis['total'] > 0
                ? round($kpis['slaAchievementRate'] - (($prevKpis['within_sla'] / $prevKpis['total']) * 100), 1)
                : null,
        ] : [];

        $statusChart = [
            'Completed' => $kpis['completed'],
            'On Delivery' => $kpis['onDelivery'],
            'Undelivered' => $kpis['undelivered'],
        ];

        $slaChart = [
            'Within SLA' => $kpis['withinSla'],
            'Over SLA' => $kpis['overSla'],
        ];

        $trend = $this->service->dailyTrend($query);
        $trendMonthly = $this->service->monthlyTrend($query);
        $provinceData = $this->service->provinceDistribution($query);
        $vendorData = $this->service->vendorDistribution($query);
        $slaStageBreakdown = $this->service->slaStageBreakdown($query);
        $leadTimes = $this->service->leadTimes($query);
        $worstVendors = $this->service->worstVendors($query);
        $worstRegions = $this->service->worstRegions($query);

        $issues = $this->service->openIssues($request);
        $openIssues = $issues['items'];
        $issuesTotal = $issues['total'];

        $latestImport = $this->service->latestImport();

        $recentShipments = $this->service->shipmentQuery($request)
            ->orderByDesc('ho_date')
            ->limit(10)
            ->get(['id', 'waybill_no', 'ho_date', 'province', 'city_regency', 'vendor_lm', 'final_status', 'is_within_sla']);

        $bastFinance = $this->service->bastFinanceBreakdown();
        $slaMmVsLm = $this->service->slaMmVsLmComparison();
        $vendorMmPerformance = $this->service->vendorMmPerformance();
        $inboundFmMetrics = $this->service->inboundFirstMileMetrics();
        $statusAkhirDistribution = $this->service->statusAkhirDistribution($query);

        $filterOptions = $this->service->filterOptions($request);

        return view('dashboard.index', array_merge(compact(
            'kpis',
            'prevKpis',
            'deltas',
            'statusChart',
            'slaChart',
            'trend',
            'trendMonthly',
            'provinceData',
            'vendorData',
            'slaStageBreakdown',
            'leadTimes',
            'worstVendors',
            'worstRegions',
            'openIssues',
            'issuesTotal',
            'latestImport',
            'recentShipments',
            'bastFinance',
            'slaMmVsLm',
            'vendorMmPerformance',
            'inboundFmMetrics',
            'statusAkhirDistribution',
        ), $filterOptions));
    }
}
