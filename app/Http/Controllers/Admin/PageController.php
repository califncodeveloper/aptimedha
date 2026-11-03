<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles static page management in the admin panel.
 */
class PageController
{
    /**
     * Display a listing of pages.
     */
    public function index(): View
    {
        return view('admin.pages.index');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(string $page): View
    {
        return view('admin.pages.edit');
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, string $page): RedirectResponse
    {
        // TODO: Implement page update logic.

        return redirect()->route('admin.pages.index');
    }
}
