<?php

namespace App\Console\Commands;

use App\Services\AnomalyDetectionService;
use Illuminate\Console\Command;

class DetectAnomaliesCommand extends Command
{
    protected $signature = 'anomalies:detect';

    protected $description = 'Deteksi shipment bermasalah (stuck transit / middle mile overdue) dan buat issue otomatis';

    public function handle(AnomalyDetectionService $service): int
    {
        $this->info('Memindai anomali pengiriman...');

        $result = $service->detectAll();

        $this->table(['Rule', 'Issue Baru'], [
            ['Stuck transit', $result['stuck_transit']],
            ['Middle mile overdue', $result['middle_mile_overdue']],
        ]);

        $total = array_sum($result);
        $this->info($total === 0
            ? 'Tidak ada anomali baru terdeteksi.'
            : "Sebanyak {$total} issue baru berhasil dibuat.");

        return self::SUCCESS;
    }
}
