<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ContactMessage;
use App\Models\StaticPage;
use App\Rules\TurnstileRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles static and informational pages backed by CMS content.
 */
class PageController
{
    /**
     * Display the about page.
     */
    public function about(): View
    {
        $page = StaticPage::where('slug', 'about')->first();

        return view('frontend.pages.about', compact('page'));
    }

    /**
     * Display the contact page.
     */
    public function contact(): View
    {
        $page = StaticPage::where('slug', 'contact')->first();

        return view('frontend.pages.contact', compact('page'));
    }

    /**
     * Handle contact form submission and persist to database.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'enquiry_type' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'page_url' => ['nullable', 'url', 'max:255'],
            'message' => ['required', 'string', 'max:3000'],
            'cf-turnstile-response' => ['required', 'string', new TurnstileRule('contact')],
        ], [
            'cf-turnstile-response.required' => 'Please complete the security verification to send your message.',
        ]);

        $subject = trim($validated['subject']);
        if (!empty($validated['enquiry_type'])) {
            $subject = '[' . ucfirst($validated['enquiry_type']) . '] ' . $subject;
        }

        $messageContent = trim($validated['message']);
        if (!empty($validated['page_url'])) {
            $messageContent .= "\n\nReferenced URL: " . $validated['page_url'];
        }

        ContactMessage::create([
            'name' => trim($validated['name']),
            'email' => trim($validated['email']),
            'subject' => $subject,
            'message' => $messageContent,
            'ip_address' => $request->ip(),
            'is_read' => false,
        ]);

        return back()->with('success', 'Thank you for contacting AptiMedha! Your message has been received and our support team will review it.');
    }

    /**
     * Display the disclaimer page.
     */
    public function disclaimer(): View
    {
        $page = StaticPage::where('slug', 'disclaimer')->first();

        return view('frontend.pages.disclaimer', compact('page'));
    }

    /**
     * Display the terms and conditions page.
     */
    public function terms(): View
    {
        $page = StaticPage::where('slug', 'terms-and-conditions')
            ->orWhere('slug', 'terms')
            ->first();

        return view('frontend.pages.terms', compact('page'));
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        $page = StaticPage::where('slug', 'privacy-policy')
            ->orWhere('slug', 'privacy')
            ->first();

        return view('frontend.pages.privacy', compact('page'));
    }

    /**
     * Display the copyright policy page.
     */
    public function copyright(): View
    {
        $page = StaticPage::where('slug', 'copyright')->first();

        return view('frontend.pages.copyright', compact('page'));
    }
}
