<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles the authenticated user's question reports.
 */
class ReportController
{
    /**
     * Display a listing of the user's reports.
     */
    public function index(): View
    {
        return view('user.reports');
    }

    /**
     * Store a new report for a question.
     */
    public function store(Request $request): RedirectResponse
    {
        // TODO: Implement report creation logic.

        return redirect()->route('user.reports');
    }
}
