<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\SubjectRequest;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles CRUD operations for subjects in the admin panel.
 */
class SubjectController
{
    /**
     * Display a listing of subjects.
     */
    public function index(Request $request): View
    {
        $query = Subject::query()
            ->with('category')
            ->withCount(['topics', 'questions']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $subjects = $query->orderBy('category_id')->orderBy('display_order')->orderBy('name')->paginate(15)->withQueryString();
        $categories = Category::ordered()->get(['id', 'name']);

        return view('admin.subjects.index', compact('subjects', 'categories', 'search', 'categoryId'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(Request $request): View
    {
        $categories = Category::ordered()->get(['id', 'name']);
        $selectedCategoryId = $request->input('category_id', $categories->first()?->id);
        $nextOrder = (Subject::where('category_id', $selectedCategoryId)->max('display_order') ?? 0) + 1;

        return view('admin.subjects.create', compact('categories', 'selectedCategoryId', 'nextOrder'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(SubjectRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Subject::generateUniqueSlug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully.');
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject): View
    {
        $categories = Category::ordered()->get(['id', 'name']);

        return view('admin.subjects.edit', compact('subject', 'categories'));
    }

    /**
     * Update the specified subject.
     */
    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Subject::generateUniqueSlug($validated['name'], $subject->id);
        }

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        $hasTopics = $subject->topics()->exists();
        $hasQuestions = $subject->questions()->exists();

        if ($hasTopics || $hasQuestions) {
            $counts = [];
            if ($hasTopics) $counts[] = $subject->topics()->count() . ' topic(s)';
            if ($hasQuestions) $counts[] = $subject->questions()->count() . ' question(s)';

            return redirect()->route('admin.subjects.index')->with(
                'error',
                'Cannot delete subject "' . $subject->name . '" because it contains dependent content (' . implode(', ', $counts) . '). Deactivate it instead.'
            );
        }

        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Subject deleted successfully.');
    }
}
