<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\Question;
use App\Models\StaticPage;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetadataAndUrlsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Helper to assert exactly one title tag and extract its content.
     */
    private function assertSingleTitle(string $content, ?string $expected = null): string
    {
        preg_match_all('/<title>(.*?)<\/title>/is', $content, $matches);
        $this->assertCount(1, $matches[0], 'Expected exactly one <title> tag in response.');
        $title = html_entity_decode(trim($matches[1][0]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($expected !== null) {
            $this->assertEquals($expected, $title);
        }
        return $title;
    }

    /**
     * Helper to assert exactly one meta description tag and extract its content.
     */
    private function assertSingleMetaDescription(string $content, ?string $expected = null): string
    {
        preg_match_all('/<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']/is', $content, $matches);
        $this->assertCount(1, $matches[0], 'Expected exactly one <meta name="description"> tag in response.');
        $description = html_entity_decode(trim($matches[1][0]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if ($expected !== null) {
            $this->assertEquals($expected, $description);
        }
        return $description;
    }

    // -------------------------------------------------------------
    // 1. Homepage Title and Description
    // -------------------------------------------------------------
    public function test_homepage_has_expected_title_and_meta_description(): void
    {
        $response = $this->get(route('home'));
        $response->assertOk();

        $content = $response->getContent();
        $this->assertSingleTitle($content, 'AptiMedha – Practice MCQs, Aptitude & Placement Preparation');
        $desc = $this->assertSingleMetaDescription($content);
        $this->assertStringContainsString('aptitude MCQs', $desc);
        $this->assertLessThanOrEqual(165, strlen($desc));
    }

    // -------------------------------------------------------------
    // 2. Category and Topic Page Metadata
    // -------------------------------------------------------------
    public function test_category_listing_and_show_metadata(): void
    {
        $category = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'description' => 'Comprehensive quantitative aptitude questions and speed calculation tricks for competitive assessments.',
            'is_active' => true,
        ]);

        // Categories index
        $indexResponse = $this->get(route('categories.index'));
        $indexResponse->assertOk();
        $this->assertSingleTitle($indexResponse->getContent(), 'All Categories & Subject Tracks | AptiMedha');
        $this->assertSingleMetaDescription($indexResponse->getContent());

        // Category show
        $showResponse = $this->get(route('categories.show', $category->slug));
        $showResponse->assertOk();
        $this->assertSingleTitle($showResponse->getContent(), 'Arithmetic Aptitude MCQs – Practice Questions | AptiMedha');
        $catDesc = $this->assertSingleMetaDescription($showResponse->getContent());
        $this->assertStringContainsString('Arithmetic Aptitude', $catDesc);
        $this->assertLessThanOrEqual(165, strlen($catDesc));
    }

    public function test_topic_mcq_practice_clean_url_and_metadata(): void
    {
        $category = Category::create([
            'name' => 'Logical Reasoning',
            'slug' => 'logical-reasoning',
            'description' => 'Logical reasoning puzzles.',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Number Series',
            'slug' => 'number-series',
            'description' => 'Practice finding patterns and missing terms in arithmetic and geometric sequences.',
            'is_active' => true,
        ]);

        // Clean SEO URL: /categories/{category}/{topic}
        $response = $this->get(route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));

        $response->assertOk();
        $this->assertSingleTitle($response->getContent(), 'Number Series MCQs – Practice Questions | AptiMedha');
        $topicDesc = $this->assertSingleMetaDescription($response->getContent());
        $this->assertStringContainsString('Number Series', $topicDesc);
        $this->assertLessThanOrEqual(165, strlen($topicDesc));

        // Legacy /mcq?category=...&topic=... also retains correct metadata
        $legacyResponse = $this->get(route('mcq.index', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));
        $legacyResponse->assertOk();
        $this->assertSingleTitle($legacyResponse->getContent(), 'Number Series MCQs – Practice Questions | AptiMedha');
    }

    public function test_individual_mcq_metadata(): void
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
            'question_number' => 14,
            'question_text' => 'A fruit seller had some apples. He sells 40% apples and still has 420 apples. How many apples had he originally?',
            'option_a' => '588 apples',
            'option_b' => '600 apples',
            'option_c' => '672 apples',
            'option_d' => '700 apples',
            'correct_option' => 'D',
            'explanation' => 'Remaining percentage = 60%. If 60% = 420, then 100% = (420/60) * 100 = 700.',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.show', $mcq->id));
        $response->assertOk();
        $this->assertSingleTitle($response->getContent(), 'Q14: Percentages MCQ | AptiMedha');
        $desc = $this->assertSingleMetaDescription($response->getContent());
        $this->assertStringContainsString('Q14 in Percentages', $desc);
        $this->assertLessThanOrEqual(165, strlen($desc));
    }

    // -------------------------------------------------------------
    // 3. Blog Listing and Article Metadata
    // -------------------------------------------------------------
    public function test_blog_listing_and_article_metadata(): void
    {
        $blogCategory = BlogCategory::create([
            'name' => 'Exam Strategies',
            'slug' => 'exam-strategies',
        ]);

        $postWithSeo = Blog::create([
            'author_id' => $this->admin->id,
            'blog_category_id' => $blogCategory->id,
            'title' => 'Top 10 Campus Placement Interview Strategies',
            'slug' => 'top-10-campus-placement-interview-strategies',
            'meta_title' => 'Campus Interview Strategies 2026 | AptiMedha Guide',
            'meta_description' => 'Comprehensive step-by-step interview guide covering technical and HR interview rounds.',
            'excerpt' => 'Short card excerpt.',
            'body' => '<p>Detailed article content.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Blog index
        $indexResponse = $this->get(route('blog.index'));
        $indexResponse->assertOk();
        $this->assertSingleTitle($indexResponse->getContent(), 'Latest Articles & Learning Resources | AptiMedha');
        $this->assertSingleMetaDescription($indexResponse->getContent());

        // Blog article with explicit SEO metadata
        $showResponse = $this->get(route('blog.show', $postWithSeo->slug));
        $showResponse->assertOk();
        $this->assertSingleTitle($showResponse->getContent(), 'Campus Interview Strategies 2026 | AptiMedha Guide');
        $this->assertSingleMetaDescription($showResponse->getContent(), 'Comprehensive step-by-step interview guide covering technical and HR interview rounds.');
    }

    public function test_blog_article_fallback_metadata_when_seo_fields_empty(): void
    {
        $postWithoutSeo = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Speed Math Techniques for CAT and Bank Exams',
            'slug' => 'speed-math-techniques',
            'meta_title' => null,
            'meta_description' => null,
            'excerpt' => 'Learn Vedic math shortcuts and mental multiplication frameworks.',
            'body' => '<p>Full techniques text.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get(route('blog.show', $postWithoutSeo->slug));
        $response->assertOk();
        $this->assertSingleTitle($response->getContent(), 'Speed Math Techniques for CAT and Bank Exams | AptiMedha');
        $desc = $this->assertSingleMetaDescription($response->getContent());
        $this->assertEquals('Learn Vedic math shortcuts and mental multiplication frameworks.', $desc);
    }

    // -------------------------------------------------------------
    // 4. Placement Listing and Guide Metadata
    // -------------------------------------------------------------
    public function test_placement_listing_and_guide_metadata(): void
    {
        $guide = PlacementGuide::create([
            'title' => 'Resume Preparation Blueprint',
            'slug' => 'resume-preparation-blueprint',
            'category' => 'resume',
            'excerpt' => 'ATS-friendly resume templates and formatting guidelines for college graduates.',
            'content' => '<p>Step 1: Choose single column layout. Step 2: Highlight projects.</p>',
            'meta_title' => 'ATS Resume Preparation Guide 2026 | AptiMedha',
            'meta_description' => 'Complete ATS resume builder checklist and actionable formatting guide for campus placements.',
            'is_published' => true,
        ]);

        // Placement Index
        $indexResponse = $this->get(route('placement.index'));
        $indexResponse->assertOk();
        $this->assertSingleTitle($indexResponse->getContent(), 'Placement Preparation Roadmap, Resume Guides & Interview Tips | AptiMedha');
        $this->assertSingleMetaDescription($indexResponse->getContent());

        // Placement Guide with explicit SEO fields
        $guideResponse = $this->get(route('placement.show', $guide->slug));
        $guideResponse->assertOk();
        $this->assertSingleTitle($guideResponse->getContent(), 'ATS Resume Preparation Guide 2026 | AptiMedha');
        $this->assertSingleMetaDescription($guideResponse->getContent(), 'Complete ATS resume builder checklist and actionable formatting guide for campus placements.');
    }

    public function test_placement_guide_fallback_metadata_when_seo_fields_empty(): void
    {
        $guide = PlacementGuide::create([
            'title' => 'Technical Interview Algorithms Guide',
            'slug' => 'technical-interview-algorithms-guide',
            'category' => 'technical',
            'excerpt' => 'Core data structures and algorithms frequently asked in product company interviews.',
            'content' => '<p>Master arrays, trees, dynamic programming.</p>',
            'meta_title' => null,
            'meta_description' => null,
            'is_published' => true,
        ]);

        $response = $this->get(route('placement.show', $guide->slug));
        $response->assertOk();
        $this->assertSingleTitle($response->getContent(), 'Technical Interview Algorithms Guide | AptiMedha');
        $desc = $this->assertSingleMetaDescription($response->getContent());
        $this->assertEquals('Core data structures and algorithms frequently asked in product company interviews.', $desc);
    }

    // -------------------------------------------------------------
    // 5. Current Affairs Listing and Monthly Issue Metadata
    // -------------------------------------------------------------
    public function test_current_affairs_listing_and_issue_metadata(): void
    {
        $issue = CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'November 2026 Monthly Digest',
            'description' => 'Key national appointments, economic summits, science developments, and international sports awards.',
            'pdf_path' => 'current-affairs/pdfs/nov-2026.pdf',
            'pdf_name' => 'nov-2026.pdf',
            'is_published' => true,
        ]);

        // Current Affairs Index
        $indexResponse = $this->get(route('current-affairs.index'));
        $indexResponse->assertOk();
        $this->assertSingleTitle($indexResponse->getContent(), 'Current Affairs & Monthly GK PDFs | AptiMedha');
        $this->assertSingleMetaDescription($indexResponse->getContent());

        // Current Affairs Issue
        $showResponse = $this->get(route('current-affairs.show', ['year' => 2026, 'month' => 11]));
        $showResponse->assertOk();
        $this->assertSingleTitle($showResponse->getContent(), 'Monthly Current Affairs – November 2026 | AptiMedha');
        $desc = $this->assertSingleMetaDescription($showResponse->getContent());
        $this->assertStringContainsString('November 2026', $desc);
        $this->assertStringContainsString('Key national appointments', $desc);
        $this->assertLessThanOrEqual(165, strlen($desc));
    }

    // -------------------------------------------------------------
    // 6. Static Page Metadata
    // -------------------------------------------------------------
    public function test_static_pages_have_unique_title_and_description(): void
    {
        $pages = [
            'about' => ['slug' => 'about', 'title' => 'About Us', 'meta_title' => 'About AptiMedha - Practise Smart. Get Placement Ready.'],
            'contact' => ['slug' => 'contact', 'title' => 'Contact Us', 'meta_title' => 'Contact Us | AptiMedha Support'],
            'disclaimer' => ['slug' => 'disclaimer', 'title' => 'Website Disclaimer', 'meta_title' => null],
            'terms' => ['slug' => 'terms-and-conditions', 'title' => 'Terms and Conditions', 'meta_title' => null],
            'privacy' => ['slug' => 'privacy-policy', 'title' => 'Privacy Policy', 'meta_title' => null],
            'copyright' => ['slug' => 'copyright', 'title' => 'Copyright Policy', 'meta_title' => null],
        ];

        foreach ($pages as $routeName => $data) {
            StaticPage::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'content' => "<p>Official content for {$data['title']}.</p>",
                    'meta_title' => $data['meta_title'],
                    'meta_description' => "Official {$data['title']} documentation and student guidelines for AptiMedha.",
                    'is_active' => true,
                ]
            );

            $response = $this->get(route($routeName));
            $response->assertOk();

            $content = $response->getContent();
            $title = $this->assertSingleTitle($content);
            $this->assertNotEmpty($title);
            $this->assertStringContainsString('AptiMedha', $title);

            $desc = $this->assertSingleMetaDescription($content);
            $this->assertNotEmpty($desc);
            $this->assertLessThanOrEqual(165, strlen($desc));
        }
    }

    // -------------------------------------------------------------
    // 7. Missing SEO Fields and Fallback Behavior
    // -------------------------------------------------------------
    public function test_seo_service_clean_description_handles_html_entities_and_whitespace(): void
    {
        $dirtyHtml = "  <p>Practice <strong>Percentage</strong> problems &amp; tricks.&nbsp;&nbsp;</p>\n\n<div>Learn mental shortcuts!</div>  ";
        $cleaned = SeoService::cleanDescription($dirtyHtml, 160);

        $this->assertEquals('Practice Percentage problems & tricks. Learn mental shortcuts!', $cleaned);
        $this->assertStringNotContainsString('&nbsp;', $cleaned);
        $this->assertStringNotContainsString('<p>', $cleaned);
        $this->assertStringNotContainsString('  ', $cleaned);
    }

    public function test_seo_service_format_title_avoids_duplicate_brand(): void
    {
        // Standalone title adds brand
        $this->assertEquals('Aptitude Practice | AptiMedha', SeoService::formatTitle('Aptitude Practice'));

        // Already has brand pipe delimiter
        $this->assertEquals('Aptitude Practice | AptiMedha', SeoService::formatTitle('Aptitude Practice | AptiMedha'));

        // Title starting with brand
        $this->assertEquals('AptiMedha – Practice MCQs, Aptitude & Placement Preparation', SeoService::formatTitle('AptiMedha – Practice MCQs, Aptitude & Placement Preparation'));
    }

    // -------------------------------------------------------------
    // 8. Duplicate Title and Meta Tag Prevention
    // -------------------------------------------------------------
    public function test_no_page_renders_multiple_title_or_meta_description_tags(): void
    {
        $routes = [
            route('home'),
            route('categories.index'),
            route('blog.index'),
            route('placement.index'),
            route('current-affairs.index'),
            route('about'),
            route('contact'),
            route('terms'),
            route('privacy'),
            route('disclaimer'),
            route('copyright'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertOk();

            $content = $response->getContent();
            $this->assertSingleTitle($content);
            $this->assertSingleMetaDescription($content);
        }
    }

    // -------------------------------------------------------------
    // 9. Slug Uniqueness Generation
    // -------------------------------------------------------------
    public function test_slug_uniqueness_across_models(): void
    {
        // Category
        Category::create(['name' => 'Data Interpretation', 'slug' => 'data-interpretation', 'is_active' => true]);
        $uniqueCatSlug = Category::generateUniqueSlug('Data Interpretation');
        $this->assertEquals('data-interpretation-1', $uniqueCatSlug);

        // Subject
        $cat = Category::first();
        Subject::create(['category_id' => $cat->id, 'name' => 'General English', 'slug' => 'general-english', 'is_active' => true]);
        $uniqueSubSlug = Subject::generateUniqueSlug('General English');
        $this->assertEquals('general-english-1', $uniqueSubSlug);

        // Topic
        Topic::create(['category_id' => $cat->id, 'name' => 'Grammar Rules', 'slug' => 'grammar-rules', 'is_active' => true]);
        $uniqueTopicSlug = Topic::generateUniqueSlug('Grammar Rules');
        $this->assertEquals('grammar-rules-1', $uniqueTopicSlug);

        // Blog
        Blog::create(['author_id' => $this->admin->id, 'title' => 'Interview Guide', 'slug' => 'interview-guide', 'body' => 'body', 'status' => 'published']);
        $uniqueBlogSlug = Blog::generateUniqueSlug('Interview Guide');
        $this->assertEquals('interview-guide-1', $uniqueBlogSlug);

        // PlacementGuide
        PlacementGuide::create(['title' => 'Roadmap 2026', 'slug' => 'roadmap-2026', 'category' => 'roadmap', 'content' => 'content', 'is_published' => true]);
        $uniquePlacementSlug = PlacementGuide::generateUniqueSlug('Roadmap 2026');
        $this->assertEquals('roadmap-2026-2', $uniquePlacementSlug);

        // StaticPage
        StaticPage::create(['title' => 'FAQ', 'slug' => 'faq', 'content' => 'content', 'is_active' => true]);
        $uniquePageSlug = StaticPage::generateUniqueSlug('FAQ');
        $this->assertEquals('faq-1', $uniquePageSlug);
    }

    // -------------------------------------------------------------
    // 10. Existing URL Compatibility and Permanent 301 Redirects
    // -------------------------------------------------------------
    public function test_permanent_301_redirects_for_legacy_and_shortcut_routes(): void
    {
        // /placement -> /placement-preparation (301)
        $this->get('/placement')->assertStatus(301)->assertRedirect(route('placement.index'));

        // /terms -> /terms-and-conditions (301)
        $this->get('/terms')->assertStatus(301)->assertRedirect(route('terms'));

        // /privacy -> /privacy-policy (301)
        $this->get('/privacy')->assertStatus(301)->assertRedirect(route('privacy'));

        // /about-us -> /about (301)
        $this->get('/about-us')->assertStatus(301)->assertRedirect(route('about'));

        // /contact-us -> /contact (301)
        $this->get('/contact-us')->assertStatus(301)->assertRedirect(route('contact'));
    }

    public function test_backward_compatibility_numeric_id_redirects_to_canonical_slugs(): void
    {
        // Category numeric ID -> 301 to slug
        $category = Category::create(['name' => 'Engineering', 'slug' => 'engineering', 'is_active' => true]);
        $this->get("/categories/{$category->id}")->assertStatus(301)->assertRedirect(route('categories.show', 'engineering'));

        // Blog numeric ID -> 301 to slug
        $blog = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'TCS NQT Preparation',
            'slug' => 'tcs-nqt-preparation',
            'body' => 'content',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $this->get("/blog/{$blog->id}")->assertStatus(301)->assertRedirect(route('blog.show', 'tcs-nqt-preparation'));

        // Placement Guide numeric ID -> 301 to slug
        $guide = PlacementGuide::create([
            'title' => 'HR Interview Checklist',
            'slug' => 'hr-interview-checklist',
            'category' => 'interview',
            'content' => 'content',
            'is_published' => true,
        ]);
        $this->get("/placement-preparation/{$guide->id}")->assertStatus(301)->assertRedirect(route('placement.show', 'hr-interview-checklist'));
    }

    public function test_uppercase_slugs_301_redirect_to_lowercase_canonical_urls(): void
    {
        $category = Category::create(['name' => 'Reasoning', 'slug' => 'reasoning', 'is_active' => true]);
        $this->get('/categories/Reasoning')->assertStatus(301)->assertRedirect(route('categories.show', 'reasoning'));

        $blog = Blog::create([
            'author_id' => $this->admin->id,
            'title' => 'Wipro Elite Guide',
            'slug' => 'wipro-elite-guide',
            'body' => 'body',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $this->get('/blog/Wipro-Elite-Guide')->assertStatus(301)->assertRedirect(route('blog.show', 'wipro-elite-guide'));

        $guide = PlacementGuide::create([
            'title' => 'Mock GD Topics',
            'slug' => 'mock-gd-topics',
            'category' => 'interview',
            'content' => 'content',
            'is_published' => true,
        ]);
        $this->get('/placement-preparation/Mock-GD-Topics')->assertStatus(301)->assertRedirect(route('placement.show', 'mock-gd-topics'));
    }

    public function test_current_affairs_month_name_and_number_urls_both_work(): void
    {
        CurrentAffair::create([
            'year' => 2026,
            'month' => 11,
            'title' => 'November 2026',
            'pdf_path' => 'pdfs/nov.pdf',
            'is_published' => true,
        ]);

        // By month name
        $this->get('/current-affairs/2026/november')
            ->assertOk()
            ->assertSee('November 2026');

        // By month number
        $this->get('/current-affairs/2026/11')
            ->assertOk()
            ->assertSee('November 2026');
    }

    // -------------------------------------------------------------
    // 11. Invalid Slugs Return 404
    // -------------------------------------------------------------
    public function test_invalid_slugs_return_404_across_modules(): void
    {
        // Category
        $this->get('/categories/non-existent-category')->assertNotFound();

        // Topic in category
        $category = Category::create(['name' => 'Aptitude', 'slug' => 'aptitude', 'is_active' => true]);
        $this->get('/categories/aptitude/non-existent-topic')->assertNotFound();

        // Non-existent category for topic route
        $this->get('/categories/ghost-cat/some-topic')->assertNotFound();

        // Blog
        $this->get('/blog/ghost-article-slug')->assertNotFound();

        // Placement Guide
        $this->get('/placement-preparation/missing-guide-slug')->assertNotFound();

        // Current Affairs invalid month or year
        $this->get('/current-affairs/2026/13')->assertNotFound();
        $this->get('/current-affairs/abcd/10')->assertNotFound();
        $this->get('/current-affairs/2026/99')->assertNotFound();

        // Individual MCQ non-existent ID or invalid slug
        $this->get('/mcq/999999')->assertNotFound();
        $this->get('/mcq/invalid-non-numeric-slug')->assertNotFound();
    }
}
