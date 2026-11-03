<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles application settings management in the admin panel.
 */
class SettingController
{
    /**
     * Display the settings page.
     */
    public function index(): View
    {
        return view('admin.settings.index');
    }

    /**
     * Update the application settings.
     */
    public function update(Request $request): RedirectResponse
    {
        // TODO: Implement settings update logic.

        return redirect()->route('admin.settings.index');
    }
}
