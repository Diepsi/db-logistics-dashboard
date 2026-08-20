<?php

namespace App\Support;

use App\Exceptions\SheetNotFoundException;
use Generator;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use SimpleXMLElement;
use XMLReader;
use ZipArchive;

class ExcelStreamReader
{
    private const MAX_COLUMN = 52; // A..AZ

    /**
     * Baca worksheet (default: pertama, atau yang sesuai $sheetName) secara
     * streaming (RAM konstan untuk .xlsx). Nama sheet dicocokkan
     * case-insensitive + trim. Baris pertama yang berisi data dianggap
     * header; baris berikutnya di-yield sebagai array ber-keys header
     * ternormalisasi.
     *
     * @return Generator<string, array<string, mixed>>
     */
    public static function rows(string $path, array &$headers, ?string $extension = null, ?int $maxRows = null, string|array|null $sheetName = null, ?callable $keyResolver = null): Generator
    {
        $ext = $extension !== null
            ? strtolower($extension)
            : strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === 'xlsx') {
            yield from self::readXlsx($path, $headers, $maxRows, $sheetName, $keyResolver);

            return;
        }

        yield from self::readSpreadsheet($path, $headers, $ext, $maxRows, $sheetName, $keyResolver);
    }

    private static function readXlsx(string $path, array &$headers, ?int $maxRows, string|array|null $sheetName, ?callable $keyResolver): Generator
    {
        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new \RuntimeException('File Excel tidak valid (bukan file xlsx yang benar).');
        }

        $sheetEntry = null;
        $triedNames = [];

        if ($sheetName !== null) {
            $candidates = is_array($sheetName) ? $sheetName : [$sheetName];

            foreach ($candidates as $candidate) {
                $triedNames[] = $candidate;
                $entry = self::sheetEntryByName($zip, $candidate);

                if ($entry !== null) {
                    $sheetEntry = $entry;
                    break;
                }
            }
        } else {
            $sheetEntry = self::firstSheetEntry($zip);
        }

        if ($sheetName !== null && $sheetEntry === null) {
            $zip->close();
            throw self::sheetNotFound($sheetName);
        }

        $sheetXml = $sheetEntry !== null ? $zip->getFromName($sheetEntry) : false;
        $shared = self::readSharedStrings($zip);
        $zip->close();

        if ($sheetXml === false) {
            throw new \RuntimeException('Tidak ada worksheet di dalam file.');
        }

        $headers = [];
        $headerKeys = [];
        $isHeaderRow = true;
        $maxRows = $maxRows ?? PHP_INT_MAX;

        $reader = new XMLReader;
        $reader->XML($sheetXml);
        unset($sheetXml);

        $cells = [];
        $inCell = false;
        $cellRef = null;
        $cellType = null;
        $cellValue = '';
        $rowNum = 0;

        while ($reader->read()) {
            $name = $reader->localName;

            if ($reader->nodeType === XMLReader::ELEMENT) {
                if ($name === 'row') {
                    $rowNum = (int) $reader->getAttribute('r');

                    if ($rowNum > $maxRows) {
                        break;
                    }

                    $cells = [];
                } elseif ($name === 'c') {
                    $inCell = true;
                    $cellRef = $reader->getAttribute('r');
                    $cellType = $reader->getAttribute('t') ?? 'n';
                    $cellValue = '';
                }

                continue;
            }

            if ($reader->nodeType === XMLReader::TEXT || $reader->nodeType === XMLReader::CDATA) {
                if ($inCell) {
                    $cellValue .= $reader->value;
                }

                continue;
            }

            if ($reader->nodeType !== XMLReader::END_ELEMENT) {
                continue;
            }

            if ($name === 'c') {
                $inCell = false;

                $value = self::resolveCell($cellType, $cellValue, $shared);

                if ($value !== null && $value !== '') {
                    $col = preg_replace('/\d+/', '', (string) $cellRef);

                    if (Coordinate::columnIndexFromString($col) <= self::MAX_COLUMN) {
                        $cells[$col] = $value;
                    }
                }
            } elseif ($name === 'row' && $cells !== []) {
                if ($isHeaderRow) {
                    $isHeaderRow = false;

                    foreach ($cells as $col => $value) {
                        $raw = trim((string) $value);

                        if ($raw !== '') {
                            $headers[$col] = $raw;
                            $headerKeys[$col] = $keyResolver
                                ? $keyResolver($raw)
                                : strtolower(preg_replace('/[^a-z0-9]+/', '_', mb_strtolower($raw)));
                        }
                    }

                    continue;
                }

                $assoc = [];

                foreach ($cells as $col => $value) {
                    $key = $headerKeys[$col] ?? null;

                    if ($key !== null) {
                        $assoc[$key] = $value;
                    }
                }

                if ($assoc !== []) {
                    yield $assoc;
                }
            }
        }

        $reader->close();
    }

    private static function resolveCell(string $type, string $value, array $shared): mixed
    {
        if ($value === '') {
            return null;
        }

        return match ($type) {
            's' => $shared[(int) $value] ?? null,
            'inlineStr', 'str' => $value,
            'b' => $value === '1',
            'e' => null,
            default => (float) $value,
        };
    }

    private static function readSharedStrings(ZipArchive $zip): array
    {
        $index = $zip->locateName('xl/sharedStrings.xml');

        if ($index === false) {
            return [];
        }

        $xml = $zip->getFromIndex($index);

        if ($xml === false || $xml === '') {
            return [];
        }

        $strings = [];
        $document = new SimpleXMLElement($xml);

        foreach ($document->si as $item) {
            $strings[] = isset($item->r) && $item->r->count() > 0
                ? implode('', array_map('strval', (array) $item->r->t))
                : implode('', array_map('strval', (array) $item->t));
        }

        return $strings;
    }

    private static function firstSheetEntry(ZipArchive $zip): ?string
    {
        foreach (['xl/worksheets/sheet1.xml', 'xl/worksheets/sheet.xml'] as $candidate) {
            if ($zip->locateName($candidate) !== false) {
                return $candidate;
            }
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if ($name !== false && preg_match('#^xl/worksheets/sheet\d+\.xml$#', $name)) {
                return $name;
            }
        }

        return null;
    }

    private static function sheetEntryByName(ZipArchive $zip, string $sheetName): ?string
    {
        $workbookIndex = $zip->locateName('xl/workbook.xml');

        if ($workbookIndex === false) {
            return null;
        }

        $workbook = $zip->getFromIndex($workbookIndex);

        if ($workbook === false || $workbook === '') {
            return null;
        }

        $target = null;
        $document = new SimpleXMLElement($workbook);
        $relationships = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

        foreach ($document->sheets->sheet as $sheet) {
            $name = (string) $sheet['name'];
            $rId = (string) $sheet->attributes($relationships)['id'];

            if (strcasecmp(trim($name), trim($sheetName)) !== 0) {
                continue;
            }

            $target = self::resolveWorkbookRelation($zip, $rId);
            break;
        }

        if ($target === null) {
            return null;
        }

        return str_starts_with($target, '/')
            ? ltrim($target, '/')
            : 'xl/'.ltrim($target, '/');
    }

    private static function resolveWorkbookRelation(ZipArchive $zip, string $rId): ?string
    {
        $relsIndex = $zip->locateName('xl/_rels/workbook.xml.rels');

        if ($relsIndex === false) {
            return null;
        }

        $rels = $zip->getFromIndex($relsIndex);

        if ($rels === false || $rels === '') {
            return null;
        }

        $document = new SimpleXMLElement($rels);

        foreach ($document->Relationship as $relationship) {
            if ((string) $relationship['Id'] === $rId) {
                return (string) $relationship['Target'];
            }
        }

        return null;
    }

    private static function sheetNotFound(string|array $sheetName): SheetNotFoundException
    {
        $names = is_array($sheetName) ? $sheetName : [$sheetName];
        $quoted = implode(' atau ', array_map(fn ($n) => "'{$n}'", $names));

        return new SheetNotFoundException(
            sprintf("Sheet %s tidak ditemukan pada file Excel yang diunggah.", $quoted)
        );
    }

    private static function readSpreadsheet(string $path, array &$headers, string $ext, ?int $maxRows, string|array|null $sheetName, ?callable $keyResolver): Generator
    {
        $reader = match ($ext) {
            'xls' => new Xls,
            default => IOFactory::createReaderForFile($path),
        };

        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);

        $maxRows = $maxRows ?? PHP_INT_MAX;

        if ($reader instanceof Xlsx || $reader instanceof Xls) {
            $reader->setReadFilter(new class($maxRows) implements IReadFilter
            {
                public function __construct(private readonly int $maxRows) {}

                public function readCell($columnAddress, $row, $worksheetName = ''): bool
                {
                    return $row <= $this->maxRows
                        && Coordinate::columnIndexFromString($columnAddress) <= ExcelStreamReader::MAX_COLUMN;
                }
            });
        }

        $spreadsheet = $reader->load($path);

        if ($sheetName !== null) {
            $candidates = is_array($sheetName) ? $sheetName : [$sheetName];
            $sheet = null;

            foreach ($candidates as $candidate) {
                foreach ($spreadsheet->getAllSheets() as $worksheet) {
                    if (strcasecmp(trim($worksheet->getTitle()), trim($candidate)) === 0) {
                        $sheet = $worksheet;
                        break 2;
                    }
                }
            }

            if ($sheet === null) {
                throw self::sheetNotFound($sheetName);
            }
        } else {
            $sheet = $spreadsheet->getActiveSheet();
        }

        $headers = [];
        $headerKeys = [];
        $buffer = [];
        $currentRow = 0;
        $isHeaderRow = true;

        foreach ($sheet->getCellCollection() as $cell) {
            $rowNo = $cell->getRow();

            if ($rowNo !== $currentRow) {
                if ($buffer !== []) {
                    yield $buffer;
                }
                $buffer = [];
                $currentRow = $rowNo;
            }

            [$letter] = Coordinate::coordinateFromString($cell->getCoordinate());

            if ($isHeaderRow) {
                $isHeaderRow = false;

                $raw = trim((string) $cell->getValue());

                if ($raw !== '') {
                    $headers[$letter] = $raw;
                    $headerKeys[$letter] = $keyResolver
                        ? $keyResolver($raw)
                        : strtolower(preg_replace('/[^a-z0-9]+/', '_', mb_strtolower($raw)));
                }

                continue;
            }

            $key = $headerKeys[$letter] ?? null;

            if ($key !== null) {
                $buffer[$key] = $cell->getValue();
            }
        }

        if ($buffer !== []) {
            yield $buffer;
        }
    }
}
