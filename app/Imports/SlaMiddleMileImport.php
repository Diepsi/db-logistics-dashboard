<?php

namespace App\Imports;

use App\Models\SlaMiddleMile;
use App\Support\SlaMiddleMileRowNormalizer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class SlaMiddleMileImport implements ToCollection, WithChunkReading, WithHeadingRow, WithUpserts
{
    public int $batchId;

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

        foreach ($rows as $row) {
            $row = SlaMiddleMileRowNormalizer::normalizeRow(is_array($row) ? $row : $row->toArray());

            if (SlaMiddleMileRowNormalizer::isEmptyRow($row)) {
                continue;
            }

            $this->total++;

            $reason = SlaMiddleMileRowNormalizer::valid($row);
            if ($reason !== null) {
                $this->invalid++;

                continue;
            }

            $waybill = trim((string) ($row['no_resi'] ?? ''));

            $record = SlaMiddleMileRowNormalizer::normalize($row, $this->batchId, 0, null);
            unset($record['vendor_id']);

            $records[] = $record;
            $waybills[] = $waybill;
            $this->valid++;
        }

        if ($records === []) {
            return;
        }

        $existingCount = SlaMiddleMile::whereIn('waybill_no', $waybills)->count();
        $this->newRows += count($records) - $existingCount;
        $this->updatedRows += $existingCount;

        SlaMiddleMile::upsert($records, ['waybill_no'], $this->updateColumns());
    }

    protected function updateColumns(): array
    {
        return [
            'import_batch_id', 'vendor_mm', 'eta_mm', 'sla_mm', 'result_mm',
            'tgl_sampai_kota_tujuan', 'province', 'city_regency', 'npsn', 'school_name',
            'updated_at',
        ];
    }
}
