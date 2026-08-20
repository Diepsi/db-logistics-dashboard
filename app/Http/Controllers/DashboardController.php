<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Analytics Operasional
     */
    public function index(Request $request)
    {
        $service = app(DashboardService::class);

        // Query dasar + filter global (FR-09)
        $query = $service->shipmentQuery($request);

        // ----------------------------------------------------
        // 1. KPI CARDS (7 KPI Utama — PRD Section 9.1)
        // ----------------------------------------------------
        $kpis = $service->kpis($query);

        // KPI periode sebelumnya + selisih % (untuk delta pada KPI card)
        $prevKpis = $service->previousPeriodKpis($request);
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

        // ----------------------------------------------------
        // 2. DATA CHART.JS (PRD Section 9.2)
        // ----------------------------------------------------

        // Chart 1: Donut — Komposisi Status Pengiriman
        $statusChart = [
            'Completed' => $kpis['completed'],
            'On Delivery' => $kpis['onDelivery'],
            'Undelivered' => $kpis['undelivered'],
        ];

        // Chart 2: Donut — Performa Kepatuhan SLA
        $slaChart = [
            'Within SLA' => $kpis['withinSla'],
            'Over SLA' => $kpis['overSla'],
        ];

        // Chart 3: Line — Tren Pengiriman per Hari (volume + SLA rate)
        $trend = (clone $query)
            ->selectRaw('DATE(ho_date) as date, COUNT(*) as total, SUM(CASE WHEN is_within_sla = 1 THEN 1 ELSE 0 END) as within_sla')
            ->whereNotNull('ho_date')
            ->groupByRaw('DATE(ho_date)')
            ->orderBy('date')
            ->get();

        // Chart 3b: Bar — Tren Pengiriman per Bulan
        $trendMonthly = $service->monthlyTrend($query);

        // Chart 4: Bar — Top 5 Volume per Provinsi
        $provinceData = (clone $query)
            ->select('province', DB::raw('count(*) as total'))
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Chart 5: Grouped Bar — Top 5 Vendor Last Mile (Volume & Kepatuhan)
        $vendorData = (clone $query)
            ->select(
                'vendor_lm',
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN is_within_sla = 1 THEN 1 ELSE 0 END) as on_time')
            )
            ->whereNotNull('vendor_lm')
            ->where('vendor_lm', '!=', '')
            ->groupBy('vendor_lm')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ----------------------------------------------------
        // 3. PANEL ANALISIS TAMBAHAN
        // ----------------------------------------------------

        // Funnel kepatuhan SLA per tahap (result_* column query-time)
        $slaStageBreakdown = $service->slaStageBreakdown($query);

        // Rata-rata lead time (hari)
        $leadTimes = $service->leadTimes($query);

        // Vendor dengan over-SLA tertinggi
        $worstVendors = $service->worstVendors($query);

        // Provinsi & kota undelivered terbanyak
        $worstRegions = $service->worstRegions($query);

        // Issue terbuka sesuai filter saat ini
        $issues = $service->openIssues($request);
        $openIssues = $issues['items'];
        $issuesTotal = $issues['total'];

        // Import terakhir yang sukses (indikator kesegaran data)
        $latestImport = $service->latestImport();

        // Table dispatch (DR-05) — 10 pengiriman terbaru sesuai filter saat ini
        $recentShipments = $service->shipmentQuery($request)
            ->orderByDesc('ho_date')
            ->limit(10)
            ->get(['id', 'waybill_no', 'ho_date', 'province', 'city_regency', 'vendor_lm', 'final_status', 'is_within_sla']);

        // ----------------------------------------------------
        // 3b. ANALYTICS BARU — BAST/Finance, SLA MM vs LM, Vendor MM, Inbound FM, Status Detail
        // ----------------------------------------------------

        // BAST & Finance pipeline breakdown
        $bastFinance = $service->bastFinanceBreakdown();

        // SLA MM vs LM comparison per vendor
        $slaMmVsLm = $service->slaMmVsLmComparison();

        // Vendor MM performance
        $vendorMmPerformance = $service->vendorMmPerformance();

        // Inbound First Mile status distribution
        $inboundFmMetrics = $service->inboundFirstMileMetrics();

        // Status Akhir detail distribution
        $statusAkhirDistribution = $service->statusAkhirDistribution($query);

        // ----------------------------------------------------
        // 4. DATA DROPDOWN FILTER
        // ----------------------------------------------------
        $filterOptions = $service->filterOptions($request);
        extract($filterOptions);

        return view('dashboard.index', compact(
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
            'provinces',
            'cities',
            'vendors',
            'statuses'
        ));
    }
}
