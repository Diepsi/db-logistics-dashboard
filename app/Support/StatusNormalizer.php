<?php

namespace App\Support;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class StatusNormalizer
{
    public const COMPLETED = 'Completed';

    public const ON_DELIVERY = 'On Delivery';

    public const UNDELIVERED = 'Undelivered';

    public const FINAL_STATUSES = [
        self::COMPLETED,
        self::ON_DELIVERY,
        self::UNDELIVERED,
    ];

    protected static array $finalStatusMap = [
        'completed' => self::COMPLETED, 'selesai' => self::COMPLETED, 'done' => self::COMPLETED,
        'terkirim' => self::COMPLETED, 'delivered' => self::COMPLETED, 'berhasil' => self::COMPLETED,
        'ok' => self::COMPLETED,
        'undelivered' => self::UNDELIVERED, 'gagal' => self::UNDELIVERED, 'failed' => self::UNDELIVERED,
        'tidak terkirim' => self::UNDELIVERED, 'batal' => self::UNDELIVERED, 'canceled' => self::UNDELIVERED,
        'on delivery' => self::ON_DELIVERY, 'in transit' => self::ON_DELIVERY, 'proses' => self::ON_DELIVERY,
        'pending' => self::ON_DELIVERY,
    ];

    protected static array $slaMap = [
        'meet sla', 'on sla', 'within sla', 'on time', 'yes', 'y', 'meet', 'ok', 'good', 'pass',
    ];

    /**
     * Token nilai yang dianggap "within SLA", dipakai untuk membangun literal SQL
     * (LOWER(TRIM(...)) IN (...)) saat menghitung funnel kepatuhan per tahap.
     *
     * @return string[]
     */
    public static function slaTokens(): array
    {
        return self::$slaMap;
    }

    public static function finalStatus(?string $value): string
    {
        $key = strtolower(trim((string) $value));

        return self::$finalStatusMap[$key] ?? self::ON_DELIVERY;
    }

    public static function withinSla(?string $result): bool
    {
        return in_array(strtolower(trim((string) $result)), self::$slaMap, true);
    }

    /**
     * Parse nilai tanggal dari Excel (serial number / string d/m/Y, m/d/Y, Y-m-d) secara aman.
     */
    public static function parseDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '' || is_bool($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
            }

            $string = trim((string) $value);

            if (preg_match('#^\d{2}/\d{2}/\d{4}#', $string)) {
                $string = str_replace('/', '-', $string);
            }

            return Carbon::parse($string);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
