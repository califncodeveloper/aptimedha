<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ImportBatch;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Services\Import\McqImportProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BulkMcqImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Category $category;
    protected Subject $subject;
    protected Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();

        $this->category = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->subject = Subject::create([
            'category_id' => $this->category->id,
            'name' => 'Number & Algebra',
            'slug' => 'number-and-algebra',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->topic = Topic::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'name' => 'Number System',
            'slug' => 'number-system',
            'is_active' => true,
            'display_order' => 1,
            'total_questions_count' => 0,
        ]);
    }

    /**
     * Helper to create a test CSV uploaded file.
     */
    protected function createCsvUpload(array $headers, array $rows, string $filename = 'test.csv'): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'csv_');
        $fh = fopen($tempPath, 'w');
        fputcsv($fh, $headers);
        foreach ($rows as $r) {
            fputcsv($fh, $r);
        }
        fclose($fh);

        return new UploadedFile($tempPath, $filename, 'text/csv', null, true);
    }

    /**
     * Helper to get standard canonical CSV headers.
     */
    protected function getCanonicalHeaders(): array
    {
        return [
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
    }

    /**
     * Test 1: Guest cannot access import routes.
     */
    public function test_guest_cannot_access_import_routes(): void
    {
        $this->get(route('admin.mcqs.import.form'))->assertRedirect(route('login'));
        $this->post(route('admin.mcqs.import'))->assertRedirect(route('login'));
        $this->post(route('admin.mcqs.import.confirm'))->assertRedirect(route('login'));
    }

    /**
     * Test 2: Regular user cannot access import routes (403 Forbidden).
     */
    public function test_regular_user_cannot_access_import_routes(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.mcqs.import.form'))
            ->assertForbidden();

        $this->actingAs($this->user)
            ->post(route('admin.mcqs.import'))
            ->assertForbidden();
    }

    /**
     * Test 3: Admin can view import form and download template.
     */
    public function test_admin_can_view_import_form_and_download_template(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mcqs.import.form'));

        $response->assertOk();
        $response->assertSee('Bulk MCQ Import');
        $response->assertSee('Required 10-Column Spreadsheet Schema');

        $templateResponse = $this->actingAs($this->admin)
            ->get(route('admin.mcqs.import.template'));

        $templateResponse->assertOk();
        $templateResponse->assertHeader('Content-Disposition', 'attachment; filename="aptimedha-mcq-import-template.csv"');
    }

    /**
     * Test 4: Invalid file extension rejected.
     */
    public function test_invalid_file_extension_rejected(): void
    {
        $file = UploadedFile::fake()->create('malicious.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test 5: Missing column detection in uploaded CSV.
     */
    public function test_missing_column_detection_in_csv(): void
    {
        // Headers missing Option C and Option D
        $badHeaders = ['Question Topic', 'Question Subtopic', 'Question Text', 'Option A', 'Option B'];
        $rows = [
            ['Arithmetic Aptitude', 'Number System', 'What is 2+2?', '4', '5'],
        ];
        $file = $this->createCsvUpload($badHeaders, $rows);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $response->assertRedirect(route('admin.mcqs.import.form'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Missing required columns', session('error'));
    }

    /**
     * Test 6: Valid CSV import workflow (upload -> preview -> confirm).
     */
    public function test_valid_csv_import_workflow(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1',
                'What is the smallest prime number?',
                '1',
                '2',
                '3',
                '4',
                'B',
                '2 is the only even and smallest prime number.',
            ],
            [
                'Arithmetic Aptitude',
                'Number System',
                '2',
                'What is 15 squared?',
                '215',
                '225',
                '235',
                '245',
                'B',
                '15 x 15 = 225.',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        // Step 1: Upload and preview
        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $previewResponse->assertViewIs('admin.mcqs.import-preview');
        $previewResponse->assertSee('What is the smallest prime number?');
        $previewResponse->assertSee('What is 15 squared?');

        $token = $previewResponse->viewData('token');
        $this->assertNotEmpty($token);
        $this->assertEquals(2, $previewResponse->viewData('summary')['valid_count']);
        $this->assertEquals(0, $previewResponse->viewData('summary')['invalid_count']);

        // Step 2: Confirm import
        $confirmResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import.confirm'), [
                'token' => $token,
                'skip_duplicates' => 1,
                'skip_invalids' => 1,
            ]);

        $confirmResponse->assertOk();
        $confirmResponse->assertViewIs('admin.mcqs.import-report');
        $confirmResponse->assertSee('Bulk MCQ Import Completed');

        // Verify in database
        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'What is the smallest prime number?',
            'correct_option' => 'B',
        ]);
        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'What is 15 squared?',
            'correct_option' => 'B',
        ]);

        $this->assertEquals(2, $this->topic->fresh()->total_questions_count);

        // Verify ImportBatch audit record
        $this->assertDatabaseHas('import_batches', [
            'user_id' => $this->admin->id,
            'total_rows' => 2,
            'successful_rows' => 2,
            'failed_rows' => 0,
            'status' => 'completed',
        ]);
    }

    /**
     * Test 7: Valid Excel import workflow with actual client workbook.
     */
    public function test_valid_excel_import_workflow_with_client_workbook(): void
    {
        $workbookPath = base_path('client_docs/Placement_All_Topics_2_Sample_MCQs.xlsx');
        if (!file_exists($workbookPath)) {
            $this->markTestSkipped('Client workbook file not found in client_docs.');
        }

        $uploadedFile = new UploadedFile(
            $workbookPath,
            'Placement_All_Topics_2_Sample_MCQs.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $uploadedFile,
            ]);

        $previewResponse->assertOk();
        $previewResponse->assertViewIs('admin.mcqs.import-preview');

        $summary = $previewResponse->viewData('summary');
        $token = $previewResponse->viewData('token');

        $this->assertGreaterThan(0, $summary['total_rows']);
        $this->assertGreaterThan(0, $summary['valid_count']);

        // Confirm import of valid rows
        $confirmResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import.confirm'), [
                'token' => $token,
                'skip_duplicates' => 1,
                'skip_invalids' => 1,
            ]);

        $confirmResponse->assertOk();
        $confirmResponse->assertViewIs('admin.mcqs.import-report');

        // Assert at least the questions for seeded Number System topic were imported
        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'How many positive factors does 36 have?',
            'correct_option' => 'C',
        ]);
    }

    /**
     * Test 8: Invalid correct option detected and flagged.
     */
    public function test_invalid_correct_option_detected(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1',
                'Sample question text?',
                'Option 1',
                'Option 2',
                'Option 3',
                'Option 4',
                'E', // Invalid! Must be A, B, C, or D
                'Sample explanation',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $summary = $previewResponse->viewData('summary');
        $this->assertEquals(0, $summary['valid_count']);
        $this->assertEquals(1, $summary['invalid_count']);

        $rowResults = $previewResponse->viewData('rows');
        $this->assertEquals('invalid', $rowResults[0]['status']);
        $this->assertStringContainsString("Correct option must be 'A', 'B', 'C', or 'D'", implode('; ', $rowResults[0]['errors']));
    }

    /**
     * Test 9: Missing option validation.
     */
    public function test_missing_option_validation(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1',
                'Sample question text?',
                'Option A',
                '', // Empty Option B!
                'Option C',
                'Option D',
                'A',
                'Sample explanation',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $summary = $previewResponse->viewData('summary');
        $this->assertEquals(1, $summary['invalid_count']);

        $rowResults = $previewResponse->viewData('rows');
        $this->assertStringContainsString('Option B is missing or empty', implode('; ', $rowResults[0]['errors']));
    }

    /**
     * Test 10: Unknown topic mapping detected.
     */
    public function test_unknown_topic_mapping_detected(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Non-Existent Mystery Topic',
                '1',
                'Sample question text?',
                'Opt 1',
                'Opt 2',
                'Opt 3',
                'Opt 4',
                'A',
                'Sample explanation',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $summary = $previewResponse->viewData('summary');
        $this->assertEquals(1, $summary['invalid_count']);

        $rowResults = $previewResponse->viewData('rows');
        $this->assertStringContainsString('Unknown topic', implode('; ', $rowResults[0]['errors']));
    }

    /**
     * Test 11: Subject / Topic mismatch detected.
     */
    public function test_subject_topic_mismatch_detected(): void
    {
        // Create an unrelated second subject under the same category
        $otherSubject = Subject::create([
            'category_id' => $this->category->id,
            'name' => 'Commercial Math',
            'slug' => 'commercial-math',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $rows = [
            [
                'Arithmetic Aptitude',
                'Commercial Math / Number System', // Mismatch! Number System belongs to 'Number & Algebra'
                '1',
                'Sample question text?',
                'Opt 1',
                'Opt 2',
                'Opt 3',
                'Opt 4',
                'A',
                'Sample explanation',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $summary = $previewResponse->viewData('summary');
        $this->assertEquals(1, $summary['invalid_count']);

        $rowResults = $previewResponse->viewData('rows');
        $this->assertStringContainsString('Subject mismatch', implode('; ', $rowResults[0]['errors']));
    }

    /**
     * Test 12: Duplicate question number in database detected.
     */
    public function test_duplicate_question_number_in_database_detected(): void
    {
        // Existing question with number 1
        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Existing question already in database.',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1', // Conflict with existing #1!
                'Brand new question text that tries to reuse number 1.',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Explanation',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $summary = $previewResponse->viewData('summary');
        $this->assertEquals(1, $summary['duplicate_count']);
        $this->assertEquals(0, $summary['valid_count']);

        $rowResults = $previewResponse->viewData('rows');
        $this->assertEquals('duplicate', $rowResults[0]['status']);
        $this->assertStringContainsString('already exists in database', implode('; ', $rowResults[0]['errors']));
    }

    /**
     * Test 13: Duplicate rows in same upload detected.
     */
    public function test_duplicate_rows_in_same_upload_detected(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '10',
                'What is prime?',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Exp',
            ],
            [
                'Arithmetic Aptitude',
                'Number System',
                '10', // Duplicate number in same file!
                'Another question with same number.',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Exp',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), [
                'file' => $file,
            ]);

        $previewResponse->assertOk();
        $summary = $previewResponse->viewData('summary');
        $this->assertEquals(1, $summary['valid_count']);
        $this->assertEquals(1, $summary['duplicate_count']);
    }

    /**
     * Test 14: Safe skip behavior on duplicates and invalids during import execution.
     */
    public function test_safe_skip_behavior_on_duplicates_and_invalids(): void
    {
        // Existing question #1 in DB
        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Existing question in DB.',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1', // Duplicate row
                'Duplicate attempt.',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Exp',
            ],
            [
                'Arithmetic Aptitude',
                'Number System',
                '2', // Valid row!
                'Brand new valid question #2.',
                'A',
                'B',
                'C',
                'D',
                'B',
                'Exp',
            ],
            [
                'Arithmetic Aptitude',
                'Number System',
                '3', // Invalid row (missing option)
                'Invalid question #3.',
                'A',
                '',
                'C',
                'D',
                'C',
                'Exp',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), ['file' => $file]);

        $token = $previewResponse->viewData('token');

        // Confirm with skip_duplicates=1 and skip_invalids=1
        $confirmResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import.confirm'), [
                'token' => $token,
                'skip_duplicates' => 1,
                'skip_invalids' => 1,
            ]);

        $confirmResponse->assertOk();
        $report = $confirmResponse->viewData('report');

        $this->assertEquals(1, $report['imported_count']);
        $this->assertEquals(2, $report['skipped_count']);
        $this->assertEquals(1, $report['duplicate_count']);
        $this->assertEquals(1, $report['invalid_count']);

        // Check DB: Question #2 imported, Question #1 untouched, Question #3 skipped
        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'Brand new valid question #2.',
        ]);
        $this->assertDatabaseMissing('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 3,
        ]);
    }

    /**
     * Test 15: Missing image references reported without broken paths created.
     */
    public function test_missing_image_references_reported_without_broken_paths(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '5',
                'Refer to figure DI-999 to answer this question.',
                '10',
                '20',
                '30',
                '40',
                'A',
                'Exp',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $previewResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import'), ['file' => $file]);

        $previewResponse->assertOk();
        $token = $previewResponse->viewData('token');

        $confirmResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import.confirm'), [
                'token' => $token,
            ]);

        $confirmResponse->assertOk();

        // The question is created, but no broken image path is set while figure_id is preserved
        $question = Question::where('topic_id', $this->topic->id)->where('question_number', 5)->first();
        $this->assertNotNull($question);
        $this->assertNull($question->question_image);
        $this->assertEquals('DI-999', $question->figure_id);
    }

    /**
     * Test 16: Transaction rollback preserves database integrity on unhandled failure.
     */
    public function test_transaction_rollback_preserves_database_integrity(): void
    {
        $processor = new McqImportProcessor();

        // Row that will fail inside transaction if skip_invalids is false
        $rows = [
            [
                'row_number' => 1,
                'sheet' => 'Sheet1',
                'status' => 'valid',
                'category_id' => $this->category->id,
                'subject_id' => $this->subject->id,
                'topic_id' => $this->topic->id,
                'question_number' => 100,
                'question_text' => 'First valid question.',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'A',
                'explanation' => null,
                'figure_id' => null,
                'question_image' => null,
                'option_a_image' => null,
                'option_b_image' => null,
                'option_c_image' => null,
                'option_d_image' => null,
            ],
            [
                'row_number' => 2,
                'sheet' => 'Sheet1',
                'status' => 'invalid',
                'errors' => ['Fatal simulated failure'],
            ],
        ];

        try {
            $processor->process($rows, [
                'filename' => 'fail_test.csv',
                'user' => $this->admin,
                'skip_duplicates' => false,
                'skip_invalids' => false, // Will throw exception and trigger rollback!
            ]);
            $this->fail('Expected exception was not thrown.');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Fatal simulated failure', $e->getMessage());
        }

        // Verify that Question #100 was NOT committed to the database
        $this->assertDatabaseMissing('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 100,
        ]);

        // Verify import batch was recorded as failed
        $this->assertDatabaseHas('import_batches', [
            'user_id' => $this->admin->id,
            'filename' => 'fail_test.csv',
            'status' => 'failed',
        ]);
    }

    /**
     * Test 17: Existing questions are strictly preserved during import.
     */
    public function test_existing_questions_are_strictly_preserved(): void
    {
        $existing = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Original Untouchable Question',
            'option_a' => 'Orig A',
            'option_b' => 'Orig B',
            'option_c' => 'Orig C',
            'option_d' => 'Orig D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '2',
                'A new question alongside existing one.',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Exp',
            ],
        ];

        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), ['file' => $file]);
        $token = $preview->viewData('token');

        $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), ['token' => $token]);

        // Original question still intact
        $this->assertDatabaseHas('questions', [
            'id' => $existing->id,
            'question_number' => 1,
            'question_text' => 'Original Untouchable Question',
            'option_a' => 'Orig A',
        ]);

        // New question added
        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'A new question alongside existing one.',
        ]);
    }

    /**
     * Test 18: Cancel import cleans up temporary storage.
     */
    public function test_cancel_import_cleans_up_temporary_storage(): void
    {
        $rows = [
            [
                'Arithmetic Aptitude',
                'Number System',
                '1',
                'Sample question text?',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Exp',
            ],
        ];
        $file = $this->createCsvUpload($this->getCanonicalHeaders(), $rows);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), ['file' => $file]);
        $token = $preview->viewData('token');

        $previewFile = storage_path("app/imports/preview_{$token}.json");
        $this->assertTrue(File::exists($previewFile));

        $cancelResponse = $this->actingAs($this->admin)
            ->post(route('admin.mcqs.import.cancel'), ['token' => $token]);

        $cancelResponse->assertRedirect(route('admin.mcqs.import.form'));
        $cancelResponse->assertSessionHas('info');

        // Verify temporary preview file was deleted
        $this->assertFalse(File::exists($previewFile));
    }

    /**
     * Test 19: Export audit errors CSV prevents spreadsheet formula injection.
     */
    public function test_export_audit_errors_csv_prevents_formula_injection(): void
    {
        $batch = ImportBatch::create([
            'user_id' => $this->admin->id,
            'filename' => 'injection_test.csv',
            'total_rows' => 1,
            'successful_rows' => 0,
            'failed_rows' => 1,
            'status' => 'completed',
            'errors' => [
                [
                    'row' => 1,
                    'sheet' => 'Sheet1',
                    'status' => 'invalid',
                    'question' => '=cmd|"/C calc"!A0', // Potential formula injection payload
                    'reason' => '+2+5+cmd|"/C calc"!A0',
                ],
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.mcqs.import.export-errors', $batch->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->getContent();

        // Formula prefix '=' and '+' must be escaped with single quote "'"
        $this->assertStringContainsString("'=cmd", $content);
        $this->assertStringContainsString("'+2+5", $content);
    }
}
