<?php

namespace App\Services;

use App\Imports\SlaLastMileImport;
use App\Support\SlaLastMileRowNormalizer;
use Illuminate\Http\UploadedFile;

class SlaLastMileImportService
{
    public const SHEET_CANDIDATES = ['Database SLA LM', 'SLA Lastmile', 'SLA LM'];

    public function preview(UploadedFile $file): array
    {
        return app(ImportService::class)->preview(
            $file,
            SlaLastMileRowNormalizer::class,
            self::SHEET_CANDIDATES
        );
    }

    public function process(string $token, int $batchId): array
    {
        return app(ImportService::class)->process(
            $token,
            $batchId,
            SlaLastMileRowNormalizer::class,
            self::SHEET_CANDIDATES,
            SlaLastMileImport::class
        );
    }
}
