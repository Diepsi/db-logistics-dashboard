<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Analytics Operasional
     */
    public function index(Request $request)
    {
        // Query Dasar Shipment
        $query = Shipment::query();

        // ----------------------------------------------------
        // 1. FILTER MULTI-KRITERIA (FR-09)
        // ----------------------------------------------------
        
        // Filter Tanggal Handover (HO)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('ho_date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Filter Provinsi
        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        // Filter Vendor Last Mile
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter Status Akhir Pengiriman
        if ($request->filled('status')) {
            $query->where('final_status', $request->status);
        }

        // ----------------------------------------------------
        // 2. PERHITUNGAN KPI CARDS (Section 9.1 PRD)
        // ----------------------------------------------------
        $totalShipments = (clone $query)->count();
        $completed      = (clone $query)->where('final_status', 'Completed')->count();
        $undelivered    = (clone $query)->where('final_status', 'Undelivered')->count();
        $onDelivery     = (clone $query)->where('final_status', 'On Delivery')->count();
        $withinSla      = (clone $query)->where('is_within_sla', true)->count();
        $overSla        = (clone $query)->where('is_within_sla', false)->count();

        // Persentase Kepatuhan SLA
        $slaAchievementRate = $totalShipments > 0 
            ? round(($withinSla / $totalShipments) * 100, 1) 
            : 0;

        // ----------------------------------------------------
        // 3. AGREGASI DATA CHART.JS (Section 9.2 PRD)
        // ----------------------------------------------------

        // Chart 1: Donut Chart - Komposisi Status Pengiriman
        $statusChartData = [
            'Completed'   => $completed,
            'On Delivery' => $onDelivery,
            'Undelivered' => $undelivered,
        ];

        // Chart 2: Doughnut Chart - Performa Kepatuhan SLA Overall
        $slaChartData = [
            'Within SLA' => $withinSla,
            'Over SLA'   => $overSla,
        ];

        // Chart 3: Bar Chart - Top 5 Volume Pengiriman per Provinsi
        $provinceData = (clone $query)
            ->select('province', DB::raw('count(*) as total'))
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->groupBy('province')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Chart 4: Grouped Bar Chart - Top 5 Vendor Last Mile Performance
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
        // 4. DATA OPSIONAL UNTUK DROPDOWN FILTER
        // ----------------------------------------------------
        $provinces = Shipment::select('province')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $vendors = Vendor::orderBy('name')->get();

        // Render ke View Dashboard
        return view('dashboard.index', compact(
            'totalShipments',
            'completed',
            'undelivered',
            'onDelivery',
            'withinSla',
            'overSla',
            'slaAchievementRate',
            'statusChartData',
            'slaChartData',
            'provinceData',
            'vendorData',
            'provinces',
            'vendors'
        ));
    }
}