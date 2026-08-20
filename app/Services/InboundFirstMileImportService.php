<?php

namespace App\Services;

use App\Imports\InboundFirstMileImport;
use App\Support\InboundFirstMileRowNormalizer;
use Illuminate\Http\UploadedFile;

class InboundFirstMileImportService
{
    public const SHEET_CANDIDATES = ['FM Inbound', 'Inventory Gudang Pusat'];

    public function preview(UploadedFile $file): array
    {
        return app(ImportService::class)->preview(
            $file,
            InboundFirstMileRowNormalizer::class,
            self::SHEET_CANDIDATES
        );
    }

    public function process(string $token, int $batchId): array
    {
        return app(ImportService::class)->process(
            $token,
            $batchId,
            InboundFirstMileRowNormalizer::class,
            self::SHEET_CANDIDATES,
            InboundFirstMileImport::class
        );
    }
}
