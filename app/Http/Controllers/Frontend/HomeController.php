<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\View\View;

/**
 * Handles the public home page.
 */
class HomeController
{
    /**
     * Display the home page.
     */
    public function __invoke(): View
    {
        return view('frontend.home');
    }
}
