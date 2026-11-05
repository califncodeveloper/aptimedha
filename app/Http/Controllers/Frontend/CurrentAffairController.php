<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Category;
use App\Models\CurrentAffair;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class CurrentAffairController
{
    /**
     * Display the dynamic public Current Affairs listing hub.
     */
    public function index(Request $request): View
    {
        $selectedYear = $request->query('year');
        $selectedMonth = $request->query('month');
        $searchTerm = $request->query('search');

        $query = CurrentAffair::published();

        if (! empty($searchTerm)) {
            $query->search($searchTerm);
        }

        if (! empty($selectedYear) && is_numeric($selectedYear)) {
            $query->year((int) $selectedYear);
        }

        if (! empty($selectedMonth)) {
            $monthNum = is_numeric($selectedMonth) ? (int) $selectedMonth : CurrentAffair::monthNameToNumber((string) $selectedMonth);
            if ($monthNum) {
                $query->month($monthNum);
            }
        }

        $issues = $query->ordered()->paginate(9)->withQueryString();

        // Spotlight / latest issue
        $latestIssue = CurrentAffair::published()->ordered()->first();

        // Available published years for filter tabs
        $availableYears = CurrentAffair::published()
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->all();

        // Related GK / Aptitude categories for cross-navigation
        $relatedCategories = Category::whereIn('slug', [
            'general-knowledge',
            'verbal-ability',
            'logical-reasoning',
            'arithmetic-aptitude',
        ])->orderBy('display_order')->get();

        return view('frontend.current-affairs.index', [
            'issues' => $issues,
            'latestIssue' => $latestIssue,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear ? (int) $selectedYear : null,
            'selectedMonth' => $selectedMonth,
            'searchTerm' => $searchTerm,
            'months' => CurrentAffair::getMonths(),
            'relatedCategories' => $relatedCategories,
        ]);
    }

    /**
     * Display a specific published monthly edition page.
     */
    public function show(string $year, string $month): View
    {
        $monthNum = is_numeric($month) ? (int) $month : CurrentAffair::monthNameToNumber($month);

        if (! $monthNum || ! is_numeric($year)) {
            abort(404);
        }

        $query = CurrentAffair::where('year', (int) $year)->where('month', $monthNum);

        // Allow previewing draft for authenticated admins
        if (! auth()->user()?->isAdmin()) {
            $query->published();
        }

        $issue = $query->firstOrFail();

        // Other issues in the same year
        $otherIssues = CurrentAffair::published()
            ->where('id', '!=', $issue->id)
            ->where('year', $issue->year)
            ->ordered()
            ->take(3)
            ->get();

        if ($otherIssues->isEmpty()) {
            $otherIssues = CurrentAffair::published()
                ->where('id', '!=', $issue->id)
                ->ordered()
                ->take(3)
                ->get();
        }

        return view('frontend.current-affairs.show', [
            'issue' => $issue,
            'otherIssues' => $otherIssues,
        ]);
    }

    /**
     * Stream or deliver PDF for inline browser viewing.
     */
    public function preview(CurrentAffair $currentAffair): Response|RedirectResponse|BinaryFileResponse
    {
        if (! $currentAffair->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        $pdfPath = $currentAffair->pdf_path;
        if (empty($pdfPath)) {
            abort(404, 'PDF file path is not available.');
        }

        $filename = $currentAffair->getPdfDownloadFilename();

        // Local public storage disk
        if (Storage::disk('public')->exists($pdfPath)) {
            return Storage::disk('public')->response(
                $pdfPath,
                $filename,
                [
                    'Content-Type' => 'application/pdf',
                    'X-Content-Type-Options' => 'nosniff',
                ],
                'inline'
            );
        }

        // Local public directory
        if (file_exists(public_path($pdfPath))) {
            return response()->file(
                public_path($pdfPath),
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
                    'X-Content-Type-Options' => 'nosniff',
                ]
            );
        }

        // Cloudinary / remote URL
        if (str_starts_with($pdfPath, 'http://') || str_starts_with($pdfPath, 'https://')) {
            return redirect()->away($pdfPath);
        }

        abort(404, 'The requested PDF document could not be located on the server.');
    }

    /**
     * Deliver PDF document download and increment downloads counter.
     */
    public function download(CurrentAffair $currentAffair): Response|RedirectResponse|BinaryFileResponse
    {
        if (! $currentAffair->is_published && ! auth()->user()?->isAdmin()) {
            abort(404);
        }

        $pdfPath = $currentAffair->pdf_path;
        if (empty($pdfPath)) {
            abort(404, 'PDF file path is not available.');
        }

        // Safely increment download counter
        $currentAffair->increment('downloads_count');

        $downloadFilename = $currentAffair->getPdfDownloadFilename();

        // Local public disk
        if (Storage::disk('public')->exists($pdfPath)) {
            return Storage::disk('public')->download($pdfPath, $downloadFilename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Public path
        if (file_exists(public_path($pdfPath))) {
            return response()->download(public_path($pdfPath), $downloadFilename, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        // Cloudinary / remote URL: add fl_attachment transformation to force download
        if (str_starts_with($pdfPath, 'http://') || str_starts_with($pdfPath, 'https://')) {
            if (str_contains($pdfPath, '/upload/')) {
                $forcedDownloadUrl = str_replace(
                    '/upload/',
                    '/upload/fl_attachment:' . rawurlencode(pathinfo($downloadFilename, PATHINFO_FILENAME)) . '/',
                    $pdfPath
                );
                return redirect()->away($forcedDownloadUrl);
            }

            return redirect()->away($pdfPath);
        }

        abort(404, 'The requested PDF document could not be located on the server.');
    }
}
