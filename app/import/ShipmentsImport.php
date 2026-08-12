<?php

namespace App\Imports;

use App\Models\Shipment;
use App\Models\Vendor;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ShipmentsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected $batchId;

    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    public function model(array $row)
    {
        // Abaikan baris jika No Resi kosong
        if (empty($row['no_resi'])) {
            return null;
        }

        // 1. Registrasi atau Cari Master Vendor
        $vendorName = trim($row['vendor_lm'] ?? 'Vendor Lainnya');
        $vendor = Vendor::firstOrCreate(['name' => $vendorName]);

        // 2. Normalisasi Kepatuhan SLA Overall Vendor
        $resultForVendor = strtoupper(trim($row['result_for_vendor'] ?? ''));
        $isWithinSla = in_array($resultForVendor, ['MEET SLA', 'ON SLA', 'WITHIN SLA', 'ON TIME']);

        // 3. Mapping Data ke Model Shipment
        return new Shipment([
            'import_batch_id'     => $this->batchId,
            'vendor_id'           => $vendor->id,
            'waybill_no'          => (string) $row['no_resi'],
            'manifest_no'         => $row['no_manifest'] ?? null,
            'npsn'                => $row['npsn'] ?? null,
            'school_name'         => $row['nama_sekolah'] ?? null,
            'province'            => $row['provinsi'] ?? null,
            'city_regency'        => $row['kabupatenkota'] ?? null,
            
            'ho_date'             => $this->parseDate($row['tgl_ho_dari_sartrans'] ?? null),
            'pickup_eta'          => $this->parseDate($row['eta_pickup'] ?? null),
            'pickup_sla_status'   => $row['sla_pickup'] ?? null,
            'pickup_result'       => $row['result_pickup_for_panthera'] ?? null,
            
            'delivery_eta'        => $this->parseDate($row['eta_delivery'] ?? null),
            'delivery_sla_status' => $row['sla'] ?? null,
            'delivery_result'     => $row['result_delivery_for_panthera'] ?? null,
            
            'vendor_lm'           => $vendorName,
            'lm_sla_status'       => $row['sla_lm'] ?? null,
            'lm_result'           => $row['result_lm'] ?? null,
            
            'vendor_sla_status'   => $row['sla_for_vendor'] ?? null,
            'vendor_result'       => $row['result_for_vendor'] ?? null,
            
            'status_update'       => $row['status_update'] ?? null,
            'final_status'        => $row['status_akhir'] ?? 'On Delivery',
            'is_within_sla'       => $isWithinSla,
        ]);
    }

    public function chunkSize(): int
    {
        return 500; // Memproses per 500 baris data
    }

    /**
     * Parse format tanggal dari Excel (Timestamp/Numeric/String)
     */
    private function parseDate($dateValue)
    {
        if (!$dateValue) return null;

        try {
            if (is_numeric($dateValue)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
            }
            return Carbon::parse($dateValue);
        } catch (\Exception $e) {
            return null;
        }
    }
}