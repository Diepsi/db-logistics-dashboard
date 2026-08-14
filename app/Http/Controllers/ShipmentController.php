<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /**
     * Tampilkan tabel data pengiriman (read-only) dengan pencarian & filter.
     */
    public function index(Request $request)
    {
        $service = app(DashboardService::class);

        $query = $service->shipmentQuery($request)
            ->when($request->filled('search'), function (Builder $q) use ($request) {
                $search = trim($request->search);
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('waybill_no', 'like', "%{$search}%")
                        ->orWhere('manifest_no', 'like', "%{$search}%")
                        ->orWhere('npsn', 'like', "%{$search}%")
                        ->orWhere('school_name', 'like', "%{$search}%");
                });
            });

        $shipments = $query->with(['vendor'])
            ->orderByDesc('ho_date')
            ->paginate(25)
            ->withQueryString();

        $filterOptions = $service->filterOptions($request);
        extract($filterOptions);

        return view('shipments.index', compact(
            'shipments',
            'provinces',
            'cities',
            'vendors',
            'statuses'
        ));
    }
}
