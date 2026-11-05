<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Handles public MCQ browsing and interactive practice interface.
 */
class McqController
{
    /**
     * Display a listing of MCQs (practice view).
     */
    public function index(Request $request): View
    {
        $selectedCategorySlug = $request->query('category');
        $selectedTopicSlug = $request->query('topic') ?? $request->query('q') ?? $request->query('search');

        // Resolve Category from real database records
        $category = null;
        $topic = null;

        if ($selectedCategorySlug) {
            $category = Category::active()
                ->where(function ($q) use ($selectedCategorySlug) {
                    $q->where('slug', $selectedCategorySlug);
                    if (is_numeric($selectedCategorySlug)) {
                        $q->orWhere('id', (int) $selectedCategorySlug);
                    }
                })->first();
        }

        // If no explicit category was passed, or topic belongs to another category, resolve via topic
        if ($selectedTopicSlug) {
            $matchedTopicQuery = Topic::active()
                ->where(function ($q) use ($selectedTopicSlug) {
                    $q->where('slug', $selectedTopicSlug)
                        ->orWhere('name', 'LIKE', '%' . trim($selectedTopicSlug) . '%');
                    if (is_numeric($selectedTopicSlug)) {
                        $q->orWhere('id', (int) $selectedTopicSlug);
                    }
                });

            if ($category) {
                // Check if topic exists in this category
                $topic = (clone $matchedTopicQuery)->where('category_id', $category->id)->first();
            }

            if (!$topic) {
                // Find topic across all categories
                $topic = $matchedTopicQuery->with('category')->first();
                if ($topic && $topic->category) {
                    $category = $topic->category;
                }
            }
        }

        if (!$category) {
            $category = Category::active()->ordered()->first();
        }

        $categoryName = $category?->name ?? 'Aptitude Practice';
        $categorySlug = $category?->slug ?? '';

        // Resolve Sidebar Topics for this Category
        $sidebarTopics = collect();
        if ($category) {
            $sidebarTopics = Cache::remember("cat_{$category->id}_sidebar_topics", 300, function () use ($category) {
                return Topic::where('category_id', $category->id)
                    ->active()
                    ->ordered()
                    ->withCount(['questions' => fn ($q) => $q->active()])
                    ->get();
            });
        }

        // Fallback topic if not resolved yet
        if (!$topic && $sidebarTopics->isNotEmpty()) {
            $topic = $sidebarTopics->first();
        }

        $topicName = $topic?->name ?? 'No Topics Available';
        $topicSlug = $topic?->slug ?? '';

        // Retrieve active questions only, 10 per page
        if ($topic) {
            $questionsQuery = Question::where('topic_id', $topic->id)
                ->active()
                ->withCount(['discussions' => fn ($q) => $q->where('status', 'approved')])
                ->orderBy('question_number');

            if ($section = $request->query('section')) {
                $questionsQuery->where('section_number', (int) $section);
            }

            $mcqs = $questionsQuery->paginate(10)->withQueryString();
            $totalQuestions = $mcqs->total();
        } else {
            $mcqs = Question::whereRaw('0 = 1')->paginate(10);
            $totalQuestions = 0;
        }

        $currentSection = (int) $request->query('section', 1);
        $bookmarkedIds = Auth::check()
            ? Auth::user()->bookmarks()->pluck('question_id')->all()
            : [];

        return view('frontend.mcq.index', [
            'mcqs' => $mcqs,
            'category' => $category,
            'categoryName' => $categoryName,
            'categorySlug' => $categorySlug,
            'topic' => $topic,
            'topicName' => $topicName,
            'topicSlug' => $topicSlug,
            'sidebarTopics' => $sidebarTopics,
            'currentSection' => $currentSection,
            'totalQuestions' => $totalQuestions,
            'bookmarkedIds' => $bookmarkedIds,
        ]);
    }

    /**
     * Display a single MCQ.
     */
    public function show(string $mcq): View
    {
        if (is_numeric($mcq)) {
            $question = Question::with(['category', 'subject', 'topic'])
                ->active()
                ->find($mcq);

            if (!$question) {
                abort(404, 'MCQ not found or inactive.');
            }

            // Deduplicated view tracking: increment once per session for active questions
            $viewKey = 'viewed_question_' . $question->id;
            if (!\Illuminate\Support\Facades\Session::has($viewKey)) {
                $question->increment('views_count');
                \Illuminate\Support\Facades\Session::put($viewKey, true);
            }
        } elseif ($mcq === 'sample-mcq') {
            $question = Question::with(['category', 'subject', 'topic'])->active()->first();
            if (!$question) {
                $question = new Question([
                    'question_number' => 1,
                    'section_number' => 1,
                    'question_text' => 'What is 25% of 240?',
                    'option_a' => '40',
                    'option_b' => '50',
                    'option_c' => '60',
                    'option_d' => '80',
                    'correct_option' => 'C',
                    'explanation' => '25% = 25/100 = 1/4. (1/4) * 240 = 60.',
                    'is_active' => true,
                ]);
                $question->id = 1;
            }
        } else {
            abort(404, 'MCQ not found.');
        }

        $previousMcq = Question::active()
            ->where('topic_id', $question->topic_id)
            ->where('question_number', '<', $question->question_number)
            ->orderBy('question_number', 'desc')
            ->first();

        $nextMcq = Question::active()
            ->where('topic_id', $question->topic_id)
            ->where('question_number', '>', $question->question_number)
            ->orderBy('question_number', 'asc')
            ->first();

        $discussions = $question->exists
            ? $question->discussions()
                ->approved()
                ->topLevel()
                ->with([
                    'user',
                    'replies' => fn ($q) => $q->approved()->with('user'),
                ])
                ->latest()
                ->get()
            : collect();

        $isBookmarked = Auth::check() && $question->exists && $question->isBookmarkedBy(Auth::user());
        $hasPendingReport = Auth::check() && $question->exists
            ? $question->reports()->where('user_id', Auth::id())->whereIn('status', ['new', 'under_review'])->exists()
            : false;

        return view('frontend.mcq.show', [
            'mcq' => $question,
            'previousMcq' => $previousMcq,
            'nextMcq' => $nextMcq,
            'discussions' => $discussions,
            'isBookmarked' => $isBookmarked,
            'hasPendingReport' => $hasPendingReport,
        ]);
    }
}
