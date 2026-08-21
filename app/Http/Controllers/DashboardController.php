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

        $deltas = [
            'total' => $prevKpis ? DashboardService::delta($kpis['totalShipments'], $prevKpis['total']) : null,
            'slaRate' => $prevKpis && $prevKpis['total'] > 0
                ? round($kpis['slaAchievementRate'] - (($prevKpis['within_sla'] / $prevKpis['total']) * 100), 1)
                : null,
            'undelivered' => $prevKpis ? DashboardService::delta($kpis['undelivered'], $prevKpis['undelivered']) : null,
            'overSla' => $prevKpis ? DashboardService::delta($kpis['overSla'], $prevKpis['total'] - $prevKpis['within_sla']) : null,
        ];

        // Ringkasan proporsi untuk stacked progress bar (pengganti donut chart).
        $statusSummary = [
            ['label' => 'Completed', 'value' => $kpis['completed'], 'color' => 'bg-emerald-500'],
            ['label' => 'On Delivery', 'value' => $kpis['onDelivery'], 'color' => 'bg-amber-500'],
            ['label' => 'Undelivered', 'value' => $kpis['undelivered'], 'color' => 'bg-rose-500'],
        ];
        $slaSummary = [
            ['label' => 'Within SLA', 'value' => $kpis['withinSla'], 'color' => 'bg-emerald-500'],
            ['label' => 'Over SLA', 'value' => $kpis['overSla'], 'color' => 'bg-rose-500'],
        ];

        $trend = $this->service->dailyTrend($query);
        $needsAttention = $this->service->needsAttention($request);
        $activityFeed = $this->service->activityFeed();
        $latestImport = $this->service->latestImport();
        $activeIssues = $this->service->openIssueCount();

        $filterOptions = $this->service->filterOptions($request);

        return view('dashboard.index', array_merge(compact(
            'kpis',
            'prevKpis',
            'deltas',
            'statusSummary',
            'slaSummary',
            'trend',
            'needsAttention',
            'activityFeed',
            'latestImport',
            'activeIssues',
        ), $filterOptions));
    }
}
