<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShipmentsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(protected Builder $query)
    {
        //
    }

    public function query()
    {
        return $this->query
            ->with(['vendor'])
            ->orderByDesc('ho_date');
    }

    public function headings(): array
    {
        return [
            'No Resi',
            'No Manifest',
            'NPSN',
            'Nama Sekolah',
            'Provinsi',
            'Kabupaten/Kota',
            'Tanggal HO',
            'Vendor LM',
            'Status Akhir',
            'Within SLA',
        ];
    }

    public function map($shipment): array
    {
        return [
            $shipment->waybill_no,
            $shipment->manifest_no,
            $shipment->npsn,
            $shipment->school_name,
            $shipment->province,
            $shipment->city_regency,
            $shipment->ho_date?->format('Y-m-d'),
            $shipment->vendor_lm,
            $shipment->final_status,
            $shipment->is_within_sla ? 'On Time' : 'Over SLA',
        ];
    }
}
