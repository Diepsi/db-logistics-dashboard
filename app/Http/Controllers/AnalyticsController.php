<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\DashboardService;
use Illuminate\Http\Request;

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
}
