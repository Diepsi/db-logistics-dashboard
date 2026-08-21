<?php

namespace App\Http\Controllers;

use App\Exports\ShipmentsExport;
use App\Services\DashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(
        private readonly DashboardService $service
    ) {}

    public function index(Request $request)
    {
        $kpis = $this->service->kpis($this->service->shipmentQuery($request));
        $filterOptions = $this->service->filterOptions($request);

        return view('reports.index', array_merge(compact('kpis'), $filterOptions));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->service->shipmentQuery($request);

        return Excel::download(new ShipmentsExport($query), 'laporan-pengiriman.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = $this->service->shipmentQuery($request);
        $kpis = $this->service->kpis($query);
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
