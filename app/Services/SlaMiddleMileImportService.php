<?php

namespace App\Services;

use App\Imports\SlaMiddleMileImport;
use App\Support\SlaMiddleMileRowNormalizer;
use Illuminate\Http\UploadedFile;

class SlaMiddleMileImportService
{
    public const SHEET_CANDIDATES = ['Database SLA MM', 'SLA Middlemile', 'SLA MM'];

    public function preview(UploadedFile $file): array
    {
        return app(ImportService::class)->preview(
            $file,
            SlaMiddleMileRowNormalizer::class,
            self::SHEET_CANDIDATES
        );
    }

    public function process(string $token, int $batchId): array
    {
        return app(ImportService::class)->process(
            $token,
            $batchId,
            SlaMiddleMileRowNormalizer::class,
            self::SHEET_CANDIDATES,
            SlaMiddleMileImport::class
        );
    }
}
