<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Handles the public home page with dynamic database-driven categories, questions, and articles.
 */
class HomeController
{
    /**
     * Display the home page.
     */
    public function __invoke(): View
    {
        $categories = Cache::remember('home_categories', 300, function () {
            return Category::active()
                ->ordered()
                ->withCount(['questions' => fn ($q) => $q->active()])
                ->take(9)
                ->get();
        });

        $counts = Cache::remember('home_stat_counts', 300, function () {
            return [
                'totalActiveQuestions' => Question::active()->count(),
                'totalCategories' => Category::active()->count(),
                'totalSubjects' => Subject::active()->count(),
                'totalTopics' => Topic::active()->count(),
            ];
        });

        $latestBlogs = Cache::remember('home_latest_blogs', 300, function () {
            return Blog::published()
                ->with(['category', 'author'])
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        return view('frontend.home', [
            'categories' => $categories,
            'totalActiveQuestions' => $counts['totalActiveQuestions'],
            'totalCategories' => $counts['totalCategories'],
            'totalSubjects' => $counts['totalSubjects'],
            'totalTopics' => $counts['totalTopics'],
            'latestBlogs' => $latestBlogs,
        ]);
    }
}
