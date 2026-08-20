<?php

namespace App\Support\Contracts;

interface ImportNormalizer
{
    /**
     * Kolom wajib yang harus ada di header Excel.
     */
    public const REQUIRED_HEADERS = [];

    /**
     * Kolom opsional — ditangkap jika ada, tidak divalidasi.
     */
    public const OPTIONAL_HEADERS = [];

    /**
     * Kamus alias nama kolom lama/baru → nama kanonik REQUIRED_HEADERS.
     * Key harus berupa lowercase tanpa spasi/tanda baca (stripped).
     */
    public const HEADER_ALIASES = [];

    /**
     * Kamus alias nama kolom opsional → nama kanonik OPTIONAL_HEADERS.
     */
    public const OPTIONAL_HEADER_ALIASES = [];

    /**
     * Petakan header Excel ke nama kanonik kolom.
     * Mengembalikan null jika header tidak dikenali.
     */
    public static function canonicalKey(string $key): ?string;

    /**
     * Validasi satu baris data.
     * Mengembalikan string error jika tidak valid, atau null jika OK.
     */
    public static function valid(array $row): ?string;

    /**
     * True jika baris dianggap kosong (primary key tidak terisi).
     */
    public static function isEmptyRow(array $row): bool;

    /**
     * Normalisasi baris Excel mentah → array siap DB upsert.
     *
     * @param  array       $row      Baris ternormalisasi (key = canonical)
     * @param  int         $batchId  ID import batch
     * @param  int         $vendorId ID vendor (resolve sebelumnya)
     * @param  int|null    $locationId ID lokasi (resolve sebelumnya)
     * @return array       Kolom DB → value
     */
    public static function normalize(array $row, int $batchId, int $vendorId, ?int $locationId): array;
}
