<?php

namespace App\Services\Import;

use Exception;
use Illuminate\Http\UploadedFile;
use SimpleXMLElement;
use ZipArchive;

class SpreadsheetReader
{
    /**
     * Canonical 10-column canonical schema headers map.
     * Maps normalized lower-case headers to internal property names.
     */
    protected const HEADER_MAP = [
        'question topic' => 'topic',
        'topic' => 'topic',
        'category' => 'topic',
        'question category' => 'topic',

        'question subtopic' => 'subtopic',
        'subtopic' => 'subtopic',
        'subject' => 'subtopic',
        'question subject' => 'subtopic',

        'question number' => 'question_number',
        'question no' => 'question_number',
        'question no.' => 'question_number',
        'q.no' => 'question_number',
        'qno' => 'question_number',
        'number' => 'question_number',
        'no' => 'question_number',

        'question text' => 'question_text',
        'question' => 'question_text',
        'statement' => 'question_text',
        'question statement' => 'question_text',

        'option a' => 'option_a',
        'option_a' => 'option_a',
        'a' => 'option_a',

        'option b' => 'option_b',
        'option_b' => 'option_b',
        'b' => 'option_b',

        'option c' => 'option_c',
        'option_c' => 'option_c',
        'c' => 'option_c',

        'option d' => 'option_d',
        'option_d' => 'option_d',
        'd' => 'option_d',

        'correct option' => 'correct_option',
        'correct_option' => 'correct_option',
        'answer' => 'correct_option',
        'correct answer' => 'correct_option',
        'correct' => 'correct_option',

        'explanation' => 'explanation',
        'solution' => 'explanation',
        'answer explanation' => 'explanation',
    ];

    /**
     * Required normalized keys in an MCQ dataset.
     */
    public const REQUIRED_KEYS = [
        'topic',
        'subtopic',
        'question_number',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
    ];

    /**
     * Read and parse an uploaded file or local path into structured row data.
     *
     * @return array{
     *     sheets: array<string, array{headers: array<string, string>, rows: list<array{row_number: int, sheet: string, data: array<string, mixed>}>}>,
     *     image_references: array<string, array<string, string>>,
     *     format: string
     * }
     */
    public function read(string|UploadedFile $file): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $originalName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension === 'xlsx') {
            return $this->readXlsx($filePath);
        } elseif ($extension === 'csv') {
            return $this->readCsv($filePath);
        }

        throw new Exception("Unsupported file extension .{$extension}. Only .xlsx and .csv are supported.");
    }

    /**
     * Read and parse an XLSX workbook.
     */
    protected function readXlsx(string $filePath): array
    {
        $zipWrapper = $this->openZip($filePath);

        // 1. Shared Strings
        $sharedStrings = $this->loadSharedStrings($zipWrapper);

        // 2. Workbook sheet mapping
        $workbookXmlStr = $zipWrapper->get('xl/workbook.xml');
        $workbookRelsStr = $zipWrapper->get('xl/_rels/workbook.xml.rels');

        if (!$workbookXmlStr) {
            $zipWrapper->close();
            throw new Exception("Malformed XLSX file: missing xl/workbook.xml.");
        }

        $wbXml = @simplexml_load_string($workbookXmlStr);
        if (!$wbXml) {
            $zipWrapper->close();
            throw new Exception("Malformed XLSX workbook XML.");
        }

        $rels = [];
        if ($workbookRelsStr && ($relsXml = @simplexml_load_string($workbookRelsStr))) {
            foreach ($relsXml->Relationship as $rel) {
                $rels[(string) $rel['Id']] = (string) $rel['Target'];
            }
        }

        $sheets = [];
        $imageReferences = [];
        $nonMcqSheets = ['coverage', 'visual figures'];

        foreach ($wbXml->sheets->sheet as $sheet) {
            $sheetName = (string) $sheet['name'];
            $rId = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            $target = $rels[$rId] ?? "worksheets/sheet{$sheet['sheetId']}.xml";
            $targetPath = str_starts_with($target, '/') ? substr($target, 1) : (str_starts_with($target, 'xl/') ? $target : 'xl/' . $target);

            $sheetXmlStr = $zipWrapper->get($targetPath);
            if (!$sheetXmlStr) {
                continue;
            }

            // Check if this is the Image References sheet
            if (strtolower(trim($sheetName)) === 'image references') {
                $imageReferences = $this->parseImageReferencesSheet($sheetXmlStr, $sharedStrings);
                continue;
            }

            // Skip known auxiliary non-mcq sheets
            if (in_array(strtolower(trim($sheetName)), $nonMcqSheets)) {
                continue;
            }

            $parsedSheet = $this->parseXlsxWorksheet($sheetXmlStr, $sharedStrings, $sheetName);
            if (!empty($parsedSheet['rows'])) {
                $sheets[$sheetName] = $parsedSheet;
            }
        }

        $zipWrapper->close();

        if (empty($sheets)) {
            throw new Exception("No valid MCQ sheets found in workbook. Ensure the sheet has the required 10-column canonical schema.");
        }

        return [
            'sheets' => $sheets,
            'image_references' => $imageReferences,
            'format' => 'xlsx',
        ];
    }

    /**
     * Read and parse a CSV file.
     */
    protected function readCsv(string $filePath): array
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Unable to open CSV file for reading.");
        }

        // Read sample to detect delimiter and strip UTF-8 BOM
        $sample = fread($handle, 4096);
        rewind($handle);

        if (str_starts_with($sample, "\xEF\xBB\xBF")) {
            fseek($handle, 3);
        }

        $delimiter = $this->detectCsvDelimiter($sample);

        $headerRow = fgetcsv($handle, 0, $delimiter);
        if (!$headerRow) {
            fclose($handle);
            throw new Exception("CSV file is empty or missing headers.");
        }

        $headerMap = $this->mapHeaders($headerRow);
        $missing = $this->findMissingHeaders($headerMap);
        if (!empty($missing)) {
            fclose($handle);
            throw new Exception("Missing required columns in CSV: " . implode(', ', $missing) . ". Required schema: " . implode(', ', self::REQUIRED_KEYS));
        }

        $rows = [];
        $rowNumber = 1;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (empty(array_filter($data, fn ($val) => trim((string) $val) !== ''))) {
                continue;
            }

            $mappedRow = [];
            foreach ($headerMap as $colIndex => $key) {
                $mappedRow[$key] = trim((string) ($data[$colIndex] ?? ''));
            }

            $rows[] = [
                'row_number' => $rowNumber,
                'sheet' => 'CSV',
                'data' => $mappedRow,
            ];
        }

        fclose($handle);

        if (empty($rows)) {
            throw new Exception("The uploaded CSV file contains no data rows.");
        }

        return [
            'sheets' => [
                'CSV' => [
                    'headers' => $headerMap,
                    'rows' => $rows,
                ],
            ],
            'image_references' => [],
            'format' => 'csv',
        ];
    }

    /**
     * Parse a single worksheet XML in XLSX.
     */
    protected function parseXlsxWorksheet(string $sheetXmlStr, array $sharedStrings, string $sheetName): array
    {
        $xml = @simplexml_load_string($sheetXmlStr);
        if (!$xml || !isset($xml->sheetData)) {
            return ['headers' => [], 'rows' => []];
        }

        $rawRows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowNum = (int) $row['r'];
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = preg_replace('/[0-9]/', '', $ref);
                $type = (string) $c['t'];
                $val = '';
                if (isset($c->v)) {
                    $rawVal = (string) $c->v;
                    if ($type === 's') {
                        $idx = (int) $rawVal;
                        $val = $sharedStrings[$idx] ?? '';
                    } else {
                        $val = $rawVal;
                    }
                } elseif (isset($c->is->t)) {
                    $val = (string) $c->is->t;
                }
                $cells[$col] = trim((string) $val);
            }
            $rawRows[$rowNum] = $cells;
        }

        if (empty($rawRows)) {
            return ['headers' => [], 'rows' => []];
        }

        // Header is expected at row 1
        $firstRowNum = array_key_first($rawRows);
        $headerCells = $rawRows[$firstRowNum];

        $headerMap = $this->mapHeaders(array_values($headerCells), array_keys($headerCells));
        $missing = $this->findMissingHeaders($headerMap);

        // If missing required headers, this sheet is not an MCQ sheet
        if (!empty($missing)) {
            return ['headers' => [], 'rows' => []];
        }

        $rows = [];
        foreach ($rawRows as $rNum => $cells) {
            if ($rNum === $firstRowNum) {
                continue;
            }

            // Check if row has any non-empty cell
            if (empty(array_filter($cells, fn ($v) => $v !== ''))) {
                continue;
            }

            $mappedRow = [];
            foreach ($headerMap as $colKey => $key) {
                $mappedRow[$key] = $cells[$colKey] ?? '';
            }

            $rows[] = [
                'row_number' => $rNum,
                'sheet' => $sheetName,
                'data' => $mappedRow,
            ];
        }

        return [
            'headers' => $headerMap,
            'rows' => $rows,
        ];
    }

    /**
     * Parse the Image References sheet into an indexed map: [figure_id => [...]]
     */
    protected function parseImageReferencesSheet(string $sheetXmlStr, array $sharedStrings): array
    {
        $xml = @simplexml_load_string($sheetXmlStr);
        if (!$xml || !isset($xml->sheetData)) {
            return [];
        }

        $references = [];
        $headerColMap = [];

        foreach ($xml->sheetData->row as $row) {
            $rowNum = (int) $row['r'];
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = preg_replace('/[0-9]/', '', $ref);
                $type = (string) $c['t'];
                $val = '';
                if (isset($c->v)) {
                    $rawVal = (string) $c->v;
                    if ($type === 's') {
                        $idx = (int) $rawVal;
                        $val = $sharedStrings[$idx] ?? '';
                    } else {
                        $val = $rawVal;
                    }
                } elseif (isset($c->is->t)) {
                    $val = (string) $c->is->t;
                }
                $cells[$col] = trim((string) $val);
            }

            if ($rowNum === 1) {
                foreach ($cells as $col => $headerName) {
                    $norm = strtolower(trim($headerName));
                    $headerColMap[$col] = $norm;
                }
                continue;
            }

            $figureId = '';
            $item = [];
            foreach ($cells as $col => $val) {
                $name = $headerColMap[$col] ?? $col;
                $item[$name] = $val;
                if ($name === 'figure id' || $name === 'figure_id') {
                    $figureId = $val;
                }
            }

            if ($figureId !== '') {
                $references[$figureId] = $item;
            }
        }

        return $references;
    }

    /**
     * Map header list/associative array to normalized schema keys.
     *
     * @param array<int|string, string> $headerRow
     * @param array<int, string>|null $colKeys
     * @return array<string|int, string>
     */
    protected function mapHeaders(array $headerRow, ?array $colKeys = null): array
    {
        $mapped = [];
        foreach ($headerRow as $idx => $headerText) {
            $clean = strtolower(trim(preg_replace('/[\r\n\t]+/', ' ', (string) $headerText)));
            $colKey = $colKeys !== null ? ($colKeys[$idx] ?? $idx) : $idx;

            if (isset(self::HEADER_MAP[$clean])) {
                $mapped[$colKey] = self::HEADER_MAP[$clean];
            }
        }

        return $mapped;
    }

    /**
     * Determine if any required headers are missing.
     *
     * @param array<string|int, string> $headerMap
     * @return list<string>
     */
    protected function findMissingHeaders(array $headerMap): array
    {
        $present = array_values($headerMap);
        $missing = [];
        foreach (self::REQUIRED_KEYS as $required) {
            if (!in_array($required, $present, true)) {
                $missing[] = $required;
            }
        }

        return $missing;
    }

    /**
     * Auto-detect CSV delimiter.
     */
    protected function detectCsvDelimiter(string $sample): string
    {
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;

        foreach ($delimiters as $delim) {
            $count = substr_count($sample, $delim);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delim;
            }
        }

        return $bestDelimiter;
    }

    /**
     * Load shared strings from XLSX package.
     */
    protected function loadSharedStrings(object $zip): array
    {
        $sstXmlStr = $zip->get('xl/sharedStrings.xml');
        if (!$sstXmlStr) {
            return [];
        }

        $sst = @simplexml_load_string($sstXmlStr);
        if (!$sst) {
            return [];
        }

        $sharedStrings = [];
        foreach ($sst->si as $si) {
            if (isset($si->t)) {
                $sharedStrings[] = (string) $si->t;
            } elseif (isset($si->r)) {
                $text = '';
                foreach ($si->r as $r) {
                    $text .= (string) $r->t;
                }
                $sharedStrings[] = $text;
            } else {
                $sharedStrings[] = '';
            }
        }

        return $sharedStrings;
    }

    /**
     * Open a zip file using ZipArchive if available, or SimpleZipReader as fallback.
     * Enforces cumulative decompressed size limit (50 MB) and entry count limits.
     */
    protected function openZip(string $filePath): object
    {
        $maxCumulativeBytes = SimpleZipReader::DEFAULT_MAX_CUMULATIVE_BYTES;
        $maxSingleEntryBytes = SimpleZipReader::DEFAULT_MAX_SINGLE_ENTRY_BYTES;
        $maxEntryCount = SimpleZipReader::DEFAULT_MAX_ENTRY_COUNT;

        if (class_exists(ZipArchive::class)) {
            $zip = new ZipArchive();
            $res = $zip->open($filePath);
            if ($res === true) {
                if ($zip->numFiles > $maxEntryCount) {
                    $zip->close();
                    throw new Exception("Archive exceeds maximum entry count limit of {$maxEntryCount} entries (found {$zip->numFiles}).");
                }

                return new class($zip, $maxCumulativeBytes, $maxSingleEntryBytes) {
                    private ZipArchive $zip;
                    private int $maxCumulativeBytes;
                    private int $maxSingleEntryBytes;
                    private int $extractedBytes = 0;

                    public function __construct(ZipArchive $zip, int $maxCumulativeBytes, int $maxSingleEntryBytes)
                    {
                        $this->zip = $zip;
                        $this->maxCumulativeBytes = $maxCumulativeBytes;
                        $this->maxSingleEntryBytes = $maxSingleEntryBytes;
                    }

                    public function get(string $name): ?string
                    {
                        $stat = $this->zip->statName($name);
                        if ($stat !== false && $stat['size'] > $this->maxSingleEntryBytes) {
                            throw new Exception("Archive entry '{$name}' exceeds single-entry decompressed size limit.");
                        }

                        $content = $this->zip->getFromName($name);
                        if ($content === false) {
                            return null;
                        }

                        $this->extractedBytes += strlen($content);
                        if ($this->extractedBytes > $this->maxCumulativeBytes) {
                            throw new Exception("Archive cumulative decompression exceeded ceiling limit of 50 MB.");
                        }

                        return $content;
                    }

                    public function close(): void
                    {
                        $this->zip->close();
                    }
                };
            }
        }

        // Fallback to hardened pure-PHP SimpleZipReader
        $simple = new SimpleZipReader($filePath, $maxCumulativeBytes, $maxSingleEntryBytes, $maxEntryCount);
        return new class($simple) {
            private SimpleZipReader $reader;
            public function __construct(SimpleZipReader $reader) { $this->reader = $reader; }
            public function get(string $name): ?string {
                return $this->reader->getEntry($name);
            }
            public function close(): void {}
        };
    }
}

