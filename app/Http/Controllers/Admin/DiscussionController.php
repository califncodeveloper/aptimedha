<?php

namespace App\Http\Controllers\Admin;

use App\Models\Discussion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles discussion moderation in the admin panel.
 */
class DiscussionController
{
    /**
     * Display a listing of discussions with search and status filtering.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Discussion::query()->with(['user', 'question.topic', 'question.category'])->latest();

        if (in_array($status, ['approved', 'pending', 'hidden'], true)) {
            $query->where('status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    })
                    ->orWhereHas('question', function ($qq) use ($search) {
                        $qq->where('question_text', 'like', "%{$search}%");
                    });
            });
        }

        $discussions = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Discussion::count(),
            'approved' => Discussion::where('status', 'approved')->count(),
            'pending' => Discussion::where('status', 'pending')->count(),
            'hidden' => Discussion::where('status', 'hidden')->count(),
        ];

        return view('admin.discussions.index', compact('discussions', 'counts', 'status', 'search'));
    }

    /**
     * Update the moderation status of a discussion (approved/hidden/pending).
     */
    public function updateStatus(Request $request, Discussion $discussion): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,pending,hidden'],
        ]);

        $discussion->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Discussion status updated to '{$validated['status']}'.");
    }

    /**
     * Remove the specified discussion post.
     */
    public function destroy(Discussion $discussion): RedirectResponse
    {
        $discussion->delete();

        return redirect()->route('admin.discussions.index')->with('success', 'Discussion post removed successfully.');
    }
}
