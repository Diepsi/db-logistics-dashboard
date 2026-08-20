<?php

namespace App\Services;

use App\Imports\IssueImport;
use App\Support\IssueRowNormalizer;
use Illuminate\Http\UploadedFile;

class IssueImportService
{
    public const SHEET_CANDIDATES = ['Database Issue', 'ISSUE KIRIMAN', 'ISSUE'];

    public function preview(UploadedFile $file): array
    {
        return app(ImportService::class)->preview(
            $file,
            IssueRowNormalizer::class,
            self::SHEET_CANDIDATES
        );
    }

    public function process(string $token, int $batchId): array
    {
        return app(ImportService::class)->process(
            $token,
            $batchId,
            IssueRowNormalizer::class,
            self::SHEET_CANDIDATES,
            IssueImport::class
        );
    }
}
