<?php

namespace App\Services\Import;

use Exception;

/**
 * Lightweight pure-PHP PKZip reader using built-in zlib.
 * Hardened against ZIP bombs, excessive archive sizes, and malformed structures.
 */
class SimpleZipReader
{
    public const DEFAULT_MAX_CUMULATIVE_BYTES = 52428800;  // 50 MB
    public const DEFAULT_MAX_SINGLE_ENTRY_BYTES = 31457280; // 30 MB
    public const DEFAULT_MAX_ENTRY_COUNT = 1000;
    public const DEFAULT_MAX_COMPRESSION_RATIO = 100;

    private string $filePath;
    private int $maxCumulativeBytes;
    private int $maxSingleEntryBytes;
    private int $maxEntryCount;
    private int $maxCompressionRatio;

    private int $extractedCumulativeBytes = 0;

    /** @var array<string, array{method: int, compSize: int, uncompSize: int, offset: int}> */
    private array $entries = [];

    public function __construct(
        string $filePath,
        int $maxCumulativeBytes = self::DEFAULT_MAX_CUMULATIVE_BYTES,
        int $maxSingleEntryBytes = self::DEFAULT_MAX_SINGLE_ENTRY_BYTES,
        int $maxEntryCount = self::DEFAULT_MAX_ENTRY_COUNT,
        int $maxCompressionRatio = self::DEFAULT_MAX_COMPRESSION_RATIO
    ) {
        $this->filePath = $filePath;
        $this->maxCumulativeBytes = $maxCumulativeBytes;
        $this->maxSingleEntryBytes = $maxSingleEntryBytes;
        $this->maxEntryCount = $maxEntryCount;
        $this->maxCompressionRatio = $maxCompressionRatio;

        $this->readCentralDirectory();
    }

    private function readCentralDirectory(): void
    {
        $fh = @fopen($this->filePath, 'rb');
        if (!$fh) {
            throw new Exception("Unable to open ZIP archive: {$this->filePath}");
        }

        fseek($fh, 0, SEEK_END);
        $fileSize = ftell($fh);

        if ($fileSize < 22) {
            fclose($fh);
            throw new Exception("File is too small to be a valid ZIP archive.");
        }

        // Search for End of Central Directory Record (EOCD signature: \x50\x4b\x05\x06)
        $searchLen = min(65557, $fileSize);
        fseek($fh, $fileSize - $searchLen);
        $buffer = fread($fh, $searchLen);

        $eocdPos = strrpos($buffer, "\x50\x4b\x05\x06");
        if ($eocdPos === false) {
            fclose($fh);
            throw new Exception("Invalid ZIP file or missing End of Central Directory.");
        }

        $eocd = unpack('vdisk/vdiskStart/vtotalDisk/vtotalEntries/VcdSize/VcdOffset/vcommentLen', substr($buffer, $eocdPos + 4, 18));
        $cdOffset = $eocd['cdOffset'];
        $totalEntries = $eocd['totalEntries'];

        // Guard against excessive archive entry count
        if ($totalEntries > $this->maxEntryCount) {
            fclose($fh);
            throw new Exception("Archive exceeds maximum entry count limit of {$this->maxEntryCount} entries (found {$totalEntries}).");
        }

        if ($cdOffset >= $fileSize) {
            fclose($fh);
            throw new Exception("Malformed ZIP archive: central directory offset points beyond file bounds.");
        }

        fseek($fh, $cdOffset);
        $declaredCumulativeSize = 0;

        for ($i = 0; $i < $totalEntries; $i++) {
            $sig = fread($fh, 4);
            if ($sig !== "\x50\x4b\x01\x02") {
                break;
            }
            $header = unpack('vversion/vversionExtract/vflags/vmethod/vmodTime/vmodDate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen/vcommentLen/vdiskStart/vintAttr/VextAttr/Voffset', fread($fh, 42));
            $name = fread($fh, $header['nameLen']);
            if ($header['extraLen'] > 0) {
                fseek($fh, $header['extraLen'], SEEK_CUR);
            }
            if ($header['commentLen'] > 0) {
                fseek($fh, $header['commentLen'], SEEK_CUR);
            }

            // Verify compression method: only Stored (0) and Deflate (8) supported
            $method = $header['method'];
            if ($method !== 0 && $method !== 8) {
                fclose($fh);
                throw new Exception("Unsupported ZIP compression method ({$method}) in entry '{$name}'. Only Stored (0) and Deflate (8) are supported.");
            }

            // Check single entry limit
            if ($header['uncompSize'] > $this->maxSingleEntryBytes) {
                fclose($fh);
                throw new Exception("Archive entry '{$name}' exceeds single-entry decompressed size limit ({$this->maxSingleEntryBytes} bytes).");
            }

            // Check declared compression ratio for deflated entry
            if ($method === 8 && $header['compSize'] > 0) {
                $ratio = $header['uncompSize'] / $header['compSize'];
                if ($ratio > $this->maxCompressionRatio) {
                    fclose($fh);
                    throw new Exception("Archive entry '{$name}' has an excessive compression ratio ({$ratio}:1), possible ZIP bomb.");
                }
            }

            $declaredCumulativeSize += $header['uncompSize'];
            if ($declaredCumulativeSize > $this->maxCumulativeBytes) {
                fclose($fh);
                throw new Exception("Archive exceeds cumulative decompressed size limit of " . round($this->maxCumulativeBytes / 1048576) . " MB.");
            }

            $this->entries[$name] = [
                'method' => $method,
                'compSize' => $header['compSize'],
                'uncompSize' => $header['uncompSize'],
                'offset' => $header['offset'],
            ];
        }
        fclose($fh);
    }

    /**
     * @return list<string>
     */
    public function getFileNames(): array
    {
        return array_keys($this->entries);
    }

    public function hasEntry(string $name): bool
    {
        return isset($this->entries[$name]);
    }

    public function getEntry(string $name): ?string
    {
        if (!isset($this->entries[$name])) {
            return null;
        }

        $meta = $this->entries[$name];
        $fh = @fopen($this->filePath, 'rb');
        if (!$fh) {
            return null;
        }

        fseek($fh, $meta['offset']);
        $sig = fread($fh, 4);
        if ($sig !== "\x50\x4b\x03\x04") {
            fclose($fh);
            throw new Exception("Malformed ZIP entry header for '{$name}'.");
        }

        $localHeader = unpack('vversion/vflags/vmethod/vmodTime/vmodDate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen', fread($fh, 26));
        fseek($fh, $localHeader['nameLen'] + $localHeader['extraLen'], SEEK_CUR);

        $data = fread($fh, $meta['compSize']);
        fclose($fh);

        $uncompressed = null;
        if ($meta['method'] === 0) {
            $uncompressed = $data;
        } elseif ($meta['method'] === 8) {
            $uncompressed = @gzinflate($data);
            if ($uncompressed === false) {
                throw new Exception("Failed to decompress entry '{$name}'. Corrupt deflated data.");
            }
        }

        if ($uncompressed === null) {
            return null;
        }

        // Verify runtime decompressed size against thresholds
        $actualLen = strlen($uncompressed);
        if ($actualLen > $this->maxSingleEntryBytes) {
            throw new Exception("Decompressed entry '{$name}' exceeded single-entry ceiling limit.");
        }

        $this->extractedCumulativeBytes += $actualLen;
        if ($this->extractedCumulativeBytes > $this->maxCumulativeBytes) {
            throw new Exception("Archive cumulative decompression exceeded ceiling limit of " . round($this->maxCumulativeBytes / 1048576) . " MB.");
        }

        return $uncompressed;
    }

    public function getExtractedCumulativeBytes(): int
    {
        return $this->extractedCumulativeBytes;
    }
}
