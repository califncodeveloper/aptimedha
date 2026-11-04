<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
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
        $categories = Category::active()
            ->ordered()
            ->withCount(['questions' => fn ($q) => $q->active()])
            ->take(9)
            ->get();

        $totalActiveQuestions = Question::active()->count();
        $totalCategories = Category::active()->count();
        $totalSubjects = Subject::active()->count();
        $totalTopics = Topic::active()->count();

        $latestBlogs = Blog::published()
            ->with(['category', 'author'])
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('frontend.home', compact(
            'categories',
            'totalActiveQuestions',
            'totalCategories',
            'totalSubjects',
            'totalTopics',
            'latestBlogs'
        ));
    }
}
