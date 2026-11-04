<?php

namespace App\Http\Controllers\User;

use App\Models\Question;
use App\Models\QuestionReport;
use Illuminate\Http\JsonResponse;
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
    public function index(Request $request): View
    {
        $reports = $request->user()
            ->reports()
            ->with(['question.category', 'question.topic'])
            ->latest()
            ->paginate(15);

        return view('user.reports', compact('reports'));
    }

    /**
     * Store a new report for a question.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'issue_type' => [
                'required',
                'string',
                'in:incorrect_answer,incorrect_explanation,wrong_question,broken_image,duplicate_question,typo_formatting,other',
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $question = Question::findOrFail($validated['question_id']);

        // Prevent duplicate spam submissions while previous report is pending or under review
        $existing = QuestionReport::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->whereIn('status', ['new', 'under_review'])
            ->first();

        if ($existing) {
            $msg = 'You have already submitted a report for this question, which is currently under review by our team.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return back()->with('warning', $msg);
        }

        $cleanDesc = trim(strip_tags($validated['description'] ?? ''));

        $report = QuestionReport::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'issue_type' => $validated['issue_type'],
            'description' => $cleanDesc ?: 'No additional notes provided.',
            'status' => 'new',
        ]);

        $message = 'Thank you! Your report has been submitted to our editorial review team.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'report_id' => $report->id,
            ]);
        }

        return back()->with('success', $message);
    }
}
