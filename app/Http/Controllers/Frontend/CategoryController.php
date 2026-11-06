<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
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
        $categories = Category::active()
            ->ordered()
            ->with([
                'subjects' => fn ($q) => $q->active()->ordered(),
                'topics' => fn ($q) => $q->active()->ordered(),
            ])
            ->withCount([
                'questions' => fn ($q) => $q->active(),
            ])
            ->get();

        return view('frontend.categories.index', compact('categories'));
    }

    /**
     * Display the specified category with active subjects and topics.
     */
    public function show(string $category): View|RedirectResponse
    {
        // Enforce lowercase slug: 301 redirect if uppercase
        if ($category !== strtolower($category)) {
            return redirect()->route('categories.show', strtolower($category), 301);
        }

        // Backward compatibility: If numeric ID was requested, 301 redirect to canonical slug
        if (is_numeric($category)) {
            $cat = Category::active()->find((int) $category);
            if ($cat) {
                return redirect()->route('categories.show', $cat->slug, 301);
            }
            abort(404, 'Category not found.');
        }

        $cat = Category::where('slug', $category)
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
            ->firstOrFail();

        return view('frontend.categories.show', ['category' => $cat]);
    }
}
