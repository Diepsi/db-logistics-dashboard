<?php

namespace App\Services;

use App\Imports\ShipmentImport;
use App\Support\ShipmentRowNormalizer;
use Illuminate\Http\UploadedFile;

class ShipmentImportService
{
    public const SHEET_CANDIDATES = ['RAW DATA MM & LM', 'RAW DATA', 'DATA PENGIRIMAN'];

    public function preview(UploadedFile $file): array
    {
        return app(ImportService::class)->preview(
            $file,
            ShipmentRowNormalizer::class,
            self::SHEET_CANDIDATES
        );
    }

    public function process(string $token, int $batchId, ?callable $onChunk = null): array
    {
        return app(ImportService::class)->process(
            $token,
            $batchId,
            ShipmentRowNormalizer::class,
            self::SHEET_CANDIDATES,
            ShipmentImport::class,
            $onChunk
        );
    }
}
