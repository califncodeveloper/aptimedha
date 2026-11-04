<?php

namespace Tests\Unit;

use App\Services\Import\SimpleZipReader;
use App\Services\Import\SpreadsheetReader;
use Exception;
use PHPUnit\Framework\TestCase;

class ZipSecurityHardeningTest extends TestCase
{
    /**
     * Helper to construct a valid in-memory ZIP binary.
     */
    protected function createZipBinary(array $files, int $method = 8, ?int $fakeUncompSize = null): string
    {
        $zipData = '';
        $cdData = '';
        $entriesCount = count($files);

        foreach ($files as $name => $content) {
            $localOffset = strlen($zipData);

            if ($method === 8) {
                $compressed = gzdeflate($content);
            } else {
                $compressed = $content;
            }

            $compSize = strlen($compressed);
            $uncompSize = $fakeUncompSize !== null ? $fakeUncompSize : strlen($content);
            $crc = crc32($content);

            // Local header
            $localHeader = "\x50\x4b\x03\x04" . pack('vvvvvVVVvv', 20, 0, $method, 0, 0, $crc, $compSize, $uncompSize, strlen($name), 0) . $name . $compressed;
            $zipData .= $localHeader;

            // Central directory header
            $cdHeader = "\x50\x4b\x01\x02" . pack('vvvvvvVVVvvvvvVV', 20, 20, 0, $method, 0, 0, $crc, $compSize, $uncompSize, strlen($name), 0, 0, 0, 0, 0, $localOffset) . $name;
            $cdData .= $cdHeader;
        }

        $cdOffset = strlen($zipData);
        $cdSize = strlen($cdData);
        $eocd = "\x50\x4b\x05\x06" . pack('vvvvVVv', 0, 0, $entriesCount, $entriesCount, $cdSize, $cdOffset, 0);

        return $zipData . $cdData . $eocd;
    }

    /**
     * Test 1: Valid client workbook is successfully read within limits.
     */
    public function test_valid_workbook_within_limits(): void
    {
        $workbookPath = 'client_docs/Placement_All_Topics_2_Sample_MCQs.xlsx';
        if (!file_exists($workbookPath)) {
            $this->markTestSkipped('Client workbook not found.');
        }

        $reader = new SimpleZipReader($workbookPath);
        $this->assertTrue($reader->hasEntry('xl/workbook.xml'));
        $content = $reader->getEntry('xl/workbook.xml');
        $this->assertNotNull($content);
        $this->assertStringContainsString('sheets', $content);
        $this->assertLessThan(SimpleZipReader::DEFAULT_MAX_CUMULATIVE_BYTES, $reader->getExtractedCumulativeBytes());
    }

    /**
     * Test 2: Malformed archive throws graceful exception.
     */
    public function test_malformed_archive_throws_exception(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'mal_');
        file_put_contents($tmp, str_repeat('X', 100));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid ZIP file or missing End of Central Directory');

        try {
            new SimpleZipReader($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * Test 3: Excessive entry count is gracefully rejected.
     */
    public function test_excessive_entry_count_rejected(): void
    {
        $files = [];
        // Create 15 entries but set max entry limit to 10
        for ($i = 1; $i <= 15; $i++) {
            $files["file_{$i}.txt"] = "Content {$i}";
        }
        $zipBin = $this->createZipBinary($files);
        $tmp = tempnam(sys_get_temp_dir(), 'zip_');
        file_put_contents($tmp, $zipBin);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Archive exceeds maximum entry count limit');

        try {
            new SimpleZipReader($tmp, 52428800, 31457280, 10); // max 10 entries
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * Test 4: Excessive declared decompressed size rejected.
     */
    public function test_excessive_decompressed_size_rejected(): void
    {
        // Use random bytes so compression ratio is ~1.0, isolating the cumulative size test
        $files = ['large.txt' => random_bytes(5000)];
        $zipBin = $this->createZipBinary($files);
        $tmp = tempnam(sys_get_temp_dir(), 'zip_');
        file_put_contents($tmp, $zipBin);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Archive exceeds cumulative decompressed size limit');

        try {
            // Set max cumulative limit to 1,000 bytes while single entry limit is 10,000 bytes (file is 5,000 bytes)
            new SimpleZipReader($tmp, 1000, 10000);
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * Test 5: ZIP bomb simulation with excessive compression ratio is rejected.
     */
    public function test_zip_bomb_simulation_rejected(): void
    {
        // 10,000 'A' characters deflates down to ~35 bytes, giving compression ratio > 250:1
        $bombPayload = str_repeat('A', 10000);
        $zipBin = $this->createZipBinary(['bomb.xml' => $bombPayload]);
        $tmp = tempnam(sys_get_temp_dir(), 'bomb_');
        file_put_contents($tmp, $zipBin);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('possible ZIP bomb');

        try {
            // Set max compression ratio to 50:1 (payload is ~280:1)
            new SimpleZipReader($tmp, 52428800, 31457280, 1000, 50);
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * Test 6: Unsupported compression methods are rejected.
     */
    public function test_unsupported_compression_method_rejected(): void
    {
        // Method 12 (BZIP2) is unsupported in standard XLSX
        $zipBin = $this->createZipBinary(['test.txt' => 'Sample'], 12);
        $tmp = tempnam(sys_get_temp_dir(), 'unsupp_');
        file_put_contents($tmp, $zipBin);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported ZIP compression method (12)');

        try {
            new SimpleZipReader($tmp);
        } finally {
            @unlink($tmp);
        }
    }

    /**
     * Test 7: SpreadsheetReader rejects malformed or bomb archives gracefully.
     */
    public function test_spreadsheet_reader_rejects_malformed_archive(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'bad_') . '.xlsx';
        file_put_contents($tmp, 'CORRUPT_NOT_A_ZIP');

        $reader = new SpreadsheetReader();

        $this->expectException(Exception::class);
        try {
            $reader->read($tmp);
        } finally {
            @unlink($tmp);
        }
    }
}
