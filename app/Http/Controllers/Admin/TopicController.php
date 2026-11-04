<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\TopicRequest;
use App\Models\Category;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles CRUD operations for topics in the admin panel.
 */
class TopicController
{
    /**
     * Display a listing of topics.
     */
    public function index(Request $request): View
    {
        $query = Topic::query()
            ->with(['category', 'subject'])
            ->withCount('questions');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($subjectId = $request->input('subject_id')) {
            $query->where('subject_id', $subjectId);
        }

        $topics = $query->orderBy('category_id')->orderBy('subject_id')->orderBy('display_order')->orderBy('name')->paginate(20)->withQueryString();
        $categories = Category::ordered()->get(['id', 'name']);
        $subjects = Subject::ordered()->get(['id', 'name', 'category_id']);

        return view('admin.topics.index', compact('topics', 'categories', 'subjects', 'search', 'categoryId', 'subjectId'));
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create(Request $request): View
    {
        $categories = Category::ordered()->get(['id', 'name']);
        $subjects = Subject::ordered()->get(['id', 'name', 'category_id']);

        $selectedCategoryId = $request->input('category_id', $categories->first()?->id);
        $selectedSubjectId = $request->input('subject_id');

        $nextOrder = (Topic::where('category_id', $selectedCategoryId)->max('display_order') ?? 0) + 1;

        return view('admin.topics.create', compact('categories', 'subjects', 'selectedCategoryId', 'selectedSubjectId', 'nextOrder'));
    }

    /**
     * Store a newly created topic.
     */
    public function store(TopicRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Topic::generateUniqueSlug($validated['name']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;
        $validated['total_questions_count'] = 0;

        Topic::create($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');
    }

    /**
     * Show the form for editing the specified topic.
     */
    public function edit(Topic $topic): View
    {
        $categories = Category::ordered()->get(['id', 'name']);
        $subjects = Subject::ordered()->get(['id', 'name', 'category_id']);

        return view('admin.topics.edit', compact('topic', 'categories', 'subjects'));
    }

    /**
     * Update the specified topic.
     */
    public function update(TopicRequest $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Topic::generateUniqueSlug($validated['name'], $topic->id);
        }

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $topic->update($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(Topic $topic): RedirectResponse
    {
        if ($topic->questions()->exists()) {
            return redirect()->route('admin.topics.index')->with(
                'error',
                'Cannot delete topic "' . $topic->name . '" because it has ' . $topic->questions()->count() . ' associated MCQ(s). Deactivate it instead.'
            );
        }

        $topic->delete();

        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted successfully.');
    }
}
