<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImportBatch;
use App\Services\Import\McqImageArchiveHandler;
use App\Services\Import\McqImportProcessor;
use App\Services\Import\McqImportValidator;
use App\Services\Import\SpreadsheetReader;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

/**
 * Handles reliable, secure, database-backed bulk MCQ import from Excel and CSV files,
 * including ZIP-based image archives integrated with Cloudinary.
 */
class McqImportController
{
    protected SpreadsheetReader $reader;
    protected McqImportValidator $validator;
    protected McqImportProcessor $processor;
    protected McqImageArchiveHandler $archiveHandler;

    public function __construct(
        ?SpreadsheetReader $reader = null,
        ?McqImportValidator $validator = null,
        ?McqImportProcessor $processor = null,
        ?McqImageArchiveHandler $archiveHandler = null
    ) {
        $this->reader = $reader ?? new SpreadsheetReader();
        $this->validator = $validator ?? new McqImportValidator();
        $this->processor = $processor ?? new McqImportProcessor();
        $this->archiveHandler = $archiveHandler ?? new McqImageArchiveHandler();
    }

    /**
     * Show the MCQ import upload form and recent import history.
     */
    public function showForm(): View
    {
        $recentBatches = ImportBatch::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('admin.mcqs.import', [
            'recentBatches' => $recentBatches,
        ]);
    }

    /**
     * Handle the uploaded workbook and optional images ZIP, validate structure, and present preview.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:15360', // 15MB
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['xlsx', 'csv'], true)) {
                        $fail('The import file must be a valid .xlsx or .csv file.');
                    }
                },
            ],
            'zip_file' => [
                'nullable',
                'file',
                'max:51200', // 50MB
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if ($ext !== 'zip') {
                        $fail('The images archive must be a valid .zip file.');
                    }
                },
            ],
            'images_zip' => [
                'nullable',
                'file',
                'max:51200', // 50MB
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if ($ext !== 'zip') {
                        $fail('The images archive must be a valid .zip file.');
                    }
                },
            ],
        ]);

        $file = $request->file('file');
        $zipUpload = $request->file('zip_file') ?? $request->file('images_zip');
        $originalName = $file->getClientOriginalName();
        $token = Str::random(32);

        // Secure temporary directory
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'imports');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0777, true, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $tempFilePath = $tempDir . DIRECTORY_SEPARATOR . "upload_{$token}.{$extension}";
        copy($file->getRealPath(), $tempFilePath);

        $extractDir = null;
        $archiveIndexData = null;

        // Process optional ZIP archive
        if ($zipUpload) {
            $extractDir = $tempDir . DIRECTORY_SEPARATOR . "extracted_{$token}";
            $archiveResult = $this->archiveHandler->validateAndExtract($zipUpload, $extractDir);

            if (!$archiveResult['success']) {
                if (File::exists($tempFilePath)) {
                    File::delete($tempFilePath);
                }
                $this->archiveHandler->cleanup($extractDir);

                return redirect()->route('admin.mcqs.import.form')
                    ->with('error', 'ZIP Archive Error: ' . ($archiveResult['error'] ?? 'Invalid ZIP archive.'));
            }

            $archiveIndexData = $archiveResult;
        }

        try {
            // Parse spreadsheet
            $parsed = $this->reader->read($tempFilePath);

            // Validate parsed rows against database taxonomy and image archive
            $validationResult = $this->validator->validateWorkbook($parsed, $archiveIndexData);

            // Store preview state in temporary JSON file
            $previewPayload = [
                'token' => $token,
                'filename' => $originalName,
                'temp_file' => $tempFilePath,
                'extract_dir' => $extractDir,
                'has_images' => !empty($archiveIndexData),
                'zip_filename' => $zipUpload?->getClientOriginalName(),
                'created_at' => now()->toIso8601String(),
                'summary' => [
                    'total_rows' => $validationResult['total_rows'],
                    'valid_count' => $validationResult['valid_count'],
                    'duplicate_count' => $validationResult['duplicate_count'],
                    'invalid_count' => $validationResult['invalid_count'],
                    'missing_image_count' => $validationResult['missing_image_count'],
                    'images_archive_count' => $archiveIndexData['files_count'] ?? 0,
                ],
                'rows' => $validationResult['rows'],
                'errors_summary' => $validationResult['errors_summary'],
            ];

            File::put($tempDir . DIRECTORY_SEPARATOR . "preview_{$token}.json", json_encode($previewPayload));

            return view('admin.mcqs.import-preview', [
                'token' => $token,
                'filename' => $originalName,
                'summary' => $previewPayload['summary'],
                'rows' => $validationResult['rows'],
                'errorsSummary' => $validationResult['errors_summary'],
                'hasImages' => !empty($archiveIndexData),
                'zipFilename' => $zipUpload?->getClientOriginalName(),
            ]);
        } catch (Throwable $e) {
            // Clean up temporary files and extracted directory on error
            if (File::exists($tempFilePath)) {
                File::delete($tempFilePath);
            }
            if ($extractDir) {
                $this->archiveHandler->cleanup($extractDir);
            }

            return redirect()->route('admin.mcqs.import.form')
                ->with('error', 'Spreadsheet validation failed: ' . $e->getMessage());
        }
    }

    /**
     * Alias for import form submission to match existing route name.
     */
    public function import(Request $request): View|RedirectResponse
    {
        return $this->preview($request);
    }

    /**
     * Confirm and execute database import for validated rows, uploading referenced images to Cloudinary.
     */
    public function confirm(Request $request): View|RedirectResponse
    {
        $request->validate([
            'token' => 'required|string',
            'skip_duplicates' => 'nullable|boolean',
            'skip_invalids' => 'nullable|boolean',
        ]);

        $token = preg_replace('/[^a-zA-Z0-9]/', '', (string) $request->input('token'));
        $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'imports');
        $previewFile = $tempDir . DIRECTORY_SEPARATOR . "preview_{$token}.json";

        if (!File::exists($previewFile)) {
            return redirect()->route('admin.mcqs.import.form')
                ->with('error', 'Import session expired or invalid. Please upload the file again.');
        }

        $payload = json_decode(File::get($previewFile), true);
        if (!$payload || !isset($payload['rows'])) {
            return redirect()->route('admin.mcqs.import.form')
                ->with('error', 'Corrupted import session. Please re-upload.');
        }

        $skipDuplicates = $request->boolean('skip_duplicates', true);
        $skipInvalids = $request->boolean('skip_invalids', true);
        $extractDir = $payload['extract_dir'] ?? null;

        try {
            $report = $this->processor->process($payload['rows'], [
                'filename' => $payload['filename'] ?? 'uploaded_mcqs.xlsx',
                'user' => $request->user(),
                'skip_duplicates' => $skipDuplicates,
                'skip_invalids' => $skipInvalids,
            ]);

            // Clean up temporary files
            if (isset($payload['temp_file']) && File::exists($payload['temp_file'])) {
                File::delete($payload['temp_file']);
            }
            File::delete($previewFile);

            // Clean up extracted image directory
            if ($extractDir) {
                $this->archiveHandler->cleanup($extractDir);
            }

            $batch = ImportBatch::find($report['batch_id']);

            return view('admin.mcqs.import-report', [
                'report' => $report,
                'batch' => $batch,
                'filename' => $payload['filename'],
            ]);
        } catch (Throwable $e) {
            // Clean up extracted image directory on exception
            if ($extractDir) {
                $this->archiveHandler->cleanup($extractDir);
            }
            if (isset($payload['temp_file']) && File::exists($payload['temp_file'])) {
                File::delete($payload['temp_file']);
            }
            File::delete($previewFile);

            return redirect()->route('admin.mcqs.import.form')
                ->with('error', 'Database import error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an import preview and clean up temporary storage and extracted images.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $token = preg_replace('/[^a-zA-Z0-9]/', '', (string) $request->input('token'));
        $tempDir = storage_path('app/imports');

        $previewFile = "{$tempDir}/preview_{$token}.json";
        if (File::exists($previewFile)) {
            $payload = json_decode(File::get($previewFile), true);
            if (isset($payload['temp_file']) && File::exists($payload['temp_file'])) {
                File::delete($payload['temp_file']);
            }
            if (isset($payload['extract_dir'])) {
                $this->archiveHandler->cleanup($payload['extract_dir']);
            }
            File::delete($previewFile);
        }

        return redirect()->route('admin.mcqs.import.form')
            ->with('info', 'Bulk import preview was cancelled. No records were modified.');
    }

    /**
     * Download audit/error log as safe CSV.
     */
    public function exportErrors(ImportBatch $batch): Response
    {
        $csvContent = $this->processor->generateSafeCsv($batch->errors ?? []);
        $filename = "import-audit-batch-{$batch->id}.csv";

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Download canonical import template (.csv) with image reference columns.
     */
    public function downloadTemplate(): Response
    {
        $headers = [
            'Question Topic',
            'Question Subtopic',
            'Question Number',
            'Question Text',
            'Option A',
            'Option B',
            'Option C',
            'Option D',
            'Correct Option',
            'Explanation',
            'Question Image',
            'Option A Image',
            'Option B Image',
            'Option C Image',
            'Option D Image',
            'Explanation Image',
        ];

        $sampleRows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1',
                'How many positive factors does 36 have?',
                '10',
                '11',
                '9',
                '8',
                'C',
                '36 = 2^2 × 3^2. The factor count is (2+1)(2+1) = 9.',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Data Interpretation',
                'Table Charts',
                '1',
                'Using the provided table figure, what is the combined sum for categories A and B?',
                '40',
                '39',
                '41',
                '42',
                'A',
                'Category A = 16, Category B = 24. Sum = 40.',
                'images/q1_table.png',
                '',
                '',
                '',
                '',
                'images/q1_explanation.png',
            ],
            [
                'Spatial Ability',
                'Pattern Completion',
                '1',
                'Which of the four options completes the matrix shown in the question figure?',
                'Figure A',
                'Figure B',
                'Figure C',
                'Figure D',
                'B',
                'Option B continues the alternating rotational pattern of 90 degrees clockwise.',
                'images/q2_pattern.png',
                'images/q2_opt_a.png',
                'images/q2_opt_b.png',
                'images/q2_opt_c.png',
                'images/q2_opt_d.png',
                '',
            ],
            [
                'Verbal Ability',
                'Synonyms and Antonyms',
                '1',
                'Which word is closest in meaning to "Rapid"?',
                'Silent',
                'Fast',
                'Purchase',
                'Begin',
                'B',
                'Rapid means occurring within a short time or at great speed.',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="aptimedha-mcq-import-template.csv"',
        ]);
    }
}
