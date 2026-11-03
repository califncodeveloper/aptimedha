<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\View\View;

/**
 * Handles the placement preparation page.
 */
class PlacementController
{
    /**
     * Display the placement preparation page.
     */
    public function __invoke(): View
    {
        return view('frontend.placement.index');
    }
}
