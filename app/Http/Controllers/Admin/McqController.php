<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles CRUD operations for MCQs in the admin panel.
 */
class McqController
{
    /**
     * Display a listing of MCQs.
     */
    public function index(): View
    {
        return view('admin.mcqs.index');
    }

    /**
     * Show the form for creating a new MCQ.
     */
    public function create(): View
    {
        return view('admin.mcqs.create');
    }

    /**
     * Store a newly created MCQ.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement MCQ creation logic.

        return redirect()->route('admin.mcqs.index');
    }

    /**
     * Show the form for editing the specified MCQ.
     */
    public function edit(string $mcq): View
    {
        return view('admin.mcqs.edit');
    }

    /**
     * Update the specified MCQ.
     */
    public function update(Request $request, string $mcq): RedirectResponse
    {
        // TODO: Implement MCQ update logic.

        return redirect()->route('admin.mcqs.index');
    }

    /**
     * Remove the specified MCQ.
     */
    public function destroy(string $mcq): RedirectResponse
    {
        // TODO: Implement MCQ deletion logic.

        return redirect()->route('admin.mcqs.index');
    }
}
