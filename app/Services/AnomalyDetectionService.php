<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentIssue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AnomalyDetectionService
{
    /**
     * Batas hari shipment boleh berada di status On Delivery
     * sebelum dianggap stuck di transit.
     */
    public const STUCK_TRANSIT_DAYS = 7;

    public const TYPE_STUCK_TRANSIT = 'auto_stuck_transit';

    public const TYPE_MIDDLE_MILE_OVERDUE = 'auto_middle_mile_overdue';

    /**
     * Jalankan seluruh rule deteksi anomali.
     *
     * @return array<string, int> jumlah issue baru per rule
     */
    public function detectAll(): array
    {
        return [
            'stuck_transit' => $this->detectStuckTransit(),
            'middle_mile_overdue' => $this->detectMiddleMileOverdue(),
        ];
    }

    /**
     * Rule 1: Shipment masih "On Delivery" melebihi STUCK_TRANSIT_DAYS
     * sejak ho_date tanpa status akhir.
     */
    public function detectStuckTransit(): int
    {
        $cutoff = Carbon::now()->subDays(self::STUCK_TRANSIT_DAYS);

        $shipments = Shipment::query()
            ->select(['id', 'waybill_no', 'ho_date'])
            ->where('final_status', 'On Delivery')
            ->whereNotNull('ho_date')
            ->where('ho_date', '<=', $cutoff)
            ->whereDoesntHave('issues', function ($query) {
                $query->where('issue_type', self::TYPE_STUCK_TRANSIT)->where('status', 'open');
            })
            ->get();

        return $this->createIssuesOnce($shipments, self::TYPE_STUCK_TRANSIT, function (Shipment $shipment): string {
            $days = max(0, (int) $shipment->ho_date->diffInDays(Carbon::now()));

            return sprintf(
                'Deteksi otomatis: shipment stuck di transit %d hari sejak HO date (%s) tanpa status akhir.',
                $days,
                $shipment->ho_date->format('d M Y'),
            );
        });
    }

    /**
     * Rule 2: ETA middle mile sudah terlewat namun barang belum tiba
     * di kota tujuan (tgl_sampai_kota_tujuan kosong).
     */
    public function detectMiddleMileOverdue(): int
    {
        $shipments = Shipment::query()
            ->select(['shipments.id', 'shipments.waybill_no'])
            ->join('sla_middle_miles', 'sla_middle_miles.waybill_no', '=', 'shipments.waybill_no')
            ->whereNotNull('sla_middle_miles.eta_mm')
            ->where('sla_middle_miles.eta_mm', '<', Carbon::now())
            ->whereNull('sla_middle_miles.tgl_sampai_kota_tujuan')
            ->where('shipments.final_status', '!=', 'Completed')
            ->whereDoesntHave('issues', function ($query) {
                $query->where('issue_type', self::TYPE_MIDDLE_MILE_OVERDUE)->where('status', 'open');
            })
            ->with('slaMiddleMile:waybill_no,eta_mm,tgl_sampai_kota_tujuan')
            ->get();

        return $this->createIssuesOnce($shipments, self::TYPE_MIDDLE_MILE_OVERDUE, function (Shipment $shipment): string {
            $eta = $shipment->slaMiddleMile?->eta_mm;

            return sprintf(
                'Deteksi otomatis: ETA Middle Mile (%s) sudah terlewat namun barang belum tiba di kota tujuan.',
                $eta?->format('d M Y H:i') ?? '-',
            );
        });
    }

    /**
     * Buat record issue untuk kumpulan shipment secara bulk.
     * Deduplikasi sudah difilter lewat whereDoesntHave pada query pemanggil.
     *
     * @param  Collection<int, Shipment>  $shipments
     */
    private function createIssuesOnce(Collection $shipments, string $type, \Closure $describe): int
    {
        if ($shipments->isEmpty()) {
            return 0;
        }

        $now = Carbon::now();

        $rows = $shipments->map(function (Shipment $shipment) use ($type, $describe, $now): array {
            return [
                'shipment_id' => $shipment->id,
                'issue_type' => $type,
                'description' => $describe($shipment),
                'reported_at' => $now,
                'status' => 'open',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            ShipmentIssue::query()->insert($chunk);
        }

        return count($rows);
    }
}
