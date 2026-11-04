<?php

namespace App\Services\Import;

use App\Models\Question;
use Illuminate\Support\Facades\Storage;

class McqImportValidator
{
    protected TaxonomyMatcher $taxonomyMatcher;

    /** @var array<int, array<int, bool>> */
    protected array $seenTopicNumbers = [];

    /** @var array<int, array<string, bool>> */
    protected array $seenTopicTextHashes = [];

    /** @var array<string, bool> */
    protected array $existingDbNumberCache = [];

    /** @var array<string, bool> */
    protected array $existingDbTextCache = [];

    public function __construct(?TaxonomyMatcher $taxonomyMatcher = null)
    {
        $this->taxonomyMatcher = $taxonomyMatcher ?? new TaxonomyMatcher();
    }

    /**
     * Validate an entire parsed spreadsheet structure.
     *
     * @param array{
     *     sheets: array<string, array{headers: array<string, string>, rows: list<array{row_number: int, sheet: string, data: array<string, mixed>}>}>,
     *     image_references?: array<string, array<string, string>>,
     *     format: string
     * } $parsedData
     * @return array{
     *     total_rows: int,
     *     valid_count: int,
     *     duplicate_count: int,
     *     invalid_count: int,
     *     missing_image_count: int,
     *     rows: list<array<string, mixed>>,
     *     errors_summary: list<string>
     * }
     */
    public function validateWorkbook(array $parsedData): array
    {
        $this->seenTopicNumbers = [];
        $this->seenTopicTextHashes = [];
        $this->existingDbNumberCache = [];
        $this->existingDbTextCache = [];

        $imageReferences = $parsedData['image_references'] ?? [];
        $validatedRows = [];
        $errorsSummary = [];

        $validCount = 0;
        $duplicateCount = 0;
        $invalidCount = 0;
        $missingImageCount = 0;
        $totalRows = 0;

        foreach ($parsedData['sheets'] as $sheetName => $sheet) {
            foreach ($sheet['rows'] as $rowItem) {
                $totalRows++;
                $rowResult = $this->validateRow($rowItem, $imageReferences);

                if ($rowResult['status'] === 'valid') {
                    $validCount++;
                } elseif ($rowResult['status'] === 'duplicate') {
                    $duplicateCount++;
                } else {
                    $invalidCount++;
                }

                if (!empty($rowResult['missing_images'])) {
                    $missingImageCount += count($rowResult['missing_images']);
                }

                foreach ($rowResult['errors'] as $err) {
                    $errorsSummary[] = "[{$sheetName} Row {$rowItem['row_number']}] {$err}";
                }

                $validatedRows[] = $rowResult;
            }
        }

        return [
            'total_rows' => $totalRows,
            'valid_count' => $validCount,
            'duplicate_count' => $duplicateCount,
            'invalid_count' => $invalidCount,
            'missing_image_count' => $missingImageCount,
            'rows' => $validatedRows,
            'errors_summary' => $errorsSummary,
        ];
    }

    /**
     * Validate a single row.
     *
     * @param array{row_number: int, sheet: string, data: array<string, mixed>} $rowItem
     * @param array<string, array<string, string>> $imageReferences
     * @return array<string, mixed>
     */
    public function validateRow(array $rowItem, array $imageReferences = []): array
    {
        $data = $rowItem['data'];
        $rowNumber = $rowItem['row_number'];
        $sheet = $rowItem['sheet'];

        $errors = [];
        $warnings = [];
        $missingImages = [];
        $isDuplicate = false;

        $topicCol = trim((string) ($data['topic'] ?? ''));
        $subtopicCol = trim((string) ($data['subtopic'] ?? ''));
        $questionNumRaw = trim((string) ($data['question_number'] ?? ''));
        $questionText = trim((string) ($data['question_text'] ?? ''));
        $optA = trim((string) ($data['option_a'] ?? ''));
        $optB = trim((string) ($data['option_b'] ?? ''));
        $optC = trim((string) ($data['option_c'] ?? ''));
        $optD = trim((string) ($data['option_d'] ?? ''));
        $correctOpt = strtoupper(trim((string) ($data['correct_option'] ?? '')));
        $explanation = trim((string) ($data['explanation'] ?? ''));

        // 1. Taxonomy Validation
        $taxResult = $this->taxonomyMatcher->resolve($topicCol, $subtopicCol);
        $categoryId = null;
        $subjectId = null;
        $topicId = null;
        $topicModel = null;

        if (!$taxResult['success']) {
            $errors[] = $taxResult['error'];
        } else {
            $categoryId = $taxResult['category_id'];
            $subjectId = $taxResult['subject_id'];
            $topicId = $taxResult['topic_id'];
            $topicModel = $taxResult['topic'];
        }

        // 2. Question Text
        if ($questionText === '') {
            $errors[] = 'Question text cannot be empty.';
        }

        // 3. Four Options Validation
        if ($optA === '') {
            $errors[] = 'Option A is missing or empty.';
        }
        if ($optB === '') {
            $errors[] = 'Option B is missing or empty.';
        }
        if ($optC === '') {
            $errors[] = 'Option C is missing or empty.';
        }
        if ($optD === '') {
            $errors[] = 'Option D is missing or empty.';
        }

        // 4. Correct Option Validation
        if (!in_array($correctOpt, ['A', 'B', 'C', 'D'], true)) {
            $errors[] = "Correct option must be 'A', 'B', 'C', or 'D'. Received '{$correctOpt}'.";
        }

        // 5. Question Number Validation & Duplicate Detection
        $questionNumber = null;
        if ($questionNumRaw !== '') {
            if (!ctype_digit($questionNumRaw) || (int) $questionNumRaw < 1) {
                $errors[] = "Question number must be a positive integer. Received '{$questionNumRaw}'.";
            } else {
                $questionNumber = (int) $questionNumRaw;
            }
        }

        // If taxonomy was valid, check duplicates
        if ($topicId !== null && empty($errors)) {
            $textHash = md5(mb_strtolower(preg_replace('/\s+/', ' ', $questionText)));

            // Duplicate in upload batch (text)
            if (isset($this->seenTopicTextHashes[$topicId][$textHash])) {
                $isDuplicate = true;
                $errors[] = "Duplicate question text detected within this file for topic '{$topicModel->name}'.";
            } else {
                $this->seenTopicTextHashes[$topicId][$textHash] = true;
            }

            // Duplicate in upload batch (number)
            if ($questionNumber !== null) {
                if (isset($this->seenTopicNumbers[$topicId][$questionNumber])) {
                    $isDuplicate = true;
                    $errors[] = "Duplicate question number #{$questionNumber} within this file for topic '{$topicModel->name}'.";
                } else {
                    $this->seenTopicNumbers[$topicId][$questionNumber] = true;
                }
            }

            // Duplicate in database (number)
            if ($questionNumber !== null) {
                $cacheKey = "{$topicId}:{$questionNumber}";
                if (!isset($this->existingDbNumberCache[$cacheKey])) {
                    $this->existingDbNumberCache[$cacheKey] = Question::where('topic_id', $topicId)
                        ->where('question_number', $questionNumber)
                        ->exists();
                }
                if ($this->existingDbNumberCache[$cacheKey]) {
                    $isDuplicate = true;
                    $errors[] = "Question #{$questionNumber} already exists in database for topic '{$topicModel->name}'.";
                }
            }

            // Duplicate in database (text)
            $textCacheKey = "{$topicId}:{$textHash}";
            if (!isset($this->existingDbTextCache[$textCacheKey])) {
                $this->existingDbTextCache[$textCacheKey] = Question::where('topic_id', $topicId)
                    ->where('question_text', $questionText)
                    ->exists();
            }
            if ($this->existingDbTextCache[$textCacheKey]) {
                $isDuplicate = true;
                $errors[] = "A question with identical text already exists in database for topic '{$topicModel->name}'.";
            }
        }

        // 6. Image & Figure References Check
        $figureId = null;
        $questionImage = null;
        $optionAImage = null;
        $optionBImage = null;
        $optionCImage = null;
        $optionDImage = null;

        // Check if figure ID can be matched (e.g. figure reference in text or Image References sheet)
        if (preg_match('/(?:figure|fig\.?)\s*([A-Za-z0-9\-_]+)/i', $questionText, $m)) {
            $figureId = trim($m[1]);
        }

        if ($figureId && isset($imageReferences[$figureId])) {
            $ref = $imageReferences[$figureId];
            $questionImage = $ref['question image'] ?? $ref['question_image'] ?? null;
            $optionAImage = $ref['option a image'] ?? $ref['option_a_image'] ?? null;
            $optionBImage = $ref['option b image'] ?? $ref['option_b_image'] ?? null;
            $optionCImage = $ref['option c image'] ?? $ref['option_c_image'] ?? null;
            $optionDImage = $ref['option d image'] ?? $ref['option_d_image'] ?? null;
        }

        // Verify images exist on server if referenced
        $allImgRefs = array_filter([
            'Question Image' => $questionImage,
            'Option A Image' => $optionAImage,
            'Option B Image' => $optionBImage,
            'Option C Image' => $optionCImage,
            'Option D Image' => $optionDImage,
        ]);

        foreach ($allImgRefs as $label => $imgRelPath) {
            if (!$this->imageExistsOnServer($imgRelPath)) {
                $missingImages[] = "{$label} ({$imgRelPath})";
                $warnings[] = "Referenced file '{$imgRelPath}' not found on server.";
                // Do not create broken image path in DB
            }
        }

        // If images were missing, don't set invalid paths into the question record
        if (!empty($missingImages)) {
            $questionImage = null;
            $optionAImage = null;
            $optionBImage = null;
            $optionCImage = null;
            $optionDImage = null;
        }

        $status = 'valid';
        if ($isDuplicate) {
            $status = 'duplicate';
        } elseif (!empty($errors)) {
            $status = 'invalid';
        }

        return [
            'row_number' => $rowNumber,
            'sheet' => $sheet,
            'status' => $status,
            'errors' => $errors,
            'warnings' => $warnings,
            'missing_images' => $missingImages,
            'topic_name' => $topicModel?->name ?? $subtopicCol,
            'category_name' => $topicModel?->category?->name ?? $topicCol,
            'subject_name' => $topicModel?->subject?->name ?? null,
            'category_id' => $categoryId,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'question_number' => $questionNumber,
            'question_text' => $questionText,
            'option_a' => $optA,
            'option_b' => $optB,
            'option_c' => $optC,
            'option_d' => $optD,
            'correct_option' => $correctOpt,
            'explanation' => $explanation ?: null,
            'figure_id' => $figureId,
            'question_image' => $questionImage,
            'option_a_image' => $optionAImage,
            'option_b_image' => $optionBImage,
            'option_c_image' => $optionCImage,
            'option_d_image' => $optionDImage,
        ];
    }

    /**
     * Check if an image file exists in public/images or storage/app/public.
     */
    protected function imageExistsOnServer(string $path): bool
    {
        $clean = ltrim($path, '/\\');

        if (file_exists(public_path($clean))) {
            return true;
        }

        if (file_exists(public_path('images/' . basename($clean)))) {
            return true;
        }

        if (Storage::disk('public')->exists($clean)) {
            return true;
        }

        return false;
    }
}

