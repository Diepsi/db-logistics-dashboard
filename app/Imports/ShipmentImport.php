<?php

namespace App\Imports;

use App\Models\Location;
use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Models\Vendor;
use App\Support\ShipmentRowNormalizer;
use App\Support\StatusNormalizer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ShipmentImport implements ToCollection, WithChunkReading, WithHeadingRow, WithUpserts
{
    public int $batchId;

    public array $vendorCache = [];

    public array $locationCache = [];

    public array $seenWaybills = [];

    public int $total = 0;

    public int $valid = 0;

    public int $invalid = 0;

    public int $duplicate = 0;

    public int $newRows = 0;

    public int $updatedRows = 0;

    public function __construct(int $batchId)
    {
        $this->batchId = $batchId;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function uniqueBy(): array
    {
        return ['waybill_no'];
    }

    public function collection(Collection $rows): void
    {
        $records = [];
        $waybills = [];
        $undeliveredNos = [];

        foreach ($rows as $row) {
            $row = ShipmentRowNormalizer::normalizeRow(is_array($row) ? $row : $row->toArray());

            if (ShipmentRowNormalizer::isEmptyRow($row)) {
                continue;
            }

            $this->total++;

            $reason = ShipmentRowNormalizer::valid($row);
            if ($reason !== null) {
                $this->invalid++;

                continue;
            }

            $waybill = trim((string) ($row['no_resi'] ?? ''));
            if (isset($this->seenWaybills[$waybill])) {
                $this->duplicate++;

                continue;
            }
            $this->seenWaybills[$waybill] = true;

            $vendorName = trim((string) ($row['vendor_lm'] ?? '')) ?: 'Vendor Lainnya';
            $vendorId = $this->vendorCache[$vendorName]
                ??= Vendor::firstOrCreate(['name' => $vendorName])->id;

            $province = trim((string) ($row['provinsi'] ?? ''));
            $city = trim((string) ($row['kabupatenkota'] ?? ''));
            $locKey = $province.'|'.$city;
            $locationId = ($province !== '' || $city !== '')
                ? ($this->locationCache[$locKey]
                    ??= Location::firstOrCreate(['province' => $province, 'city_regency' => $city])->id)
                : null;

            $records[] = ShipmentRowNormalizer::normalize($row, $this->batchId, $vendorId, $locationId);
            $waybills[] = $waybill;

            if (StatusNormalizer::finalStatus($row['status_akhir'] ?? null) === StatusNormalizer::UNDELIVERED) {
                $undeliveredNos[] = $waybill;
            }

            $this->valid++;
        }

        if ($records === []) {
            return;
        }

        $existingCount = Shipment::whereIn('waybill_no', $waybills)->count();
        $this->newRows += count($records) - $existingCount;
        $this->updatedRows += $existingCount;

        Shipment::upsert($records, ['waybill_no'], $this->updateColumns());

        $this->createIssues($undeliveredNos);
    }

    protected function updateColumns(): array
    {
        return [
            'import_batch_id', 'vendor_id', 'manifest_no', 'npsn', 'school_name',
            'province', 'city_regency', 'location_id', 'ho_date', 'pickup_eta',
            'pickup_sla_status', 'pickup_result', 'delivery_eta', 'delivery_sla_status',
            'delivery_result', 'vendor_lm', 'lm_sla_status', 'lm_result',
            'vendor_sla_status', 'vendor_result', 'status_update', 'final_status',
            'is_within_sla', 'updated_at',
        ];
    }

    protected function createIssues(array $undeliveredNos): void
    {
        if ($undeliveredNos === []) {
            return;
        }

        $ids = Shipment::whereIn('waybill_no', $undeliveredNos)->pluck('id', 'waybill_no');

        foreach ($ids as $waybillNo => $shipmentId) {
            ShipmentIssue::updateOrCreate(
                ['shipment_id' => $shipmentId, 'issue_type' => 'undelivered'],
                [
                    'description' => 'Pengiriman tidak berhasil (status Undelivered). No Resi: '.$waybillNo,
                    'reported_at' => now(),
                    'status' => 'open',
                ]
            );
        }
    }
}
