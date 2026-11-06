<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\StaticPage;
use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Service for generating, formatting, and caching dynamic XML sitemaps.
 */
class SitemapService
{
    public const CACHE_KEY = 'sitemap_xml_content';
    public const CACHE_TTL = 86400; // 24 hours

    /**
     * Get the base production canonical URL.
     */
    public static function getBaseUrl(): string
    {
        $appUrl = config('app.canonical_url') ?: config('app.url');

        if (empty($appUrl) || $appUrl === 'http://localhost' || $appUrl === 'http://localhost:8000') {
            $appUrl = 'https://aptimedha.onrender.com';
        }

        // Always force https for canonical sitemap URLs per protocol requirements
        $appUrl = preg_replace('/^http:/i', 'https:', (string) $appUrl);

        return rtrim($appUrl, '/');
    }

    /**
     * Clear the cached sitemap XML.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Retrieve the cached sitemap XML or generate a fresh copy.
     */
    public static function getCachedXml(bool $forceFresh = false): string
    {
        if ($forceFresh) {
            self::clearCache();
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::generateXml();
        });
    }

    /**
     * Build an absolute canonical HTTPS URL from a relative path or route.
     */
    public static function canonicalUrl(string $relativeUri): string
    {
        $base = self::getBaseUrl();
        $trimmed = trim($relativeUri);

        if ($trimmed === '' || $trimmed === '/') {
            return $base . '/';
        }

        return $base . '/' . ltrim($trimmed, '/');
    }

    /**
     * Collect all eligible public URLs with actual modification timestamps and priorities.
     *
     * @return array<string, array{loc: string, lastmod: ?Carbon, changefreq: string, priority: float}>
     */
    public static function getUrls(): array
    {
        $urls = [];

        // 1. Static informational pages backed by CMS or layout
        $staticPageSlugs = [
            'about' => ['path' => '/about', 'slug' => 'about', 'priority' => 0.6, 'changefreq' => 'monthly'],
            'contact' => ['path' => '/contact', 'slug' => 'contact', 'priority' => 0.6, 'changefreq' => 'monthly'],
            'disclaimer' => ['path' => '/disclaimer', 'slug' => 'disclaimer', 'priority' => 0.4, 'changefreq' => 'monthly'],
            'terms' => ['path' => '/terms-and-conditions', 'slug' => 'terms-and-conditions', 'priority' => 0.4, 'changefreq' => 'monthly'],
            'privacy' => ['path' => '/privacy-policy', 'slug' => 'privacy-policy', 'priority' => 0.4, 'changefreq' => 'monthly'],
            'copyright' => ['path' => '/copyright', 'slug' => 'copyright', 'priority' => 0.4, 'changefreq' => 'monthly'],
        ];

        // Retrieve static page update timestamps from database
        $staticPages = StaticPage::where('is_active', true)
            ->get(['slug', 'updated_at'])
            ->keyBy('slug');

        $latestStaticUpdate = null;

        foreach ($staticPageSlugs as $page) {
            $record = $staticPages->get($page['slug']);
            $lastmod = $record?->updated_at;

            if ($lastmod && ($latestStaticUpdate === null || $lastmod->gt($latestStaticUpdate))) {
                $latestStaticUpdate = $lastmod;
            }

            $loc = self::canonicalUrl($page['path']);
            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        // 2. Hub Indexes (MCQ, Categories, Blog, Placement, Current Affairs)
        $hubs = [
            ['path' => '/categories', 'priority' => 0.9, 'changefreq' => 'weekly'],
            ['path' => '/mcq', 'priority' => 0.9, 'changefreq' => 'daily'],
            ['path' => '/blog', 'priority' => 0.9, 'changefreq' => 'daily'],
            ['path' => '/placement-preparation', 'priority' => 0.9, 'changefreq' => 'weekly'],
            ['path' => '/current-affairs', 'priority' => 0.9, 'changefreq' => 'weekly'],
        ];

        foreach ($hubs as $hub) {
            $loc = self::canonicalUrl($hub['path']);
            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => null, // Dynamic index lists; lastmod omitted or derived from children
                'changefreq' => $hub['changefreq'],
                'priority' => $hub['priority'],
            ];
        }

        // 3. Categories (Published/Active categories only)
        $latestCategoryUpdate = null;
        $categories = Category::active()
            ->ordered()
            ->get(['id', 'slug', 'updated_at']);

        foreach ($categories as $category) {
            if ($category->updated_at && ($latestCategoryUpdate === null || $category->updated_at->gt($latestCategoryUpdate))) {
                $latestCategoryUpdate = $category->updated_at;
            }

            $loc = self::canonicalUrl(route('categories.show', $category->slug, false));
            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $category->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.8,
            ];
        }

        // 4. Topic Practice Pages (Active topics under active categories only)
        $topics = Topic::active()
            ->whereHas('category', fn ($q) => $q->active())
            ->with(['category:id,slug'])
            ->ordered()
            ->get(['id', 'category_id', 'slug', 'updated_at']);

        foreach ($topics as $topic) {
            if (! $topic->category) {
                continue;
            }

            $loc = self::canonicalUrl(route('categories.topics.show', [
                'category' => $topic->category->slug,
                'topic' => $topic->slug,
            ], false));

            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $topic->updated_at,
                'changefreq' => 'weekly',
                'priority' => 0.8,
            ];
        }

        // 5. Blog Articles (Published posts only; strictly exclude drafts/archived/deleted)
        $latestBlogUpdate = null;
        $blogs = Blog::published()
            ->latest('published_at')
            ->get(['id', 'slug', 'updated_at', 'published_at']);

        foreach ($blogs as $blog) {
            $lastmod = $blog->updated_at ?: $blog->published_at;
            if ($lastmod && ($latestBlogUpdate === null || $lastmod->gt($latestBlogUpdate))) {
                $latestBlogUpdate = $lastmod;
            }

            $loc = self::canonicalUrl(route('blog.show', $blog->slug, false));
            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => 0.7,
            ];
        }

        // 6. Placement Preparation Guides (Published guides only)
        $latestPlacementUpdate = null;
        $guides = PlacementGuide::published()
            ->ordered()
            ->get(['id', 'slug', 'updated_at', 'published_at']);

        foreach ($guides as $guide) {
            $lastmod = $guide->updated_at ?: $guide->published_at;
            if ($lastmod && ($latestPlacementUpdate === null || $lastmod->gt($latestPlacementUpdate))) {
                $latestPlacementUpdate = $lastmod;
            }

            $loc = self::canonicalUrl(route('placement.show', $guide->slug, false));
            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => 0.7,
            ];
        }

        // 7. Current Affairs Monthly Issues (Published issues only)
        $latestCurrentAffairUpdate = null;
        $issues = CurrentAffair::published()
            ->ordered()
            ->get(['id', 'year', 'month', 'updated_at', 'published_at']);

        foreach ($issues as $issue) {
            $lastmod = $issue->updated_at ?: $issue->published_at;
            if ($lastmod && ($latestCurrentAffairUpdate === null || $lastmod->gt($latestCurrentAffairUpdate))) {
                $latestCurrentAffairUpdate = $lastmod;
            }

            $loc = self::canonicalUrl(route('current-affairs.show', [
                'year' => $issue->year,
                'month' => $issue->month,
            ], false));

            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => 0.7,
            ];
        }

        // 8. Homepage (Include lastmod derived truthfully from latest content update)
        $homeLastmod = collect([
            $latestBlogUpdate,
            $latestPlacementUpdate,
            $latestCurrentAffairUpdate,
            $latestCategoryUpdate,
            $latestStaticUpdate,
        ])->filter()->max();

        $homeUrl = self::canonicalUrl('/');
        $urls = array_merge([
            $homeUrl => [
                'loc' => $homeUrl,
                'lastmod' => $homeLastmod,
                'changefreq' => 'daily',
                'priority' => 1.0,
            ],
        ], $urls);

        return $urls;
    }

    /**
     * Generate standard compliant XML sitemap string.
     */
    public static function generateXml(): string
    {
        $urls = self::getUrls();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $item) {
            $xml .= "    <url>\n";
            $xml .= '        <loc>' . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";

            if (! empty($item['lastmod']) && $item['lastmod'] instanceof Carbon) {
                $xml .= '        <lastmod>' . $item['lastmod']->toAtomString() . "</lastmod>\n";
            }

            if (! empty($item['changefreq'])) {
                $xml .= '        <changefreq>' . htmlspecialchars($item['changefreq'], ENT_XML1, 'UTF-8') . "</changefreq>\n";
            }

            if (isset($item['priority'])) {
                $xml .= '        <priority>' . number_format((float) $item['priority'], 1, '.', '') . "</priority>\n";
            }

            $xml .= "    </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Register model event listeners for automatic cache invalidation.
     */
    public static function registerModelEvents(): void
    {
        $models = [
            Blog::class,
            PlacementGuide::class,
            CurrentAffair::class,
            Category::class,
            Topic::class,
            StaticPage::class,
        ];

        foreach ($models as $modelClass) {
            $modelClass::saved(function () {
                self::clearCache();
            });
            $modelClass::deleted(function () {
                self::clearCache();
            });
        }
    }
}
