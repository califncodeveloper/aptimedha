<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles question report management in the admin panel.
 */
class ReportController
{
    /**
     * Display a listing of reports.
     */
    public function index(): View
    {
        return view('admin.reports.index');
    }

    /**
     * Display the specified report.
     */
    public function show(string $report): View
    {
        return view('admin.reports.show');
    }

    /**
     * Update the specified report (e.g., mark as resolved).
     */
    public function update(Request $request, string $report): RedirectResponse
    {
        // TODO: Implement report update logic.

        return redirect()->route('admin.reports.index');
    }
}
