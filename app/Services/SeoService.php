<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Category;
use App\Models\CurrentAffair;
use App\Models\PlacementGuide;
use App\Models\Question;
use App\Models\StaticPage;
use App\Models\Topic;
use Illuminate\Support\Str;

/**
 * Centralized service for dynamic, consistent SEO titles and meta descriptions.
 */
class SeoService
{
    public const SITE_NAME = 'AptiMedha';
    public const DEFAULT_TITLE = 'AptiMedha – Practice MCQs, Aptitude & Placement Preparation';
    public const DEFAULT_DESCRIPTION = 'AptiMedha - Sharpen your aptitude, reasoning, and subject skills to crack placement exams, screening tests, and technical interviews.';

    /**
     * Get the base production canonical URL using APP_URL or canonical configuration.
     */
    public static function getBaseUrl(): string
    {
        $appUrl = config('app.canonical_url') ?: config('app.url');

        if (empty($appUrl) || $appUrl === 'http://localhost' || $appUrl === 'http://localhost:8000') {
            $appUrl = 'https://aptimedha.onrender.com';
        }

        // Always force https for canonical URLs
        $appUrl = preg_replace('/^http:/i', 'https:', (string) $appUrl);

        return rtrim($appUrl, '/');
    }

    /**
     * Generate a canonical absolute URL adhering to the production APP_URL base.
     */
    public static function canonicalUrl(?string $url = null): string
    {
        $base = self::getBaseUrl();

        if ($url === null || trim($url) === '') {
            $path = '/' . ltrim(request()->path(), '/');
        } else {
            $trimmed = trim($url);
            if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
                $parsedPath = parse_url($trimmed, PHP_URL_PATH);
                $path = '/' . ltrim($parsedPath ?: '', '/');
            } else {
                $parsedPath = parse_url($trimmed, PHP_URL_PATH);
                $path = '/' . ltrim($parsedPath ?: '', '/');
            }
        }

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path === '/' ? $base . '/' : $base . $path;
    }

    /**
     * Resolve Open Graph and Twitter image absolute URL.
     */
    public static function resolveOgImage(?string $image = null): string
    {
        if (empty($image)) {
            return self::canonicalUrl('/images/aptimedha-logo.png');
        }

        $trimmed = trim($image);

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        return self::canonicalUrl($trimmed);
    }

    /**
     * Clean, strip HTML, normalize whitespace, and truncate meta description to 150-160 characters.
     */
    public static function cleanDescription(?string $text, int $limit = 160, ?string $fallback = null): string
    {
        $raw = $text ?: ($fallback ?: self::DEFAULT_DESCRIPTION);

        // Strip HTML tags
        $clean = strip_tags($raw);

        // Decode HTML entities
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Replace non-breaking spaces with normal spaces
        $clean = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $clean);

        // Normalize multiple spaces, newlines, and tabs into a single space
        $clean = preg_replace('/\s+/', ' ', $clean);

        $clean = trim($clean ?? '');

        if ($clean === '' && $fallback !== null) {
            return self::cleanDescription($fallback, $limit);
        }

        if ($clean === '') {
            $clean = self::DEFAULT_DESCRIPTION;
        }

        return Str::limit($clean, $limit);
    }

    /**
     * Format a descriptive page title with brand suffix, avoiding duplicate brand names or separators.
     */
    public static function formatTitle(?string $title, string $siteName = self::SITE_NAME): string
    {
        if (empty($title)) {
            return self::DEFAULT_TITLE;
        }

        $title = trim(strip_tags((string) $title));

        // Avoid duplicating brand name if already present
        if (str_ends_with($title, " | {$siteName}") ||
            str_ends_with($title, " – {$siteName}") ||
            str_ends_with($title, " - {$siteName}") ||
            str_starts_with($title, "{$siteName} –") ||
            str_starts_with($title, "{$siteName} -") ||
            str_starts_with($title, "{$siteName} |")) {
            return $title;
        }

        // If title already has pipe delimiter with custom branding or suffix
        if (str_contains($title, ' | ')) {
            return $title;
        }

        return "{$title} | {$siteName}";
    }

    /**
     * SEO title for a category.
     */
    public static function categoryTitle(Category $category): string
    {
        return "{$category->name} MCQs – Practice Questions | " . self::SITE_NAME;
    }

    /**
     * SEO description for a category.
     */
    public static function categoryDescription(Category $category): string
    {
        if (!empty($category->description)) {
            return self::cleanDescription(
                "Practice {$category->name} MCQs on AptiMedha. " . $category->description,
                160
            );
        }

        return self::cleanDescription(
            "Practice {$category->name} MCQs, syllabus topics, and structured questions with detailed explanations on AptiMedha."
        );
    }

    /**
     * SEO title for topic MCQ practice.
     */
    public static function topicTitle(Topic|string $topic, ?Category $category = null): string
    {
        $topicName = $topic instanceof Topic ? $topic->name : $topic;

        return "{$topicName} MCQs – Practice Questions | " . self::SITE_NAME;
    }

    /**
     * SEO description for topic MCQ practice.
     */
    public static function topicDescription(Topic|string $topic, ?Category $category = null): string
    {
        $topicName = $topic instanceof Topic ? $topic->name : $topic;
        $categoryName = $category ? $category->name : ($topic instanceof Topic && $topic->category ? $topic->category->name : 'Aptitude');
        $topicDesc = $topic instanceof Topic ? $topic->description : null;

        if (!empty($topicDesc)) {
            return self::cleanDescription(
                "Practice {$topicName} MCQs in {$categoryName} with verified answers and step-by-step explanations on AptiMedha. {$topicDesc}",
                160
            );
        }

        return self::cleanDescription(
            "Practice {$topicName} MCQs in {$categoryName} with verified answers, step-by-step explanations, and discussion on AptiMedha."
        );
    }

    /**
     * SEO title for an individual MCQ question.
     */
    public static function mcqTitle(Question $mcq): string
    {
        $topicName = $mcq->topic?->name ?? 'Aptitude';

        return "Q{$mcq->question_number}: {$topicName} MCQ | " . self::SITE_NAME;
    }

    /**
     * SEO description for an individual MCQ question.
     */
    public static function mcqDescription(Question $mcq): string
    {
        $topicName = $mcq->topic?->name ?? 'Aptitude';
        $questionSnippet = self::cleanDescription($mcq->question_text, 100);

        return self::cleanDescription(
            "Q{$mcq->question_number} in {$topicName}: {$questionSnippet} - Practice with verified answer key and detailed explanation on AptiMedha.",
            160
        );
    }

    /**
     * SEO title for a blog article.
     */
    public static function blogTitle(Blog $blog): string
    {
        if (!empty($blog->meta_title)) {
            return $blog->meta_title;
        }

        return self::formatTitle($blog->title);
    }

    /**
     * SEO description for a blog article.
     */
    public static function blogDescription(Blog $blog): string
    {
        if (!empty($blog->meta_description)) {
            return self::cleanDescription($blog->meta_description);
        }

        $intro = $blog->excerpt ?: $blog->body;

        return self::cleanDescription($intro);
    }

    /**
     * SEO title for a placement preparation guide.
     */
    public static function placementTitle(PlacementGuide $guide): string
    {
        if (!empty($guide->meta_title)) {
            return $guide->meta_title;
        }

        return self::formatTitle($guide->title);
    }

    /**
     * SEO description for a placement preparation guide.
     */
    public static function placementDescription(PlacementGuide $guide): string
    {
        if (!empty($guide->meta_description)) {
            return self::cleanDescription($guide->meta_description);
        }

        $summary = $guide->excerpt ?: $guide->content;

        return self::cleanDescription($summary);
    }

    /**
     * SEO title for a monthly Current Affairs issue.
     */
    public static function currentAffairsTitle(CurrentAffair $issue): string
    {
        return "Monthly Current Affairs – {$issue->month_name} {$issue->year} | " . self::SITE_NAME;
    }

    /**
     * SEO description for a monthly Current Affairs issue.
     */
    public static function currentAffairsDescription(CurrentAffair $issue): string
    {
        if (!empty($issue->description)) {
            return self::cleanDescription(
                "Monthly Current Affairs – {$issue->period_label}: " . $issue->description,
                160
            );
        }

        return self::cleanDescription(
            "Download {$issue->title} monthly Current Affairs and General Knowledge digest with curated MCQs on AptiMedha."
        );
    }

    /**
     * SEO title for static CMS pages.
     */
    public static function staticPageTitle(?StaticPage $page, string $fallbackTitle): string
    {
        if ($page && !empty($page->meta_title)) {
            return $page->meta_title;
        }

        $title = $page && !empty($page->title) ? $page->title : $fallbackTitle;

        return self::formatTitle($title);
    }

    /**
     * SEO description for static CMS pages.
     */
    public static function staticPageDescription(?StaticPage $page, string $fallback): string
    {
        if ($page && !empty($page->meta_description)) {
            return self::cleanDescription($page->meta_description);
        }

        if ($page && !empty($page->content)) {
            return self::cleanDescription($page->content, 160, $fallback);
        }

        return self::cleanDescription($fallback);
    }
}

