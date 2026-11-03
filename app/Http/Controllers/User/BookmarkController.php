<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles the authenticated user's bookmarked MCQs.
 */
class BookmarkController
{
    /**
     * Display a listing of the user's bookmarks.
     */
    public function index(): View
    {
        return view('user.bookmarks');
    }

    /**
     * Remove the specified bookmark.
     */
    public function destroy(string $bookmark): RedirectResponse
    {
        // TODO: Implement bookmark deletion logic.

        return redirect()->route('user.bookmarks');
    }
}
