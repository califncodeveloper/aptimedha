<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Services\CloudinaryService;
use App\Services\HtmlSanitizer;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class CloudinaryMediaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_admin_can_create_mcq_with_all_option_and_explanation_images(): void
    {
        $admin = User::factory()->admin()->create();

        $category = Category::create([
            'name' => 'Quantitative Aptitude',
            'slug' => 'quantitative-aptitude',
            'is_active' => true,
        ]);
        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Arithmetic',
            'slug' => 'arithmetic',
            'is_active' => true,
        ]);
        $topic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Geometry',
            'slug' => 'geometry',
            'is_active' => true,
        ]);

        // Mock CloudinaryService to simulate successful Cloudinary uploads
        $mockService = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockService->shouldReceive('isConfigured')->andReturn(true);
        $mockService->shouldReceive('uploadImage')
            ->andReturnUsing(function ($file, $folder) {
                $basename = pathinfo($file instanceof UploadedFile ? $file->getClientOriginalName() : $file, PATHINFO_FILENAME);
                return [
                    'success' => true,
                    'public_id' => "{$folder}/mock_{$basename}",
                    'secure_url' => "https://res.cloudinary.com/j7zcyoc5/image/upload/v1/{$folder}/mock_{$basename}.jpg",
                ];
            });

        $this->app->instance(CloudinaryService::class, $mockService);

        $response = $this->actingAs($admin)->post(route('admin.mcqs.store'), [
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_text' => 'Which figure represents a regular hexagon?',
            'question_image' => UploadedFile::fake()->create('q_diagram.png', 50, 'image/png'),
            'question_image_alt' => 'Hexagonal reference diagram',
            'option_a' => 'Figure A',
            'option_a_image' => UploadedFile::fake()->create('opt_a.png', 40, 'image/png'),
            'option_b' => 'Figure B',
            'option_b_image' => UploadedFile::fake()->create('opt_b.png', 40, 'image/png'),
            'option_c' => 'Figure C',
            'option_c_image' => UploadedFile::fake()->create('opt_c.png', 40, 'image/png'),
            'option_d' => 'Figure D',
            'option_d_image' => UploadedFile::fake()->create('opt_d.png', 40, 'image/png'),
            'correct_option' => 'A',
            'explanation' => 'A regular hexagon has 6 equal sides and internal angles of 120 degrees.',
            'explanation_image' => UploadedFile::fake()->create('sol_diagram.png', 50, 'image/png'),
        ]);

        $response->assertRedirect(route('admin.mcqs.index'));

        $question = Question::where('question_text', 'Which figure represents a regular hexagon?')->first();
        $this->assertNotNull($question);

        // Verify secure Cloudinary URLs are stored
        $this->assertStringStartsWith('https://res.cloudinary.com/', $question->question_image);
        $this->assertEquals('Hexagonal reference diagram', $question->question_image_alt);
        $this->assertStringStartsWith('https://res.cloudinary.com/', $question->option_a_image);
        $this->assertStringStartsWith('https://res.cloudinary.com/', $question->option_b_image);
        $this->assertStringStartsWith('https://res.cloudinary.com/', $question->option_c_image);
        $this->assertStringStartsWith('https://res.cloudinary.com/', $question->option_d_image);
        $this->assertStringStartsWith('https://res.cloudinary.com/', $question->explanation_image);

        // Verify model accessors resolve URLs correctly
        $this->assertEquals($question->question_image, $question->image_url);
        $this->assertEquals($question->option_a_image, $question->option_a_image_url);
        $this->assertEquals($question->option_b_image, $question->option_b_image_url);
        $this->assertEquals($question->option_c_image, $question->option_c_image_url);
        $this->assertEquals($question->option_d_image, $question->option_d_image_url);
        $this->assertEquals($question->explanation_image, $question->explanation_image_url);
        $this->assertTrue($question->hasOptionImage('A'));
        $this->assertTrue($question->has_explanation_image);
    }

    public function test_public_mcq_view_renders_question_option_and_explanation_diagrams(): void
    {
        $category = Category::create(['name' => 'Verbal Ability', 'slug' => 'verbal-ability', 'is_active' => true]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Logic', 'slug' => 'logic', 'is_active' => true]);
        $topic = Topic::create(['category_id' => $category->id, 'subject_id' => $subject->id, 'name' => 'Venn Diagrams', 'slug' => 'venn-diagrams', 'is_active' => true]);

        $mcq = Question::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'Which Venn diagram depicts Mammals, Dogs, and Cats?',
            'question_image' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/questions/q1_venn.png',
            'question_image_alt' => 'Three circles relationship diagram',
            'option_a' => 'Concentric set',
            'option_a_image' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/options/opt_a.png',
            'option_b' => 'Disjoint set',
            'option_c' => 'Partially intersecting',
            'option_d' => 'Nested disjoint',
            'correct_option' => 'A',
            'explanation' => 'Both dogs and cats are sub-classes of mammals.',
            'explanation_image' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/answers/exp_venn.png',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.show', $mcq->id));
        $response->assertOk();

        // Question Diagram & Alt
        $response->assertSee('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/questions/q1_venn.png');
        $response->assertSee('Three circles relationship diagram');

        // Option A Diagram
        $response->assertSee('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/options/opt_a.png');

        // Explanation Diagram
        $response->assertSee('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/answers/exp_venn.png');
    }

    public function test_mcq_image_removal_and_replacement(): void
    {
        $admin = User::factory()->admin()->create();

        $category = Category::create(['name' => 'Tech', 'slug' => 'tech', 'is_active' => true]);
        $subject = Subject::create(['category_id' => $category->id, 'name' => 'Data Structures', 'slug' => 'ds', 'is_active' => true]);
        $topic = Topic::create(['category_id' => $category->id, 'subject_id' => $subject->id, 'name' => 'Trees', 'slug' => 'trees', 'is_active' => true]);

        $mcq = Question::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_number' => 1,
            'question_text' => 'Sample Tree Question',
            'question_image' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/questions/old_tree.png',
            'option_a' => 'A',
            'option_a_image' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/options/old_opt_a.png',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'explanation' => 'Old explanation',
            'is_active' => true,
        ]);

        $mockService = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockService->shouldReceive('isConfigured')->andReturn(true);
        $mockService->shouldReceive('deleteAsset')->andReturn(['success' => true, 'result' => 'ok']);
        $mockService->shouldReceive('uploadImage')->andReturn([
            'success' => true,
            'public_id' => 'aptimedha/mcq/questions/new_tree',
            'secure_url' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/questions/new_tree.png',
        ]);
        $this->app->instance(CloudinaryService::class, $mockService);

        // Update: remove option_a_image and replace question_image
        $response = $this->actingAs($admin)->put(route('admin.mcqs.update', $mcq->id), [
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_text' => 'Sample Tree Question Updated',
            'question_image' => UploadedFile::fake()->create('new_tree.png', 50, 'image/png'),
            'remove_option_a_image' => 1,
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        $response->assertRedirect(route('admin.mcqs.index'));

        $mcq->refresh();
        $this->assertEquals('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/mcq/questions/new_tree.png', $mcq->question_image);
        $this->assertNull($mcq->option_a_image);
    }

    public function test_admin_can_upload_blog_featured_image_to_cloudinary(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create(['name' => 'Placement Tips', 'slug' => 'placement-tips']);

        $mockService = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockService->shouldReceive('isConfigured')->andReturn(true);
        $mockService->shouldReceive('uploadImage')
            ->once()
            ->with(Mockery::any(), CloudinaryService::FOLDER_BLOGS_FEATURED)
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/blogs/featured/hero_mock',
                'secure_url' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/featured/hero_mock.jpg',
            ]);
        $this->app->instance(CloudinaryService::class, $mockService);

        $response = $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'How to Crack Technical Interviews in 2026',
            'blog_category_id' => $category->id,
            'status' => 'published',
            'body' => '<p>Comprehensive technical guide.</p>',
            'featured_image' => UploadedFile::fake()->create('hero.jpg', 80, 'image/jpeg'),
            'featured_image_alt' => 'Interview preparation roadmap',
        ]);

        $response->assertRedirect(route('admin.blogs.index'));

        $blog = Blog::where('title', 'How to Crack Technical Interviews in 2026')->first();
        $this->assertNotNull($blog);
        $this->assertEquals('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/featured/hero_mock.jpg', $blog->featured_image);
        $this->assertEquals('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/featured/hero_mock.jpg', $blog->featured_image_url);
    }

    public function test_quill_embedded_image_upload_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $regularUser = User::factory()->create();

        // 1. Guest is redirected
        $guestResponse = $this->postJson(route('admin.blogs.upload-image'), []);
        $guestResponse->assertUnauthorized();

        // 2. Regular user is forbidden
        $userResponse = $this->actingAs($regularUser)->postJson(route('admin.blogs.upload-image'), []);
        $userResponse->assertForbidden();

        // 3. Validation rejects non-images
        $badFile = UploadedFile::fake()->create('code.js', 10, 'text/javascript');
        $valResponse = $this->actingAs($admin)->postJson(route('admin.blogs.upload-image'), [
            'image' => $badFile,
        ]);
        $valResponse->assertUnprocessable();

        // 4. Admin upload succeeds and returns Cloudinary secure URL
        $mockService = Mockery::mock(CloudinaryService::class)->makePartial();
        $mockService->shouldReceive('isConfigured')->andReturn(true);
        $mockService->shouldReceive('uploadImage')
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/blogs/content/embedded_chart_123',
                'secure_url' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/content/embedded_chart_123.png',
            ]);
        $this->app->instance(CloudinaryService::class, $mockService);

        $goodImage = UploadedFile::fake()->create('chart.png', 60, 'image/png');
        $uploadResponse = $this->actingAs($admin)->post(route('admin.blogs.upload-image'), [
            'image' => $goodImage,
        ], ['Accept' => 'application/json']);

        $uploadResponse->assertOk();
        $uploadResponse->assertJson([
            'success' => true,
            'url' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/content/embedded_chart_123.png',
            'public_id' => 'aptimedha/blogs/content/embedded_chart_123',
        ]);
    }

    public function test_html_sanitizer_disallows_base64_data_images_in_blog_body(): void
    {
        $dirtyWithBase64 = '<p>Normal text</p><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" alt="Sample"><p>Ending</p>';
        $cleaned = HtmlSanitizer::clean($dirtyWithBase64);

        // src should have been stripped because data: scheme is disallowed
        $this->assertStringNotContainsString('data:image/png;base64', $cleaned);
        $this->assertStringNotContainsString('src=', $cleaned);

        // HTTPS image URLs remain intact
        $dirtyWithCloudinary = '<p>Diagram</p><img src="https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/content/sample.png" alt="Cloudinary Asset">';
        $cleanedCloudinary = HtmlSanitizer::clean($dirtyWithCloudinary);
        $this->assertStringContainsString('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/content/sample.png', $cleanedCloudinary);
    }

    public function test_existing_local_images_maintain_full_backward_compatibility(): void
    {
        // 1. Question with local path
        $question = new Question([
            'question_image' => 'questions/local_figure.jpg',
            'option_a_image' => 'questions/local_opt.png',
        ]);

        $expectedQuestionUrl = Storage::disk('public')->url('questions/local_figure.jpg');
        $this->assertEquals($expectedQuestionUrl, $question->image_url);
        $this->assertEquals(Storage::disk('public')->url('questions/local_opt.png'), $question->option_a_image_url);

        // 2. Blog with local path
        $blog = new Blog([
            'featured_image' => 'blogs/local_hero.jpg',
        ]);
        $this->assertEquals(Storage::disk('public')->url('blogs/local_hero.jpg'), $blog->featured_image_url);
    }

    public function test_raw_resource_type_upload_method(): void
    {
        $mockClient = Mockery::mock(Cloudinary::class);
        $mockUploadApi = Mockery::mock(UploadApi::class);

        $mockClient->shouldReceive('uploadApi')->andReturn($mockUploadApi);

        $mockUploadApi->shouldReceive('upload')
            ->once()
            ->with(Mockery::any(), Mockery::subset([
                'resource_type' => 'raw',
                'folder' => 'aptimedha/current-affairs',
            ]))
            ->andReturn(new ApiResponse([
                'public_id' => 'aptimedha/current-affairs/march_2026_pdf',
                'secure_url' => 'https://res.cloudinary.com/j7zcyoc5/raw/upload/v1/aptimedha/current-affairs/march_2026_pdf.pdf',
                'resource_type' => 'raw',
            ], []));

        $service = new CloudinaryService();
        Config::set('services.cloudinary.cloud_name', 'j7zcyoc5');
        Config::set('services.cloudinary.api_key', '123456');
        Config::set('services.cloudinary.api_secret', 'secret');
        $service->setClient($mockClient);

        $tempPdf = tempnam(sys_get_temp_dir(), 'test_pdf_');
        file_put_contents($tempPdf, '%PDF-1.4 test');

        $result = $service->uploadRaw($tempPdf, CloudinaryService::FOLDER_CURRENT_AFFAIRS);

        if (file_exists($tempPdf)) {
            @unlink($tempPdf);
        }

        $this->assertTrue($result['success']);
        $this->assertEquals('raw', $result['resource_type']);
        $this->assertStringContainsString('/raw/upload/', $result['secure_url']);
    }
}
