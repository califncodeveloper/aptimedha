<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\StaticPage;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchemaStructuredDataTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Helper to extract and decode JSON-LD script blocks from HTML response.
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractJsonLdBlocks(string $html): array
    {
        preg_match_all('/<script\s+type=["\']application\/ld\+json["\']\s*>(.*?)<\/script>/is', $html, $matches);
        $this->assertNotEmpty($matches[1], 'Expected at least one <script type="application/ld+json"> tag in response.');

        $blocks = [];
        foreach ($matches[1] as $jsonString) {
            $decoded = json_decode(trim($jsonString), true);
            $this->assertNotNull($decoded, 'Invalid JSON-LD syntax: ' . json_last_error_msg());
            $blocks[] = $decoded;
        }

        return $blocks;
    }

    /**
     * Helper to flatten @graph items if present or return root.
     *
     * @param  array<string, mixed>  $block
     * @return array<int, array<string, mixed>>
     */
    private function flattenGraph(array $block): array
    {
        if (isset($block['@graph']) && is_array($block['@graph'])) {
            return $block['@graph'];
        }

        return [$block];
    }

    // -------------------------------------------------------------
    // 1. Homepage Structured Data (Organization, WebSite, WebPage)
    // -------------------------------------------------------------
    public function test_homepage_renders_organization_website_and_webpage_schemas(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $this->assertCount(1, $blocks, 'Homepage should output exactly one consolidated JSON-LD block.');

        $graph = $this->flattenGraph($blocks[0]);
        $types = array_column($graph, '@type');

        $this->assertContains('Organization', $types);
        $this->assertContains('WebSite', $types);
        $this->assertContains('WebPage', $types);

        // Verify Organization details
        $org = collect($graph)->firstWhere('@type', 'Organization');
        $this->assertEquals('AptiMedha', $org['name']);
        $this->assertEquals(url('/'), $org['url']);
        $this->assertNotEmpty($org['logo']['url']);

        // Verify WebSite SearchAction
        $website = collect($graph)->firstWhere('@type', 'WebSite');
        $this->assertEquals('AptiMedha', $website['name']);
        $this->assertEquals('SearchAction', $website['potentialAction']['@type']);
        $this->assertStringContainsString('required name=search_term_string', $website['potentialAction']['query-input']);
    }

    // -------------------------------------------------------------
    // 2. Category and Topic Pages (CollectionPage, BreadcrumbList)
    // -------------------------------------------------------------
    public function test_categories_index_renders_collection_and_breadcrumbs(): void
    {
        $response = $this->get(route('categories.index'));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);
        $types = array_column($graph, '@type');

        $this->assertContains('CollectionPage', $types);
        $this->assertContains('BreadcrumbList', $types);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $items = $breadcrumbs['itemListElement'];
        $this->assertCount(2, $items);
        $this->assertEquals('Home', $items[0]['name']);
        $this->assertEquals('Categories', $items[1]['name']);
    }

    public function test_category_show_renders_collection_and_breadcrumbs(): void
    {
        $category = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'description' => 'Aptitude track description.',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.show', $category->slug));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);

        $collection = collect($graph)->firstWhere('@type', 'CollectionPage');
        $this->assertStringContainsString('Arithmetic Aptitude', $collection['name']);
        $this->assertEquals(route('categories.show', $category->slug), $collection['url']);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $items = $breadcrumbs['itemListElement'];
        $this->assertCount(3, $items);
        $this->assertEquals('Home', $items[0]['name']);
        $this->assertEquals('Categories', $items[1]['name']);
        $this->assertEquals('Arithmetic Aptitude', $items[2]['name']);
    }

    public function test_topic_practice_page_renders_collection_and_breadcrumbs(): void
    {
        $category = Category::create([
            'name' => 'Logical Reasoning',
            'slug' => 'logical-reasoning',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Blood Relations',
            'slug' => 'blood-relations',
            'description' => 'Family tree and relation puzzles.',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);

        $collection = collect($graph)->firstWhere('@type', 'CollectionPage');
        $this->assertStringContainsString('Blood Relations', $collection['name']);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $items = $breadcrumbs['itemListElement'];
        $this->assertCount(4, $items);
        $this->assertEquals('Home', $items[0]['name']);
        $this->assertEquals('Categories', $items[1]['name']);
        $this->assertEquals('Logical Reasoning', $items[2]['name']);
        $this->assertEquals('Blood Relations', $items[3]['name']);
    }

    // -------------------------------------------------------------
    // 3. Blog Detail Pages (BlogPosting / Article & Breadcrumbs)
    // -------------------------------------------------------------
    public function test_blog_detail_renders_blog_posting_schema_and_breadcrumbs(): void
    {
        $post = Blog::create([
            'author_id' => $this->admin->id,
            'author_name' => 'Senior Technical Editor',
            'title' => 'Mastering Campus Placements in 2026',
            'slug' => 'mastering-campus-placements-2026',
            'meta_title' => 'Campus Placements 2026 | Preparation Strategy',
            'meta_description' => 'Complete roadmap and tips for university placement drives.',
            'excerpt' => 'Short teaser',
            'body' => '<p>Detailed article content with advice.</p>',
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        $response = $this->get(route('blog.show', $post->slug));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);

        $article = collect($graph)->firstWhere('@type', 'BlogPosting');
        $this->assertNotNull($article);
        $this->assertEquals('Mastering Campus Placements in 2026', $article['headline']);
        $this->assertStringContainsString('roadmap', $article['description']);
        $this->assertEquals('Senior Technical Editor', $article['author']['name']);
        $this->assertEquals('Person', $article['author']['@type']);
        $this->assertEquals('Organization', $article['publisher']['@type']);
        $this->assertEquals('AptiMedha', $article['publisher']['name']);
        $this->assertNotEmpty($article['datePublished']);
        $this->assertNotEmpty($article['dateModified']);
        $this->assertEquals(route('blog.show', $post->slug), $article['url']);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $this->assertCount(3, $breadcrumbs['itemListElement']);
        $this->assertEquals('Mastering Campus Placements in 2026', $breadcrumbs['itemListElement'][2]['name']);
    }

    // -------------------------------------------------------------
    // 4. Placement Preparation Pages (Article & Breadcrumbs)
    // -------------------------------------------------------------
    public function test_placement_guide_renders_article_schema_and_breadcrumbs(): void
    {
        $guide = PlacementGuide::create([
            'title' => 'Ultimate HR Interview Questions Guide',
            'slug' => 'ultimate-hr-interview-questions-guide',
            'category' => 'interview',
            'excerpt' => 'Top 50 HR behavioral interview questions with sample frameworks.',
            'content' => '<p>Tell me about yourself, strengths, weaknesses.</p>',
            'meta_title' => 'HR Interview Questions Guide | AptiMedha',
            'meta_description' => 'Top 50 HR behavioral questions answered.',
            'is_published' => true,
            'published_at' => now()->subWeek(),
        ]);

        $response = $this->get(route('placement.show', $guide->slug));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);

        $article = collect($graph)->firstWhere('@type', 'Article');
        $this->assertNotNull($article);
        $this->assertEquals('Ultimate HR Interview Questions Guide', $article['headline']);
        $this->assertEquals('Interviews & GD', $article['articleSection']);
        $this->assertNotEmpty($article['datePublished']);
        $this->assertEquals(route('placement.show', $guide->slug), $article['url']);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $items = $breadcrumbs['itemListElement'];
        $this->assertCount(3, $items);
        $this->assertEquals('Placement Preparation', $items[1]['name']);
        $this->assertEquals('Ultimate HR Interview Questions Guide', $items[2]['name']);
    }

    // -------------------------------------------------------------
    // 5. Current Affairs Pages (Article & PDF Metadata)
    // -------------------------------------------------------------
    public function test_current_affairs_monthly_issue_renders_article_schema(): void
    {
        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'November 2026 Monthly Capsule',
            'description' => 'Important appointments, national summits and sports awards.',
            'pdf_path' => 'current-affairs/pdfs/nov-2026.pdf',
            'pdf_name' => 'nov-2026.pdf',
            'file_size_bytes' => 2048576,
            'is_published' => true,
            'published_at' => now()->subDays(5),
        ]);

        $response = $this->get(route('current-affairs.show', ['year' => 2026, 'month' => 11]));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);

        $article = collect($graph)->firstWhere('@type', 'Article');
        $this->assertNotNull($article);
        $this->assertEquals('November 2026 Monthly Capsule', $article['headline']);
        $this->assertStringContainsString('appointments', $article['description']);
        $this->assertEquals('nov-2026.pdf', $article['encoding']['name']);
        $this->assertEquals('application/pdf', $article['encoding']['encodingFormat']);
        $this->assertEquals('2048576', $article['encoding']['contentSize']);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $items = $breadcrumbs['itemListElement'];
        $this->assertCount(3, $items);
        $this->assertEquals('Current Affairs', $items[1]['name']);
        $this->assertEquals('November 2026 Monthly Capsule', $items[2]['name']);
    }

    // -------------------------------------------------------------
    // 6. Static Pages (WebPage & Breadcrumbs)
    // -------------------------------------------------------------
    public function test_static_pages_render_webpage_schema_and_breadcrumbs(): void
    {
        StaticPage::updateOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About AptiMedha',
                'meta_title' => 'About Us - AptiMedha',
                'meta_description' => 'AptiMedha platform details and mission.',
                'content' => '<p>About page content</p>',
                'is_active' => true,
            ]
        );

        $response = $this->get(route('about'));
        $response->assertOk();

        $blocks = $this->extractJsonLdBlocks($response->getContent());
        $graph = $this->flattenGraph($blocks[0]);

        $webpage = collect($graph)->firstWhere('@type', 'WebPage');
        $this->assertNotNull($webpage);
        $this->assertEquals('About Us - AptiMedha', $webpage['name']);
        $this->assertStringContainsString('AptiMedha', $webpage['description']);
        $this->assertEquals(route('about'), $webpage['url']);

        $breadcrumbs = collect($graph)->firstWhere('@type', 'BreadcrumbList');
        $items = $breadcrumbs['itemListElement'];
        $this->assertCount(2, $items);
        $this->assertEquals('Home', $items[0]['name']);
        $this->assertEquals('About Us', $items[1]['name']);
    }

    // -------------------------------------------------------------
    // 7. Security: Unpublished Content Not Accessible for Schema
    // -------------------------------------------------------------
    public function test_draft_content_is_inaccessible_to_public(): void
    {
        $draft = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Secret Draft Post',
            'slug' => 'secret-draft-post',
            'body' => '<p>Draft</p>',
            'status' => 'draft',
        ]);

        $this->get(route('blog.show', $draft->slug))->assertNotFound();

        $unpublishedGuide = PlacementGuide::create([
            'title' => 'Unpublished Guide',
            'slug' => 'unpublished-guide',
            'category' => 'resume',
            'content' => '<p>Unpublished</p>',
            'is_published' => false,
        ]);

        $this->get(route('placement.show', $unpublishedGuide->slug))->assertNotFound();
    }

    // -------------------------------------------------------------
    // 8. No Duplicate Schema Blocks or Unescaped Scripts
    // -------------------------------------------------------------
    public function test_no_duplicate_ld_json_blocks_on_pages(): void
    {
        $urls = [
            route('home'),
            route('categories.index'),
            route('blog.index'),
            route('placement.index'),
            route('current-affairs.index'),
            route('about'),
            route('contact'),
            route('terms'),
            route('privacy'),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertOk();

            preg_match_all('/<script\s+type=["\']application\/ld\+json["\']\s*>/i', $response->getContent(), $matches);
            $this->assertCount(1, $matches[0], "Page [{$url}] should contain exactly one JSON-LD script block.");
        }
    }
}
