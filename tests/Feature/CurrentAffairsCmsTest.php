<?php

namespace Tests\Feature;

use App\Models\CurrentAffair;
use App\Models\User;
use App\Services\CloudinaryService;
use Database\Seeders\CurrentAffairSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CurrentAffairsCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_current_affairs_admin(): void
    {
        $response = $this->get(route('admin.current-affairs.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('admin.current-affairs.store'), ['title' => 'Unauthorized Issue']);
        $response->assertRedirect(route('login'));
    }

    public function test_standard_user_cannot_access_current_affairs_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.current-affairs.index'));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('admin.current-affairs.store'), ['title' => 'Unauthorized Issue']);
        $response->assertForbidden();
    }

    public function test_admin_can_view_current_affairs_index_with_metrics(): void
    {
        $admin = User::factory()->admin()->create();

        CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'Monthly Current Affairs – November 2026',
            'description' => 'November key GK highlights.',
            'pdf_path' => 'current-affairs/pdfs/nov-2026.pdf',
            'pdf_name' => 'nov-2026.pdf',
            'file_size_bytes' => 2097152, // 2MB
            'downloads_count' => 150,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.current-affairs.index'));
        $response->assertOk();
        $response->assertSee('Monthly Current Affairs – November 2026');
        $response->assertSee('November 2026');
        $response->assertSee('Total Issues');
        $response->assertSee('PDF Storage');
    }

    public function test_admin_can_filter_and_search_current_affairs(): void
    {
        $admin = User::factory()->admin()->create();

        CurrentAffair::create([
            'year' => 2026,
            'month' => 10,
            'title' => 'October 2026 Summit Highlights',
            'pdf_path' => 'current-affairs/pdfs/oct.pdf',
            'is_published' => true,
        ]);

        CurrentAffair::create([
            'year' => 2025,
            'month' => 5,
            'title' => 'May 2025 Global Economy Review',
            'pdf_path' => 'current-affairs/pdfs/may.pdf',
            'is_published' => true,
        ]);

        // Search by keyword
        $response = $this->actingAs($admin)->get(route('admin.current-affairs.index', ['search' => 'Summit']));
        $response->assertOk();
        $response->assertSee('October 2026 Summit Highlights');
        $response->assertDontSee('May 2025 Global Economy Review');

        // Filter by Year
        $response = $this->actingAs($admin)->get(route('admin.current-affairs.index', ['year' => 2025]));
        $response->assertOk();
        $response->assertSee('May 2025 Global Economy Review');
        $response->assertDontSee('October 2026 Summit Highlights');

        // Filter by Month
        $response = $this->actingAs($admin)->get(route('admin.current-affairs.index', ['month' => 10]));
        $response->assertOk();
        $response->assertSee('October 2026 Summit Highlights');
        $response->assertDontSee('May 2025 Global Economy Review');
    }

    public function test_admin_can_create_monthly_current_affairs_with_pdf_upload(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
        $pdfFile = UploadedFile::fake()->createWithContent('aptimedha-ca-dec-2026.pdf', $pdfContent);

        $response = $this->actingAs($admin)->post(route('admin.current-affairs.store'), [
            'year' => 2026,
            'month' => 12,
            'title' => 'Monthly Current Affairs – December 2026',
            'description' => '<p>Year-end capsule with <script>alert("xss")</script> safe text.</p>',
            'pdf' => $pdfFile,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.current-affairs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('current_affairs', [
            'year' => 2026,
            'month' => 12,
            'title' => 'Monthly Current Affairs – December 2026',
            'is_published' => 1,
        ]);

        $record = CurrentAffair::where('year', 2026)->where('month', 12)->first();
        $this->assertNotNull($record);
        $this->assertNotNull($record->pdf_path);
        $this->assertStringNotContainsString('<script>', $record->description);
        $this->assertStringContainsString('Year-end capsule with', $record->description);
        $this->assertTrue(Storage::disk('public')->exists($record->pdf_path));
    }

    public function test_cannot_create_duplicate_issue_for_same_month_and_year(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        CurrentAffair::create([
            'year' => 2026,
            'month' => 7,
            'title' => 'Existing July 2026 Issue',
            'pdf_path' => 'current-affairs/pdfs/july-2026.pdf',
            'is_published' => true,
        ]);

        $pdfFile = UploadedFile::fake()->create('duplicate.pdf', 200, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.current-affairs.store'), [
            'year' => 2026,
            'month' => 7,
            'title' => 'Duplicate July 2026 Issue',
            'pdf' => $pdfFile,
            'is_published' => 1,
        ]);

        $response->assertSessionHasErrors('month');
        $this->assertEquals(1, CurrentAffair::where('year', 2026)->where('month', 7)->count());
    }

    public function test_admin_can_update_current_affairs_and_replace_pdf(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // Initial PDF
        $initialPdf = 'current-affairs/pdfs/old-issue.pdf';
        Storage::disk('public')->put($initialPdf, 'Old PDF content');

        $affair = CurrentAffair::create([
            'year' => 2026,
            'month' => 6,
            'title' => 'June 2026 Issue Original',
            'pdf_path' => $initialPdf,
            'pdf_name' => 'old-issue.pdf',
            'file_size_bytes' => 1024,
            'is_published' => true,
        ]);

        $newPdfContent = "%PDF-1.4\nNew Updated PDF Content\n%%EOF";
        $newPdfFile = UploadedFile::fake()->createWithContent('june-2026-revised.pdf', $newPdfContent);

        $response = $this->actingAs($admin)->put(route('admin.current-affairs.update', $affair), [
            'year' => 2026,
            'month' => 6,
            'title' => 'June 2026 Issue Updated Title',
            'description' => 'Updated editorial content',
            'pdf' => $newPdfFile,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.current-affairs.index'));
        $affair->refresh();

        $this->assertEquals('June 2026 Issue Updated Title', $affair->title);
        $this->assertNotEquals($initialPdf, $affair->pdf_path);
        $this->assertTrue(Storage::disk('public')->exists($affair->pdf_path));
        // Old PDF should have been cleaned up
        $this->assertFalse(Storage::disk('public')->exists($initialPdf));
    }

    public function test_admin_can_toggle_published_status(): void
    {
        $admin = User::factory()->admin()->create();

        $affair = CurrentAffair::create([
            'year' => 2026,
            'month' => 4,
            'title' => 'April 2026 Issue',
            'pdf_path' => 'current-affairs/pdfs/april.pdf',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Toggle to draft
        $response = $this->actingAs($admin)->patch(route('admin.current-affairs.toggle-status', $affair));
        $response->assertRedirect();
        $this->assertFalse($affair->fresh()->is_published);

        // Toggle back to published
        $response = $this->actingAs($admin)->patch(route('admin.current-affairs.toggle-status', $affair));
        $response->assertRedirect();
        $this->assertTrue($affair->fresh()->is_published);
    }

    public function test_admin_can_delete_current_affairs_safely(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pdfPath = 'current-affairs/pdfs/to-delete.pdf';
        Storage::disk('public')->put($pdfPath, 'PDF content');

        $affair = CurrentAffair::create([
            'year' => 2026,
            'month' => 3,
            'title' => 'March 2026 To Delete',
            'pdf_path' => $pdfPath,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.current-affairs.destroy', $affair));
        $response->assertRedirect(route('admin.current-affairs.index'));

        $this->assertDatabaseMissing('current_affairs', ['id' => $affair->id]);
        $this->assertFalse(Storage::disk('public')->exists($pdfPath));
    }

    public function test_admin_inline_preview_serves_pdf_with_proper_headers(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pdfPath = 'current-affairs/pdfs/inline-preview-test.pdf';
        $pdfContent = "%PDF-1.4\nTest PDF Stream Content\n%%EOF";
        Storage::disk('public')->put($pdfPath, $pdfContent);

        $affair = CurrentAffair::create([
            'year' => 2026,
            'month' => 2,
            'title' => 'February 2026 Admin Preview',
            'pdf_path' => $pdfPath,
            'pdf_name' => 'feb-2026.pdf',
            'is_published' => false, // Even draft is viewable by admin
        ]);

        $response = $this->actingAs($admin)->get(route('admin.current-affairs.preview', $affair));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_public_user_can_view_current_affairs_hub_and_filter_by_year(): void
    {
        CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'November 2026 Digest',
            'description' => 'November overview',
            'pdf_path' => 'current-affairs/pdfs/nov.pdf',
            'is_published' => true,
        ]);

        CurrentAffair::create([
            'year' => 2025,
            'month' => 12,
            'title' => 'December 2025 Digest',
            'description' => 'December overview',
            'pdf_path' => 'current-affairs/pdfs/dec.pdf',
            'is_published' => true,
        ]);

        $response = $this->get(route('current-affairs.index'));
        $response->assertOk();
        $response->assertSee('Monthly Current Affairs');
        $response->assertSee('November 2026 Digest');
        $response->assertSee('December 2025 Digest');

        // Filter by 2026
        $response = $this->get(route('current-affairs.index', ['year' => 2026]));
        $response->assertOk();
        $response->assertSee('November 2026 Digest');
        $response->assertDontSee('December 2025 Digest');
    }

    public function test_public_user_cannot_view_unpublished_current_affairs(): void
    {
        $draft = CurrentAffair::create([
            'year' => 2026,
            'month' => 1,
            'title' => 'Unpublished January 2026 Issue',
            'pdf_path' => 'current-affairs/pdfs/jan-draft.pdf',
            'is_published' => false,
        ]);

        // Hub listing does not show draft
        $response = $this->get(route('current-affairs.index'));
        $response->assertOk();
        $response->assertDontSee('Unpublished January 2026 Issue');

        // Public detail page returns 404
        $response = $this->get(route('current-affairs.show', ['year' => 2026, 'month' => 'january']));
        $response->assertNotFound();

        // Public preview returns 404
        $response = $this->get(route('current-affairs.preview', $draft));
        $response->assertNotFound();

        // Public download returns 404
        $response = $this->get(route('current-affairs.download', $draft));
        $response->assertNotFound();
    }

    public function test_public_user_can_view_monthly_issue_detail_page(): void
    {
        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'Monthly Current Affairs – November 2026 Full Guide',
            'description' => '<p>National defense exercises, appointments, and banking policy changes.</p>',
            'pdf_path' => 'current-affairs/pdfs/nov-full.pdf',
            'pdf_name' => 'nov-full.pdf',
            'file_size_bytes' => 1048576,
            'is_published' => true,
        ]);

        $response = $this->get(route('current-affairs.show', ['year' => 2026, 'month' => 11]));
        $response->assertOk();
        $response->assertSee('Monthly Current Affairs – November 2026 Full Guide');
        $response->assertSee('National defense exercises');
        $response->assertSee('Online PDF Reader');

        // Also test access via month name
        $response = $this->get(route('current-affairs.show', ['year' => 2026, 'month' => 'november']));
        $response->assertOk();
        $response->assertSee('Monthly Current Affairs – November 2026 Full Guide');
    }

    public function test_public_preview_serves_pdf_inline(): void
    {
        Storage::fake('public');

        $pdfPath = 'current-affairs/pdfs/public-inline.pdf';
        Storage::disk('public')->put($pdfPath, "%PDF-1.4\nPublic Inline Content\n%%EOF");

        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 10,
            'title' => 'October 2026 Issue',
            'pdf_path' => $pdfPath,
            'pdf_name' => 'oct-2026.pdf',
            'is_published' => true,
        ]);

        $response = $this->get(route('current-affairs.preview', $issue));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_public_download_increments_download_counter_and_serves_file(): void
    {
        Storage::fake('public');

        $pdfPath = 'current-affairs/pdfs/download-test.pdf';
        Storage::disk('public')->put($pdfPath, "%PDF-1.4\nDownloadable PDF content\n%%EOF");

        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 9,
            'title' => 'September 2026 Download Test',
            'pdf_path' => $pdfPath,
            'pdf_name' => 'september-2026.pdf',
            'downloads_count' => 10,
            'is_published' => true,
        ]);

        $response = $this->get(route('current-affairs.download', $issue));
        $response->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));

        // Verify download counter incremented
        $this->assertEquals(11, $issue->fresh()->downloads_count);
    }

    public function test_download_or_preview_returns_404_when_pdf_missing(): void
    {
        Storage::fake('public');

        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 8,
            'title' => 'August 2026 Missing Asset',
            'pdf_path' => 'current-affairs/pdfs/non-existent.pdf',
            'is_published' => true,
        ]);

        $response = $this->get(route('current-affairs.download', $issue));
        $response->assertNotFound();

        $response = $this->get(route('current-affairs.preview', $issue));
        $response->assertNotFound();
    }

    public function test_model_month_name_and_formatted_file_size_accessors(): void
    {
        $affair = new CurrentAffair([
            'year' => 2026,
            'month' => 11,
            'file_size_bytes' => 3145728, // 3 MB
        ]);

        $this->assertEquals('November', $affair->month_name);
        $this->assertEquals('November 2026', $affair->period_label);
        $this->assertEquals('3.00 MB', $affair->formatted_file_size);

        $smallAffair = new CurrentAffair([
            'year' => 2026,
            'month' => 1,
            'file_size_bytes' => 51200, // 50 KB
        ]);
        $this->assertEquals('50.0 KB', $smallAffair->formatted_file_size);
    }

    public function test_cloudinary_upload_and_local_fallback_behavior(): void
    {
        $admin = User::factory()->admin()->create();

        $mockCloudinary = \Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadRaw')
            ->with(\Mockery::any(), CloudinaryService::FOLDER_CURRENT_AFFAIRS_PDFS)
            ->andReturn([
                'success' => true,
                'public_id' => 'aptimedha/current-affairs/pdfs/november_2026_test.pdf',
                'secure_url' => 'https://res.cloudinary.com/demo/raw/upload/v1/aptimedha/current-affairs/pdfs/november_2026_test.pdf',
                'bytes' => 123456,
            ]);
        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $pdfFile = UploadedFile::fake()->create('ca-test.pdf', 300, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.current-affairs.store'), [
            'year' => 2026,
            'month' => 11,
            'title' => 'Cloudinary Uploaded Issue',
            'pdf' => $pdfFile,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.current-affairs.index'));

        $this->assertDatabaseHas('current_affairs', [
            'year' => 2026,
            'month' => 11,
            'pdf_path' => 'https://res.cloudinary.com/demo/raw/upload/v1/aptimedha/current-affairs/pdfs/november_2026_test.pdf',
            'file_size_bytes' => 123456,
        ]);
    }

    public function test_cloudinary_upload_failure_falls_back_to_local_disk(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $mockCloudinary = \Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadRaw')
            ->with(\Mockery::any(), CloudinaryService::FOLDER_CURRENT_AFFAIRS_PDFS)
            ->andReturn([
                'success' => false,
                'error' => 'Simulated upload timeout',
            ]);
        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $pdfFile = UploadedFile::fake()->create('ca-fallback.pdf', 300, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.current-affairs.store'), [
            'year' => 2026,
            'month' => 9,
            'title' => 'Fallback Stored Issue',
            'pdf' => $pdfFile,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.current-affairs.index'));

        $record = CurrentAffair::where('year', 2026)->where('month', 9)->first();
        $this->assertNotNull($record);
        $this->assertTrue(Storage::disk('public')->exists($record->pdf_path));
    }

    public function test_safe_asset_cleanup_preserves_shared_assets(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $sharedPdfPath = 'current-affairs/pdfs/shared-capsule.pdf';
        Storage::disk('public')->put($sharedPdfPath, "%PDF-1.4\nShared PDF Content\n%%EOF");

        $issue1 = CurrentAffair::create([
            'year' => 2026,
            'month' => 1,
            'title' => 'Issue 1',
            'pdf_path' => $sharedPdfPath,
            'is_published' => true,
        ]);

        $issue2 = CurrentAffair::create([
            'year' => 2026,
            'month' => 2,
            'title' => 'Issue 2',
            'pdf_path' => $sharedPdfPath,
            'is_published' => true,
        ]);

        // Delete issue 1
        $this->actingAs($admin)->delete(route('admin.current-affairs.destroy', $issue1));

        // Shared PDF should still exist because issue 2 references it
        $this->assertTrue(Storage::disk('public')->exists($sharedPdfPath));
    }

    public function test_empty_state_rendered_when_no_published_issues(): void
    {
        $response = $this->get(route('current-affairs.index'));
        $response->assertOk();
        $response->assertSee('No Current Affairs Issues Found');
    }

    public function test_remote_cloudinary_preview_and_download_redirects(): void
    {
        $cloudUrl = 'https://res.cloudinary.com/demo/raw/upload/v123456/aptimedha/current-affairs/pdfs/issue.pdf';

        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 5,
            'title' => 'May 2026 Cloudinary Edition',
            'pdf_path' => $cloudUrl,
            'pdf_name' => 'issue.pdf',
            'is_published' => true,
        ]);

        // Preview redirects to cloud URL
        $response = $this->get(route('current-affairs.preview', $issue));
        $response->assertRedirect($cloudUrl);

        // Download redirects with attachment parameter added
        $response = $this->get(route('current-affairs.download', $issue));
        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('fl_attachment', $redirectUrl);
    }

    public function test_database_seeder_populates_monthly_current_affairs(): void
    {
        $this->seed(CurrentAffairSeeder::class);

        $this->assertDatabaseHas('current_affairs', [
            'year' => 2026,
            'month' => 11,
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('current_affairs', [
            'year' => 2026,
            'month' => 10,
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('current_affairs', [
            'year' => 2025,
            'month' => 12,
            'is_published' => true,
        ]);
    }

    public function test_production_environment_prevents_silent_local_fallback_on_cloudinary_failure(): void
    {
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);
        $admin = User::factory()->admin()->create();

        // Simulate production environment
        config(['app.env' => 'production']);
        $this->app['env'] = 'production';

        $mockCloudinary = \Mockery::mock(CloudinaryService::class)->makePartial();
        $mockCloudinary->shouldReceive('isConfigured')->andReturn(true);
        $mockCloudinary->shouldReceive('uploadRaw')
            ->with(\Mockery::any(), CloudinaryService::FOLDER_CURRENT_AFFAIRS_PDFS)
            ->andReturn([
                'success' => false,
                'error' => 'Cloudinary network failure',
            ]);
        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        $pdfFile = UploadedFile::fake()->create('prod-test.pdf', 300, 'application/pdf');

        $response = $this->actingAs($admin)->from(route('admin.current-affairs.create'))->post(route('admin.current-affairs.store'), [
            'year' => 2026,
            'month' => 11,
            'title' => 'Production Failing Issue',
            'pdf' => $pdfFile,
            'is_published' => 1,
        ]);

        // In production, must not fall back to local disk and must return validation error
        $response->assertSessionHasErrors('pdf');
        $this->assertDatabaseMissing('current_affairs', [
            'title' => 'Production Failing Issue',
        ]);
    }

    public function test_current_affair_seeder_skips_in_production(): void
    {
        // Simulate production environment
        config(['app.env' => 'production']);
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--class' => CurrentAffairSeeder::class, '--force' => true]);

        // In production, no mock/sample records should be seeded
        $this->assertEquals(0, CurrentAffair::count());
    }
}
