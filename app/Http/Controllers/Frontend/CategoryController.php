<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Handles public category browsing and topic taxonomy.
 */
class CategoryController
{
    /**
     * Display a listing of active categories.
     */
    public function index(): View
    {
        $categories = Cache::remember('frontend_categories_index', 300, function () {
            return Category::active()
                ->ordered()
                ->with([
                    'subjects' => fn ($q) => $q->active()->ordered(),
                    'topics' => fn ($q) => $q->active()->ordered(),
                ])
                ->withCount([
                    'questions' => fn ($q) => $q->active(),
                ])
                ->get();
        });

        return view('frontend.categories.index', compact('categories'));
    }

    /**
     * Display the specified category with active subjects and topics.
     */
    public function show(string $category): View
    {
        $cat = Cache::remember("frontend_category_show_{$category}", 300, function () use ($category) {
            return Category::where('slug', $category)
                ->active()
                ->with([
                    'subjects' => fn ($q) => $q->active()->ordered()->with([
                        'topics' => fn ($tq) => $tq->active()->ordered(),
                    ]),
                    'topics' => fn ($q) => $q->whereNull('subject_id')->active()->ordered(),
                ])
                ->withCount([
                    'questions' => fn ($q) => $q->active(),
                ])
                ->first();
        });

        if (! $cat) {
            abort(404, 'Category not found or inactive.');
        }

        return view('frontend.categories.show', ['category' => $cat]);
    }
}
