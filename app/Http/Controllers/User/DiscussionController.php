<?php

namespace App\Http\Controllers\User;

use App\Models\Discussion;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Handles the authenticated user's discussions on MCQs.
 */
class DiscussionController
{
    /**
     * Display a listing of the user's discussions.
     */
    public function index(Request $request): View
    {
        $discussions = $request->user()
            ->discussions()
            ->with(['question.category', 'question.topic', 'replies.user'])
            ->latest()
            ->paginate(15);

        return view('user.discussions', compact('discussions'));
    }

    /**
     * Store a new discussion entry.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'parent_id' => ['nullable', 'integer', 'exists:discussions,id'],
            'comment' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $question = Question::findOrFail($validated['question_id']);

        // Prevent cross-question replies by validating parent discussion ownership
        if (!empty($validated['parent_id'])) {
            $parentDiscussion = Discussion::findOrFail($validated['parent_id']);
            if ((int) $parentDiscussion->question_id !== (int) $question->id) {
                throw ValidationException::withMessages([
                    'parent_id' => ['The selected parent discussion does not belong to this question.'],
                ]);
            }
        }

        // Clean user input
        $cleanComment = trim(strip_tags($validated['comment']));

        if (mb_strlen($cleanComment) < 3) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your comment must contain at least 3 characters of text.',
                ], 422);
            }

            return back()->withErrors(['comment' => 'Your comment must contain at least 3 characters of text.']);
        }

        $discussion = Discussion::create([
            'question_id' => $question->id,
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $cleanComment,
            'status' => 'approved',
        ]);

        $discussion->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Your comment has been posted successfully.',
                'discussion' => [
                    'id' => $discussion->id,
                    'comment' => $discussion->comment,
                    'author' => $discussion->user->name,
                    'created_at' => $discussion->created_at->diffForHumans(),
                    'is_owner' => true,
                ],
            ]);
        }

        return back()->with('success', 'Your comment has been posted successfully.');
    }

    /**
     * Update an existing discussion post owned by the user.
     */
    public function update(Request $request, Discussion $discussion): JsonResponse|RedirectResponse
    {
        abort_if($discussion->user_id !== $request->user()->id, 403, 'Unauthorized. You can only edit your own posts.');

        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $cleanComment = trim(strip_tags($validated['comment']));

        if (mb_strlen($cleanComment) < 3) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your comment must contain at least 3 characters of text.',
                ], 422);
            }

            return back()->withErrors(['comment' => 'Your comment must contain at least 3 characters of text.']);
        }

        $discussion->update([
            'comment' => $cleanComment,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Discussion post updated successfully.',
                'comment' => $discussion->comment,
            ]);
        }

        return back()->with('success', 'Discussion post updated successfully.');
    }

    /**
     * Remove the specified discussion post owned by the user.
     */
    public function destroy(Discussion $discussion, Request $request): JsonResponse|RedirectResponse
    {
        abort_if($discussion->user_id !== $request->user()->id, 403, 'Unauthorized. You can only delete your own posts.');

        $discussion->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Discussion post deleted successfully.',
            ]);
        }

        return redirect()->route('user.discussions')->with('success', 'Discussion post deleted successfully.');
    }
}
