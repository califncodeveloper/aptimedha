<?php

namespace App\Http\Controllers\Admin;

use App\Models\StaticPage;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles static and policy page management in the admin panel.
 */
class PageController
{
    /**
     * Display a listing of pages.
     */
    public function index(): View
    {
        $pages = StaticPage::orderBy('id')->get();

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(StaticPage $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, StaticPage $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['content'] = HtmlSanitizer::clean($validated['content']);

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page "' . $page->title . '" updated successfully.');
    }
}
