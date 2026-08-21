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

    /**
     * Endpoint JSON untuk quick AWB search (command palette Ctrl+K).
     */
    public function search(Request $request)
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $results = \App\Models\Shipment::query()
            ->where(function (Builder $q) use ($term) {
                $q->where('waybill_no', 'like', "%{$term}%")
                    ->orWhere('school_name', 'like', "%{$term}%");
            })
            ->orderBy('waybill_no')
            ->limit(8)
            ->get(['id', 'waybill_no', 'school_name', 'city_regency', 'final_status']);

        return response()->json(['results' => $results]);
    }

    public function show(int $id)
    {
        $shipment = \App\Models\Shipment::with([
            'vendor',
            'issues.resolvedBy',
            'importBatch',
            'inboundFirstMile',
            'slaMiddleMile',
            'slaLastMile',
        ])
            ->findOrFail($id);

        return view('shipments.show', [
            'shipment' => $shipment,
            'timeline' => $this->buildTimeline($shipment),
        ]);
    }

    /**
     * Susun tahapan lifecycle pengiriman untuk timeline visual.
     *
     * @return array<int, array{key: string, label: string, date: ?string, caption: string, state: string}>
     */
    private function buildTimeline(\App\Models\Shipment $shipment): array
    {
        $inbound = $shipment->inboundFirstMile;
        $middle = $shipment->slaMiddleMile;
        $last = $shipment->slaLastMile;

        $hasIssue = $shipment->issues->where('status', 'open')->isNotEmpty();
        $delivered = $shipment->final_status === 'Completed';

        $steps = [
            [
                'key' => 'order',
                'label' => 'Order Created',
                'date' => $shipment->ho_date?->format('d M Y H:i'),
                'caption' => 'HO Date'.($shipment->manifest_no ? ' · '.$shipment->manifest_no : ''),
                'state' => $shipment->ho_date ? 'done' : 'pending',
            ],
            [
                'key' => 'first_mile',
                'label' => 'First Mile Inbound',
                'date' => $inbound?->eta_pickup?->format('d M Y H:i'),
                'caption' => $inbound?->status_inbound ?? 'Data inbound belum tersedia',
                'state' => match (true) {
                    $inbound === null => 'pending',
                    str_contains(strtolower((string) $inbound->status_inbound), 'succ') || $inbound->eta_pickup !== null => 'done',
                    default => 'current',
                },
            ],
            [
                'key' => 'middle_mile',
                'label' => 'Middle Mile Linehaul',
                'date' => ($middle?->tgl_sampai_kota_tujuan ?? $middle?->eta_mm)?->format('d M Y H:i'),
                'caption' => $middle === null
                    ? 'Data linehaul belum tersedia'
                    : (($middle->result_mm ?? $middle->sla_mm) ?? 'ETA linehaul'),
                'state' => match (true) {
                    $middle === null => 'pending',
                    $middle->tgl_sampai_kota_tujuan !== null => 'done',
                    $middle->eta_mm !== null && $middle->eta_mm->isPast() => 'issue',
                    default => 'current',
                },
            ],
            [
                'key' => 'last_mile',
                'label' => 'Last Mile Hub',
                'date' => ($last?->tgl_sampai_kota_tujuan ?? $last?->eta_lm)?->format('d M Y H:i'),
                'caption' => $last === null
                    ? 'Data last mile belum tersedia'
                    : (($last->result_lm ?? $last->sla_lm) ?? 'ETA last mile'),
                'state' => match (true) {
                    $last === null => 'pending',
                    $last->tgl_sampai_kota_tujuan !== null => 'done',
                    $last->eta_lm !== null && $last->eta_lm->isPast() => 'issue',
                    default => 'current',
                },
            ],
            [
                'key' => 'destination',
                'label' => $hasIssue && ! $delivered ? 'Delivered / Issue' : 'Delivered',
                'date' => $last?->tgl_sampai_kota_tujuan?->format('d M Y H:i'),
                'caption' => $delivered
                    ? 'Pengiriman selesai'
                    : ($hasIssue ? 'Terdeteksi issue terbuka' : ($shipment->status_update ?? 'Dalam proses')),
                'state' => match (true) {
                    $delivered => 'done',
                    $hasIssue => 'issue',
                    default => 'current',
                },
            ],
        ];

        // Tahap setelah tahap pertama yang masih pending tidak boleh tampil "done".
        $pendingReached = false;
        foreach ($steps as &$step) {
            if ($step['state'] === 'pending') {
                $pendingReached = true;
            } elseif ($pendingReached) {
                $step['state'] = 'pending';
            }
        }
        unset($step);

        return $steps;
    }
}
