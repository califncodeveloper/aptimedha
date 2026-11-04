<?php

namespace App\Services\Import;

use App\Models\ImportBatch;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class McqImportProcessor
{
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

        $auditLog = [];
        $topicIncrementCounts = [];

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
                &$auditLog,
                &$topicIncrementCounts
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

                    // Handle Valid rows
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
                        'question_image' => $row['question_image'],
                        'option_a_image' => $row['option_a_image'],
                        'option_b_image' => $row['option_b_image'],
                        'option_c_image' => $row['option_c_image'],
                        'option_d_image' => $row['option_d_image'],
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

