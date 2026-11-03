<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles the authenticated user's discussions on MCQs.
 */
class DiscussionController
{
    /**
     * Display a listing of the user's discussions.
     */
    public function index(): View
    {
        return view('user.discussions');
    }

    /**
     * Store a new discussion entry.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement discussion creation logic.

        return redirect()->route('user.discussions');
    }
}
