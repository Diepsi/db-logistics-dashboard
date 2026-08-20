<?php

namespace App\Services;

use App\Imports\SlaAllImport;
use App\Support\SlaAllRowNormalizer;
use Illuminate\Http\UploadedFile;

class SlaAllImportService
{
    public const SHEET_CANDIDATES = ['Database SLA ALL', 'SLA ALL'];

    public function preview(UploadedFile $file): array
    {
        return app(ImportService::class)->preview(
            $file,
            SlaAllRowNormalizer::class,
            self::SHEET_CANDIDATES
        );
    }

    public function process(string $token, int $batchId): array
    {
        return app(ImportService::class)->process(
            $token,
            $batchId,
            SlaAllRowNormalizer::class,
            self::SHEET_CANDIDATES,
            SlaAllImport::class
        );
    }
}
