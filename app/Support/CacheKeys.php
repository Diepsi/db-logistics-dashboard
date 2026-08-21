<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Registry seluruh key cache agregasi dashboard/analytics.
 *
 * Driver cache aktif adalah `database` yang TIDAK mendukung tagging,
 * sehingga invalidasi dilakukan eksplisit lewat daftar key ini
 * (dipanggil otomatis setelah import selesai / clear data).
 */
final class CacheKeys
{
    public const AGGREGATION_KEYS = [
        'dashboard:provinces',
        'dashboard:vendors',
        'dashboard:latest_import',
        'dashboard:bast_finance',
        'dashboard:sla_mm_lm',
        'dashboard:vendor_mm',
        'dashboard:inbound_fm',
        'analytics:map_data',
    ];

    /**
     * Hapus seluruh cache agregasi (panggil saat data berubah).
     */
    public static function flushAll(): void
    {
        foreach (self::AGGREGATION_KEYS as $key) {
            Cache::forget($key);
        }
    }
}
