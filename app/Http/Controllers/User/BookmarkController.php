<?php

namespace App\Http\Controllers\User;

use App\Models\Bookmark;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles the authenticated user's bookmarked MCQs.
 */
class BookmarkController
{
    /**
     * Display a listing of the user's bookmarks.
     */
    public function index(Request $request): View
    {
        $bookmarks = $request->user()
            ->bookmarks()
            ->with(['question.category', 'question.topic'])
            ->latest('created_at')
            ->paginate(15);

        return view('user.bookmarks', compact('bookmarks'));
    }

    /**
     * Toggle bookmark state for a question (create if missing, remove if present).
     */
    public function toggle(Request $request, ?Question $question = null): JsonResponse|RedirectResponse
    {
        if (!$question || !$question->exists) {
            $validated = $request->validate([
                'question_id' => ['required', 'integer', 'exists:questions,id'],
            ]);
            $question = Question::findOrFail($validated['question_id']);
        }

        // Inactive questions cannot be bookmarked
        abort_unless($question->is_active, 404, 'Question not found or inactive.');

        $user = $request->user();
        $existing = Bookmark::where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
            $message = 'Question removed from your bookmarks.';
        } else {
            Bookmark::firstOrCreate([
                'user_id' => $user->id,
                'question_id' => $question->id,
            ], [
                'created_at' => now(),
            ]);
            $bookmarked = true;
            $message = 'Question bookmarked successfully.';
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'bookmarked' => $bookmarked,
                'message' => $message,
                'question_id' => $question->id,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove the specified bookmark.
     */
    public function destroy(Bookmark $bookmark, Request $request): JsonResponse|RedirectResponse
    {
        // Enforce strict ownership check
        abort_if($bookmark->user_id !== $request->user()->id, 403, 'Unauthorized.');

        $bookmark->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Bookmark removed successfully.',
            ]);
        }

        return redirect()->route('user.bookmarks')->with('success', 'Bookmark removed successfully.');
    }
}
