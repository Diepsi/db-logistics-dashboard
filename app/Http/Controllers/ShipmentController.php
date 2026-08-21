<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        private readonly DashboardService $service
    ) {}

    public function index(Request $request)
    {
        $query = $this->service->shipmentQuery($request)
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

        $filterOptions = $this->service->filterOptions($request);

        return view('shipments.index', array_merge(compact('shipments'), $filterOptions));
    }

    public function show(int $id)
    {
        $shipment = \App\Models\Shipment::with(['vendor', 'issues.resolvedBy', 'importBatch'])
            ->findOrFail($id);

        return view('shipments.show', compact('shipment'));
    }
}
