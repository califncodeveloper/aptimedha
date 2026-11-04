<?php

namespace App\Http\Controllers\Admin;

use App\Models\QuestionReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles question report management and moderation in the admin panel.
 */
class ReportController
{
    /**
     * Display a listing of reports with filters.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $issueType = $request->query('issue_type');
        $search = $request->query('search');

        $query = QuestionReport::query()
            ->with(['user', 'question.topic', 'question.category', 'resolver'])
            ->latest();

        if (in_array($status, ['new', 'under_review', 'resolved', 'closed'], true)) {
            $query->where('status', $status);
        }

        if (!empty($issueType)) {
            $query->where('issue_type', $issueType);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('admin_notes', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('question', function ($qq) use ($search) {
                        $qq->where('question_text', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                    });
            });
        }

        $reports = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => QuestionReport::count(),
            'new' => QuestionReport::where('status', 'new')->count(),
            'under_review' => QuestionReport::where('status', 'under_review')->count(),
            'resolved' => QuestionReport::where('status', 'resolved')->count(),
            'closed' => QuestionReport::where('status', 'closed')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'counts', 'status', 'issueType', 'search'));
    }

    /**
     * Display the specified report details.
     */
    public function show(QuestionReport $report): View
    {
        $report->load(['user', 'question.topic', 'question.category', 'resolver']);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Update the report status and add administrator resolution notes.
     */
    public function update(Request $request, QuestionReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,under_review,resolved,closed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $updateData = [
            'status' => $validated['status'],
            'admin_notes' => trim($validated['admin_notes'] ?? '') ?: null,
        ];

        // Track who resolved the report and when
        if (in_array($validated['status'], ['resolved', 'closed'], true)) {
            $updateData['resolved_by'] = $request->user()->id;
            $updateData['resolved_at'] = now();
        } else {
            // Reopened
            if ($report->status === 'resolved' || $report->status === 'closed') {
                $updateData['resolved_by'] = null;
                $updateData['resolved_at'] = null;
            }
        }

        $report->update($updateData);

        return redirect()->route('admin.reports.show', $report)
            ->with('success', "Report #REP-{$report->id} updated successfully.");
    }
}
