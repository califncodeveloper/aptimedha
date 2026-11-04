<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImportBatch;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Handles reliable, secure, database-backed bulk MCQ import from Excel and CSV files.
 */
class McqImportController
{
    protected SpreadsheetReader $reader;
    protected McqImportValidator $validator;
    protected McqImportProcessor $processor;

    public function __construct(
        ?SpreadsheetReader $reader = null,
        ?McqImportValidator $validator = null,
        ?McqImportProcessor $processor = null
    ) {
        $this->reader = $reader ?? new SpreadsheetReader();
        $this->validator = $validator ?? new McqImportValidator();
        $this->processor = $processor ?? new McqImportProcessor();
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
     * Handle the uploaded workbook, validate structure, and present preview.
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
        ]);

        $file = $request->file('file');
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

        try {
            // Parse spreadsheet
            $parsed = $this->reader->read($tempFilePath);

            // Validate parsed rows against database taxonomy and constraints
            $validationResult = $this->validator->validateWorkbook($parsed);

            // Store preview state in temporary JSON file
            $previewPayload = [
                'token' => $token,
                'filename' => $originalName,
                'temp_file' => $tempFilePath,
                'created_at' => now()->toIso8601String(),
                'summary' => [
                    'total_rows' => $validationResult['total_rows'],
                    'valid_count' => $validationResult['valid_count'],
                    'duplicate_count' => $validationResult['duplicate_count'],
                    'invalid_count' => $validationResult['invalid_count'],
                    'missing_image_count' => $validationResult['missing_image_count'],
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
            ]);
        } catch (Throwable $e) {
            // Clean up temporary file
            if (File::exists($tempFilePath)) {
                File::delete($tempFilePath);
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
     * Confirm and execute database import for validated rows.
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

            $batch = ImportBatch::find($report['batch_id']);

            return view('admin.mcqs.import-report', [
                'report' => $report,
                'batch' => $batch,
                'filename' => $payload['filename'],
            ]);
        } catch (Throwable $e) {
            return redirect()->route('admin.mcqs.import.form')
                ->with('error', 'Database import error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an import preview and clean up temporary storage.
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
     * Download canonical 10-column import template (.csv).
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
            ],
            [
                'Arithmetic Aptitude',
                'Number System',
                '2',
                'How many positive factors does 72 have?',
                '12',
                '11',
                '14',
                '13',
                'A',
                '72 = 2^3 × 3^2. The factor count is (3+1)(2+1) = 12.',
            ],
            [
                'Data Interpretation',
                'Table Charts',
                '1',
                'Using the provided table, what is the combined sum for categories A and B?',
                '40',
                '39',
                '41',
                '42',
                'A',
                'Category A = 16, Category B = 24. Sum = 40.',
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
