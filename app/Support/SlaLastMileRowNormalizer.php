<?php

namespace App\Support;

use App\Support\Contracts\ImportNormalizer;

class SlaLastMileRowNormalizer implements ImportNormalizer
{
    public const REQUIRED_HEADERS = [
        'no_resi', 'vendor_lm', 'eta_lm', 'sla_lm', 'result_lm',
    ];

    public const OPTIONAL_HEADERS = [
        'provinsi', 'kabupatenkota', 'npsn', 'school_name', 'tgl_sampai_kota_tujuan',
    ];

    public const HEADER_ALIASES = [
        'deliveryorder' => 'no_resi',
        'vendorlm' => 'vendor_lm',
        'etalm' => 'eta_lm',
        'slalm' => 'sla_lm',
        'resultlm' => 'result_lm',
    ];

    public const OPTIONAL_HEADER_ALIASES = [];

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
            'vendor_lm' => self::nullable($row['vendor_lm'] ?? null),
            'eta_lm' => StatusNormalizer::parseDate($row['eta_lm'] ?? null),
            'sla_lm' => self::nullable($row['sla_lm'] ?? null),
            'result_lm' => self::nullable($row['result_lm'] ?? null),
            'province' => self::nullable($row['provinsi'] ?? null),
            'city_regency' => self::nullable($row['kabupatenkota'] ?? null),
            'npsn' => self::nullable($row['npsn'] ?? null),
            'school_name' => self::nullable($row['school_name'] ?? null),
            'tgl_sampai_kota_tujuan' => StatusNormalizer::parseDate($row['tgl_sampai_kota_tujuan'] ?? null),
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
