<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles bulk MCQ import from files in the admin panel.
 */
class McqImportController
{
    /**
     * Show the MCQ import form.
     */
    public function showForm(): View
    {
        return view('admin.mcqs.import');
    }

    /**
     * Handle the MCQ import process.
     */
    public function import(Request $request): RedirectResponse
    {
        // TODO: Implement MCQ import logic.

        return redirect()->route('admin.mcqs.import.form');
    }
}
