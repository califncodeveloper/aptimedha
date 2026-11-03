<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

/**
 * Handles the admin dashboard.
 */
class DashboardController
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard');
    }
}
