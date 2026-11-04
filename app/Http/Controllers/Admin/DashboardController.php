<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Models\Blog;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Discussion;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Handles the admin dashboard with 100% database-backed metrics and analytics.
 */
class DashboardController
{
    /**
     * Display the admin dashboard.
     */
    public function __invoke(): View
    {
        // 1. Users Metrics
        $totalUsers = User::count();
        $studentCount = User::where('role', UserRole::User->value)->count();
        $adminCount = User::where('role', UserRole::Admin->value)->count();

        // Supported, documented active-user definition: users with bookmarks, discussions, reports, or updated within the last 30 days
        $activeUsersCount = User::where(function ($q) {
            $q->has('bookmarks')
              ->orHas('discussions')
              ->orHas('reports')
              ->orWhere('updated_at', '>=', now()->subDays(30));
        })->count();

        // 2. Question / MCQ Metrics
        $totalQuestions = Question::count();
        $activeQuestions = Question::where('is_active', true)->count();
        $inactiveQuestions = Question::where('is_active', false)->count();
        $totalQuestionViews = (int) Question::sum('views_count');

        // 3. Taxonomy Hierarchy Metrics
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $totalSubjects = Subject::count();
        $activeSubjects = Subject::where('is_active', true)->count();
        $totalTopics = Topic::count();
        $activeTopics = Topic::where('is_active', true)->count();

        // 4. Blog & Content Metrics
        $totalBlogs = Blog::count();
        $publishedBlogs = Blog::where('status', 'published')->count();
        $draftBlogs = Blog::where('status', 'draft')->count();
        $totalBlogViews = (int) Blog::sum('views_count');

        // 5. User Engagement & Moderation
        $totalDiscussions = Discussion::count();
        $pendingDiscussions = Discussion::where('status', 'pending')->count();
        $totalBookmarks = Bookmark::count();
        $totalReports = QuestionReport::count();
        $pendingReports = QuestionReport::whereIn('status', ['new', 'under_review'])->count();
        $resolvedReports = QuestionReport::where('status', 'resolved')->count();
        $totalContacts = ContactMessage::count();
        $unreadContacts = ContactMessage::where('is_read', false)->count();

        // 6. Recent Database Lists
        $recentQuestions = Question::with(['category', 'subject', 'topic'])
            ->latest('id')
            ->take(5)
            ->get();

        $pendingReportsList = QuestionReport::with(['question', 'user'])
            ->whereIn('status', ['new', 'under_review'])
            ->latest('id')
            ->take(5)
            ->get();

        $recentDiscussionsList = Discussion::with(['question', 'user'])
            ->latest('id')
            ->take(4)
            ->get();

        // 7. Analytics Data Aggregation (Actual Database Values)
        $categoryBreakdown = Category::withCount(['questions' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('questions_count')
            ->take(6)
            ->get();

        $reportStatusBreakdown = QuestionReport::select('status', DB::raw('count(*) as aggregate_count'))
            ->groupBy('status')
            ->pluck('aggregate_count', 'status')
            ->all();

        // Past 7 days user registration activity trend
        $userTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $userTrend[] = [
                'day' => now()->subDays($i)->format('D, M d'),
                'count' => $count,
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'studentCount',
            'adminCount',
            'activeUsersCount',
            'totalQuestions',
            'activeQuestions',
            'inactiveQuestions',
            'totalQuestionViews',
            'totalCategories',
            'activeCategories',
            'totalSubjects',
            'activeSubjects',
            'totalTopics',
            'activeTopics',
            'totalBlogs',
            'publishedBlogs',
            'draftBlogs',
            'totalBlogViews',
            'totalDiscussions',
            'pendingDiscussions',
            'totalBookmarks',
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'totalContacts',
            'unreadContacts',
            'recentQuestions',
            'pendingReportsList',
            'recentDiscussionsList',
            'categoryBreakdown',
            'reportStatusBreakdown',
            'userTrend'
        ));
    }
}
