<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\Question;
use App\Models\StaticPage;
use App\Models\Topic;
use App\Models\User;
use App\Services\SeoService;
use App\Services\SitemapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicalSeoAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->regularUser = User::factory()->create();
        SitemapService::clearCache();
    }

    /**
     * Helper to assert exactly one canonical tag and return its href.
     */
    private function assertSingleCanonical(string $content, ?string $expected = null): string
    {
        preg_match_all('/<link\s+rel=["\']canonical["\']\s+href=["\'](.*?)["\']/is', $content, $matches);
        $this->assertCount(1, $matches[0], 'Expected exactly one <link rel="canonical"> tag in HTML head.');
        $canonical = $matches[1][0];

        $this->assertStringStartsWith('https://', $canonical, 'Canonical URL must use HTTPS.');
        $this->assertStringContainsString('aptimedha.onrender.com', $canonical, 'Canonical URL must use APP_URL host.');

        if ($expected !== null) {
            $this->assertEquals($expected, $canonical);
        }

        return $canonical;
    }

    /**
     * Helper to assert exactly one Open Graph tag by property.
     */
    private function assertOpenGraphTag(string $content, string $property, ?string $expected = null): string
    {
        preg_match_all('/<meta\s+property=["\']' . preg_quote($property, '/') . '["\']\s+content=["\'](.*?)["\']/is', $content, $matches);
        $this->assertCount(1, $matches[0], "Expected exactly one <meta property=\"{$property}\"> tag.");
        $val = html_entity_decode(trim($matches[1][0]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($expected !== null) {
            $this->assertEquals($expected, $val);
        }

        return $val;
    }

    /**
     * Helper to assert exactly one Twitter Card tag by name.
     */
    private function assertTwitterTag(string $content, string $name, ?string $expected = null): string
    {
        preg_match_all('/<meta\s+name=["\']' . preg_quote($name, '/') . '["\']\s+content=["\'](.*?)["\']/is', $content, $matches);
        $this->assertCount(1, $matches[0], "Expected exactly one <meta name=\"{$name}\"> tag.");
        $val = html_entity_decode(trim($matches[1][0]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($expected !== null) {
            $this->assertEquals($expected, $val);
        }

        return $val;
    }

    // -------------------------------------------------------------
    // 1. Canonical URLs on All Public Pages
    // -------------------------------------------------------------
    public function test_public_pages_have_canonical_urls_using_app_url(): void
    {
        $category = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
        ]);

        $mcq = Question::create([
            'category_id' => $category->id,
            'topic_id' => $topic->id,
            'question_number' => 1,
            'question_text' => 'What is 10% of 200?',
            'option_a' => '10',
            'option_b' => '20',
            'option_c' => '30',
            'option_d' => '40',
            'correct_option' => 'B',
            'is_active' => true,
        ]);

        $blog = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Sample Article',
            'slug' => 'sample-article',
            'body' => '<p>Article body</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $guide = PlacementGuide::create([
            'title' => 'Interview Guide',
            'slug' => 'interview-guide',
            'category' => 'interview',
            'content' => '<p>Guide body</p>',
            'is_published' => true,
        ]);

        $affair = CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'Nov 2026',
            'pdf_path' => 'pdfs/nov-2026.pdf',
            'is_published' => true,
        ]);

        $staticPage = StaticPage::updateOrCreate(
            ['slug' => 'about'],
            ['title' => 'About Us', 'content' => 'Content', 'is_active' => true]
        );

        $testCases = [
            ['url' => route('home'), 'expected' => SeoService::canonicalUrl('/')],
            ['url' => route('categories.index'), 'expected' => SeoService::canonicalUrl('/categories')],
            ['url' => route('categories.show', $category->slug), 'expected' => SeoService::canonicalUrl("/categories/{$category->slug}")],
            ['url' => route('categories.topics.show', ['category' => $category->slug, 'topic' => $topic->slug]), 'expected' => SeoService::canonicalUrl("/categories/{$category->slug}/{$topic->slug}")],
            ['url' => route('mcq.show', $mcq->id), 'expected' => SeoService::canonicalUrl("/mcq/{$mcq->id}")],
            ['url' => route('blog.index'), 'expected' => SeoService::canonicalUrl('/blog')],
            ['url' => route('blog.show', $blog->slug), 'expected' => SeoService::canonicalUrl("/blog/{$blog->slug}")],
            ['url' => route('placement.index'), 'expected' => SeoService::canonicalUrl('/placement-preparation')],
            ['url' => route('placement.show', $guide->slug), 'expected' => SeoService::canonicalUrl("/placement-preparation/{$guide->slug}")],
            ['url' => route('current-affairs.index'), 'expected' => SeoService::canonicalUrl('/current-affairs')],
            ['url' => route('current-affairs.show', ['year' => 2026, 'month' => 11]), 'expected' => SeoService::canonicalUrl('/current-affairs/2026/11')],
            ['url' => route('about'), 'expected' => SeoService::canonicalUrl('/about')],
        ];

        foreach ($testCases as $case) {
            $response = $this->get($case['url']);
            $response->assertOk();
            $this->assertSingleCanonical($response->getContent(), $case['expected']);
        }
    }

    public function test_legacy_mcq_query_route_points_canonical_to_clean_topic_route(): void
    {
        $category = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.index', [
            'category' => $category->slug,
            'topic' => $topic->slug,
            'utm_source' => 'google',
        ]));

        $response->assertOk();
        // Canonical points to clean /categories/arithmetic-aptitude/percentages, ignoring query params
        $this->assertSingleCanonical(
            $response->getContent(),
            SeoService::canonicalUrl("/categories/{$category->slug}/{$topic->slug}")
        );
    }

    public function test_canonical_urls_strip_unnecessary_query_parameters(): void
    {
        $category = Category::create([
            'name' => 'Reasoning',
            'slug' => 'reasoning',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.show', ['category' => $category->slug, 'page' => 2, 'sort' => 'asc']));
        $response->assertOk();

        // Canonical URL strips ?page=2&sort=asc
        $this->assertSingleCanonical($response->getContent(), SeoService::canonicalUrl("/categories/{$category->slug}"));
    }

    // -------------------------------------------------------------
    // 2. Indexability: Private vs Public Pages
    // -------------------------------------------------------------
    public function test_public_pages_do_not_have_noindex_and_are_indexable(): void
    {
        $publicUrls = [
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

        foreach ($publicUrls as $url) {
            $response = $this->get($url);
            $response->assertOk();

            // HTTP header must NOT contain noindex
            $this->assertFalse(
                str_contains((string) $response->headers->get('X-Robots-Tag'), 'noindex'),
                "Public page [{$url}] must not have X-Robots-Tag: noindex header."
            );

            // HTML content must NOT contain <meta name="robots" content="noindex
            $content = $response->getContent();
            $this->assertStringNotContainsString('<meta name="robots" content="noindex', $content);
        }
    }

    public function test_private_admin_user_and_auth_pages_enforce_noindex(): void
    {
        // 1. Auth pages (guest)
        $authRoutes = [
            route('login'),
            route('register'),
            route('password.request'),
        ];

        foreach ($authRoutes as $url) {
            $response = $this->get($url);
            $response->assertOk();

            // HTTP header must contain noindex, nofollow
            $this->assertStringContainsString('noindex', (string) $response->headers->get('X-Robots-Tag'));
            $this->assertStringContainsString('nofollow', (string) $response->headers->get('X-Robots-Tag'));

            // HTML head must contain <meta name="robots" content="noindex, nofollow">
            $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $response->getContent());
        }

        // 2. User Dashboard (authenticated)
        $userResponse = $this->actingAs($this->regularUser)->get(route('user.dashboard'));
        $userResponse->assertOk();
        $this->assertStringContainsString('noindex', (string) $userResponse->headers->get('X-Robots-Tag'));
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $userResponse->getContent());

        // 3. Admin Panel (authenticated admin)
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminResponse->assertOk();
        $this->assertStringContainsString('noindex', (string) $adminResponse->headers->get('X-Robots-Tag'));
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $adminResponse->getContent());
    }

    public function test_admin_previews_contain_noindex_tag(): void
    {
        $blog = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Draft Preview Post',
            'slug' => 'draft-preview-post',
            'body' => '<p>Draft</p>',
            'status' => 'draft',
        ]);

        $previewResponse = $this->actingAs($this->admin)->get(route('admin.blogs.preview', $blog));
        $previewResponse->assertOk();
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $previewResponse->getContent());
        $this->assertStringContainsString('noindex', (string) $previewResponse->headers->get('X-Robots-Tag'));
    }

    public function test_robots_txt_disallows_private_routes_and_provides_canonical_sitemap(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertOk();
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));

        $content = $response->getContent();
        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Disallow: /dashboard', $content);
        $this->assertStringContainsString('Disallow: /login', $content);
        $this->assertStringContainsString('Disallow: /register', $content);
        $this->assertStringContainsString('Disallow: /forgot-password', $content);
        $this->assertStringContainsString('Disallow: /reset-password', $content);
        $this->assertStringContainsString('Disallow: /auth/', $content);
        $this->assertStringContainsString('Disallow: /user/', $content);
        $this->assertStringContainsString('Sitemap: ' . SeoService::canonicalUrl('/sitemap.xml'), $content);
    }

    // -------------------------------------------------------------
    // 3. Open Graph and Twitter Card Metadata
    // -------------------------------------------------------------
    public function test_homepage_has_complete_open_graph_and_twitter_metadata(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $content = $response->getContent();

        $this->assertOpenGraphTag($content, 'og:site_name', 'AptiMedha');
        $this->assertOpenGraphTag($content, 'og:type', 'website');
        $this->assertOpenGraphTag($content, 'og:title', 'AptiMedha – Practice MCQs, Aptitude & Placement Preparation');
        $this->assertOpenGraphTag($content, 'og:url', SeoService::canonicalUrl('/'));
        $this->assertOpenGraphTag($content, 'og:image', SeoService::canonicalUrl('/images/aptimedha-logo.png'));

        $this->assertTwitterTag($content, 'twitter:card', 'summary_large_image');
        $this->assertTwitterTag($content, 'twitter:title', 'AptiMedha – Practice MCQs, Aptitude & Placement Preparation');
        $this->assertTwitterTag($content, 'twitter:image', SeoService::canonicalUrl('/images/aptimedha-logo.png'));
    }

    public function test_blog_article_has_article_og_type_and_featured_image(): void
    {
        $blog = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Campus Interview Strategies 2026',
            'slug' => 'campus-interview-strategies-2026',
            'featured_image' => 'https://res.cloudinary.com/demo/image/upload/sample.jpg',
            'excerpt' => 'Actionable placement interview blueprint.',
            'body' => '<p>Full guide</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('blog.show', $blog->slug));
        $response->assertOk();
        $content = $response->getContent();

        $this->assertOpenGraphTag($content, 'og:type', 'article');
        $this->assertOpenGraphTag($content, 'og:title', 'Campus Interview Strategies 2026 | AptiMedha');
        $this->assertOpenGraphTag($content, 'og:image', 'https://res.cloudinary.com/demo/image/upload/sample.jpg');
        $this->assertTwitterTag($content, 'twitter:image', 'https://res.cloudinary.com/demo/image/upload/sample.jpg');
    }

    public function test_placement_guide_has_article_og_type_and_featured_image(): void
    {
        $guide = PlacementGuide::create([
            'title' => 'Resume Formatting Blueprint',
            'slug' => 'resume-formatting-blueprint',
            'category' => 'resume',
            'featured_image' => 'https://res.cloudinary.com/demo/image/upload/resume.jpg',
            'excerpt' => 'ATS resume guide.',
            'content' => '<p>Resume content</p>',
            'is_published' => true,
        ]);

        $response = $this->get(route('placement.show', $guide->slug));
        $response->assertOk();
        $content = $response->getContent();

        $this->assertOpenGraphTag($content, 'og:type', 'article');
        $this->assertOpenGraphTag($content, 'og:title', 'Resume Formatting Blueprint | AptiMedha');
        $this->assertOpenGraphTag($content, 'og:image', 'https://res.cloudinary.com/demo/image/upload/resume.jpg');
        $this->assertTwitterTag($content, 'twitter:image', 'https://res.cloudinary.com/demo/image/upload/resume.jpg');
    }

    // -------------------------------------------------------------
    // 4. Internal Linking & Clean Canonical URLs
    // -------------------------------------------------------------
    public function test_category_show_internal_links_point_to_canonical_topic_urls(): void
    {
        $category = Category::create([
            'name' => 'Data Interpretation',
            'slug' => 'data-interpretation',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Bar Graphs',
            'slug' => 'bar-graphs',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.show', $category->slug));
        $response->assertOk();

        // Must contain clean link /categories/data-interpretation/bar-graphs
        $expectedCanonicalTopicRoute = route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]);

        $response->assertSee($expectedCanonicalTopicRoute);
        // Must NOT link to /mcq?category=...&topic=...
        $this->assertStringNotContainsString(
            route('mcq.index', ['category' => $category->slug, 'topic' => $topic->slug]),
            $response->getContent()
        );
    }

    public function test_mcq_show_breadcrumbs_link_to_canonical_topic_url(): void
    {
        $category = Category::create([
            'name' => 'Quantitative',
            'slug' => 'quantitative',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Ratio and Proportion',
            'slug' => 'ratio-and-proportion',
            'is_active' => true,
        ]);

        $mcq = Question::create([
            'category_id' => $category->id,
            'topic_id' => $topic->id,
            'question_number' => 5,
            'question_text' => 'If A:B = 2:3 and B:C = 4:5, what is A:C?',
            'option_a' => '8:15',
            'option_b' => '2:5',
            'option_c' => '3:5',
            'option_d' => '1:2',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.show', $mcq->id));
        $response->assertOk();

        $expectedTopicCanonicalUrl = route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]);

        $response->assertSee($expectedTopicCanonicalUrl);
    }

    // -------------------------------------------------------------
    // 5. Assets and Core Web Vitals
    // -------------------------------------------------------------
    public function test_application_logo_specifies_explicit_dimensions_and_eager_loading(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $content = $response->getContent();

        // Check for width="1024" height="341" to avoid Cumulative Layout Shift (CLS)
        $this->assertMatchesRegularExpression('/<img[^>]+src="[^"]+aptimedha-logo\.png"[^>]+width="1024"[^>]+height="341"/i', $content);
        $this->assertMatchesRegularExpression('/<img[^>]+src="[^"]+aptimedha-logo\.png"[^>]+loading="eager"/i', $content);
    }

    public function test_third_party_scripts_use_defer_attribute(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();
        $content = $response->getContent();

        // Lucide script must be deferred
        $this->assertMatchesRegularExpression('/<script\s+defer\s+src="https:\/\/cdn\.jsdelivr\.net\/npm\/lucide@latest\/dist\/umd\/lucide\.min\.js"><\/script>/i', $content);
        // Alpine script must be deferred
        $this->assertMatchesRegularExpression('/<script\s+defer\s+src="https:\/\/cdn\.jsdelivr\.net\/npm\/alpinejs@3\.x\.x\/dist\/cdn\.min\.js"><\/script>/i', $content);
    }
}
