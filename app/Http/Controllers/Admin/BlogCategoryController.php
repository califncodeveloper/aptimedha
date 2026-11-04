<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Handles CRUD management for reusable blog categories.
 */
class BlogCategoryController
{
    /**
     * Display a listing of blog categories.
     */
    public function index(Request $request): View
    {
        $categories = BlogCategory::withCount('blogs')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where('name', 'like', $term)
                  ->orWhere('description', 'like', $term);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog_categories.index', compact('categories'));
    }

    /**
     * Store a newly created blog category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:blog_categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        BlogCategory::create($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category "' . $validated['name'] . '" created successfully.');
    }

    /**
     * Show the form for editing the category.
     */
    public function edit(BlogCategory $blogCategory): View
    {
        return view('admin.blog_categories.edit', ['category' => $blogCategory]);
    }

    /**
     * Update the category.
     */
    public function update(Request $request, BlogCategory $blogCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('blog_categories', 'slug')->ignore($blogCategory->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        } else {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $blogCategory->update($validated);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category "' . $blogCategory->name . '" updated successfully.');
    }

    /**
     * Delete the category.
     */
    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        if ($blogCategory->blogs()->count() > 0) {
            return redirect()->route('admin.blog-categories.index')
                ->with('error', 'Cannot delete category "' . $blogCategory->name . '" because it is linked to ' . $blogCategory->blogs()->count() . ' article(s). Please reassign or delete those articles first.');
        }

        $name = $blogCategory->name;
        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category "' . $name . '" deleted successfully.');
    }
}

