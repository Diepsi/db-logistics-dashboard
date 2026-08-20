<?php

namespace App\Services;

use App\Exceptions\ImportException;
use App\Exceptions\SheetNotFoundException;
use App\Imports\ShipmentImport;
use App\Support\Contracts\ImportNormalizer;
use App\Support\ExcelStreamReader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportService
{
    /**
     * Generic preview: validasi header, hitung baris valid/invalid/duplicate.
     *
     * @param  class-string<ImportNormalizer>  $normalizerClass
     */
    public function preview(UploadedFile $file, string $normalizerClass, array $sheetCandidates): array
    {
        ini_set('memory_limit', '1024M');

        $headers = [];
        $rows = ExcelStreamReader::rows(
            $file->getRealPath(),
            $headers,
            $file->getClientOriginalExtension(),
            null,
            $sheetCandidates,
            [$normalizerClass, 'canonicalKey']
        );

        try {
            $rows->rewind();

            $found = [];

            foreach ($headers as $header) {
                $canonical = $normalizerClass::canonicalKey($header);

                if ($canonical !== null) {
                    $found[$canonical] = true;
                }
            }

            if ($found === []) {
                throw new ImportException('File Excel kosong atau tidak memiliki data.');
            }

            $missing = array_values(array_diff($normalizerClass::REQUIRED_HEADERS, array_keys($found)));

            if ($missing !== []) {
                throw new ImportException(
                    'Header kolom wajib tidak lengkap. Kolom berikut tidak ditemukan: '.implode(', ', $missing)
                );
            }

            $total = 0;
            $valid = 0;
            $invalid = 0;
            $duplicate = 0;
            $invalidSamples = [];
            $seenWaybills = [];

            while ($rows->valid()) {
                $row = $rows->current();
                $rows->next();

                if ($normalizerClass::isEmptyRow($row)) {
                    continue;
                }

                $total++;

                $reason = $normalizerClass::valid($row);
                if ($reason !== null) {
                    $invalid++;

                    if (count($invalidSamples) < 10) {
                        $invalidSamples[] = [
                            'primary_key' => $row['no_resi'] ?? $row['waybill_no'] ?? $row[array_key_first($row)] ?? null,
                            'reason' => $reason,
                        ];
                    }

                    continue;
                }

                $primaryKey = trim((string) ($row['no_resi'] ?? $row['waybill_no'] ?? $row[array_key_first($row)] ?? ''));
                if (isset($seenWaybills[$primaryKey])) {
                    $duplicate++;

                    continue;
                }
                $seenWaybills[$primaryKey] = true;

                $valid++;
            }
        } catch (SheetNotFoundException $e) {
            throw new ImportException($e->getMessage());
        } catch (ImportException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ImportException('File tidak dapat dibaca (terlalu besar atau format tidak didukung): '.$e->getMessage());
        }

        $token = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension());

        Storage::disk('local')->putFileAs('imports/temp', $file, $token.'.'.$extension);

        return compact('total', 'valid', 'invalid', 'duplicate', 'invalidSamples', 'token');
    }

    /**
     * Generic process: baca ulang file dari token, eksekusi import per chunk.
     *
     * @param  class-string<ImportNormalizer>  $normalizerClass
     * @param  class-string                     $importClass  Maatwebsite Excel import class
     */
    public function process(
        string $token,
        int $batchId,
        string $normalizerClass,
        array $sheetCandidates,
        string $importClass = ShipmentImport::class
    ): array {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '600');

        $tempDir = Storage::disk('local')->path('imports/temp');
        $matches = glob($tempDir.DIRECTORY_SEPARATOR.$token.'.*');

        if (! $matches) {
            throw new ImportException('File sementara tidak ditemukan. Silakan unggah ulang file.');
        }

        $filePath = $matches[0];

        try {
            $import = new $importClass($batchId);

            $headers = [];
            $chunk = [];

            foreach (ExcelStreamReader::rows(
                $filePath,
                $headers,
                null,
                null,
                $sheetCandidates,
                [$normalizerClass, 'canonicalKey']
            ) as $row) {
                $chunk[] = $row;

                if (count($chunk) >= 1000) {
                    $import->collection(new Collection($chunk));
                    $chunk = [];
                }
            }

            if ($chunk !== []) {
                $import->collection(new Collection($chunk));
            }
        } finally {
            Storage::disk('local')->delete('imports/temp/'.basename($filePath));
        }

        return [
            'total' => $import->total,
            'valid' => $import->valid,
            'invalid' => $import->invalid,
            'duplicate' => $import->duplicate,
            'new_rows' => $import->newRows,
            'updated_rows' => $import->updatedRows,
        ];
    }
}
