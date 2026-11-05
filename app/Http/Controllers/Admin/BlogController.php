<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\CloudinaryService;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Handles CRUD operations and publishing workflow for blog posts in the admin panel.
 */
class BlogController
{
    public function __construct(
        protected ?CloudinaryService $cloudinaryService = null
    ) {}

    protected function getCloudinaryService(): CloudinaryService
    {
        return $this->cloudinaryService ?? app(CloudinaryService::class);
    }
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request): View
    {
        $query = Blog::with(['category', 'author']);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('excerpt', 'like', $term)
                  ->orWhere('author_name', 'like', $term);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }

        $blogs = $query->latest('id')->paginate(15)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();

        $stats = [
            'total' => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'draft' => Blog::where('status', 'draft')->count(),
            'views' => (int) Blog::sum('views_count'),
        ];

        return view('admin.blogs.index', compact('blogs', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create(): View
    {
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blogs.create', compact('categories'));
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blogs,slug'],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Generate slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Blog::generateUniqueSlug($validated['title']);
        } else {
            $baseSlug = Str::slug($validated['slug']);
            $validated['slug'] = Blog::where('slug', $baseSlug)->exists()
                ? Blog::generateUniqueSlug($baseSlug)
                : $baseSlug;
        }

        // Publication timestamp logic
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Rich-text HTML sanitization
        $validated['body'] = HtmlSanitizer::clean($validated['body']);

        // Author attribution
        $validated['author_id'] = Auth::id() ?? 1;

        // Image upload handling
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->uploadFeaturedImage($request->file('featured_image'));
        }

        $blog = Blog::create($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Article "' . $blog->title . '" created successfully.');
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit(Blog $blog): View
    {
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blogs', 'slug')->ignore($blog->id)],
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Generate / sanitize slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Blog::generateUniqueSlug($validated['title'], $blog->id);
        } else {
            $baseSlug = Str::slug($validated['slug']);
            $validated['slug'] = Blog::where('slug', $baseSlug)->where('id', '!=', $blog->id)->exists()
                ? Blog::generateUniqueSlug($baseSlug, $blog->id)
                : $baseSlug;
        }

        // Publication date logic
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = $blog->published_at ?? now();
        }

        // Sanitize rich-text HTML
        $validated['body'] = HtmlSanitizer::clean($validated['body']);

        // Handle image removal
        if ($request->boolean('remove_featured_image')) {
            $this->deleteFeaturedImageIfUnreferenced($blog->featured_image, $blog->id);
            $validated['featured_image'] = null;
        }

        // Handle image replacement
        if ($request->hasFile('featured_image')) {
            $this->deleteFeaturedImageIfUnreferenced($blog->featured_image, $blog->id);
            $validated['featured_image'] = $this->uploadFeaturedImage($request->file('featured_image'));
        }

        $blog->update($validated);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Article "' . $blog->title . '" updated successfully.');
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        $title = $blog->title;

        // Safely delete image if not referenced elsewhere
        $this->deleteFeaturedImageIfUnreferenced($blog->featured_image, $blog->id);

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Article "' . $title . '" deleted successfully.');
    }

    /**
     * Upload an embedded image for Quill editor inside blog content.
     */
    public function uploadEmbeddedImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('image');

        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadImage($file, CloudinaryService::FOLDER_BLOGS_CONTENT);
            if (!empty($result['success']) && !empty($result['secure_url'])) {
                return response()->json([
                    'success' => true,
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id'],
                ]);
            }
        }

        // Fallback to local storage if Cloudinary is not configured or upload fails
        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('blogs/content', $filename, 'public');

        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($path),
            'public_id' => $path,
        ]);
    }

    /**
     * Upload featured image with Cloudinary and local fallback.
     */
    protected function uploadFeaturedImage($file): string
    {
        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadImage($file, CloudinaryService::FOLDER_BLOGS_FEATURED);
            if (!empty($result['success']) && !empty($result['secure_url'])) {
                return $result['secure_url'];
            }
        }

        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('blogs', $filename, 'public');
    }

    /**
     * Delete a featured image if no other article references it.
     */
    protected function deleteFeaturedImageIfUnreferenced(?string $imagePath, ?int $excludeBlogId = null): void
    {
        if (empty($imagePath)) {
            return;
        }

        $isStillInUse = Blog::when($excludeBlogId, fn ($q) => $q->where('id', '!=', $excludeBlogId))
            ->where('featured_image', $imagePath)
            ->exists();

        if ($isStillInUse) {
            return;
        }

        // Cloudinary asset
        $cloudinary = $this->getCloudinaryService();
        $publicId = $cloudinary->getPublicId($imagePath);
        if ($publicId && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, 'aptimedha/'))) {
            if ($cloudinary->isConfigured()) {
                $cloudinary->deleteAsset($publicId);
            }
            return;
        }

        // Local storage asset
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    /**
     * Toggle status between published and draft.
     */
    public function toggleStatus(Blog $blog): RedirectResponse
    {
        if ($blog->status === 'published') {
            $blog->update(['status' => 'draft']);
            $msg = 'Article "' . $blog->title . '" unpublished (moved to Draft).';
        } else {
            $blog->update([
                'status' => 'published',
                'published_at' => $blog->published_at ?? now(),
            ]);
            $msg = 'Article "' . $blog->title . '" published successfully.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Preview article for administrators.
     */
    public function preview(Blog $blog): View
    {
        $blog->load(['category', 'author']);

        $relatedPosts = Blog::where('id', '!=', $blog->id)
            ->where('status', 'published')
            ->latest('id')
            ->take(3)
            ->get();

        return view('admin.blogs.preview', compact('blog', 'relatedPosts'));
    }
}
