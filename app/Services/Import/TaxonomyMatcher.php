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
     * Refresh in-memory taxonomy collections.
     */
    public function refreshTaxonomy(): void
    {
        $this->categories = Category::all();
        $this->subjects = Subject::all();
        $this->topics = Topic::with(['category', 'subject'])->get();
    }

    /**
     * Resolve Category, Subject, and Topic from workbook row values.
     *
     * @return array{
     *     success: bool,
     *     category_id?: int,
     *     subject_id?: int|null,
     *     topic_id?: int,
     *     topic?: Topic,
     *     error?: string
     * }
     */
    public function resolve(string $topicCol, string $subtopicCol): array
    {
        $topicCol = trim($topicCol);
        $subtopicCol = trim($subtopicCol);

        if ($topicCol === '' && $subtopicCol === '') {
            return [
                'success' => false,
                'error' => 'Both Topic and Subtopic columns are empty.',
            ];
        }

        // 1. Try to identify parent Category
        $matchedCategory = $this->matchCategory($topicCol);

        // 2. Identify candidate Topic and Subject names from Col B or Col A
        $candidateTopicName = $subtopicCol;
        $candidateSubjectName = null;

        if (str_contains($subtopicCol, '/')) {
            $parts = array_values(array_filter(array_map('trim', explode('/', $subtopicCol))));
            if (!empty($parts)) {
                $candidateTopicName = end($parts);
                if (count($parts) >= 2) {
                    $candidateSubjectName = $parts[count($parts) - 2];
                }
            }
        }

        // 3. Find matching topics
        $matchingTopics = $this->findMatchingTopics($candidateTopicName, $subtopicCol, $topicCol);

        if ($matchingTopics->isEmpty()) {
            $catLabel = $matchedCategory ? " under category '{$matchedCategory->name}'" : " for topic column '{$topicCol}'";
            return [
                'success' => false,
                'error' => "Unknown topic '{$candidateTopicName}'{$catLabel}. Topic does not match any existing topic in taxonomy.",
            ];
        }

        // 4. Disambiguate if category was matched
        if ($matchedCategory) {
            $filtered = $matchingTopics->filter(fn (Topic $t) => (int) $t->category_id === (int) $matchedCategory->id);
            if ($filtered->isEmpty()) {
                $first = $matchingTopics->first();
                $actualCat = $first->category?->name ?? 'Unknown';
                return [
                    'success' => false,
                    'error' => "Category mismatch: topic '{$first->name}' belongs to category '{$actualCat}', but row specifies '{$matchedCategory->name}'.",
                ];
            }
            $matchingTopics = $filtered;
        }

        // 5. Disambiguate if subject was indicated
        if ($candidateSubjectName !== null) {
            $matchedSubject = $this->matchSubject($candidateSubjectName, $matchedCategory?->id);
            if ($matchedSubject) {
                $filtered = $matchingTopics->filter(fn (Topic $t) => (int) $t->subject_id === (int) $matchedSubject->id);
                if ($filtered->isEmpty()) {
                    $first = $matchingTopics->first();
                    $actualSub = $first->subject?->name ?? 'None';
                    return [
                        'success' => false,
                        'error' => "Subject mismatch: topic '{$first->name}' belongs to subject '{$actualSub}', but row specifies '{$matchedSubject->name}'.",
                    ];
                }
                $matchingTopics = $filtered;
            }
        }

        // 6. Check for remaining ambiguity
        if ($matchingTopics->count() > 1) {
            return [
                'success' => false,
                'error' => "Ambiguous topic mapping: multiple taxonomy records match '{$candidateTopicName}'. Please specify the exact category and subject.",
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
     * Match category by name, slug or known acronyms.
     */
    protected function matchCategory(string $name): ?Category
    {
        $clean = strtolower(trim($name));
        if ($clean === '') {
            return null;
        }

        if ($clean === 'gk') {
            $clean = 'general knowledge';
        }

        $slug = Str::slug($clean);

        return $this->categories->first(function (Category $cat) use ($clean, $slug) {
            return strtolower(trim($cat->name)) === $clean || $cat->slug === $slug;
        });
    }

    /**
     * Match subject by name or slug, optionally scoped to a category.
     */
    protected function matchSubject(string $name, ?int $categoryId = null): ?Subject
    {
        $clean = strtolower(trim($name));
        $slug = Str::slug($clean);

        return $this->subjects->first(function (Subject $sub) use ($clean, $slug, $categoryId) {
            if ($categoryId !== null && (int) $sub->category_id !== $categoryId) {
                return false;
            }
            return strtolower(trim($sub->name)) === $clean || $sub->slug === $slug;
        });
    }

    /**
     * Find topics matching candidate strings.
     *
     * @return Collection<int, Topic>
     */
    protected function findMatchingTopics(string $candidate, string $subtopicCol, string $topicCol): Collection
    {
        $cleanCandidate = strtolower(trim($candidate));
        $candidateSlug = Str::slug($cleanCandidate);

        $cleanSub = strtolower(trim($subtopicCol));
        $subSlug = Str::slug($cleanSub);

        $cleanTopic = strtolower(trim($topicCol));
        $topicSlug = Str::slug($cleanTopic);

        return $this->topics->filter(function (Topic $topic) use ($cleanCandidate, $candidateSlug, $cleanSub, $subSlug, $cleanTopic, $topicSlug) {
            $tName = strtolower(trim($topic->name));
            $tSlug = $topic->slug;

            // Direct matches
            if ($tName === $cleanCandidate || $tSlug === $candidateSlug) {
                return true;
            }

            if ($tName === $cleanSub || $tSlug === $subSlug) {
                return true;
            }

            // In case topic was passed in topicCol
            if ($tName === $cleanTopic || $tSlug === $topicSlug) {
                return true;
            }

            return false;
        });
    }
}
