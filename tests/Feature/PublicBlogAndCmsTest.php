<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\StaticPage;
use App\Models\User;
use Database\Seeders\StaticPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogAndCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_visitors_can_view_published_blogs_only(): void
    {
        $admin = User::factory()->admin()->create();
        $cat = BlogCategory::create(['name' => 'Campus Strategy', 'slug' => 'campus-strategy']);

        $published = Blog::create([
            'blog_category_id' => $cat->id,
            'author_id' => $admin->id,
            'title' => 'Live Campus Guide 2026',
            'slug' => 'live-campus-guide-2026',
            'body' => '<p>Public content</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $draft = Blog::create([
            'blog_category_id' => $cat->id,
            'author_id' => $admin->id,
            'title' => 'Secret Draft Tips',
            'slug' => 'secret-draft-tips',
            'body' => '<p>Secret content</p>',
            'status' => 'draft',
        ]);

        $archived = Blog::create([
            'blog_category_id' => $cat->id,
            'author_id' => $admin->id,
            'title' => 'Old Archived Info',
            'slug' => 'old-archived-info',
            'body' => '<p>Old content</p>',
            'status' => 'archived',
        ]);

        $response = $this->get(route('blog.index'));
        $response->assertOk();
        $response->assertSee('Live Campus Guide 2026');
        $response->assertDontSee('Secret Draft Tips');
        $response->assertDontSee('Old Archived Info');
    }

    public function test_public_search_and_category_filter(): void
    {
        $admin = User::factory()->admin()->create();
        $catA = BlogCategory::create(['name' => 'Quantitative', 'slug' => 'quantitative']);
        $catB = BlogCategory::create(['name' => 'Verbal Ability', 'slug' => 'verbal-ability']);

        Blog::create([
            'blog_category_id' => $catA->id,
            'author_id' => $admin->id,
            'title' => 'Master Percentages and Ratios',
            'slug' => 'master-percentages-and-ratios',
            'body' => '<p>Percentages</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Blog::create([
            'blog_category_id' => $catB->id,
            'author_id' => $admin->id,
            'title' => 'Sentence Correction Rules',
            'slug' => 'sentence-correction-rules',
            'body' => '<p>Grammar</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Filter by category
        $response = $this->get(route('blog.index', ['category' => 'quantitative']));
        $response->assertOk();
        $response->assertSee('Master Percentages and Ratios');
        $response->assertDontSee('Sentence Correction Rules');

        // Search by keyword
        $response = $this->get(route('blog.index', ['q' => 'Correction']));
        $response->assertOk();
        $response->assertSee('Sentence Correction Rules');
        $response->assertDontSee('Master Percentages and Ratios');
    }

    public function test_visitor_can_read_published_article_and_increments_views(): void
    {
        $admin = User::factory()->admin()->create();
        $cat = BlogCategory::create(['name' => 'Technical Interview', 'slug' => 'technical-interview']);

        $blog = Blog::create([
            'blog_category_id' => $cat->id,
            'author_id' => $admin->id,
            'author_name' => 'Siddharth Rao',
            'title' => 'Top 20 OOPs Interview Questions',
            'slug' => 'top-20-oops-interview-questions',
            'excerpt' => 'Clear explanations of polymorphism and inheritance.',
            'body' => '<p>Object-Oriented Programming core principles explained with real placement examples.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'views_count' => 10,
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();
        $response->assertSee('Top 20 OOPs Interview Questions');
        $response->assertSee('Siddharth Rao');
        $response->assertSee('Technical Interview');
        $response->assertSee('Object-Oriented Programming');

        $this->assertEquals(11, $blog->fresh()->views_count);
    }

    public function test_visitor_cannot_access_draft_or_archived_post(): void
    {
        $admin = User::factory()->admin()->create();

        $draft = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Unpublished Draft',
            'slug' => 'unpublished-draft',
            'body' => '<p>Secret</p>',
            'status' => 'draft',
        ]);

        $archived = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Archived Post',
            'slug' => 'archived-post',
            'body' => '<p>Archived</p>',
            'status' => 'archived',
        ]);

        $this->get(route('blog.show', $draft->slug))->assertNotFound();
        $this->get(route('blog.show', $archived->slug))->assertNotFound();
    }

    public function test_admin_can_preview_draft_post_on_public_route(): void
    {
        $admin = User::factory()->admin()->create();

        $draft = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Draft Under Review',
            'slug' => 'draft-under-review',
            'body' => '<p>Content for admin check</p>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get(route('blog.show', $draft->slug));
        $response->assertOk();
        $response->assertSee('Admin Preview Mode');
        $response->assertSee('Draft Under Review');
    }

    public function test_seo_metadata_is_rendered_accurately(): void
    {
        $admin = User::factory()->admin()->create();

        $blog = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Crack TCS NQT on First Attempt',
            'slug' => 'crack-tcs-nqt-first-attempt',
            'meta_title' => 'How to Crack TCS NQT 2026 | Full Preparation Guide',
            'meta_description' => 'Complete preparation schedule, sectional cutoff analysis, and syllabus breakdown for TCS NQT 2026.',
            'excerpt' => 'Short card teaser for TCS NQT',
            'body' => '<p>Detailed TCS guide</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();
        $response->assertSee('<title>How to Crack TCS NQT 2026 | Full Preparation Guide</title>', false);
        $response->assertSee('name="description" content="Complete preparation schedule, sectional cutoff analysis, and syllabus breakdown for TCS NQT 2026."', false);
        $response->assertSee('property="og:title" content="How to Crack TCS NQT 2026 | Full Preparation Guide"', false);
        $response->assertSee('property="og:type" content="article"', false);
        $response->assertSee('name="twitter:card" content="summary_large_image"', false);
    }

    public function test_static_pages_render_client_content(): void
    {
        $this->seed(StaticPageSeeder::class);

        // About Us
        $response = $this->get(route('about'));
        $response->assertOk();
        $response->assertSee('Practise Smart. Get Placement Ready.');
        $response->assertSee('Our Mission');
        $response->assertSee('Our Vision');

        // Contact Us
        $response = $this->get(route('contact'));
        $response->assertOk();
        $response->assertSee('Contact Us');
        $response->assertSee('aptimedha@gmail.com');

        // Disclaimer
        $response = $this->get(route('disclaimer'));
        $response->assertOk();
        $response->assertSee('Website Disclaimer');
        $response->assertSee('Educational Purpose');

        // Terms and Conditions
        $response = $this->get(route('terms'));
        $response->assertOk();
        $response->assertSee('Terms and Conditions');
        $response->assertSee('Acceptable Use');

        // Privacy Policy
        $response = $this->get(route('privacy'));
        $response->assertOk();
        $response->assertSee('Privacy Policy');
        $response->assertSee('Information We Collect');

        // Copyright Policy
        $response = $this->get(route('copyright'));
        $response->assertOk();
        $response->assertSee('Copyright Policy');
        $response->assertSee('Respecting Original Work');
    }

    public function test_admin_can_update_static_page_in_cms_and_changes_reflect(): void
    {
        $this->seed(StaticPageSeeder::class);
        $admin = User::factory()->admin()->create();

        $page = StaticPage::where('slug', 'about')->first();

        $response = $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'About AptiMedha Updated Title',
            'meta_title' => 'Updated About Meta Title',
            'meta_description' => 'Updated meta description for about page.',
            'content' => '<h2>Customized Placement Platform</h2><p>Tailored practice sets for college engineers.</p>',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseHas('static_pages', [
            'slug' => 'about',
            'title' => 'About AptiMedha Updated Title',
            'meta_title' => 'Updated About Meta Title',
        ]);

        // Public page reflects new content
        $public = $this->get(route('about'));
        $public->assertOk();
        $public->assertSee('About AptiMedha Updated Title');
    }

    public function test_standard_user_cannot_access_static_pages_admin(): void
    {
        $this->seed(StaticPageSeeder::class);
        $user = User::factory()->create();
        $page = StaticPage::where('slug', 'about')->first();

        $this->actingAs($user)->get(route('admin.pages.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.pages.edit', $page))->assertForbidden();
        $this->actingAs($user)->put(route('admin.pages.update', $page), [
            'title' => 'Hacked Title',
            'content' => '<p>Hack</p>',
        ])->assertForbidden();
    }
}

