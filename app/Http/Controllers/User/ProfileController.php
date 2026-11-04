<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Handles the authenticated user's profile management.
 */
class ProfileController
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        return view('user.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Handle password update if password field is submitted
        if ($request->filled('new_password')) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'new_password' => ['required', 'string', Password::min(8), 'confirmed'],
            ]);

            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            return redirect()->route('user.profile')->with('status', 'Password updated successfully.');
        }

        // Handle profile personal details update
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:users,username,'.$user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        // Explicitly update only safe attributes; role can never be updated here
        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('user.profile')->with('status', 'Profile updated successfully.');
    }
}
