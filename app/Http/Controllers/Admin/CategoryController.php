<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles CRUD operations for categories in the admin panel.
 */
class CategoryController
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $query = Category::query()
            ->withCount(['subjects', 'topics', 'questions']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('display_order')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories', 'search'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        $nextOrder = (Category::max('display_order') ?? 0) + 1;

        return view('admin.categories.create', compact('nextOrder'));
    }

    /**
     * Store a newly created category.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Category::generateUniqueSlug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Category::generateUniqueSlug($validated['name'], $category->id);
        }

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $hasSubjects = $category->subjects()->exists();
        $hasTopics = $category->topics()->exists();
        $hasQuestions = $category->questions()->exists();

        if ($hasSubjects || $hasTopics || $hasQuestions) {
            $counts = [];
            if ($hasSubjects) $counts[] = $category->subjects()->count() . ' subject(s)';
            if ($hasTopics) $counts[] = $category->topics()->count() . ' topic(s)';
            if ($hasQuestions) $counts[] = $category->questions()->count() . ' question(s)';

            return redirect()->route('admin.categories.index')->with(
                'error',
                'Cannot delete category "' . $category->name . '" because it contains dependent content (' . implode(', ', $counts) . '). Deactivate it instead.'
            );
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
