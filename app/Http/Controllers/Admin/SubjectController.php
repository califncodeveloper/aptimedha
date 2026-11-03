<?php

namespace App\Http\Controllers\Admin;

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
    public function index(): View
    {
        return view('admin.subjects.index');
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create(): View
    {
        return view('admin.subjects.create');
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement subject creation logic.

        return redirect()->route('admin.subjects.index');
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(string $subject): View
    {
        return view('admin.subjects.edit');
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, string $subject): RedirectResponse
    {
        // TODO: Implement subject update logic.

        return redirect()->route('admin.subjects.index');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(string $subject): RedirectResponse
    {
        // TODO: Implement subject deletion logic.

        return redirect()->route('admin.subjects.index');
    }
}
