<?php

namespace App\Http\Controllers\Admin;

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
    public function index(): View
    {
        return view('admin.topics.index');
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create(): View
    {
        return view('admin.topics.create');
    }

    /**
     * Store a newly created topic.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement topic creation logic.

        return redirect()->route('admin.topics.index');
    }

    /**
     * Show the form for editing the specified topic.
     */
    public function edit(string $topic): View
    {
        return view('admin.topics.edit');
    }

    /**
     * Update the specified topic.
     */
    public function update(Request $request, string $topic): RedirectResponse
    {
        // TODO: Implement topic update logic.

        return redirect()->route('admin.topics.index');
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(string $topic): RedirectResponse
    {
        // TODO: Implement topic deletion logic.

        return redirect()->route('admin.topics.index');
    }
}
