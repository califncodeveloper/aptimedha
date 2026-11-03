<?php

namespace App\Http\Controllers\Admin;

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
    public function index(): View
    {
        return view('admin.categories.index');
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement category creation logic.

        return redirect()->route('admin.categories.index');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(string $category): View
    {
        return view('admin.categories.edit');
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, string $category): RedirectResponse
    {
        // TODO: Implement category update logic.

        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(string $category): RedirectResponse
    {
        // TODO: Implement category deletion logic.

        return redirect()->route('admin.categories.index');
    }
}
