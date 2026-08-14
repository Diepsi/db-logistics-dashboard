<?php

namespace App\Http\Controllers;

use App\Exports\ShipmentsExport;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Halaman Laporan & Export
     */
    public function index(Request $request)
    {
        $service = app(DashboardService::class);

        $kpis = $service->kpis($service->shipmentQuery($request));

        $filterOptions = $service->filterOptions($request);
        extract($filterOptions);

        return view('reports.index', compact(
            'kpis',
            'provinces',
            'cities',
            'vendors',
            'statuses'
        ));
    }

    /**
     * Unduh laporan terfilter dalam format Excel
     */
    public function exportExcel(Request $request)
    {
        $query = app(DashboardService::class)->shipmentQuery($request);

        return Excel::download(new ShipmentsExport($query), 'laporan-pengiriman.xlsx');
    }

    /**
     * Unduh laporan terfilter dalam format PDF (ringkasan KPI + daftar pengiriman)
     */
    public function exportPdf(Request $request)
    {
        $service = app(DashboardService::class);
        $query = $service->shipmentQuery($request);

        $kpis = $service->kpis($query);
        $shipments = (clone $query)
            ->with('vendor')
            ->orderByDesc('ho_date')
            ->limit(500)
            ->get();

        $pdf = Pdf::loadView('reports.pdf', compact('kpis', 'shipments'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pengiriman.pdf');
    }
}
