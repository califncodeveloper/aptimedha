<?php

namespace App\Services\Import;

use App\Models\ImportBatch;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use App\Services\CloudinaryService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class McqImportProcessor
{
    protected CloudinaryService $cloudinary;

    public function __construct(?CloudinaryService $cloudinary = null)
    {
        $this->cloudinary = $cloudinary ?? app(CloudinaryService::class);
    }

    /**
     * Process and commit validated import rows into the database.
     *
     * @param list<array<string, mixed>> $validatedRows
     * @param array{
     *     filename: string,
     *     user: User,
     *     skip_duplicates?: bool,
     *     skip_invalids?: bool
     * } $options
     * @return array{
     *     batch_id: int,
     *     total_rows: int,
     *     imported_count: int,
     *     skipped_count: int,
     *     duplicate_count: int,
     *     invalid_count: int,
     *     missing_image_count: int,
     *     uploaded_images_count: int,
     *     failed_images_count: int,
     *     details: list<array<string, mixed>>
     * }
     */
    public function process(array $validatedRows, array $options): array
    {
        $filename = $options['filename'] ?? 'import.xlsx';
        $user = $options['user'];
        $skipDuplicates = (bool) ($options['skip_duplicates'] ?? true);
        $skipInvalids = (bool) ($options['skip_invalids'] ?? true);

        $totalRows = count($validatedRows);
        $importedCount = 0;
        $skippedCount = 0;
        $duplicateCount = 0;
        $invalidCount = 0;
        $missingImageCount = 0;
        $uploadedImagesCount = 0;
        $failedImagesCount = 0;

        $auditLog = [];
        $topicIncrementCounts = [];

        // Track Cloudinary assets uploaded during this batch for safe rollback cleanup
        $uploadedPublicIdsThisBatch = [];
        $uploadedUrlCache = [];

        // Create initial pending batch
        $batch = ImportBatch::create([
            'user_id' => $user->id,
            'filename' => $filename,
            'total_rows' => $totalRows,
            'successful_rows' => 0,
            'failed_rows' => 0,
            'status' => 'processing',
        ]);

        try {
            DB::transaction(function () use (
                $validatedRows,
                $skipDuplicates,
                $skipInvalids,
                &$importedCount,
                &$skippedCount,
                &$duplicateCount,
                &$invalidCount,
                &$missingImageCount,
                &$uploadedImagesCount,
                &$failedImagesCount,
                &$auditLog,
                &$topicIncrementCounts,
                &$uploadedPublicIdsThisBatch,
                &$uploadedUrlCache
            ) {
                // Track dynamic max question number per topic within transaction
                $topicCurrentNumbers = [];

                foreach ($validatedRows as $row) {
                    $status = $row['status'];
                    $sheet = $row['sheet'] ?? 'Default';
                    $rowNum = $row['row_number'] ?? 0;

                    if (!empty($row['missing_images'])) {
                        $missingImageCount += count($row['missing_images']);
                    }

                    // Handle Invalid rows
                    if ($status === 'invalid') {
                        $invalidCount++;
                        $skippedCount++;
                        $auditLog[] = [
                            'row' => $rowNum,
                            'sheet' => $sheet,
                            'status' => 'invalid',
                            'reason' => implode('; ', $row['errors'] ?? ['Validation failed']),
                            'question' => mb_substr($row['question_text'] ?? '', 0, 80),
                        ];
                        if (!$skipInvalids) {
                            throw new Exception("Row {$rowNum} in {$sheet} is invalid: " . implode('; ', $row['errors']));
                        }
                        continue;
                    }

                    // Handle Duplicate rows
                    if ($status === 'duplicate') {
                        $duplicateCount++;
                        $skippedCount++;
                        $auditLog[] = [
                            'row' => $rowNum,
                            'sheet' => $sheet,
                            'status' => 'duplicate',
                            'reason' => implode('; ', $row['errors'] ?? ['Duplicate question']),
                            'question' => mb_substr($row['question_text'] ?? '', 0, 80),
                        ];
                        if (!$skipDuplicates) {
                            throw new Exception("Row {$rowNum} in {$sheet} is a duplicate: " . implode('; ', $row['errors']));
                        }
                        continue;
                    }

                    // Handle Valid rows: upload any referenced local images to Cloudinary
                    $rowPublicIds = [];
                    $rowImageUrls = [
                        'question_image' => $row['question_image'] ?? null,
                        'option_a_image' => $row['option_a_image'] ?? null,
                        'option_b_image' => $row['option_b_image'] ?? null,
                        'option_c_image' => $row['option_c_image'] ?? null,
                        'option_d_image' => $row['option_d_image'] ?? null,
                        'explanation_image' => $row['explanation_image'] ?? null,
                    ];

                    $fieldFolderMap = [
                        'question_image' => 'aptimedha/mcqs/questions',
                        'option_a_image' => 'aptimedha/mcqs/options',
                        'option_b_image' => 'aptimedha/mcqs/options',
                        'option_c_image' => 'aptimedha/mcqs/options',
                        'option_d_image' => 'aptimedha/mcqs/options',
                        'explanation_image' => 'aptimedha/mcqs/explanations',
                    ];

                    $imageUploadFailed = false;
                    $imageUploadError = null;
                    $failedField = null;

                    if (!empty($row['image_files']) && is_array($row['image_files'])) {
                        foreach ($fieldFolderMap as $imgField => $folder) {
                            if (!empty($row['image_files'][$imgField])) {
                                $localFilePath = $row['image_files'][$imgField];

                                if (file_exists($localFilePath)) {
                                    // Check local cache if file was already uploaded during this run
                                    if (isset($uploadedUrlCache[$localFilePath])) {
                                        $rowImageUrls[$imgField] = $uploadedUrlCache[$localFilePath];
                                    } else {
                                        $uploadRes = $this->cloudinary->uploadImage($localFilePath, $folder);

                                        if (!empty($uploadRes['success'])) {
                                            $secureUrl = $uploadRes['secure_url'];
                                            $uploadedUrlCache[$localFilePath] = $secureUrl;
                                            $rowImageUrls[$imgField] = $secureUrl;
                                            $uploadedImagesCount++;

                                            if (!empty($uploadRes['public_id'])) {
                                                $rowPublicIds[] = $uploadRes['public_id'];
                                                $uploadedPublicIdsThisBatch[] = $uploadRes['public_id'];
                                            }
                                        } else {
                                            $imageUploadFailed = true;
                                            $failedImagesCount++;
                                            $failedField = $imgField;
                                            $imageUploadError = $uploadRes['error'] ?? 'Unknown Cloudinary upload failure';
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($imageUploadFailed) {
                        // Clean up any images that were uploaded for this failed row
                        foreach ($rowPublicIds as $pid) {
                            $this->cloudinary->deleteAsset($pid);
                        }

                        $auditReason = "Cloudinary upload failed for {$failedField} (" . ($row[$failedField] ?? '') . "): {$imageUploadError}";
                        $auditLog[] = [
                            'row' => $rowNum,
                            'sheet' => $sheet,
                            'status' => 'failed',
                            'reason' => $auditReason,
                            'question' => mb_substr($row['question_text'] ?? '', 0, 80),
                        ];

                        $invalidCount++;
                        $skippedCount++;

                        if (!$skipInvalids) {
                            throw new Exception("Row {$rowNum} in {$sheet} image upload failed: {$imageUploadError}");
                        }
                        continue;
                    }

                    $topicId = (int) $row['topic_id'];
                    $qNum = $row['question_number'] !== null ? (int) $row['question_number'] : null;

                    // Ensure topic row lock and serial number assignment
                    if (!isset($topicCurrentNumbers[$topicId])) {
                        Topic::where('id', $topicId)->lockForUpdate()->first();
                        $maxInDb = Question::where('topic_id', $topicId)->lockForUpdate()->max('question_number') ?? 0;
                        $topicCurrentNumbers[$topicId] = $maxInDb;
                    }

                    if ($qNum === null) {
                        $topicCurrentNumbers[$topicId]++;
                        $qNum = $topicCurrentNumbers[$topicId];
                    } else {
                        if ($qNum > $topicCurrentNumbers[$topicId]) {
                            $topicCurrentNumbers[$topicId] = $qNum;
                        }
                    }

                    Question::create([
                        'category_id' => $row['category_id'],
                        'subject_id' => $row['subject_id'],
                        'topic_id' => $topicId,
                        'question_number' => $qNum,
                        'section_number' => 1,
                        'question_text' => $row['question_text'],
                        'option_a' => $row['option_a'],
                        'option_b' => $row['option_b'],
                        'option_c' => $row['option_c'],
                        'option_d' => $row['option_d'],
                        'correct_option' => $row['correct_option'],
                        'explanation' => $row['explanation'],
                        'difficulty' => 'medium',
                        'figure_id' => $row['figure_id'],
                        'question_image' => $rowImageUrls['question_image'],
                        'option_a_image' => $rowImageUrls['option_a_image'],
                        'option_b_image' => $rowImageUrls['option_b_image'],
                        'option_c_image' => $rowImageUrls['option_c_image'],
                        'option_d_image' => $rowImageUrls['option_d_image'],
                        'explanation_image' => $rowImageUrls['explanation_image'],
                        'is_active' => true,
                    ]);

                    $topicIncrementCounts[$topicId] = ($topicIncrementCounts[$topicId] ?? 0) + 1;
                    $importedCount++;

                    $auditLog[] = [
                        'row' => $rowNum,
                        'sheet' => $sheet,
                        'status' => 'imported',
                        'reason' => 'Successfully imported',
                        'question' => mb_substr($row['question_text'] ?? '', 0, 80),
                    ];
                }

                // Batch increment total_questions_count on affected topics
                foreach ($topicIncrementCounts as $tid => $inc) {
                    Topic::where('id', $tid)->increment('total_questions_count', $inc);
                }
            });

            // Update batch record
            $batch->update([
                'successful_rows' => $importedCount,
                'failed_rows' => $skippedCount,
                'errors' => $auditLog,
                'status' => 'completed',
            ]);
        } catch (Throwable $e) {
            // Rollback cleanup: delete any newly created Cloudinary assets from this batch to avoid orphans
            foreach ($uploadedPublicIdsThisBatch as $pid) {
                try {
                    $this->cloudinary->deleteAsset($pid);
                } catch (Throwable $delEx) {
                    Log::warning("Failed to clean up Cloudinary asset during rollback: {$pid}");
                }
            }

            $batch->update([
                'status' => 'failed',
                'errors' => [
                    [
                        'row' => 0,
                        'sheet' => 'Transaction',
                        'status' => 'failed',
                        'reason' => 'Transaction rolled back: ' . $e->getMessage(),
                    ],
                ],
            ]);
            throw $e;
        }

        return [
            'batch_id' => $batch->id,
            'total_rows' => $totalRows,
            'imported_count' => $importedCount,
            'skipped_count' => $skippedCount,
            'duplicate_count' => $duplicateCount,
            'invalid_count' => $invalidCount,
            'missing_image_count' => $missingImageCount,
            'uploaded_images_count' => $uploadedImagesCount,
            'failed_images_count' => $failedImagesCount,
            'details' => $auditLog,
        ];
    }

    /**
     * Generate safe CSV string for error and audit reports.
     * Prevents spreadsheet formula injection by sanitizing cells starting with '=', '+', '-', '@'.
     *
     * @param list<array<string, mixed>> $auditEntries
     */
    public function generateSafeCsv(array $auditEntries): string
    {
        $output = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, ['Sheet', 'Row Number', 'Status', 'Question Snippet', 'Audit Reason / Error Details']);

        foreach ($auditEntries as $entry) {
            $sheet = $this->sanitizeForCsv((string) ($entry['sheet'] ?? ''));
            $row = $this->sanitizeForCsv((string) ($entry['row'] ?? ''));
            $status = $this->sanitizeForCsv((string) ($entry['status'] ?? ''));
            $question = $this->sanitizeForCsv((string) ($entry['question'] ?? ''));
            $reason = $this->sanitizeForCsv((string) ($entry['reason'] ?? ''));

            fputcsv($output, [$sheet, $row, $status, $question, $reason]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }

    /**
     * Sanitize string against formula injection.
     */
    protected function sanitizeForCsv(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed !== '' && in_array($trimmed[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $trimmed;
        }
        return $value;
    }
}
