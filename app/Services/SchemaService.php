<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\StaticPage;
use App\Models\Topic;

/**
 * Service for generating structured data (Schema.org JSON-LD).
 */
class SchemaService
{
    /**
     * Build organization schema.
     *
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => url('/#organization'),
            'name' => 'AptiMedha',
            'url' => url('/'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => asset('images/aptimedha-logo.png'),
            ],
            'description' => 'AptiMedha - Sharpen your aptitude, reasoning and subject skills to crack placement exams and interviews.',
            'sameAs' => [
                'https://linkedin.com',
                'https://twitter.com',
                'https://github.com',
            ],
        ];
    }

    /**
     * Build website schema with SearchAction.
     *
     * @return array<string, mixed>
     */
    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/#website'),
            'url' => url('/'),
            'name' => 'AptiMedha',
            'description' => 'Practice MCQs, Aptitude & Placement Preparation',
            'publisher' => ['@id' => url('/#organization')],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/mcq?topic={search_term_string}'),
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Build breadcrumb list schema.
     *
     * @param  array<int, array{name: string, url: string}>  $breadcrumbs
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $breadcrumbs): array
    {
        $items = [];
        $position = 1;

        foreach ($breadcrumbs as $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => strip_tags($crumb['name']),
                'item' => $crumb['url'],
            ];
        }

        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Homepage Schema Graph: Organization, WebSite, and WebPage.
     *
     * @return array<string, mixed>
     */
    public static function forHome(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::website(),
                [
                    '@type' => 'WebPage',
                    '@id' => url('/#webpage'),
                    'url' => url('/'),
                    'name' => 'AptiMedha – Practice MCQs, Aptitude & Placement Preparation',
                    'isPartOf' => ['@id' => url('/#website')],
                    'about' => ['@id' => url('/#organization')],
                    'description' => 'Practice aptitude MCQs, reasoning puzzles, programming questions, and placement interview preparation guides on AptiMedha.',
                ],
            ],
        ];
    }

    /**
     * Category Listing Schema (`/categories`).
     *
     * @return array<string, mixed>
     */
    public static function forCategoriesIndex(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => 'All Categories & Subject Tracks | AptiMedha',
                    'url' => route('categories.index'),
                    'description' => 'Explore structured question categories covering arithmetic aptitude, logical reasoning, verbal ability, programming, engineering, and general knowledge on AptiMedha.',
                ],
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Categories', 'url' => route('categories.index')],
                ]),
            ],
        ];
    }

    /**
     * Category Show Page Schema (`/categories/{category}`).
     *
     * @return array<string, mixed>
     */
    public static function forCategory(Category $category): array
    {
        $categoryUrl = route('categories.show', $category->slug);

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => "{$category->name} MCQs – Practice Questions | AptiMedha",
                    'url' => $categoryUrl,
                    'description' => SeoService::categoryDescription($category),
                ],
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Categories', 'url' => route('categories.index')],
                    ['name' => $category->name, 'url' => $categoryUrl],
                ]),
            ],
        ];
    }

    /**
     * Topic Practice Page Schema (`/categories/{category}/{topic}`).
     *
     * @return array<string, mixed>
     */
    public static function forTopic(Topic $topic, ?Category $category = null): array
    {
        $cat = $category ?: $topic->category;
        $categoryUrl = $cat ? route('categories.show', $cat->slug) : route('categories.index');
        $topicUrl = $cat
            ? route('categories.topics.show', ['category' => $cat->slug, 'topic' => $topic->slug])
            : route('mcq.index', ['topic' => $topic->slug]);

        $breadcrumbs = [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Categories', 'url' => route('categories.index')],
        ];

        if ($cat) {
            $breadcrumbs[] = ['name' => $cat->name, 'url' => $categoryUrl];
        }

        $breadcrumbs[] = ['name' => $topic->name, 'url' => $topicUrl];

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => "{$topic->name} MCQs – Practice Questions | AptiMedha",
                    'url' => $topicUrl,
                    'description' => SeoService::topicDescription($topic, $cat),
                ],
                self::breadcrumbs($breadcrumbs),
            ],
        ];
    }

    /**
     * Blog Index Schema (`/blog`).
     *
     * @return array<string, mixed>
     */
    public static function forBlogIndex(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => 'Latest Articles & Learning Resources | AptiMedha',
                    'url' => route('blog.index'),
                    'description' => 'Explore placement preparation roadmaps, speed math techniques, interview frameworks, and aptitude exam patterns on AptiMedha.',
                ],
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                ]),
            ],
        ];
    }

    /**
     * Blog Detail Schema (`/blog/{post}`).
     *
     * @return array<string, mixed>
     */
    public static function forBlog(Blog $blog): array
    {
        $postUrl = route('blog.show', $blog->slug);
        $imageUrl = $blog->featured_image_url ?: asset('images/aptimedha-logo.png');

        $article = [
            '@type' => 'BlogPosting',
            'headline' => strip_tags($blog->title),
            'description' => SeoService::blogDescription($blog),
            'url' => $postUrl,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $postUrl,
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $blog->author_name ?: ($blog->author?->name ?? 'AptiMedha Editorial Team'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'AptiMedha',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/aptimedha-logo.png'),
                ],
            ],
            'image' => [$imageUrl],
        ];

        if ($blog->published_at) {
            $article['datePublished'] = $blog->published_at->toIso8601String();
        }

        if ($blog->updated_at) {
            $article['dateModified'] = $blog->updated_at->toIso8601String();
        } elseif ($blog->published_at) {
            $article['dateModified'] = $blog->published_at->toIso8601String();
        }

        if ($blog->category) {
            $article['articleSection'] = $blog->category->name;
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $article,
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                    ['name' => $blog->title, 'url' => $postUrl],
                ]),
            ],
        ];
    }

    /**
     * Placement Index Schema (`/placement-preparation`).
     *
     * @return array<string, mixed>
     */
    public static function forPlacementIndex(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => 'Placement Preparation Roadmap, Resume Guides & Interview Tips | AptiMedha',
                    'url' => route('placement.index'),
                    'description' => 'Complete placement preparation guides for campus placements, fresh graduates, and professionals. Master aptitude tests, resumes, technical rounds, and HR interviews.',
                ],
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Placement Preparation', 'url' => route('placement.index')],
                ]),
            ],
        ];
    }

    /**
     * Placement Guide Schema (`/placement-preparation/{slug}`).
     *
     * @return array<string, mixed>
     */
    public static function forPlacementGuide(PlacementGuide $guide): array
    {
        $guideUrl = route('placement.show', $guide->slug);

        $article = [
            '@type' => 'Article',
            'headline' => strip_tags($guide->title),
            'description' => SeoService::placementDescription($guide),
            'url' => $guideUrl,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $guideUrl,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'AptiMedha',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/aptimedha-logo.png'),
                ],
            ],
            'articleSection' => $guide->categoryLabel(),
        ];

        if ($guide->published_at) {
            $article['datePublished'] = $guide->published_at->toIso8601String();
        }

        if ($guide->updated_at) {
            $article['dateModified'] = $guide->updated_at->toIso8601String();
        } elseif ($guide->published_at) {
            $article['dateModified'] = $guide->published_at->toIso8601String();
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $article,
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Placement Preparation', 'url' => route('placement.index')],
                    ['name' => $guide->title, 'url' => $guideUrl],
                ]),
            ],
        ];
    }

    /**
     * Current Affairs Index Schema (`/current-affairs`).
     *
     * @return array<string, mixed>
     */
    public static function forCurrentAffairsIndex(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => 'Current Affairs & Monthly GK PDFs | AptiMedha',
                    'url' => route('current-affairs.index'),
                    'description' => 'Download curated monthly Current Affairs PDF capsules and practice General Knowledge MCQs for campus placements, banking, SSC, and competitive examinations.',
                ],
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Current Affairs', 'url' => route('current-affairs.index')],
                ]),
            ],
        ];
    }

    /**
     * Current Affairs Monthly Issue Schema (`/current-affairs/{year}/{month}`).
     *
     * @return array<string, mixed>
     */
    public static function forCurrentAffair(CurrentAffair $issue): array
    {
        $issueUrl = route('current-affairs.show', ['year' => $issue->year, 'month' => $issue->month]);

        $article = [
            '@type' => 'Article',
            'headline' => strip_tags($issue->title),
            'description' => SeoService::currentAffairsDescription($issue),
            'url' => $issueUrl,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $issueUrl,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'AptiMedha',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/aptimedha-logo.png'),
                ],
            ],
        ];

        if ($issue->published_at) {
            $article['datePublished'] = $issue->published_at->toIso8601String();
        }

        if ($issue->updated_at) {
            $article['dateModified'] = $issue->updated_at->toIso8601String();
        } elseif ($issue->published_at) {
            $article['dateModified'] = $issue->published_at->toIso8601String();
        }

        if (! empty($issue->pdf_path)) {
            $article['encoding'] = [
                '@type' => 'MediaObject',
                'encodingFormat' => 'application/pdf',
                'name' => $issue->pdf_name ?: basename($issue->pdf_path),
            ];
            if ($issue->file_size_bytes) {
                $article['encoding']['contentSize'] = (string) $issue->file_size_bytes;
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $article,
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Current Affairs', 'url' => route('current-affairs.index')],
                    ['name' => $issue->title, 'url' => $issueUrl],
                ]),
            ],
        ];
    }

    /**
     * Static CMS Page Schema (`/about`, `/contact`, `/terms-and-conditions`, etc.).
     *
     * @return array<string, mixed>
     */
    public static function forStaticPage(?StaticPage $page, string $fallbackTitle, string $url): array
    {
        $title = $page && ! empty($page->meta_title) ? $page->meta_title : ($page?->title ?: $fallbackTitle);
        $description = SeoService::staticPageDescription($page, $fallbackTitle);

        $webPage = [
            '@type' => 'WebPage',
            'name' => strip_tags($title),
            'url' => $url,
            'description' => $description,
            'isPartOf' => ['@type' => 'WebSite', 'url' => url('/')],
        ];

        if ($page?->updated_at) {
            $webPage['dateModified'] = $page->updated_at->toIso8601String();
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $webPage,
                self::breadcrumbs([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => $fallbackTitle, 'url' => $url],
                ]),
            ],
        ];
    }

    /**
     * Safely encode schema array to a valid JSON-LD script string.
     *
     * @param  array<string, mixed>|string  $data
     */
    public static function toJsonLd(array|string $data): string
    {
        if (is_string($data)) {
            return $data;
        }

        return json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_PRETTY_PRINT
        );
    }
}

