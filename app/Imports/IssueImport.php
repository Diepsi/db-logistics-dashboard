<?php

namespace App\Imports;

use App\Models\Shipment;
use App\Models\ShipmentIssue;
use App\Support\IssueRowNormalizer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class IssueImport implements ToCollection, WithChunkReading, WithHeadingRow, WithUpserts
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
        return ['waybill_no', 'issue_type'];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $row = IssueRowNormalizer::normalizeRow(is_array($row) ? $row : $row->toArray());

            if (IssueRowNormalizer::isEmptyRow($row)) {
                continue;
            }

            $this->total++;

            $reason = IssueRowNormalizer::valid($row);
            if ($reason !== null) {
                $this->invalid++;

                continue;
            }

            $waybillNo = trim((string) ($row['no_resi'] ?? ''));
            $issueType = trim((string) ($row['issue_type'] ?? ''));

            $shipment = Shipment::where('waybill_no', $waybillNo)->first();
            if ($shipment === null) {
                $this->invalid++;

                continue;
            }

            $description = IssueRowNormalizer::nullable($row['description'] ?? null);
            $status = IssueRowNormalizer::nullable($row['status'] ?? null);
            $reportedAt = \App\Support\StatusNormalizer::parseDate($row['reported_at'] ?? null);
            $resolvedAt = \App\Support\StatusNormalizer::parseDate($row['resolved_at'] ?? null);

            $existing = ShipmentIssue::where('shipment_id', $shipment->id)
                ->where('issue_type', $issueType)
                ->first();

            if ($existing) {
                $existing->update([
                    'description' => $description,
                    'status' => $status,
                    'reported_at' => $reportedAt,
                    'resolved_at' => $resolvedAt,
                ]);
                $this->duplicate++;
                $this->updatedRows++;
            } else {
                ShipmentIssue::create([
                    'shipment_id' => $shipment->id,
                    'issue_type' => $issueType,
                    'description' => $description,
                    'status' => $status,
                    'reported_at' => $reportedAt,
                    'resolved_at' => $resolvedAt,
                ]);
                $this->newRows++;
            }

            $this->valid++;
        }
    }
}
