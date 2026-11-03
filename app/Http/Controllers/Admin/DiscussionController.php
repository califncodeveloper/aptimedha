<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles discussion moderation in the admin panel.
 */
class DiscussionController
{
    /**
     * Display a listing of discussions.
     */
    public function index(): View
    {
        return view('admin.discussions.index');
    }

    /**
     * Remove the specified discussion.
     */
    public function destroy(string $discussion): RedirectResponse
    {
        // TODO: Implement discussion deletion logic.

        return redirect()->route('admin.discussions.index');
    }
}
