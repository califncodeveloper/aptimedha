<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ImportBatch;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\Import\McqImageArchiveHandler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;
use ZipArchive;

class BulkMcqImportCloudinaryIntegrationTest extends TestCase
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
            'name' => 'Quantitative Aptitude',
            'slug' => 'quantitative-aptitude',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->subject = Subject::create([
            'category_id' => $this->category->id,
            'name' => 'Arithmetic',
            'slug' => 'arithmetic',
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
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
     * Helper to create a test ZIP archive containing image files.
     * Uses pure-PHP PKZip format so it does not require ext-zip to be installed.
     */
    protected function createZipUpload(array $files, string $filename = 'images.zip'): UploadedFile
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'zip_');
        $fh = fopen($tempPath, 'wb');
        $cd = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $nameLen = strlen($name);
            $size = strlen($content);
            $crc = crc32($content);

            // Local file header (PK\x03\x04)
            $localHeader = pack('VvvvvvVVVvv',
                0x04034b50, // signature
                20,         // version needed (2.0)
                0,          // flags
                0,          // compression method (0 = stored)
                0,          // mod time
                0,          // mod date
                $crc,       // crc32
                $size,      // comp size
                $size,      // uncomp size
                $nameLen,   // name length
                0           // extra len
            ) . $name;

            fwrite($fh, $localHeader . $content);

            // Central directory entry (PK\x01\x02)
            $cd .= pack('VvvvvvvVVVvvvvvVV',
                0x02014b50, // signature
                20,         // version made by
                20,         // version needed
                0,          // flags
                0,          // method
                0,          // time
                0,          // date
                $crc,       // crc32
                $size,      // comp size
                $size,      // uncomp size
                $nameLen,   // name len
                0,          // extra len
                0,          // comment len
                0,          // disk start
                0,          // int attr
                0,          // ext attr
                $offset     // local header offset
            ) . $name;

            $offset += strlen($localHeader) + $size;
        }

        $cdOffset = $offset;
        $cdSize = strlen($cd);
        $totalEntries = count($files);

        fwrite($fh, $cd);

        // End of Central Directory (PK\x05\x06)
        $eocd = pack('VvvvvVVv',
            0x06054b50,    // signature
            0,             // disk number
            0,             // disk with CD
            $totalEntries, // entries on disk
            $totalEntries, // total entries
            $cdSize,       // CD size
            $cdOffset,     // CD offset
            0              // comment len
        );

        fwrite($fh, $eocd);
        fclose($fh);

        return new UploadedFile($tempPath, $filename, 'application/zip', null, true);
    }

    /**
     * Canonical 16-column headers map (10 canonical + 6 image fields).
     */
    protected function getCanonicalImageHeaders(): array
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
            'Question Image',
            'Option A Image',
            'Option B Image',
            'Option C Image',
            'Option D Image',
            'Explanation Image',
        ];
    }

    /**
     * Test 1: Excel/CSV import without images continues to work seamlessly.
     */
    public function test_import_without_images_succeeds(): void
    {
        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '1',
                'What is the unit digit of 3^65?',
                '1',
                '3',
                '7',
                '9',
                'B',
                'Power cyclicity of 3 is 4. 65 mod 4 = 1, so unit digit is 3.',
            ],
        ];

        $csv = $this->createCsvUpload(array_slice($this->getCanonicalImageHeaders(), 0, 10), $rows);

        $previewResponse = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
        ]);

        $previewResponse->assertOk();
        $token = $previewResponse->viewData('token');

        $confirmResponse = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
        ]);

        $confirmResponse->assertOk();
        $confirmResponse->assertViewIs('admin.mcqs.import-report');

        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'What is the unit digit of 3^65?',
            'question_image' => null,
            'option_a_image' => null,
            'option_b_image' => null,
            'option_c_image' => null,
            'option_d_image' => null,
            'explanation_image' => null,
        ]);
    }

    /**
     * Test 2: Excel + ZIP import with question images uploaded to Cloudinary folder aptimedha/mcqs/questions.
     */
    public function test_import_with_question_image_uploads_to_cloudinary(): void
    {
        $mockCloudinary = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadImage')
            ->once()
            ->withArgs(function ($filePath, $folder) {
                return $folder === 'aptimedha/mcqs/questions' && str_ends_with($filePath, 'q1_diagram.png');
            })
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/mcqs/questions/mock_q1_diagram',
                'secure_url' => 'https://res.cloudinary.com/aptimedha/image/upload/v1/aptimedha/mcqs/questions/mock_q1_diagram.png',
            ]);

        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '10',
                'Look at the number sequence diagram and find X.',
                '10',
                '15',
                '20',
                '25',
                'C',
                'Step is +5.',
                'images/q1_diagram.png',
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/q1_diagram.png' => 'fake_png_binary_data',
        ]);

        $previewResponse = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);

        $previewResponse->assertOk();
        $token = $previewResponse->viewData('token');

        $confirmResponse = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
        ]);

        $confirmResponse->assertOk();

        $question = Question::where('topic_id', $this->topic->id)->where('question_number', 10)->first();
        $this->assertNotNull($question);
        $this->assertEquals('https://res.cloudinary.com/aptimedha/image/upload/v1/aptimedha/mcqs/questions/mock_q1_diagram.png', $question->question_image);
        $this->assertTrue($question->has_image);
        $this->assertEquals('https://res.cloudinary.com/aptimedha/image/upload/v1/aptimedha/mcqs/questions/mock_q1_diagram.png', $question->image_url);
    }

    /**
     * Test 3: Option images A–D uploaded to Cloudinary folder aptimedha/mcqs/options.
     */
    public function test_import_with_option_images_a_to_d_uploads_to_cloudinary(): void
    {
        $mockCloudinary = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadImage')
            ->times(4)
            ->withArgs(function ($filePath, $folder) {
                return $folder === 'aptimedha/mcqs/options';
            })
            ->andReturnUsing(function ($filePath, $folder) {
                $base = pathinfo($filePath, PATHINFO_FILENAME);
                return [
                    'success' => true,
                    'public_id' => "{$folder}/mock_{$base}",
                    'secure_url' => "https://res.cloudinary.com/aptimedha/image/upload/v1/{$folder}/mock_{$base}.png",
                ];
            });

        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '20',
                'Which of the four figures is symmetric?',
                'Figure A',
                'Figure B',
                'Figure C',
                'Figure D',
                'A',
                'Figure A has bilateral symmetry.',
                '',
                'images/opt_a.png',
                'images/opt_b.png',
                'images/opt_c.png',
                'images/opt_d.png',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/opt_a.png' => 'data_a',
            'images/opt_b.png' => 'data_b',
            'images/opt_c.png' => 'data_c',
            'images/opt_d.png' => 'data_d',
        ]);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);
        $preview->assertOk();
        $token = $preview->viewData('token');

        $confirm = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
        ]);
        $confirm->assertOk();

        $question = Question::where('topic_id', $this->topic->id)->where('question_number', 20)->first();
        $this->assertNotNull($question);
        $this->assertStringContainsString('aptimedha/mcqs/options/mock_opt_a.png', $question->option_a_image);
        $this->assertStringContainsString('aptimedha/mcqs/options/mock_opt_b.png', $question->option_b_image);
        $this->assertStringContainsString('aptimedha/mcqs/options/mock_opt_c.png', $question->option_c_image);
        $this->assertStringContainsString('aptimedha/mcqs/options/mock_opt_d.png', $question->option_d_image);
        $this->assertTrue($question->hasOptionImage('A'));
        $this->assertTrue($question->hasOptionImage('D'));
    }

    /**
     * Test 4: Explanation image uploaded to Cloudinary folder aptimedha/mcqs/explanations.
     */
    public function test_import_with_explanation_image_uploads_to_cloudinary(): void
    {
        $mockCloudinary = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadImage')
            ->once()
            ->withArgs(function ($filePath, $folder) {
                return $folder === 'aptimedha/mcqs/explanations';
            })
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/mcqs/explanations/mock_exp',
                'secure_url' => 'https://res.cloudinary.com/aptimedha/image/upload/v1/aptimedha/mcqs/explanations/mock_exp.png',
            ]);

        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '30',
                'Calculate the shaded area in the diagram.',
                '12',
                '14',
                '16',
                '18',
                'C',
                'See attached proof below.',
                '',
                '',
                '',
                '',
                '',
                'images/exp_diagram.png',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/exp_diagram.png' => 'proof_data',
        ]);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);
        $token = $preview->viewData('token');

        $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
        ])->assertOk();

        $question = Question::where('topic_id', $this->topic->id)->where('question_number', 30)->first();
        $this->assertNotNull($question);
        $this->assertEquals('https://res.cloudinary.com/aptimedha/image/upload/v1/aptimedha/mcqs/explanations/mock_exp.png', $question->explanation_image);
        $this->assertTrue($question->has_explanation_image);
        $this->assertEquals('https://res.cloudinary.com/aptimedha/image/upload/v1/aptimedha/mcqs/explanations/mock_exp.png', $question->explanation_image_url);
    }

    /**
     * Test 5: Missing image references reported and invalid row skipped.
     */
    public function test_missing_image_references_reported_and_prevent_import(): void
    {
        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '40',
                'Question referencing a non-existent image.',
                'A',
                'B',
                'C',
                'D',
                'A',
                'Sol',
                'images/missing_diagram.png', // Not in ZIP!
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/other_file.png' => 'some_data',
        ]);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);

        $preview->assertOk();
        $summary = $preview->viewData('summary');
        $this->assertEquals(0, $summary['valid_count']);
        $this->assertEquals(1, $summary['invalid_count']);
        $this->assertEquals(1, $summary['missing_image_count']);

        $token = $preview->viewData('token');

        // Confirm import with skip_invalids=true
        $confirm = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
            'skip_invalids' => 1,
        ]);
        $confirm->assertOk();

        // Question must NOT be created with broken image path
        $this->assertDatabaseMissing('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 40,
        ]);
    }

    /**
     * Test 6: Invalid or corrupted ZIP file is safely rejected.
     */
    public function test_corrupted_zip_file_is_rejected(): void
    {
        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '50',
                'Question statement.',
                '1',
                '2',
                '3',
                '4',
                'A',
                'Sol',
                'q.png',
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);

        // Create corrupt fake ZIP file
        $tempPath = tempnam(sys_get_temp_dir(), 'corrupt_');
        file_put_contents($tempPath, 'This is definitely not a zip file archive header!');
        $corruptZip = new UploadedFile($tempPath, 'corrupt.zip', 'application/zip', null, true);

        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $corruptZip,
        ]);

        $response->assertRedirect(route('admin.mcqs.import.form'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString('ZIP Archive Error', session('error'));
    }

    /**
     * Test 7: Path traversal ZIP attack (zip slip) safely blocked.
     */
    public function test_zip_path_traversal_is_blocked(): void
    {
        $archiveHandler = new McqImageArchiveHandler();
        $this->assertTrue($archiveHandler->isUnsafeZipEntry('../etc/passwd'));
        $this->assertTrue($archiveHandler->isUnsafeZipEntry('../../images/hack.png'));
        $this->assertTrue($archiveHandler->isUnsafeZipEntry('sub/../../hack.png'));
        $this->assertTrue($archiveHandler->isUnsafeZipEntry('/absolute/path.png'));
        $this->assertTrue($archiveHandler->isUnsafeZipEntry('C:\windows\system32.png'));
        $this->assertFalse($archiveHandler->isUnsafeZipEntry('images/q1.png'));
        $this->assertFalse($archiveHandler->isUnsafeZipEntry('q1.png'));
    }

    /**
     * Test 8: Duplicate MCQs handled properly without uploading images for skipped duplicates.
     */
    public function test_duplicate_mcqs_skip_without_uploading_images(): void
    {
        // Pre-create question 60 in database
        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 60,
            'section_number' => 1,
            'question_text' => 'Existing question text for number 60.',
            'option_a' => '1',
            'option_b' => '2',
            'option_c' => '3',
            'option_d' => '4',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $mockCloudinary = Mockery::mock(CloudinaryService::class)->makePartial();
        // Should NEVER be called for duplicate rows!
        $mockCloudinary->shouldNotReceive('uploadImage');
        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '60',
                'Existing question text for number 60.',
                '1',
                '2',
                '3',
                '4',
                'A',
                'Sol',
                'images/unused.png',
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/unused.png' => 'data',
        ]);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);

        $preview->assertOk();
        $this->assertEquals(1, $preview->viewData('summary')['duplicate_count']);
        $token = $preview->viewData('token');

        $confirm = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
            'skip_duplicates' => 1,
        ]);

        $confirm->assertOk();
        $report = $confirm->viewData('report');
        $this->assertEquals(1, $report['duplicate_count']);
        $this->assertEquals(0, $report['imported_count']);
        $this->assertEquals(0, $report['uploaded_images_count']);
    }

    /**
     * Test 9: Cloudinary upload failure handling and cleanup.
     */
    public function test_cloudinary_upload_failure_reported_cleanly(): void
    {
        $mockCloudinary = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadImage')
            ->once()
            ->andReturn([
                'success' => false,
                'error' => 'API rate limit exceeded or network failure',
            ]);
        $mockCloudinary->shouldReceive('deleteAsset')->zeroOrMoreTimes();

        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $rows = [
            [
                'Quantitative Aptitude',
                'Number System',
                '70',
                'Question that will fail upload.',
                '1',
                '2',
                '3',
                '4',
                'A',
                'Sol',
                'images/fail.png',
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/fail.png' => 'fail_data',
        ]);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);
        $token = $preview->viewData('token');

        $confirm = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
            'skip_invalids' => 1,
        ]);

        $confirm->assertOk();
        $report = $confirm->viewData('report');
        $this->assertEquals(1, $report['failed_images_count']);
        $this->assertEquals(0, $report['imported_count']);
        $this->assertDatabaseMissing('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 70,
        ]);
    }

    /**
     * Test 10: Partial import failure allows valid rows to import while failing bad rows.
     */
    public function test_partial_import_success_with_skip_invalids(): void
    {
        $mockCloudinary = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadImage')
            ->once()
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/mcqs/questions/good',
                'secure_url' => 'https://res.cloudinary.com/aptimedha/good.png',
            ]);

        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $rows = [
            // Row 1: Valid with image
            [
                'Quantitative Aptitude',
                'Number System',
                '81',
                'Valid Question 81.',
                '1',
                '2',
                '3',
                '4',
                'A',
                'Sol',
                'images/good.png',
                '',
                '',
                '',
                '',
                '',
            ],
            // Row 2: Invalid with missing option
            [
                'Quantitative Aptitude',
                'Number System',
                '82',
                'Invalid Question missing option C.',
                '1',
                '2',
                '', // Missing!
                '4',
                'A',
                'Sol',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
        ];

        $csv = $this->createCsvUpload($this->getCanonicalImageHeaders(), $rows);
        $zip = $this->createZipUpload([
            'images/good.png' => 'good_data',
        ]);

        $preview = $this->actingAs($this->admin)->post(route('admin.mcqs.import'), [
            'file' => $csv,
            'zip_file' => $zip,
        ]);
        $token = $preview->viewData('token');

        $confirm = $this->actingAs($this->admin)->post(route('admin.mcqs.import.confirm'), [
            'token' => $token,
            'skip_invalids' => 1,
        ]);

        $confirm->assertOk();
        $report = $confirm->viewData('report');
        $this->assertEquals(1, $report['imported_count']);
        $this->assertEquals(1, $report['invalid_count']);
        $this->assertEquals(1, $report['uploaded_images_count']);

        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 81,
            'question_image' => 'https://res.cloudinary.com/aptimedha/good.png',
        ]);
        $this->assertDatabaseMissing('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 82,
        ]);
    }

    /**
     * Test 11: Public MCQ image rendering with Cloudinary URLs.
     */
    public function test_public_mcq_image_rendering(): void
    {
        $question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 90,
            'section_number' => 1,
            'question_text' => 'Public view test question with diagram.',
            'question_image' => 'https://res.cloudinary.com/aptimedha/mcqs/questions/q90.png',
            'option_a' => 'Option Alpha',
            'option_a_image' => 'https://res.cloudinary.com/aptimedha/mcqs/options/opt_a90.png',
            'option_b' => 'Option Beta',
            'option_c' => 'Option Gamma',
            'option_d' => 'Option Delta',
            'correct_option' => 'A',
            'explanation' => 'Step by step derivation.',
            'explanation_image' => 'https://res.cloudinary.com/aptimedha/mcqs/explanations/exp90.png',
            'is_active' => true,
        ]);

        $this->assertEquals('https://res.cloudinary.com/aptimedha/mcqs/questions/q90.png', $question->image_url);
        $this->assertEquals('https://res.cloudinary.com/aptimedha/mcqs/options/opt_a90.png', $question->option_a_image_url);
        $this->assertEquals('https://res.cloudinary.com/aptimedha/mcqs/explanations/exp90.png', $question->explanation_image_url);

        $response = $this->get(route('mcq.show', $question->id));

        $response->assertOk();
        $response->assertSee('https://res.cloudinary.com/aptimedha/mcqs/questions/q90.png');
        $response->assertSee('https://res.cloudinary.com/aptimedha/mcqs/options/opt_a90.png');
        $response->assertSee('https://res.cloudinary.com/aptimedha/mcqs/explanations/exp90.png');
    }

    /**
     * Test 12: Download import template includes 16 columns with image references.
     */
    public function test_download_template_includes_image_reference_columns(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.mcqs.import.template'));
        $response->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('Question Image', $content);
        $this->assertStringContainsString('Option A Image', $content);
        $this->assertStringContainsString('Option B Image', $content);
        $this->assertStringContainsString('Option C Image', $content);
        $this->assertStringContainsString('Option D Image', $content);
        $this->assertStringContainsString('Explanation Image', $content);
    }
}
