<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles user management in the admin panel.
 */
class UserController
{
    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        return view('admin.users.index');
    }

    /**
     * Display the specified user.
     */
    public function show(string $user): View
    {
        return view('admin.users.show');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $user): View
    {
        return view('admin.users.edit');
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $user): RedirectResponse
    {
        // TODO: Implement user update logic.

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $user): RedirectResponse
    {
        // TODO: Implement user deletion logic.

        return redirect()->route('admin.users.index');
    }
}
