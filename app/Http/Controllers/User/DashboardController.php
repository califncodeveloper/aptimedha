<?php

namespace App\Http\Controllers\User;

use Illuminate\View\View;

/**
 * Handles the authenticated user's dashboard.
 */
class DashboardController
{
    /**
     * Display the user dashboard.
     */
    public function __invoke(): View
    {
        return view('user.dashboard');
    }
}
