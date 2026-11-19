<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTableStylingTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_detail_page_wraps_tables_in_responsive_scrollable_container(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create([
            'name' => 'Placement Strategy',
            'slug' => 'placement-strategy',
        ]);

        $tableHtml = '<table><thead><tr><th>Topic</th><th>Weightage</th></tr></thead><tbody><tr><td>Arithmetic</td><td>35%</td></tr></tbody></table>';

        $blog = Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'author_name' => 'AptiMedha Editorial Team',
            'title' => 'Complete Aptitude Weightage Guide',
            'slug' => 'complete-aptitude-weightage-guide',
            'excerpt' => 'A complete breakdown of sectional weightage.',
            'body' => '<p>Here is the syllabus breakdown:</p>' . $tableHtml . '<p>Plan your studies accordingly.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();

        // Ensure the blog article content wrapper class is present
        $response->assertSee('blog-article-content', false);

        // Ensure the table is wrapped inside the responsive horizontally scrollable container
        $response->assertSee('<div class="blog-table-container overflow-x-auto"><table>', false);
        $response->assertSee('<th>Topic</th>', false);
        $response->assertSee('<th>Weightage</th>', false);
        $response->assertSee('<td>Arithmetic</td>', false);
        $response->assertSee('<td>35%</td>', false);
    }

    public function test_blog_detail_page_without_tables_renders_cleanly_without_extra_containers(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create([
            'name' => 'Interview Prep',
            'slug' => 'interview-prep',
        ]);

        $blog = Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'author_name' => 'AptiMedha Editorial Team',
            'title' => 'Top HR Questions',
            'slug' => 'top-hr-questions',
            'excerpt' => 'HR questions and answers.',
            'body' => '<p>Be authentic and practice the STAR method.</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();
        $response->assertSee('blog-article-content', false);
        $response->assertDontSee('<div class="blog-table-container', false);
    }

    public function test_tables_without_headers_render_correctly_inside_container(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create([
            'name' => 'Aptitude Insights',
            'slug' => 'aptitude-insights',
        ]);

        $tableWithoutHeaders = '<table><tbody><tr><td>Formula 1</td><td>Speed = Distance / Time</td></tr><tr><td>Formula 2</td><td>Time = Distance / Speed</td></tr></tbody></table>';

        $blog = Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'author_name' => 'AptiMedha Editorial Team',
            'title' => 'Speed Math Cheat Sheet',
            'slug' => 'speed-math-cheat-sheet',
            'body' => '<p>Essential formulas:</p>' . $tableWithoutHeaders,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();
        $response->assertSee('<div class="blog-table-container overflow-x-auto"><table>', false);
        $response->assertSee('Speed = Distance / Time', false);
    }

    public function test_pre_wrapped_tables_are_not_double_wrapped(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create([
            'name' => 'Exam Patterns',
            'slug' => 'exam-patterns',
        ]);

        $preWrappedTable = '<div class="blog-table-container overflow-x-auto"><table><tr><td>Cell 1</td></tr></table></div>';

        $blog = Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'author_name' => 'AptiMedha Editorial Team',
            'title' => 'Pre wrapped Table Test',
            'slug' => 'pre-wrapped-table-test',
            'body' => '<p>Overview</p>' . $preWrappedTable,
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();

        // Should only appear once as an HTML div, not nested as blog-table-container inside blog-table-container
        $content = $response->getContent();
        $count = substr_count($content, '<div class="blog-table-container');
        $this->assertEquals(1, $count);
    }

    public function test_admin_blog_preview_also_wraps_tables_responsively(): void
    {
        $admin = User::factory()->admin()->create();
        $category = BlogCategory::create([
            'name' => 'Placement Strategy',
            'slug' => 'placement-strategy',
        ]);

        $blog = Blog::create([
            'blog_category_id' => $category->id,
            'author_id' => $admin->id,
            'title' => 'Draft Article with Table',
            'slug' => 'draft-article-with-table',
            'body' => '<p>Draft preview:</p><table><tr><td>Draft Cell</td></tr></table>',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.blogs.preview', $blog));
        $response->assertOk();
        $response->assertSee('blog-article-content', false);
        $response->assertSee('<div class="blog-table-container overflow-x-auto"><table>', false);
        $response->assertSee('Draft Cell', false);
    }

    public function test_css_contains_blog_table_styling_tokens(): void
    {
        $cssSource = file_get_contents(resource_path('css/app.css'));

        // Check required border color and tokens
        $this->assertStringContainsString('#DCE3EC', $cssSource);
        $this->assertStringContainsString('#153B70', $cssSource);
        $this->assertStringContainsString('#F7F9FC', $cssSource);
        $this->assertStringContainsString('#243247', $cssSource);

        // Check scoped styling classes
        $this->assertStringContainsString('.blog-article-content .blog-table-container', $cssSource);
        $this->assertStringContainsString('.blog-article-content table', $cssSource);
        $this->assertStringContainsString('overflow-x: auto', $cssSource);
        $this->assertStringContainsString('border-bottom: 1px solid #DCE3EC', $cssSource);
        $this->assertStringContainsString('border-right: 1px solid #DCE3EC', $cssSource);
    }
}
