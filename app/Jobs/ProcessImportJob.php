<?php

namespace App\Jobs;

use App\Models\ImportBatch;
use App\Services\AnomalyDetectionService;
use App\Services\ShipmentImportService;
use App\Support\CacheKeys;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 900;

    public function __construct(
        public string $token,
        public int $batchId,
        public string $fileName,
    ) {}

    public function handle(ShipmentImportService $importService, AnomalyDetectionService $anomalyDetection): void
    {
        $batch = ImportBatch::find($this->batchId);

        if (! $batch || $batch->status === 'completed') {
            return;
        }

        $batch->update([
            'status' => 'processing',
            'notes' => 'Memproses impor file Excel di latar belakang.',
        ]);

        try {
            $result = $importService->process(
                $this->token,
                $this->batchId,
                fn (int $rowCount) => ImportBatch::query()
                    ->whereKey($this->batchId)
                    ->increment('processed_rows', $rowCount),
            );

            $batch->update([
                'total_rows' => $result['total'],
                'valid_rows' => $result['valid'],
                'invalid_rows' => $result['invalid'],
                'duplicate_rows' => $result['duplicate'],
                'new_rows' => $result['new_rows'],
                'updated_rows' => $result['updated_rows'],
                'failed_rows' => $result['invalid'],
                'status' => 'completed',
                'notes' => sprintf(
                    'Import selesai: %d baru, %d diperbarui, %d tidak valid, %d duplikat.',
                    $result['new_rows'],
                    $result['updated_rows'],
                    $result['invalid'],
                    $result['duplicate']
                ),
            ]);

            $anomalyDetection->detectAll();
        } catch (Throwable $e) {
            $batch->update([
                'status' => 'failed',
                'notes' => 'Gagal diproses: '.$e->getMessage(),
            ]);
        } finally {
            CacheKeys::flushAll();
        }
    }
}
