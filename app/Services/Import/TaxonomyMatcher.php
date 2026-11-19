<?php

namespace App\Services\Import;

use App\Models\Category;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TaxonomyMatcher
{
    /** @var Collection<int, Category> */
    protected Collection $categories;

    /** @var Collection<int, Subject> */
    protected Collection $subjects;

    /** @var Collection<int, Topic> */
    protected Collection $topics;

    public function __construct()
    {
        $this->refreshTaxonomy();
    }

    /**
     * Refresh in-memory taxonomy collections from database.
     */
    public function refreshTaxonomy(): void
    {
        $this->categories = Category::all();
        $this->subjects = Subject::all();
        $this->topics = Topic::with(['category', 'subject'])->get();
    }

    /**
     * Normalize string for robust, whitespace/dash/case-insensitive matching.
     */
    public function normalizeString(string $value): string
    {
        // Replace Unicode non-breaking spaces and invisible characters with standard space
        $value = preg_replace('/[\x{00A0}\x{2000}-\x{200B}\x{FEFF}]+/u', ' ', $value) ?? $value;

        // Normalize Unicode dashes (en-dash, em-dash, minus) to standard hyphen
        $value = preg_replace('/[\x{2010}\x{2011}\x{2012}\x{2013}\x{2014}\x{2015}\x{2212}]/u', '-', $value) ?? $value;

        // Trim and lowercase using UTF-8
        $value = mb_strtolower(trim($value), 'UTF-8');

        // Collapse multiple whitespace chars to a single space
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        // Collapse multiple hyphens to a single hyphen
        $value = preg_replace('/-+/', '-', $value) ?? $value;

        return trim($value);
    }

    /**
     * Compute canonical alphanumeric key for punctuation-agnostic comparison.
     */
    public function canonicalKey(string $value): string
    {
        $norm = $this->normalizeString($value);

        // Normalize ampersand variants to "and"
        $norm = str_replace([' & ', '&'], [' and ', 'and'], $norm);

        // Retain only lowercase alphanumeric characters
        return preg_replace('/[^a-z0-9]/', '', $norm) ?? '';
    }

    /**
     * Resolve Category, Subject, and Topic from workbook row values.
     *
     * Supports:
     * 1. Associative array: ['category' => ..., 'subject' => ..., 'topic' => ...]
     * 2. 3 parameters: resolve($categoryCol, $topicCol, $subjectCol)
     * 3. 2 parameters (legacy): resolve($topicCol, $subtopicCol)
     *
     * @param string|array<string, mixed> $col1
     * @param string $col2
     * @param string|null $col3
     * @return array{
     *     success: bool,
     *     category_id?: int,
     *     subject_id?: int|null,
     *     topic_id?: int,
     *     topic?: Topic,
     *     error?: string
     * }
     */
    public function resolve(string|array $col1, string $col2 = '', ?string $col3 = null): array
    {
        $categoryInput = '';
        $subjectInput = null;
        $topicInput = '';

        if (is_array($col1)) {
            $categoryInput = trim((string) ($col1['category'] ?? ''));
            $subjectInput = isset($col1['subject']) ? trim((string) $col1['subject']) : null;
            $topicInput = trim((string) ($col1['topic'] ?? ''));
            $subtopic = trim((string) ($col1['subtopic'] ?? ''));

            // Legacy array mapping where 'topic' had Category and 'subtopic' had Topic
            if ($categoryInput === '' && $topicInput !== '' && $subtopic !== '') {
                $categoryInput = $topicInput;
                $topicInput = $subtopic;
            } elseif ($topicInput === '' && $subtopic !== '') {
                $topicInput = $subtopic;
            }
        } elseif ($col3 !== null) {
            // Explicit 3-column call: (category, topic, subject)
            $categoryInput = trim((string) $col1);
            $topicInput = trim((string) $col2);
            $subjectInput = trim((string) $col3);
        } else {
            // Legacy 2-column call: ($topicCol, $subtopicCol)
            $topicCol = trim((string) $col1);
            $subtopicCol = trim((string) $col2);

            $categoryInput = $topicCol;
            $topicInput = $subtopicCol;

            // Check if subtopic contains slash-separated hierarchy, e.g. "Subject / Topic"
            if (str_contains($subtopicCol, '/')) {
                $parts = array_values(array_filter(array_map('trim', explode('/', $subtopicCol))));
                if (!empty($parts)) {
                    $topicInput = end($parts);
                    if (count($parts) >= 2) {
                        $subjectInput = $parts[count($parts) - 2];
                    }
                }
            }

            // If topicCol is empty and subtopicCol has value, use subtopicCol as topic
            if ($categoryInput === '' && $topicInput !== '') {
                $categoryInput = '';
            }
        }

        if ($subjectInput !== null && $subjectInput === '') {
            $subjectInput = null;
        }

        // If subtopic/topic still contains slash-separated hierarchy, extract subject and leaf topic
        if (str_contains($topicInput, '/')) {
            $parts = array_values(array_filter(array_map('trim', explode('/', $topicInput))));
            if (!empty($parts)) {
                $topicInput = end($parts);
                if ($subjectInput === null && count($parts) >= 2) {
                    $subjectInput = $parts[count($parts) - 2];
                }
            }
        }

        if ($categoryInput === '' && $topicInput === '') {
            return [
                'success' => false,
                'error' => 'Both Topic and Subtopic columns are empty.',
            ];
        }

        // 1. Resolve Category if specified
        $matchedCategory = null;
        if ($categoryInput !== '') {
            $matchedCategory = $this->matchCategory($categoryInput);
            if (!$matchedCategory) {
                // Refresh once in case newly created category in database
                $this->refreshTaxonomy();
                $matchedCategory = $this->matchCategory($categoryInput);
            }

            if (!$matchedCategory) {
                return [
                    'success' => false,
                    'error' => "Unknown category '{$categoryInput}'. Category does not exist in taxonomy.",
                ];
            }
        }

        // 2. Resolve Subject if specified
        $matchedSubject = null;
        if ($subjectInput !== null) {
            $matchedSubject = $this->matchSubject($subjectInput, $matchedCategory?->id);
            if (!$matchedSubject) {
                $this->refreshTaxonomy();
                $matchedSubject = $this->matchSubject($subjectInput, $matchedCategory?->id);
            }

            if (!$matchedSubject) {
                // Check if subject exists under another category to provide an informative mismatch error
                $anySubject = $this->matchSubject($subjectInput, null);
                if ($anySubject && $matchedCategory) {
                    return [
                        'success' => false,
                        'error' => "Subject '{$anySubject->name}' does not belong to category '{$matchedCategory->name}'.",
                    ];
                }

                return [
                    'success' => false,
                    'error' => "Unknown subject '{$subjectInput}'. Subject does not exist in taxonomy.",
                ];
            }

            if ($matchedCategory && (int) $matchedSubject->category_id !== (int) $matchedCategory->id) {
                return [
                    'success' => false,
                    'error' => "Subject '{$matchedSubject->name}' does not belong to category '{$matchedCategory->name}'.",
                ];
            }
        }

        // 3. Resolve Topic
        if ($topicInput === '') {
            return [
                'success' => false,
                'error' => 'Topic name cannot be empty.',
            ];
        }

        $matchingTopics = $this->findMatchingTopics($topicInput);
        if ($matchingTopics->isEmpty()) {
            // Refresh cache to pick up any newly created topics
            $this->refreshTaxonomy();
            $matchingTopics = $this->findMatchingTopics($topicInput);
        }

        if ($matchingTopics->isEmpty()) {
            $catLabel = '';
            if ($matchedCategory && $matchedSubject) {
                $catLabel = " under category '{$matchedCategory->name}' and subject '{$matchedSubject->name}'";
            } elseif ($matchedCategory) {
                $catLabel = " under category '{$matchedCategory->name}'";
            } elseif ($categoryInput !== '') {
                $catLabel = " for topic column '{$categoryInput}'";
            }

            return [
                'success' => false,
                'error' => "Unknown topic '{$topicInput}'{$catLabel}. Topic does not match any existing topic in taxonomy.",
            ];
        }

        // 4. Validate category relationship and disambiguate
        if ($matchedCategory) {
            $categoryMatches = $matchingTopics->filter(fn (Topic $t) => (int) $t->category_id === (int) $matchedCategory->id);
            if ($categoryMatches->isEmpty()) {
                $first = $matchingTopics->first();
                $actualCat = $first->category?->name ?? 'Unknown';
                return [
                    'success' => false,
                    'error' => "Category mismatch: topic '{$first->name}' belongs to category '{$actualCat}', but row specifies '{$matchedCategory->name}'.",
                ];
            }
            $matchingTopics = $categoryMatches;
        }

        // 5. Validate subject relationship and disambiguate
        if ($matchedSubject) {
            $subjectMatches = $matchingTopics->filter(fn (Topic $t) => (int) $t->subject_id === (int) $matchedSubject->id);
            if ($subjectMatches->isEmpty()) {
                $first = $matchingTopics->first();
                $actualSub = $first->subject?->name ?? 'None';
                return [
                    'success' => false,
                    'error' => "Subject mismatch: topic '{$first->name}' belongs to subject '{$actualSub}', but row specifies '{$matchedSubject->name}'.",
                ];
            }
            $matchingTopics = $subjectMatches;
        }

        // 6. Check for remaining ambiguity
        if ($matchingTopics->count() > 1) {
            return [
                'success' => false,
                'error' => "Ambiguous topic mapping: multiple taxonomy records match '{$topicInput}'. Please specify the exact category and subject.",
            ];
        }

        /** @var Topic $topic */
        $topic = $matchingTopics->first();

        return [
            'success' => true,
            'category_id' => (int) $topic->category_id,
            'subject_id' => $topic->subject_id ? (int) $topic->subject_id : null,
            'topic_id' => (int) $topic->id,
            'topic' => $topic,
        ];
    }

    /**
     * Match category by name, slug or known acronyms with robust normalization.
     */
    public function matchCategory(string $name): ?Category
    {
        $clean = trim($name);
        if ($clean === '') {
            return null;
        }

        $norm = $this->normalizeString($clean);
        if ($norm === 'gk') {
            $norm = 'general knowledge';
        }

        $key = $this->canonicalKey($norm);
        $slug = Str::slug($norm);

        return $this->categories->first(function (Category $cat) use ($norm, $key, $slug) {
            $catNorm = $this->normalizeString($cat->name);
            $catKey = $this->canonicalKey($cat->name);

            return $catNorm === $norm
                || $catKey === $key
                || $cat->slug === $slug
                || Str::slug($cat->name) === $slug;
        });
    }

    /**
     * Match subject by name or slug, optionally scoped to a category.
     */
    public function matchSubject(string $name, ?int $categoryId = null): ?Subject
    {
        $clean = trim($name);
        if ($clean === '') {
            return null;
        }

        $norm = $this->normalizeString($clean);
        $key = $this->canonicalKey($norm);
        $slug = Str::slug($norm);

        return $this->subjects->first(function (Subject $sub) use ($norm, $key, $slug, $categoryId) {
            if ($categoryId !== null && (int) $sub->category_id !== $categoryId) {
                return false;
            }

            $subNorm = $this->normalizeString($sub->name);
            $subKey = $this->canonicalKey($sub->name);

            return $subNorm === $norm
                || $subKey === $key
                || $sub->slug === $slug
                || Str::slug($sub->name) === $slug;
        });
    }

    /**
     * Find topics matching candidate string with space, dash, and symbol normalization.
     *
     * @return Collection<int, Topic>
     */
    public function findMatchingTopics(string $candidate): Collection
    {
        $clean = trim($candidate);
        if ($clean === '') {
            return new Collection();
        }

        $norm = $this->normalizeString($clean);
        $key = $this->canonicalKey($norm);
        $slug = Str::slug($norm);

        // Also prepare variant stripping trailing suffixes like " - cross-link"
        $strippedNorm = preg_replace('/\s*[-–—]\s*cross-?link\b/i', '', $norm) ?? $norm;
        $strippedKey = $this->canonicalKey($strippedNorm);
        $strippedSlug = Str::slug($strippedNorm);

        return $this->topics->filter(function (Topic $topic) use ($norm, $key, $slug, $strippedNorm, $strippedKey, $strippedSlug) {
            $tNorm = $this->normalizeString($topic->name);
            $tKey = $this->canonicalKey($topic->name);
            $tSlug = $topic->slug;

            // Direct normalized or canonical key match
            if ($tNorm === $norm || $tKey === $key || $tSlug === $slug || Str::slug($topic->name) === $slug) {
                return true;
            }

            // Cross-link or suffix-stripped match
            if ($strippedNorm !== $norm) {
                if ($tNorm === $strippedNorm || $tKey === $strippedKey || $tSlug === $strippedSlug) {
                    return true;
                }
            }

            return false;
        });
    }
}
