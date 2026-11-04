<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles the authenticated user's dashboard with real database metrics.
 */
class DashboardController
{
    /**
     * Display the user dashboard.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $bookmarksCount = $user->bookmarks()->count();
        $discussionsCount = $user->discussions()->count();
        $reportsCount = $user->reports()->count();

        $recentBookmarks = $user->bookmarks()
            ->with(['question.category', 'question.topic'])
            ->latest('created_at')
            ->take(5)
            ->get();

        $recentDiscussions = $user->discussions()
            ->with(['question.category', 'question.topic'])
            ->latest()
            ->take(5)
            ->get();

        $recentReports = $user->reports()
            ->with(['question.category', 'question.topic'])
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'bookmarksCount',
            'discussionsCount',
            'reportsCount',
            'recentBookmarks',
            'recentDiscussions',
            'recentReports'
        ));
    }
}
