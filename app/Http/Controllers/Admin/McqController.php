<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\QuestionRequest;
use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Handles CRUD operations for MCQs in the admin panel.
 */
class McqController
{
    public function __construct(
        protected ?CloudinaryService $cloudinaryService = null
    ) {}

    protected function getCloudinaryService(): CloudinaryService
    {
        return $this->cloudinaryService ?? app(CloudinaryService::class);
    }
    /**
     * Display a listing of MCQs.
     */
    public function index(Request $request): View
    {
        $query = Question::query()
            ->with(['category', 'subject', 'topic']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%");
                if (is_numeric($search)) {
                    $q->orWhere('question_number', (int) $search);
                }
            });
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($subjectId = $request->input('subject_id')) {
            $query->where('subject_id', $subjectId);
        }

        if ($topicId = $request->input('topic_id')) {
            $query->where('topic_id', $topicId);
        }

        $status = $request->input('status');
        if ($status !== null && $status !== '') {
            if ($status === '1' || $status === 'active' || $status === 'published') {
                $query->where('is_active', true);
            } elseif ($status === '0' || $status === 'inactive' || $status === 'draft') {
                $query->where('is_active', false);
            }
        }

        $mcqs = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $categories = Category::ordered()->get(['id', 'name']);
        $subjects = Subject::ordered()->get(['id', 'name', 'category_id']);
        $topics = Topic::ordered()->get(['id', 'name', 'category_id', 'subject_id']);

        return view('admin.mcqs.index', compact(
            'mcqs',
            'categories',
            'subjects',
            'topics',
            'search',
            'categoryId',
            'subjectId',
            'topicId',
            'status'
        ));
    }

    /**
     * Show the form for creating a new MCQ.
     */
    public function create(Request $request): View
    {
        $categories = Category::ordered()->get(['id', 'name']);
        $subjects = Subject::ordered()->get(['id', 'name', 'category_id']);
        $topics = Topic::ordered()->get(['id', 'name', 'category_id', 'subject_id']);

        $selectedCategoryId = $request->input('category_id', $categories->first()?->id);
        $selectedSubjectId = $request->input('subject_id');
        $selectedTopicId = $request->input('topic_id');

        $nextNumber = 1;
        if ($selectedTopicId) {
            $nextNumber = (Question::where('topic_id', $selectedTopicId)->max('question_number') ?? 0) + 1;
        }

        return view('admin.mcqs.create', compact(
            'categories',
            'subjects',
            'topics',
            'selectedCategoryId',
            'selectedSubjectId',
            'selectedTopicId',
            'nextNumber'
        ));
    }

    /**
     * Store a newly created MCQ.
     */
    public function store(QuestionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $uploadedAssets = [];

        // Upload Question Image
        if ($request->hasFile('question_image')) {
            $path = $this->uploadMcqAsset($request->file('question_image'), CloudinaryService::FOLDER_MCQ_QUESTIONS);
            $validated['question_image'] = $path;
            $uploadedAssets[] = $path;
        }

        // Upload Option Images
        foreach (['a', 'b', 'c', 'd'] as $opt) {
            $field = "option_{$opt}_image";
            if ($request->hasFile($field)) {
                $path = $this->uploadMcqAsset($request->file($field), CloudinaryService::FOLDER_MCQ_OPTIONS);
                $validated[$field] = $path;
                $uploadedAssets[] = $path;
            }
        }

        // Upload Explanation Image
        if ($request->hasFile('explanation_image')) {
            $path = $this->uploadMcqAsset($request->file('explanation_image'), CloudinaryService::FOLDER_MCQ_ANSWERS);
            $validated['explanation_image'] = $path;
            $uploadedAssets[] = $path;
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        if (empty($validated['section_number'])) {
            $validated['section_number'] = 1;
        }

        if (empty($validated['difficulty'])) {
            $validated['difficulty'] = 'medium';
        }

        if (empty($validated['subject_id'])) {
            $topic = Topic::find($validated['topic_id']);
            if ($topic && $topic->subject_id) {
                $validated['subject_id'] = $topic->subject_id;
            }
        }

        try {
            DB::transaction(function () use (&$validated) {
                // Lock the parent topic to serialize concurrent question numbering within this topic
                Topic::where('id', $validated['topic_id'])->lockForUpdate()->first();

                if (empty($validated['question_number'])) {
                    $maxNumber = Question::where('topic_id', $validated['topic_id'])
                        ->lockForUpdate()
                        ->max('question_number') ?? 0;
                    $validated['question_number'] = $maxNumber + 1;
                }

                $question = Question::create($validated);
                Topic::where('id', $question->topic_id)->increment('total_questions_count');
            });
        } catch (\Throwable $e) {
            // Clean up uploaded assets if transaction failed
            foreach ($uploadedAssets as $assetPath) {
                $this->deleteImageIfUnreferenced($assetPath);
            }
            throw $e;
        }

        return redirect()->route('admin.mcqs.index')->with('success', 'MCQ created successfully.');
    }

    /**
     * Show the form for editing the specified MCQ.
     */
    public function edit(Question $mcq): View
    {
        $mcq->load(['category', 'subject', 'topic']);
        $categories = Category::ordered()->get(['id', 'name']);
        $subjects = Subject::ordered()->get(['id', 'name', 'category_id']);
        $topics = Topic::ordered()->get(['id', 'name', 'category_id', 'subject_id']);

        return view('admin.mcqs.edit', [
            'question' => $mcq,
            'categories' => $categories,
            'subjects' => $subjects,
            'topics' => $topics,
        ]);
    }

    /**
     * Update the specified MCQ.
     */
    public function update(QuestionRequest $request, Question $mcq): RedirectResponse
    {
        $validated = $request->validated();

        $oldTopicId = $mcq->topic_id;
        $newTopicId = (int) $validated['topic_id'];

        // Handle Question Image removal
        if (!empty($validated['remove_image']) || !empty($validated['remove_question_image'])) {
            $this->deleteImageIfUnreferenced($mcq->question_image, $mcq->id);
            $validated['question_image'] = null;
        }

        // Handle Question Image upload replacement
        if ($request->hasFile('question_image')) {
            $this->deleteImageIfUnreferenced($mcq->question_image, $mcq->id);
            $validated['question_image'] = $this->uploadMcqAsset($request->file('question_image'), CloudinaryService::FOLDER_MCQ_QUESTIONS);
        }

        // Handle Option Images removal & replacement
        foreach (['a', 'b', 'c', 'd'] as $opt) {
            $field = "option_{$opt}_image";
            $removeField = "remove_option_{$opt}_image";

            if (!empty($validated[$removeField])) {
                $this->deleteImageIfUnreferenced($mcq->{$field}, $mcq->id);
                $validated[$field] = null;
            }

            if ($request->hasFile($field)) {
                $this->deleteImageIfUnreferenced($mcq->{$field}, $mcq->id);
                $validated[$field] = $this->uploadMcqAsset($request->file($field), CloudinaryService::FOLDER_MCQ_OPTIONS);
            }
        }

        // Handle Explanation Image removal & replacement
        if (!empty($validated['remove_explanation_image'])) {
            $this->deleteImageIfUnreferenced($mcq->explanation_image, $mcq->id);
            $validated['explanation_image'] = null;
        }

        if ($request->hasFile('explanation_image')) {
            $this->deleteImageIfUnreferenced($mcq->explanation_image, $mcq->id);
            $validated['explanation_image'] = $this->uploadMcqAsset($request->file('explanation_image'), CloudinaryService::FOLDER_MCQ_ANSWERS);
        }

        if (empty($validated['subject_id'])) {
            $topic = Topic::find($newTopicId);
            if ($topic && $topic->subject_id) {
                $validated['subject_id'] = $topic->subject_id;
            }
        }

        DB::transaction(function () use ($mcq, $validated, $oldTopicId, $newTopicId) {
            $mcq->update($validated);

            if ($oldTopicId !== $newTopicId) {
                Topic::where('id', $oldTopicId)->decrement('total_questions_count');
                Topic::where('id', $newTopicId)->increment('total_questions_count');
            }
        });

        return redirect()->route('admin.mcqs.index')->with('success', 'MCQ updated successfully.');
    }

    /**
     * Toggle active/draft status.
     */
    public function toggleStatus(Question $mcq): RedirectResponse
    {
        $mcq->is_active = !$mcq->is_active;
        $mcq->save();

        $statusText = $mcq->is_active ? 'published and active' : 'deactivated and draft';

        return redirect()->back()->with('success', "MCQ #{$mcq->question_number} is now {$statusText}.");
    }

    /**
     * Remove the specified MCQ safely.
     */
    public function destroy(Question $mcq): RedirectResponse
    {
        $hasBookmarks = DB::table('bookmarks')->where('question_id', $mcq->id)->exists();
        $hasDiscussions = DB::table('discussions')->where('question_id', $mcq->id)->exists();
        $hasReports = DB::table('question_reports')->where('question_id', $mcq->id)->exists();

        if ($hasBookmarks || $hasDiscussions || $hasReports) {
            $mcq->update(['is_active' => false]);

            return redirect()->route('admin.mcqs.index')
                ->with('warning', 'Question has active user bookmarks, discussions, or reports. To preserve user data, the question was deactivated instead of permanently deleted.');
        }

        // Clean up all question images if unreferenced
        $this->deleteImageIfUnreferenced($mcq->question_image, $mcq->id);
        $this->deleteImageIfUnreferenced($mcq->option_a_image, $mcq->id);
        $this->deleteImageIfUnreferenced($mcq->option_b_image, $mcq->id);
        $this->deleteImageIfUnreferenced($mcq->option_c_image, $mcq->id);
        $this->deleteImageIfUnreferenced($mcq->option_d_image, $mcq->id);
        $this->deleteImageIfUnreferenced($mcq->explanation_image, $mcq->id);

        $topicId = $mcq->topic_id;
        $mcq->delete();

        Topic::where('id', $topicId)->decrement('total_questions_count');

        return redirect()->route('admin.mcqs.index')
            ->with('success', 'MCQ permanently deleted successfully.');
    }

    /**
     * Upload an MCQ media asset (Cloudinary with local fallback).
     */
    protected function uploadMcqAsset($file, string $folder): string
    {
        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadImage($file, $folder);
            if (!empty($result['success']) && !empty($result['secure_url'])) {
                return $result['secure_url'];
            }
        }

        return $file->store('questions', 'public');
    }

    /**
     * Safely delete an image file from storage ONLY if no other question references it.
     */
    protected function deleteImageIfUnreferenced(?string $imagePath, ?int $excludeQuestionId = null): void
    {
        if (empty($imagePath)) {
            return;
        }

        $isStillInUse = Question::when($excludeQuestionId, fn ($q) => $q->where('id', '!=', $excludeQuestionId))
            ->where(function ($q) use ($imagePath) {
                $q->where('question_image', $imagePath)
                  ->orWhere('explanation_image', $imagePath)
                  ->orWhere('option_a_image', $imagePath)
                  ->orWhere('option_b_image', $imagePath)
                  ->orWhere('option_c_image', $imagePath)
                  ->orWhere('option_d_image', $imagePath);
            })
            ->exists();

        if ($isStillInUse) {
            return;
        }

        // Cloudinary asset check
        $cloudinary = $this->getCloudinaryService();
        $publicId = $cloudinary->getPublicId($imagePath);
        if ($publicId && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, 'aptimedha/'))) {
            if ($cloudinary->isConfigured()) {
                $cloudinary->deleteAsset($publicId);
            }
            return;
        }

        // Local storage asset check
        if (str_starts_with($imagePath, 'questions/') && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
