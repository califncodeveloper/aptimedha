<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_blog_administration(): void
    {
        $response = $this->get(route('admin.blogs.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('admin.blogs.store'), ['title' => 'Unauthorized Post']);
        $response->assertRedirect(route('login'));
    }

    public function test_standard_user_cannot_access_blog_administration(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.blogs.index'));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('admin.blogs.store'), ['title' => 'Unauthorized Post']);
        $response->assertForbidden();
    }

    public function test_admin_can_view_blog_index_and_stats(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create(['name' => 'Placement Tips', 'slug' => 'placement-tips']);

        Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'title' => 'Campus Recruitment Roadmap',
            'slug' => 'campus-recruitment-roadmap',
            'body' => '<p>Campus roadmap guide</p>',
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 150,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.blogs.index'));
        $response->assertOk();
        $response->assertSee('Campus Recruitment Roadmap');
        $response->assertSee('Placement Tips');
        $response->assertSee('150 views');
    }

    public function test_admin_can_create_blog_post_with_auto_slug_and_sanitization(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create(['name' => 'Speed Math', 'slug' => 'speed-math']);

        $dirtyBody = '<h2>Quick Tips</h2><p>Normal text</p><script>alert("xss")</script><a href="javascript:hack()">Bad link</a>';

        $response = $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'Fast Vedic Math for Placements',
            'slug' => '', // should auto-generate
            'blog_category_id' => $category->id,
            'author_name' => 'Pooja Nair',
            'status' => 'published',
            'excerpt' => 'Practical mental arithmetic shortcuts.',
            'body' => $dirtyBody,
            'meta_title' => 'Vedic Math Shortcuts for Placements',
            'meta_description' => 'Learn 5 mental arithmetic techniques for aptitude tests.',
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $this->assertDatabaseHas('blogs', [
            'title' => 'Fast Vedic Math for Placements',
            'slug' => 'fast-vedic-math-for-placements',
            'status' => 'published',
            'author_name' => 'Pooja Nair',
            'meta_title' => 'Vedic Math Shortcuts for Placements',
        ]);

        $created = Blog::where('slug', 'fast-vedic-math-for-placements')->first();
        $this->assertNotNull($created);
        $this->assertStringNotContainsString('<script>', $created->body);
        $this->assertStringNotContainsString('javascript:hack()', $created->body);
        $this->assertStringContainsString('<h2>Quick Tips</h2>', $created->body);
    }

    public function test_slug_uniqueness_is_enforced(): void
    {
        $admin = User::factory()->admin()->create();

        Blog::create([
            'author_id' => $admin->id,
            'title' => 'Data Interpretation Guide',
            'slug' => 'data-interpretation-guide',
            'body' => '<p>Part 1</p>',
            'status' => 'published',
        ]);

        // Attempt to create second post with same title and empty slug -> auto-generates data-interpretation-guide-1
        $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'Data Interpretation Guide',
            'slug' => '',
            'status' => 'draft',
            'body' => '<p>Part 2</p>',
        ]);

        $this->assertDatabaseHas('blogs', [
            'slug' => 'data-interpretation-guide-1',
        ]);
    }

    public function test_image_upload_validation_and_storage(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // Invalid file (e.g., pdf or text)
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');
        $response = $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'Article With Bad File',
            'status' => 'draft',
            'body' => '<p>Text</p>',
            'featured_image' => $invalidFile,
        ]);
        $response->assertSessionHasErrors('featured_image');

        // Valid image (1x1 PNG binary bytes, works without GD extension)
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $validImage = UploadedFile::fake()->createWithContent('cover.png', $pngBytes);
        $response = $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'Article With Good Image',
            'status' => 'published',
            'body' => '<p>Text with image</p>',
            'featured_image' => $validImage,
            'featured_image_alt' => 'Cover image for post',
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $blog = Blog::where('title', 'Article With Good Image')->first();
        $this->assertNotNull($blog->featured_image);
        Storage::disk('public')->assertExists($blog->featured_image);
    }

    public function test_admin_can_update_blog_and_replace_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $oldImage = UploadedFile::fake()->createWithContent('old.png', $pngBytes);
        $oldPath = $oldImage->storeAs('blogs', 'old_file.png', 'public');

        $blog = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Initial Title',
            'slug' => 'initial-title',
            'body' => '<p>Original body</p>',
            'status' => 'draft',
            'featured_image' => $oldPath,
        ]);

        $newImage = UploadedFile::fake()->createWithContent('new.png', $pngBytes);

        $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
            'title' => 'Updated Article Title',
            'slug' => 'updated-article-title',
            'status' => 'published',
            'body' => '<p>Updated content body</p>',
            'featured_image' => $newImage,
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $blog->refresh();
        $this->assertEquals('Updated Article Title', $blog->title);
        $this->assertEquals('published', $blog->status);
        Storage::disk('public')->assertMissing('blogs/old_file.png');
        Storage::disk('public')->assertExists($blog->featured_image);
    }

    public function test_admin_can_delete_blog_post_and_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $image = UploadedFile::fake()->createWithContent('post.png', $pngBytes);
        $path = $image->storeAs('blogs', 'unique_to_delete.png', 'public');

        $blog = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Post To Delete',
            'slug' => 'post-to-delete',
            'body' => '<p>To be deleted</p>',
            'status' => 'draft',
            'featured_image' => $path,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.blogs.destroy', $blog));
        $response->assertRedirect(route('admin.blogs.index'));

        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_admin_can_toggle_publication_status(): void
    {
        $admin = User::factory()->admin()->create();

        $blog = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Toggleable Post',
            'slug' => 'toggleable-post',
            'body' => '<p>Body</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Toggle from published -> draft
        $response = $this->actingAs($admin)->patch(route('admin.blogs.toggle-status', $blog));
        $response->assertSessionHas('success');
        $this->assertEquals('draft', $blog->fresh()->status);

        // Toggle from draft -> published
        $response = $this->actingAs($admin)->patch(route('admin.blogs.toggle-status', $blog));
        $response->assertSessionHas('success');
        $this->assertEquals('published', $blog->fresh()->status);
    }

    public function test_admin_can_manage_blog_categories(): void
    {
        $admin = User::factory()->admin()->create();

        // Create
        $response = $this->actingAs($admin)->post(route('admin.blog-categories.store'), [
            'name' => 'Interview Questions',
            'slug' => 'interview-questions',
            'description' => 'All HR and technical interview tips',
        ]);
        $response->assertRedirect(route('admin.blog-categories.index'));
        $this->assertDatabaseHas('blog_categories', ['slug' => 'interview-questions']);

        $category = BlogCategory::where('slug', 'interview-questions')->first();

        // Update
        $response = $this->actingAs($admin)->put(route('admin.blog-categories.update', $category), [
            'name' => 'HR & Tech Interviews',
            'slug' => 'hr-tech-interviews',
            'description' => 'Updated description',
        ]);
        $response->assertRedirect(route('admin.blog-categories.index'));
        $this->assertDatabaseHas('blog_categories', ['slug' => 'hr-tech-interviews']);

        // Delete with no posts succeeds
        $response = $this->actingAs($admin)->delete(route('admin.blog-categories.destroy', $category));
        $response->assertRedirect(route('admin.blog-categories.index'));
        $this->assertDatabaseMissing('blog_categories', ['id' => $category->id]);
    }

    public function test_category_with_posts_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create(['name' => 'Critical Category', 'slug' => 'critical-category']);

        Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'title' => 'Article In Critical Category',
            'slug' => 'article-critical',
            'body' => '<p>Body</p>',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.blog-categories.destroy', $category));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('blog_categories', ['id' => $category->id]);
    }

    public function test_admin_can_preview_article(): void
    {
        $admin = User::factory()->admin()->create();
        $blog = Blog::create([
            'author_id' => $admin->id,
            'title' => 'Upcoming Placement Guide',
            'slug' => 'upcoming-placement-guide',
            'body' => '<p>Confidential preparation guide</p>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.blogs.preview', $blog));
        $response->assertOk();
        $response->assertSee('Admin Preview Mode');
        $response->assertSee('Upcoming Placement Guide');
    }
}
