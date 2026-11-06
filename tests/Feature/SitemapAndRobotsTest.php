<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\StaticPage;
use App\Models\Topic;
use App\Models\User;
use App\Services\SitemapService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;
use Tests\TestCase;

class SitemapAndRobotsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        SitemapService::clearCache();
    }

    /**
     * Helper to parse and assert XML validity.
     */
    private function parseSitemapXml(string $content): SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        $this->assertNotFalse($xml, 'Failed to parse XML sitemap: ' . json_encode($errors));
        $this->assertEquals('urlset', $xml->getName());

        return $xml;
    }

    /**
     * Helper to extract all <loc> values from sitemap XML.
     *
     * @return list<string>
     */
    private function extractSitemapUrls(SimpleXMLElement $xml): array
    {
        $urls = [];
        foreach ($xml->url as $urlElement) {
            $urls[] = (string) $urlElement->loc;
        }

        return $urls;
    }

    // -------------------------------------------------------------
    // 1 & 2. /sitemap.xml returns HTTP 200, correct headers & valid XML
    // -------------------------------------------------------------
    public function test_sitemap_returns_http_200_with_correct_content_type(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertEquals('noindex', $response->headers->get('X-Robots-Tag'));

        $xml = $this->parseSitemapXml($response->getContent());
        $namespaces = $xml->getNamespaces(true);
        $this->assertArrayHasKey('', $namespaces);
        $this->assertEquals('http://www.sitemaps.org/schemas/sitemap/0.9', $namespaces['']);
    }

    // -------------------------------------------------------------
    // 3. Homepage is included
    // -------------------------------------------------------------
    public function test_homepage_is_included_in_sitemap(): void
    {
        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        $expectedHome = SitemapService::canonicalUrl('/');
        $this->assertContains($expectedHome, $urls);

        // Verify homepage url element has expected priority
        foreach ($xml->url as $url) {
            if ((string) $url->loc === $expectedHome) {
                $this->assertEquals('1.0', (string) $url->priority);
                $this->assertEquals('daily', (string) $url->changefreq);
                break;
            }
        }
    }

    // -------------------------------------------------------------
    // 4. Public category and topic URLs are included
    // -------------------------------------------------------------
    public function test_public_category_and_topic_urls_are_included(): void
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

        $inactiveCategory = Category::create([
            'name' => 'Draft Inactive Category',
            'slug' => 'draft-inactive-category',
            'is_active' => false,
        ]);

        $inactiveTopic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Hidden Inactive Topic',
            'slug' => 'hidden-inactive-topic',
            'is_active' => false,
        ]);

        SitemapService::clearCache();

        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        // Active category and topic must be included
        $this->assertContains(SitemapService::canonicalUrl('/categories'), $urls);
        $this->assertContains(SitemapService::canonicalUrl('/categories/arithmetic-aptitude'), $urls);
        $this->assertContains(SitemapService::canonicalUrl('/categories/arithmetic-aptitude/percentages'), $urls);

        // Inactive records must be excluded
        $this->assertNotContains(SitemapService::canonicalUrl('/categories/draft-inactive-category'), $urls);
        $this->assertNotContains(SitemapService::canonicalUrl('/categories/arithmetic-aptitude/hidden-inactive-topic'), $urls);

        // Individual question URLs (/mcq/{id}) must NOT be in sitemap per prompt instructions
        foreach ($urls as $url) {
            $this->assertDoesNotMatchRegularExpression('/\/mcq\/\d+$/', $url, 'Individual MCQ ID should not be present in sitemap.');
        }
    }

    // -------------------------------------------------------------
    // 5 & 6. Published blog articles included, drafts excluded
    // -------------------------------------------------------------
    public function test_published_blog_articles_included_and_drafts_excluded(): void
    {
        $published = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Speed Math Multiplication Shortcuts',
            'slug' => 'speed-math-multiplication-shortcuts',
            'body' => '<p>Article body</p>',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        $draft = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Secret Unpublished Strategy',
            'slug' => 'secret-unpublished-strategy',
            'body' => '<p>Secret</p>',
            'status' => 'draft',
        ]);

        $archived = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Outdated Syllabus Notes',
            'slug' => 'outdated-syllabus-notes',
            'body' => '<p>Outdated</p>',
            'status' => 'archived',
        ]);

        SitemapService::clearCache();

        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        // Blog index and published article must be included
        $this->assertContains(SitemapService::canonicalUrl('/blog'), $urls);
        $this->assertContains(SitemapService::canonicalUrl('/blog/speed-math-multiplication-shortcuts'), $urls);

        // Draft and archived articles must be excluded
        $this->assertNotContains(SitemapService::canonicalUrl('/blog/secret-unpublished-strategy'), $urls);
        $this->assertNotContains(SitemapService::canonicalUrl('/blog/outdated-syllabus-notes'), $urls);
    }

    // -------------------------------------------------------------
    // 7 & 8. Published placement guides included, unpublished excluded
    // -------------------------------------------------------------
    public function test_published_placement_guides_included_and_unpublished_excluded(): void
    {
        $publishedGuide = PlacementGuide::create([
            'title' => 'Complete HR Interview Guide',
            'slug' => 'complete-hr-interview-guide',
            'category' => 'interview',
            'content' => '<p>HR interview guide</p>',
            'is_published' => true,
            'published_at' => now()->subDay(),
        ]);

        $unpublishedGuide = PlacementGuide::create([
            'title' => 'Confidential Interview Internal Notes',
            'slug' => 'confidential-interview-internal-notes',
            'category' => 'interview',
            'content' => '<p>Confidential</p>',
            'is_published' => false,
        ]);

        SitemapService::clearCache();

        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        $this->assertContains(SitemapService::canonicalUrl('/placement-preparation'), $urls);
        $this->assertContains(SitemapService::canonicalUrl('/placement-preparation/complete-hr-interview-guide'), $urls);
        $this->assertNotContains(SitemapService::canonicalUrl('/placement-preparation/confidential-interview-internal-notes'), $urls);
    }

    // -------------------------------------------------------------
    // 9 & 10. Published Current Affairs included, unpublished excluded
    // -------------------------------------------------------------
    public function test_published_current_affairs_included_and_unpublished_excluded(): void
    {
        $publishedIssue = CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'November 2026 Issue',
            'description' => 'November digest.',
            'pdf_path' => 'pdfs/nov.pdf',
            'is_published' => true,
        ]);

        $unpublishedIssue = CurrentAffair::create([
            'year' => 2026,
            'month' => 12,
            'title' => 'December 2026 Internal Draft',
            'description' => 'Draft',
            'pdf_path' => 'pdfs/dec.pdf',
            'is_published' => false,
        ]);

        SitemapService::clearCache();

        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        $this->assertContains(SitemapService::canonicalUrl('/current-affairs'), $urls);
        $this->assertContains(SitemapService::canonicalUrl('/current-affairs/2026/11'), $urls);
        $this->assertNotContains(SitemapService::canonicalUrl('/current-affairs/2026/12'), $urls);
    }

    // -------------------------------------------------------------
    // 11. Private, dashboard and authentication routes are excluded
    // -------------------------------------------------------------
    public function test_private_admin_and_auth_routes_are_excluded(): void
    {
        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        $forbiddenPrefixes = [
            '/admin',
            '/dashboard',
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password',
            '/auth',
            '/user',
            '/storage',
        ];

        foreach ($urls as $url) {
            $path = parse_url($url, PHP_URL_PATH);
            foreach ($forbiddenPrefixes as $forbidden) {
                $this->assertFalse(
                    str_starts_with($path, $forbidden),
                    "Sitemap should not contain private or auth URL: {$url}"
                );
            }
        }
    }

    // -------------------------------------------------------------
    // 12. Duplicate URLs are not generated
    // -------------------------------------------------------------
    public function test_no_duplicate_urls_in_sitemap(): void
    {
        // Seed duplicate-prone dummy structures
        Category::create(['name' => 'Programming', 'slug' => 'programming', 'is_active' => true]);
        StaticPage::updateOrCreate(['slug' => 'about'], ['title' => 'About Us', 'content' => 'content', 'is_active' => true]);

        SitemapService::clearCache();

        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        $uniqueUrls = array_unique($urls);
        $this->assertCount(count($uniqueUrls), $urls, 'Duplicate URLs detected in sitemap XML.');
    }

    // -------------------------------------------------------------
    // 13. Canonical HTTPS URL formatting is correct
    // -------------------------------------------------------------
    public function test_canonical_https_url_formatting(): void
    {
        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        foreach ($urls as $url) {
            $this->assertStringStartsWith('https://', $url, "URL [{$url}] must use canonical HTTPS scheme.");
            $this->assertStringContainsString('aptimedha.onrender.com', $url, "URL [{$url}] must use canonical production host.");
            $this->assertFalse(str_contains($url, '?'), "URL [{$url}] must not contain query parameters.");
        }
    }

    // -------------------------------------------------------------
    // 14. lastmod uses actual modification dates
    // -------------------------------------------------------------
    public function test_lastmod_uses_actual_modification_dates(): void
    {
        $customDate = Carbon::create(2026, 8, 15, 10, 30, 0, 'UTC');

        $blog = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Timestamp Test Article',
            'slug' => 'timestamp-test-article',
            'body' => '<p>Content</p>',
            'status' => 'published',
            'published_at' => $customDate,
        ]);

        Blog::withoutTimestamps(function () use ($blog, $customDate) {
            $blog->timestamps = false;
            $blog->updated_at = $customDate;
            $blog->saveQuietly();
        });

        SitemapService::clearCache();

        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());

        $found = false;
        $expectedUrl = SitemapService::canonicalUrl('/blog/timestamp-test-article');

        foreach ($xml->url as $url) {
            if ((string) $url->loc === $expectedUrl) {
                $found = true;
                $this->assertNotEmpty((string) $url->lastmod);
                $this->assertStringStartsWith('2026-08-15', (string) $url->lastmod);
                break;
            }
        }

        $this->assertTrue($found, "Article URL [{$expectedUrl}] not found in sitemap.");
    }

    // -------------------------------------------------------------
    // 15. /robots.txt contains correct sitemap reference & crawl rules
    // -------------------------------------------------------------
    public function test_robots_txt_contains_correct_sitemap_and_crawl_rules(): void
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
        $this->assertStringContainsString('Sitemap: https://aptimedha.onrender.com/sitemap.xml', $content);
    }

    // -------------------------------------------------------------
    // 16. Caching and Automatic Invalidation
    // -------------------------------------------------------------
    public function test_sitemap_is_cached_and_invalidated_when_content_changes(): void
    {
        // 1. First request caches output
        $this->assertFalse(Cache::has(SitemapService::CACHE_KEY));
        $this->get('/sitemap.xml')->assertOk();
        $this->assertTrue(Cache::has(SitemapService::CACHE_KEY));

        // 2. Creating a new published blog automatically clears the cache
        Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Fresh Cached Post',
            'slug' => 'fresh-cached-post',
            'body' => '<p>Fresh</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->assertFalse(Cache::has(SitemapService::CACHE_KEY), 'Cache should be invalidated after model creation.');

        // 3. Requesting again repopulates cache with the new URL
        $response = $this->get('/sitemap.xml');
        $xml = $this->parseSitemapXml($response->getContent());
        $urls = $this->extractSitemapUrls($xml);

        $this->assertContains(SitemapService::canonicalUrl('/blog/fresh-cached-post'), $urls);
        $this->assertTrue(Cache::has(SitemapService::CACHE_KEY));
    }
}
