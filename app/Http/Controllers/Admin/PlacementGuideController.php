<?php

namespace App\Http\Controllers\Admin;

use App\Models\PlacementGuide;
use App\Services\CloudinaryService;
use App\Services\HtmlSanitizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Handles CRUD operations and publishing workflow for Placement Preparation guides in the admin panel.
 */
class PlacementGuideController
{
    public function __construct(
        protected ?CloudinaryService $cloudinaryService = null
    ) {}

    protected function getCloudinaryService(): CloudinaryService
    {
        return $this->cloudinaryService ?? app(CloudinaryService::class);
    }

    /**
     * Display a listing of placement guides.
     */
    public function index(Request $request): View
    {
        $query = PlacementGuide::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $guides = $query->orderBy('display_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = PlacementGuide::getCategories();

        $stats = [
            'total' => PlacementGuide::count(),
            'published' => PlacementGuide::where('is_published', true)->count(),
            'draft' => PlacementGuide::where('is_published', false)->count(),
            'with_downloads' => PlacementGuide::whereNotNull('downloadable_asset')->where('downloadable_asset', '!=', '')->count(),
        ];

        return view('admin.placement.index', compact('guides', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new placement guide.
     */
    public function create(): View
    {
        $categories = PlacementGuide::getCategories();

        return view('admin.placement.create', compact('categories'));
    }

    /**
     * Store a newly created placement guide.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:placement_guides,slug'],
            'category' => ['required', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'downloadable_asset' => ['nullable', 'file', 'mimes:pdf,docx,doc', 'max:20480'],
            'downloadable_asset_name' => ['nullable', 'string', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Generate / sanitize slug
        if (empty($validated['slug'])) {
            $validated['slug'] = PlacementGuide::generateUniqueSlug($validated['title']);
        } else {
            $baseSlug = Str::slug($validated['slug']);
            $validated['slug'] = PlacementGuide::where('slug', $baseSlug)->exists()
                ? PlacementGuide::generateUniqueSlug($baseSlug)
                : $baseSlug;
        }

        $validated['display_order'] = (int) ($validated['display_order'] ?? 0);
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // HTML Sanitization
        $validated['content'] = HtmlSanitizer::clean($validated['content']);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $this->uploadFeaturedImage($request->file('featured_image'));
        }

        // Handle downloadable asset upload
        if ($request->hasFile('downloadable_asset')) {
            $file = $request->file('downloadable_asset');
            $uploadResult = $this->uploadDownloadableAsset($file);
            $validated['downloadable_asset'] = $uploadResult['url'];
            $validated['downloadable_asset_mime'] = $uploadResult['mime'];
            if (empty($validated['downloadable_asset_name'])) {
                $validated['downloadable_asset_name'] = $file->getClientOriginalName();
            }
        }

        $guide = PlacementGuide::create($validated);

        return redirect()->route('admin.placement.index')
            ->with('success', 'Placement guide "' . $guide->title . '" created successfully.');
    }

    /**
     * Show the form for editing the specified placement guide.
     */
    public function edit(PlacementGuide $placement): View
    {
        $categories = PlacementGuide::getCategories();

        return view('admin.placement.edit', [
            'guide' => $placement,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified placement guide.
     */
    public function update(Request $request, PlacementGuide $placement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('placement_guides', 'slug')->ignore($placement->id)],
            'category' => ['required', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'downloadable_asset' => ['nullable', 'file', 'mimes:pdf,docx,doc', 'max:20480'],
            'remove_downloadable_asset' => ['nullable', 'boolean'],
            'downloadable_asset_name' => ['nullable', 'string', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Generate / sanitize slug
        if (empty($validated['slug'])) {
            $validated['slug'] = PlacementGuide::generateUniqueSlug($validated['title'], $placement->id);
        } else {
            $baseSlug = Str::slug($validated['slug']);
            $validated['slug'] = PlacementGuide::where('slug', $baseSlug)->where('id', '!=', $placement->id)->exists()
                ? PlacementGuide::generateUniqueSlug($baseSlug, $placement->id)
                : $baseSlug;
        }

        $validated['display_order'] = (int) ($validated['display_order'] ?? 0);
        $validated['is_published'] = $request->boolean('is_published', true);

        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = $placement->published_at ?? now();
        }

        // HTML Sanitization
        $validated['content'] = HtmlSanitizer::clean($validated['content']);

        // Handle image removal
        if ($request->boolean('remove_featured_image')) {
            $this->deleteAssetIfUnreferenced($placement->featured_image, $placement->id, 'image');
            $validated['featured_image'] = null;
        }

        // Handle image replacement
        if ($request->hasFile('featured_image')) {
            $this->deleteAssetIfUnreferenced($placement->featured_image, $placement->id, 'image');
            $validated['featured_image'] = $this->uploadFeaturedImage($request->file('featured_image'));
        }

        // Handle downloadable asset removal
        if ($request->boolean('remove_downloadable_asset')) {
            $this->deleteAssetIfUnreferenced($placement->downloadable_asset, $placement->id, 'raw');
            $validated['downloadable_asset'] = null;
            $validated['downloadable_asset_name'] = null;
            $validated['downloadable_asset_mime'] = null;
        }

        // Handle downloadable asset replacement
        if ($request->hasFile('downloadable_asset')) {
            $file = $request->file('downloadable_asset');
            $this->deleteAssetIfUnreferenced($placement->downloadable_asset, $placement->id, 'raw');
            $uploadResult = $this->uploadDownloadableAsset($file);
            $validated['downloadable_asset'] = $uploadResult['url'];
            $validated['downloadable_asset_mime'] = $uploadResult['mime'];
            if (empty($validated['downloadable_asset_name'])) {
                $validated['downloadable_asset_name'] = $file->getClientOriginalName();
            }
        }

        $placement->update($validated);

        return redirect()->route('admin.placement.index')
            ->with('success', 'Placement guide "' . $placement->title . '" updated successfully.');
    }

    /**
     * Remove the specified placement guide.
     */
    public function destroy(PlacementGuide $placement): RedirectResponse
    {
        $title = $placement->title;

        // Safely clean up assets if not referenced by another record
        $this->deleteAssetIfUnreferenced($placement->featured_image, $placement->id, 'image');
        $this->deleteAssetIfUnreferenced($placement->downloadable_asset, $placement->id, 'raw');

        $placement->delete();

        return redirect()->route('admin.placement.index')
            ->with('success', 'Placement guide "' . $title . '" deleted successfully.');
    }

    /**
     * Toggle published / draft status.
     */
    public function toggleStatus(PlacementGuide $placement): RedirectResponse
    {
        $newStatus = ! $placement->is_published;
        $placement->update([
            'is_published' => $newStatus,
            'published_at' => ($newStatus && empty($placement->published_at)) ? now() : $placement->published_at,
        ]);

        $msg = $newStatus
            ? 'Guide "' . $placement->title . '" published successfully.'
            : 'Guide "' . $placement->title . '" moved to Draft (unpublished).';

        return back()->with('success', $msg);
    }

    /**
     * Preview guide for administrators.
     */
    public function preview(PlacementGuide $placement): View
    {
        $relatedGuides = PlacementGuide::where('id', '!=', $placement->id)
            ->where('category', $placement->category)
            ->take(3)
            ->get();

        return view('admin.placement.preview', [
            'guide' => $placement,
            'relatedGuides' => $relatedGuides,
        ]);
    }

    /**
     * Upload an embedded image for Quill editor inside guide content.
     */
    public function uploadEmbeddedImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('image');

        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadImage($file, CloudinaryService::FOLDER_PLACEMENT_CONTENT);
            if (! empty($result['success']) && ! empty($result['secure_url'])) {
                return response()->json([
                    'success' => true,
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id'],
                ]);
            }
        }

        // Fallback to local storage
        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('placement/content', $filename, 'public');

        return response()->json([
            'success' => true,
            'url' => Storage::disk('public')->url($path),
            'public_id' => $path,
        ]);
    }

    /**
     * Reorder guides via POST / PATCH.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'integer', 'exists:placement_guides,id'],
            'orders.*.display_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->input('orders') as $order) {
            PlacementGuide::where('id', $order['id'])->update(['display_order' => $order['display_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Upload featured image with Cloudinary and local fallback.
     */
    protected function uploadFeaturedImage($file): string
    {
        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadImage($file, CloudinaryService::FOLDER_PLACEMENT_FEATURED);
            if (! empty($result['success']) && ! empty($result['secure_url'])) {
                return $result['secure_url'];
            }
        }

        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();

        return $file->storeAs('placement/featured', $filename, 'public');
    }

    /**
     * Upload downloadable asset (PDF, DOCX) with Cloudinary and local fallback.
     */
    protected function uploadDownloadableAsset($file): array
    {
        $mime = $file->getClientMimeType() ?: $file->getMimeType() ?: 'application/octet-stream';

        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadRaw($file, CloudinaryService::FOLDER_PLACEMENT_DOWNLOADS);
            if (! empty($result['success']) && ! empty($result['secure_url'])) {
                return [
                    'url' => $result['secure_url'],
                    'mime' => $mime,
                    'public_id' => $result['public_id'] ?? null,
                ];
            }
        }

        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('placement/downloads', $filename, 'public');

        return [
            'url' => $path,
            'mime' => $mime,
            'public_id' => $path,
        ];
    }

    /**
     * Safely delete an asset if unreferenced by another guide or blog.
     */
    protected function deleteAssetIfUnreferenced(?string $assetPath, ?int $excludeGuideId = null, string $resourceType = 'image'): void
    {
        if (empty($assetPath)) {
            return;
        }

        // Check if another guide references this asset
        $column = ($resourceType === 'raw') ? 'downloadable_asset' : 'featured_image';
        $inOtherGuide = PlacementGuide::when($excludeGuideId, fn ($q) => $q->where('id', '!=', $excludeGuideId))
            ->where($column, $assetPath)
            ->exists();

        if ($inOtherGuide) {
            return;
        }

        // Check Cloudinary
        $cloudinary = $this->getCloudinaryService();
        $publicId = $cloudinary->getPublicId($assetPath);
        if ($publicId && (str_starts_with($assetPath, 'http://') || str_starts_with($assetPath, 'https://') || str_starts_with($assetPath, 'aptimedha/'))) {
            if ($cloudinary->isConfigured()) {
                $cloudinary->deleteAsset($publicId, $resourceType);
            }

            return;
        }

        // Local storage asset
        if (Storage::disk('public')->exists($assetPath)) {
            Storage::disk('public')->delete($assetPath);
        }
    }
}

