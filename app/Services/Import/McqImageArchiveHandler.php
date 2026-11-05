<?php

namespace App\Services\Import;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Throwable;
use ZipArchive;

class McqImageArchiveHandler
{
    /**
     * Supported image extensions for bulk import.
     */
    public const SUPPORTED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];

    /**
     * Maximum allowed single image size in bytes (5 MB).
     */
    public const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

    /**
     * Maximum allowed uncompressed total size in bytes (150 MB) to prevent zip bombs.
     */
    public const MAX_TOTAL_UNCOMPRESSED_BYTES = 150 * 1024 * 1024;

    /**
     * Maximum total files in ZIP archive.
     */
    public const MAX_FILE_COUNT = 1000;

    /**
     * Validate and extract a ZIP archive into a target temporary directory.
     *
     * @param UploadedFile|string $zipFile
     * @param string $targetExtractDir
     * @return array{
     *     success: bool,
     *     error?: string,
     *     files_count: int,
     *     images_index: array<string, string>,
     *     basename_index: array<string, string>,
     *     duplicate_basenames: array<string, bool>
     * }
     */
    public function validateAndExtract(UploadedFile|string $zipFile, string $targetExtractDir): array
    {
        $realPath = $zipFile instanceof UploadedFile ? $zipFile->getRealPath() : $zipFile;

        if (!file_exists($realPath) || filesize($realPath) === 0) {
            return [
                'success' => false,
                'error' => 'The uploaded ZIP archive is empty or does not exist.',
                'files_count' => 0,
                'images_index' => [],
                'basename_index' => [],
                'duplicate_basenames' => [],
            ];
        }

        if (class_exists(ZipArchive::class)) {
            return $this->extractWithZipArchive($realPath, $targetExtractDir);
        }

        return $this->extractWithSimpleZipReader($realPath, $targetExtractDir);
    }

    /**
     * Extract using PHP's native ZipArchive extension.
     */
    protected function extractWithZipArchive(string $realPath, string $targetExtractDir): array
    {
        $zip = new ZipArchive();
        $openResult = $zip->open($realPath);

        if ($openResult !== true) {
            return [
                'success' => false,
                'error' => 'Failed to open ZIP archive. File may be corrupted or invalid.',
                'files_count' => 0,
                'images_index' => [],
                'basename_index' => [],
                'duplicate_basenames' => [],
            ];
        }

        $numFiles = $zip->numFiles;
        if ($numFiles > self::MAX_FILE_COUNT) {
            $zip->close();
            return [
                'success' => false,
                'error' => "ZIP archive contains too many files ({$numFiles}). Maximum allowed is " . self::MAX_FILE_COUNT . ".",
                'files_count' => 0,
                'images_index' => [],
                'basename_index' => [],
                'duplicate_basenames' => [],
            ];
        }

        $totalSize = 0;
        for ($i = 0; $i < $numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) {
                continue;
            }

            $entryName = $stat['name'];
            $entrySize = (int) ($stat['size'] ?? 0);
            $totalSize += $entrySize;

            if ($totalSize > self::MAX_TOTAL_UNCOMPRESSED_BYTES) {
                $zip->close();
                return [
                    'success' => false,
                    'error' => 'ZIP archive uncompressed size exceeds maximum limit of 150MB.',
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }

            if ($this->isUnsafeZipEntry($entryName)) {
                $zip->close();
                return [
                    'success' => false,
                    'error' => "Malicious or unsafe path detected in ZIP entry: '{$entryName}'. Extraction blocked.",
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }
        }

        if (!File::isDirectory($targetExtractDir)) {
            File::makeDirectory($targetExtractDir, 0777, true, true);
        }

        $realTargetDir = realpath($targetExtractDir);
        if (!$realTargetDir) {
            $zip->close();
            return [
                'success' => false,
                'error' => 'Failed to resolve temporary extraction directory.',
                'files_count' => 0,
                'images_index' => [],
                'basename_index' => [],
                'duplicate_basenames' => [],
            ];
        }

        $imagesIndex = [];
        $basenameIndex = [];
        $duplicateBasenames = [];
        $extractedFilesCount = 0;

        for ($i = 0; $i < $numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) {
                continue;
            }

            $entryName = $stat['name'];
            $normalizedRelative = str_replace('\\', '/', $entryName);

            if (str_ends_with($normalizedRelative, '/') || str_contains($normalizedRelative, '__MACOSX/') || basename($normalizedRelative) === '.DS_Store') {
                continue;
            }

            $ext = strtolower(pathinfo($normalizedRelative, PATHINFO_EXTENSION));
            if (!in_array($ext, self::SUPPORTED_EXTENSIONS, true)) {
                continue;
            }

            if ((int) $stat['size'] > self::MAX_IMAGE_BYTES) {
                $zip->close();
                $this->cleanup($targetExtractDir);
                return [
                    'success' => false,
                    'error' => "Image '{$entryName}' exceeds the maximum allowed file size of 5MB.",
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }

            $destinationPath = $realTargetDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedRelative);
            $destinationDir = dirname($destinationPath);

            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0777, true, true);
            }

            $parentReal = realpath($destinationDir);
            if (!$parentReal || !str_starts_with($parentReal, $realTargetDir)) {
                $zip->close();
                $this->cleanup($targetExtractDir);
                return [
                    'success' => false,
                    'error' => "Extraction path traversal attempt detected for entry: '{$entryName}'.",
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }

            $stream = $zip->getStream($entryName);
            if (!$stream) {
                continue;
            }

            $destHandle = @fopen($destinationPath, 'wb');
            if (!$destHandle) {
                fclose($stream);
                continue;
            }

            stream_copy_to_stream($stream, $destHandle);
            fclose($destHandle);
            fclose($stream);

            $extractedFilesCount++;
            $canonicalKey = strtolower(trim($normalizedRelative, '/'));
            $imagesIndex[$canonicalKey] = $destinationPath;

            $baseName = strtolower(basename($normalizedRelative));
            if (isset($basenameIndex[$baseName])) {
                $duplicateBasenames[$baseName] = true;
            } else {
                $basenameIndex[$baseName] = $destinationPath;
            }
        }

        $zip->close();

        return [
            'success' => true,
            'files_count' => $extractedFilesCount,
            'images_index' => $imagesIndex,
            'basename_index' => $basenameIndex,
            'duplicate_basenames' => $duplicateBasenames,
        ];
    }

    /**
     * Extract using pure-PHP SimpleZipReader fallback.
     */
    protected function extractWithSimpleZipReader(string $realPath, string $targetExtractDir): array
    {
        try {
            $reader = new SimpleZipReader(
                $realPath,
                self::MAX_TOTAL_UNCOMPRESSED_BYTES,
                self::MAX_IMAGE_BYTES,
                self::MAX_FILE_COUNT
            );
        } catch (Throwable $e) {
            return [
                'success' => false,
                'error' => 'Failed to parse ZIP archive: ' . $e->getMessage(),
                'files_count' => 0,
                'images_index' => [],
                'basename_index' => [],
                'duplicate_basenames' => [],
            ];
        }

        $fileNames = $reader->getFileNames();

        // Path traversal checks
        foreach ($fileNames as $name) {
            if ($this->isUnsafeZipEntry($name)) {
                return [
                    'success' => false,
                    'error' => "Malicious or unsafe path detected in ZIP entry: '{$name}'. Extraction blocked.",
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }
        }

        if (!File::isDirectory($targetExtractDir)) {
            File::makeDirectory($targetExtractDir, 0777, true, true);
        }

        $realTargetDir = realpath($targetExtractDir);
        if (!$realTargetDir) {
            return [
                'success' => false,
                'error' => 'Failed to resolve temporary extraction directory.',
                'files_count' => 0,
                'images_index' => [],
                'basename_index' => [],
                'duplicate_basenames' => [],
            ];
        }

        $imagesIndex = [];
        $basenameIndex = [];
        $duplicateBasenames = [];
        $extractedFilesCount = 0;

        foreach ($fileNames as $entryName) {
            $normalizedRelative = str_replace('\\', '/', $entryName);

            if (str_ends_with($normalizedRelative, '/') || str_contains($normalizedRelative, '__MACOSX/') || basename($normalizedRelative) === '.DS_Store') {
                continue;
            }

            $ext = strtolower(pathinfo($normalizedRelative, PATHINFO_EXTENSION));
            if (!in_array($ext, self::SUPPORTED_EXTENSIONS, true)) {
                continue;
            }

            try {
                $content = $reader->getEntry($entryName);
            } catch (Throwable $e) {
                $this->cleanup($targetExtractDir);
                return [
                    'success' => false,
                    'error' => "Failed to decompress image '{$entryName}': " . $e->getMessage(),
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }

            if ($content === null) {
                continue;
            }

            $destinationPath = $realTargetDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalizedRelative);
            $destinationDir = dirname($destinationPath);

            if (!File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0777, true, true);
            }

            $parentReal = realpath($destinationDir);
            if (!$parentReal || !str_starts_with($parentReal, $realTargetDir)) {
                $this->cleanup($targetExtractDir);
                return [
                    'success' => false,
                    'error' => "Extraction path traversal attempt detected for entry: '{$entryName}'.",
                    'files_count' => 0,
                    'images_index' => [],
                    'basename_index' => [],
                    'duplicate_basenames' => [],
                ];
            }

            file_put_contents($destinationPath, $content);

            $extractedFilesCount++;
            $canonicalKey = strtolower(trim($normalizedRelative, '/'));
            $imagesIndex[$canonicalKey] = $destinationPath;

            $baseName = strtolower(basename($normalizedRelative));
            if (isset($basenameIndex[$baseName])) {
                $duplicateBasenames[$baseName] = true;
            } else {
                $basenameIndex[$baseName] = $destinationPath;
            }
        }

        return [
            'success' => true,
            'files_count' => $extractedFilesCount,
            'images_index' => $imagesIndex,
            'basename_index' => $basenameIndex,
            'duplicate_basenames' => $duplicateBasenames,
        ];
    }

    /**
     * Check if a zip entry path contains path traversal or unsafe characters.
     */
    public function isUnsafeZipEntry(string $entryName): bool
    {
        if (str_contains($entryName, "\0")) {
            return true;
        }

        $normalized = str_replace('\\', '/', $entryName);

        if (str_starts_with($normalized, '/') || preg_match('/^[a-zA-Z]:/i', $normalized)) {
            return true;
        }

        $segments = explode('/', $normalized);
        foreach ($segments as $segment) {
            if ($segment === '..') {
                return true;
            }
        }

        return false;
    }

    /**
     * Locate an image file on disk from an image reference string.
     *
     * @param string $reference
     * @param array{
     *     images_index: array<string, string>,
     *     basename_index: array<string, string>,
     *     duplicate_basenames: array<string, bool>
     * } $indexData
     * @return array{found: bool, path?: string, error?: string}
     */
    public function locateImage(string $reference, array $indexData): array
    {
        $ref = trim($reference);
        if ($ref === '') {
            return ['found' => false];
        }

        $normalizedRef = strtolower(trim(str_replace('\\', '/', $ref), '/'));
        $imagesIndex = $indexData['images_index'] ?? [];
        $basenameIndex = $indexData['basename_index'] ?? [];
        $duplicateBasenames = $indexData['duplicate_basenames'] ?? [];

        if (isset($imagesIndex[$normalizedRef])) {
            return ['found' => true, 'path' => $imagesIndex[$normalizedRef]];
        }

        $base = strtolower(basename($normalizedRef));
        if (isset($duplicateBasenames[$base])) {
            return [
                'found' => false,
                'error' => "Multiple files named '{$base}' exist in the ZIP. Please specify the full relative folder path (e.g., 'images/{$base}').",
            ];
        }

        if (isset($basenameIndex[$base])) {
            return ['found' => true, 'path' => $basenameIndex[$base]];
        }

        return ['found' => false];
    }

    /**
     * Safely delete the temporary extracted directory.
     */
    public function cleanup(?string $dir): void
    {
        if (!empty($dir) && File::isDirectory($dir)) {
            try {
                File::deleteDirectory($dir);
            } catch (Exception $e) {
                // Ignore cleanup errors
            }
        }
    }
}
