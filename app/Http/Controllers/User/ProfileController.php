<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles the authenticated user's profile management.
 */
class ProfileController
{
    /**
     * Display the user's profile.
     */
    public function show(): View
    {
        return view('user.profile');
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        // TODO: Implement profile update logic.

        return redirect()->route('user.profile');
    }
}
