<?php

namespace App\Support;

class ShipmentRowNormalizer
{
    public const REQUIRED_HEADERS = [
        'no_resi', 'npsn', 'no_manifest', 'vendor_lm', 'provinsi', 'kabupatenkota',
        'tgl_ho_dari_sartrans', 'eta_pickup', 'sla_pickup', 'result_pickup_for_panthera',
        'eta_delivery', 'sla', 'result_delivery_for_panthera', 'sla_lm', 'result_lm',
        'sla_for_vendor', 'result_for_vendor', 'status_update', 'status_akhir',
    ];

    /**
     * Nama kolom alternatif/lama dari berbagai template Excel yang dipetakan
     * ke header kanonik (19 kolom wajib). Key sudah dalam bentuk stripped
     * (huruf kecil, tanpa spasi/tanda baca).
     */
    protected const HEADER_ALIASES = [
        'npsnresidb' => 'npsn',                         // 'NPSN / Resi DB'
        'manifestfirstmile' => 'no_manifest',           // 'Manifest First Mile'
        'kotakabtujuan' => 'kabupatenkota',             // 'Kota/Kab Tujuan'
        'resultpickupfordb' => 'result_pickup_for_panthera',   // 'Result Pickup for DB'
        'slafordb' => 'sla',                            // 'SLA for DB'
        'resultdeliveryfordb' => 'result_delivery_for_panthera', // 'Result Delivery for DB'
    ];

    /**
     * Kolom opsional yang tetap ditangkap dari Excel (dipetakan ke field
     * terkait), namun tidak divalidasi sebagai kolom wajib.
     */
    public const OPTIONAL_HEADERS = ['nama_sekolah'];

    /**
     * Alias nama kolom opsional -> nama kanonik opsional.
     */
    protected const OPTIONAL_HEADER_ALIASES = [
        'sekolah' => 'nama_sekolah',          // 'Sekolah'
        'namasekolahnpsn' => 'nama_sekolah',  // 'Nama Sekolah / NPSN'
    ];

    public static function valid(array $row): ?string
    {
        if (trim((string) ($row['no_resi'] ?? '')) === '') {
            return 'No Resi kosong';
        }

        $hoDate = $row['tgl_ho_dari_sartrans'] ?? null;
        if ($hoDate !== null && trim((string) $hoDate) !== '' && StatusNormalizer::parseDate($hoDate) === null) {
            return 'Format tanggal HO tidak valid';
        }

        return null;
    }

    public static function key(string $key): string
    {
        $key = mb_strtolower(trim((string) $key));
        $key = preg_replace('/[^a-z0-9]+/', '_', $key);

        return trim($key, '_');
    }

    /**
     * Petakan header Excel ke nama kanonik kolom wajib, dengan mengabaikan
     * huruf besar/kecil, spasi, underscore, dan semua tanda baca.
     * Contoh: 'Kabupaten/Kota' -> 'kabupatenkota', 'No Resi' -> 'no_resi'.
     * Mengembalikan null jika header tidak termasuk kolom wajib.
     */
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

    /**
     * True jika no_resi bernilai null / empty / hanya spasi -> baris dilewati
     * (tidak dihitung sebagai error validasi import).
     */
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
            'manifest_no' => self::nullable($row['no_manifest'] ?? null),
            'npsn' => self::nullable($row['npsn'] ?? null),
            'school_name' => self::nullable($row['nama_sekolah'] ?? null),
            'province' => self::nullable($row['provinsi'] ?? null),
            'city_regency' => self::nullable($row['kabupatenkota'] ?? null),
            'location_id' => $locationId,
            'ho_date' => StatusNormalizer::parseDate($row['tgl_ho_dari_sartrans'] ?? null),
            'pickup_eta' => StatusNormalizer::parseDate($row['eta_pickup'] ?? null),
            'pickup_sla_status' => self::nullable($row['sla_pickup'] ?? null),
            'pickup_result' => self::nullable($row['result_pickup_for_panthera'] ?? null),
            'delivery_eta' => StatusNormalizer::parseDate($row['eta_delivery'] ?? null),
            'delivery_sla_status' => self::nullable($row['sla'] ?? null),
            'delivery_result' => self::nullable($row['result_delivery_for_panthera'] ?? null),
            'vendor_lm' => trim((string) ($row['vendor_lm'] ?? '')) ?: 'Vendor Lainnya',
            'lm_sla_status' => self::nullable($row['sla_lm'] ?? null),
            'lm_result' => self::nullable($row['result_lm'] ?? null),
            'vendor_sla_status' => self::nullable($row['sla_for_vendor'] ?? null),
            'vendor_result' => self::nullable($row['result_for_vendor'] ?? null),
            'status_update' => self::nullable($row['status_update'] ?? null),
            'final_status' => StatusNormalizer::finalStatus($row['status_akhir'] ?? null),
            'is_within_sla' => StatusNormalizer::withinSla($row['result_for_vendor'] ?? null) ? 1 : 0,
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
