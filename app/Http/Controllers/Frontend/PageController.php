<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles static and informational pages.
 */
class PageController
{
    /**
     * Display the about page.
     */
    public function about(): View
    {
        return view('frontend.pages.about');
    }

    /**
     * Display the contact page.
     */
    public function contact(): View
    {
        return view('frontend.pages.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'enquiry_type' => 'required|string',
            'subject' => 'required|string|max:200',
            'page_url' => 'nullable|url|max:255',
            'message' => 'required|string|max:3000',
        ]);

        return back()->with('success', 'Thank you for contacting AptiMedha. Your message has been received. We will review your enquiry and reply where a response is needed.');
    }

    /**
     * Display the disclaimer page.
     */
    public function disclaimer(): View
    {
        return view('frontend.pages.disclaimer');
    }

    /**
     * Display the terms and conditions page.
     */
    public function terms(): View
    {
        return view('frontend.pages.terms');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        return view('frontend.pages.privacy');
    }

    /**
     * Display the copyright policy page.
     */
    public function copyright(): View
    {
        return view('frontend.pages.copyright');
    }
}
