<?php

namespace App\Support;

use App\Support\Contracts\ImportNormalizer;

/**
 * Sheet candidates: 'Database Issue', 'ISSUE KIRIMAN', 'ISSUE'
 */
class IssueRowNormalizer implements ImportNormalizer
{
    public const REQUIRED_HEADERS = [
        'no_resi', 'issue_type', 'description', 'status',
    ];

    public const OPTIONAL_HEADERS = [
        'reported_at', 'resolved_at', 'vendor_lm', 'provinsi', 'kabupatenkota',
    ];

    public const HEADER_ALIASES = [
        'nomorresi' => 'no_resi',
        'tipeissue' => 'issue_type',
        'keterangan' => 'description',
        'statustiket' => 'status',
        'nomorsurat' => 'no_resi',
    ];

    public const OPTIONAL_HEADER_ALIASES = [
        'tanggalpelaporan' => 'reported_at',
        'tanggalpenyelesaian' => 'resolved_at',
    ];

    public static function valid(array $row): ?string
    {
        if (trim((string) ($row['no_resi'] ?? '')) === '') {
            return 'No Resi kosong';
        }

        return null;
    }

    public static function key(string $key): string
    {
        $key = mb_strtolower(trim((string) $key));
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        return trim($key, '_');
    }

    public static function canonicalKey(string $key): ?string
    {
        $stripped = self::stripped($key);

        if ($stripped === '') {
            return null;
        }

        foreach (self::REQUIRED_HEADERS as $canonical) {
            if (self::stripped($canonical) === $stripped) {
                return $canonical;
            }
        }

        foreach (self::OPTIONAL_HEADERS as $canonical) {
            if (self::stripped($canonical) === $stripped) {
                return $canonical;
            }
        }

        return self::OPTIONAL_HEADER_ALIASES[$stripped] ?? self::HEADER_ALIASES[$stripped] ?? null;
    }

    public static function isEmptyRow(array $row): bool
    {
        return trim((string) ($row['no_resi'] ?? '')) === '';
    }

    protected static function stripped(string $key): string
    {
        return preg_replace('/[^a-z0-9]+/', '', mb_strtolower((string) $key)) ?? '';
    }

    public static function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $normalized[self::key($key)] = $value;
        }

        return $normalized;
    }

    public static function normalize(array $row, int $batchId, int $vendorId, ?int $locationId): array
    {
        $now = now();

        return [
            'import_batch_id' => $batchId,
            'vendor_id' => $vendorId,
            'waybill_no' => trim((string) ($row['no_resi'] ?? '')),
            'issue_type' => self::nullable($row['issue_type'] ?? null),
            'description' => self::nullable($row['description'] ?? null),
            'status' => self::nullable($row['status'] ?? null),
            'reported_at' => StatusNormalizer::parseDate($row['reported_at'] ?? null),
            'resolved_at' => StatusNormalizer::parseDate($row['resolved_at'] ?? null),
            'vendor_lm' => self::nullable($row['vendor_lm'] ?? null),
            'province' => self::nullable($row['provinsi'] ?? null),
            'city_regency' => self::nullable($row['kabupatenkota'] ?? null),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    protected static function nullable(mixed $value): ?string
    {
        $string = trim((string) ($value ?? ''));

        return $string === '' ? null : $string;
    }
}
