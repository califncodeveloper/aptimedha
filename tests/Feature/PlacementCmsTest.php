<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PlacementGuide;
use App\Models\User;
use Database\Seeders\PlacementGuideSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlacementCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_placement_admin(): void
    {
        $response = $this->get(route('admin.placement.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('admin.placement.store'), ['title' => 'Unauthorized Guide']);
        $response->assertRedirect(route('login'));
    }

    public function test_standard_user_cannot_access_placement_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.placement.index'));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('admin.placement.store'), ['title' => 'Unauthorized Guide']);
        $response->assertForbidden();
    }

    public function test_admin_can_view_placement_guides_index_with_stats(): void
    {
        $admin = User::factory()->admin()->create();

        PlacementGuide::create([
            'title' => '4-Stage Placement Roadmap',
            'slug' => '4-stage-placement-roadmap',
            'category' => 'roadmap',
            'excerpt' => 'Structured step-by-step roadmap.',
            'content' => '<p>Full roadmap content</p>',
            'is_published' => true,
            'display_order' => 1,
            'downloadable_asset' => 'sample.pdf',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.placement.index'));
        $response->assertOk();
        $response->assertSee('4-Stage Placement Roadmap');
        $response->assertSee('Placement Roadmap');
        $response->assertSee('Total Guides');
    }

    public function test_admin_can_filter_and_search_placement_guides(): void
    {
        $admin = User::factory()->admin()->create();

        PlacementGuide::create([
            'title' => 'Resume Writing Masterclass',
            'slug' => 'resume-writing-masterclass',
            'category' => 'resume',
            'content' => '<p>Resume content</p>',
            'is_published' => true,
        ]);

        PlacementGuide::create([
            'title' => 'HR Behavioral Interview Questions',
            'slug' => 'hr-behavioral-interview-questions',
            'category' => 'interview',
            'content' => '<p>HR content</p>',
            'is_published' => true,
        ]);

        // Search by keyword
        $response = $this->actingAs($admin)->get(route('admin.placement.index', ['search' => 'Resume']));
        $response->assertOk();
        $response->assertSee('Resume Writing Masterclass');
        $response->assertDontSee('HR Behavioral Interview Questions');

        // Filter by category
        $response = $this->actingAs($admin)->get(route('admin.placement.index', ['category' => 'interview']));
        $response->assertOk();
        $response->assertSee('HR Behavioral Interview Questions');
        $response->assertDontSee('Resume Writing Masterclass');
    }

    public function test_admin_can_create_placement_guide_with_auto_slug_and_html_sanitization(): void
    {
        $admin = User::factory()->admin()->create();

        $dirtyContent = '<h2>Preparation Steps</h2><p>Safe paragraph</p><script>alert("xss")</script><a href="javascript:attack()">Click</a>';

        $response = $this->actingAs($admin)->post(route('admin.placement.store'), [
            'title' => 'Group Discussion Success Blueprint',
            'slug' => '', // should auto-generate
            'category' => 'interview',
            'excerpt' => 'Strategies to initiate and summarize group discussions.',
            'content' => $dirtyContent,
            'display_order' => 3,
            'is_published' => 1,
            'meta_title' => 'Group Discussion Preparation Tips | AptiMedha',
            'meta_description' => 'Master campus placement group discussions.',
        ]);

        $response->assertRedirect(route('admin.placement.index'));

        $this->assertDatabaseHas('placement_guides', [
            'title' => 'Group Discussion Success Blueprint',
            'slug' => 'group-discussion-success-blueprint',
            'category' => 'interview',
            'display_order' => 3,
            'is_published' => 1,
            'meta_title' => 'Group Discussion Preparation Tips | AptiMedha',
        ]);

        $guide = PlacementGuide::where('slug', 'group-discussion-success-blueprint')->first();
        $this->assertNotNull($guide);
        $this->assertStringNotContainsString('<script>', $guide->content);
        $this->assertStringNotContainsString('javascript:attack()', $guide->content);
        $this->assertStringContainsString('Preparation Steps', $guide->content);
    }

    public function test_admin_can_upload_featured_image_and_downloadable_resource(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $imageFile = UploadedFile::fake()->createWithContent('banner.png', $pngBytes);
        $docFile = UploadedFile::fake()->create('AptiMedha_Resume_Template.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($admin)->post(route('admin.placement.store'), [
            'title' => 'ATS Optimized Resume Template Guide',
            'slug' => 'ats-optimized-resume-template-guide',
            'category' => 'resume',
            'excerpt' => 'Downloadable ATS friendly resume.',
            'content' => '<p>Resume formatting instructions.</p>',
            'featured_image' => $imageFile,
            'featured_image_alt' => 'ATS Resume Template Banner',
            'downloadable_asset' => $docFile,
            'downloadable_asset_name' => 'AptiMedha_Resume_Template.docx',
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.placement.index'));

        $guide = PlacementGuide::where('slug', 'ats-optimized-resume-template-guide')->first();
        $this->assertNotNull($guide);
        $this->assertNotNull($guide->featured_image);
        $this->assertNotNull($guide->downloadable_asset);
        $this->assertEquals('AptiMedha_Resume_Template.docx', $guide->downloadable_asset_name);
        $this->assertTrue($guide->hasDownloadableAsset());
    }

    public function test_admin_can_update_placement_guide_and_manage_assets(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $guide = PlacementGuide::create([
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'category' => 'roadmap',
            'content' => '<p>Initial content</p>',
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.placement.update', $guide), [
            'title' => 'Updated Placement Title',
            'slug' => 'updated-placement-title',
            'category' => 'aptitude',
            'excerpt' => 'Updated excerpt',
            'content' => '<p>Updated content</p>',
            'display_order' => 5,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.placement.index'));

        $guide->refresh();
        $this->assertEquals('Updated Placement Title', $guide->title);
        $this->assertEquals('updated-placement-title', $guide->slug);
        $this->assertEquals('aptitude', $guide->category);
        $this->assertEquals(5, $guide->display_order);
    }

    public function test_admin_can_toggle_publication_status(): void
    {
        $admin = User::factory()->admin()->create();

        $guide = PlacementGuide::create([
            'title' => 'Draft Guide',
            'slug' => 'draft-guide',
            'category' => 'general',
            'content' => '<p>Content</p>',
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.placement.toggle-status', $guide));
        $response->assertRedirect();

        $guide->refresh();
        $this->assertTrue($guide->is_published);

        $response = $this->actingAs($admin)->patch(route('admin.placement.toggle-status', $guide));
        $response->assertRedirect();

        $guide->refresh();
        $this->assertFalse($guide->is_published);
    }

    public function test_admin_can_delete_guide_with_safe_asset_cleanup(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $imagePath = 'placement/featured/test-banner.png';
        Storage::disk('public')->put($imagePath, 'dummy-image-content');

        $guide = PlacementGuide::create([
            'title' => 'Temporary Guide',
            'slug' => 'temporary-guide',
            'category' => 'roadmap',
            'content' => '<p>Content</p>',
            'featured_image' => $imagePath,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.placement.destroy', $guide));
        $response->assertRedirect(route('admin.placement.index'));

        $this->assertDatabaseMissing('placement_guides', ['id' => $guide->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_admin_can_upload_embedded_content_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $imageFile = UploadedFile::fake()->createWithContent('diagram.png', $pngBytes);

        $response = $this->actingAs($admin)->postJson(route('admin.placement.upload-image'), [
            'image' => $imageFile,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['success', 'url', 'public_id']);
        $this->assertTrue($response->json('success'));
    }

    public function test_admin_can_reorder_placement_guides(): void
    {
        $admin = User::factory()->admin()->create();

        $g1 = PlacementGuide::create(['title' => 'G1', 'slug' => 'g1', 'category' => 'roadmap', 'content' => 'c', 'display_order' => 1]);
        $g2 = PlacementGuide::create(['title' => 'G2', 'slug' => 'g2', 'category' => 'roadmap', 'content' => 'c', 'display_order' => 2]);

        $response = $this->actingAs($admin)->postJson(route('admin.placement.reorder'), [
            'orders' => [
                ['id' => $g1->id, 'display_order' => 10],
                ['id' => $g2->id, 'display_order' => 5],
            ],
        ]);

        $response->assertOk();
        $this->assertEquals(10, $g1->fresh()->display_order);
        $this->assertEquals(5, $g2->fresh()->display_order);
    }

    public function test_admin_can_preview_guide(): void
    {
        $admin = User::factory()->admin()->create();

        $guide = PlacementGuide::create([
            'title' => 'Previewable Guide',
            'slug' => 'previewable-guide',
            'category' => 'roadmap',
            'content' => '<p>Secret unpublished content</p>',
            'is_published' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.placement.preview', $guide));
        $response->assertOk();
        $response->assertSee('ADMIN PREVIEW MODE');
        $response->assertSee('Secret unpublished content');
    }

    public function test_public_can_view_placement_hub_with_database_guides(): void
    {
        PlacementGuide::create([
            'title' => 'Mastering Aptitude Rounds',
            'slug' => 'mastering-aptitude-rounds',
            'category' => 'aptitude',
            'excerpt' => 'Speed math and analytical reasoning.',
            'content' => '<p>Aptitude content</p>',
            'is_published' => true,
        ]);

        $response = $this->get(route('placement.index'));
        $response->assertOk();
        $response->assertSee('Mastering Aptitude Rounds');
        $response->assertSee('4-Stage Placement Preparation Roadmap');
        $response->assertSee('AptiMedha Resume Preparation Guide');
    }

    public function test_public_can_filter_placement_hub_by_category(): void
    {
        PlacementGuide::create([
            'title' => 'Resume Guide For Placements',
            'slug' => 'resume-guide-for-placements',
            'category' => 'resume',
            'content' => '<p>Resume details</p>',
            'is_published' => true,
        ]);

        PlacementGuide::create([
            'title' => 'Technical Interview Algorithms',
            'slug' => 'technical-interview-algorithms',
            'category' => 'technical',
            'content' => '<p>Tech details</p>',
            'is_published' => true,
        ]);

        $response = $this->get(route('placement.index', ['category' => 'resume']));
        $response->assertOk();
        $response->assertSee('Resume Guide For Placements');
        $response->assertDontSee('Technical Interview Algorithms');
    }

    public function test_public_can_view_published_placement_guide_with_seo_and_breadcrumbs(): void
    {
        Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'display_order' => 1,
        ]);

        $guide = PlacementGuide::create([
            'title' => 'Complete Aptitude Speed Math Guide',
            'slug' => 'complete-aptitude-speed-math-guide',
            'category' => 'aptitude',
            'excerpt' => 'Shortcuts for square roots and mental multiplication.',
            'content' => '<h2>Vedic Math Principles</h2><p>Multiply two digits in seconds.</p>',
            'meta_title' => 'Aptitude Speed Math Techniques | AptiMedha',
            'meta_description' => 'Learn rapid mental math calculations for placement aptitude rounds.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('placement.show', $guide->slug));
        $response->assertOk();
        $response->assertSee('Complete Aptitude Speed Math Guide');
        $response->assertSee('Aptitude Speed Math Techniques | AptiMedha');
        $response->assertSee('Vedic Math Principles');
        $response->assertSee('Placement Preparation');
        $response->assertSee('Aptitude &amp; Assessments', false);
    }

    public function test_public_cannot_view_unpublished_placement_guide(): void
    {
        $guide = PlacementGuide::create([
            'title' => 'Unpublished Roadmap',
            'slug' => 'unpublished-roadmap',
            'category' => 'roadmap',
            'content' => '<p>Confidential</p>',
            'is_published' => false,
        ]);

        $response = $this->get(route('placement.show', $guide->slug));
        $response->assertNotFound();
    }

    public function test_public_can_download_genuine_resource(): void
    {
        Storage::fake('public');

        $docPath = 'placement/downloads/AptiMedha_Resume_Template.docx';
        Storage::disk('public')->put($docPath, 'sample docx binary content');

        $guide = PlacementGuide::create([
            'title' => 'Resume Guide with Template',
            'slug' => 'resume-guide-with-template',
            'category' => 'resume',
            'content' => '<p>Resume content</p>',
            'downloadable_asset' => $docPath,
            'downloadable_asset_name' => 'AptiMedha_Resume_Template.docx',
            'is_published' => true,
        ]);

        $response = $this->get(route('placement.download', $guide->slug));
        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=AptiMedha_Resume_Template.docx');
    }

    public function test_download_handles_missing_asset_gracefully(): void
    {
        $guide = PlacementGuide::create([
            'title' => 'Guide Without Asset',
            'slug' => 'guide-without-asset',
            'category' => 'roadmap',
            'content' => '<p>No asset attached</p>',
            'downloadable_asset' => null,
            'is_published' => true,
        ]);

        $response = $this->get(route('placement.download', $guide->slug));
        $response->assertRedirect(route('placement.show', $guide->slug));
        $response->assertSessionHas('error');
    }

    public function test_backward_compatibility_placement_route_redirects(): void
    {
        $response = $this->get('/placement');
        $response->assertRedirect(route('placement.index'));
    }

    public function test_database_seeder_populates_official_placement_guides(): void
    {
        $this->seed(PlacementGuideSeeder::class);

        $this->assertDatabaseHas('placement_guides', [
            'slug' => 'placement-preparation-roadmap',
            'category' => 'roadmap',
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('placement_guides', [
            'slug' => 'resume-preparation-guide',
            'category' => 'resume',
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('placement_guides', [
            'slug' => 'hr-interview-preparation-guide',
            'category' => 'interview',
            'is_published' => true,
        ]);
    }

    public function test_cloudinary_service_integration_for_placement_featured_and_raw_assets(): void
    {
        $admin = User::factory()->admin()->create();

        $mockCloudinary = \Mockery::mock(\App\Services\CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadImage')
            ->with(\Mockery::any(), \App\Services\CloudinaryService::FOLDER_PLACEMENT_FEATURED)
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/placement/featured/hero_mock',
                'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v1/aptimedha/placement/featured/hero_mock.png',
            ]);
        $mockCloudinary->shouldReceive('uploadRaw')
            ->with(\Mockery::any(), \App\Services\CloudinaryService::FOLDER_PLACEMENT_DOWNLOADS)
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/placement/downloads/resume_template_mock.docx',
                'secure_url' => 'https://res.cloudinary.com/demo/raw/upload/v1/aptimedha/placement/downloads/resume_template_mock.docx',
            ]);
        $this->app->instance(\App\Services\CloudinaryService::class, $mockCloudinary);

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $imageFile = UploadedFile::fake()->createWithContent('banner.png', $pngBytes);
        $docFile = UploadedFile::fake()->create('AptiMedha_Resume_Template.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($admin)->post(route('admin.placement.store'), [
            'title' => 'Cloudinary Hosted Placement Guide',
            'slug' => 'cloudinary-hosted-placement-guide',
            'category' => 'resume',
            'content' => '<p>Cloudinary hosted assets</p>',
            'featured_image' => $imageFile,
            'downloadable_asset' => $docFile,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.placement.index'));

        $guide = PlacementGuide::where('slug', 'cloudinary-hosted-placement-guide')->first();
        $this->assertNotNull($guide);
        $this->assertEquals('https://res.cloudinary.com/demo/image/upload/v1/aptimedha/placement/featured/hero_mock.png', $guide->featured_image);
        $this->assertEquals('https://res.cloudinary.com/demo/raw/upload/v1/aptimedha/placement/downloads/resume_template_mock.docx', $guide->downloadable_asset);
    }
}
