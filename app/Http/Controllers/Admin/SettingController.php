<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles application settings management in the admin panel.
 */
class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(Request $request): View
    {
        $settings = SettingService::all();
        $activeTab = $request->query('tab', 'contact');

        return view('admin.settings.index', compact('settings', 'activeTab'));
    }

    /**
     * Update the application settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Contact Information
            'support_email' => ['nullable', 'email', 'max:150'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'whatsapp_url' => ['nullable', 'url', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:255'],

            // Social Channels
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],

            // General & Branding
            'site_title' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],

            // Form state
            'active_tab' => ['nullable', 'string', 'max:50'],
        ]);

        SettingService::updateMany($validated);

        $tab = $request->input('active_tab', 'contact');

        return redirect()->route('admin.settings.index', ['tab' => $tab])
            ->with('success', 'Settings updated successfully.');
    }
}
