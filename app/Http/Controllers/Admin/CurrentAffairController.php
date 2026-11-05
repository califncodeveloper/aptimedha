<?php

namespace App\Http\Controllers\Admin;

use App\Models\CurrentAffair;
use App\Services\CloudinaryService;
use App\Services\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class CurrentAffairController
{
    /**
     * Cloudinary service instance.
     */
    protected ?CloudinaryService $cloudinaryService = null;

    public function __construct(?CloudinaryService $cloudinaryService = null)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    protected function getCloudinaryService(): CloudinaryService
    {
        return $this->cloudinaryService ?? app(CloudinaryService::class);
    }

    /**
     * Display a paginated listing of current affairs monthly issues with filters and metrics.
     */
    public function index(Request $request): View
    {
        $query = CurrentAffair::query();

        // Search by keyword
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by Year
        if ($year = $request->input('year')) {
            $query->where('year', (int) $year);
        }

        // Filter by Month
        if ($month = $request->input('month')) {
            $query->where('month', (int) $month);
        }

        // Filter by Status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_published', (bool) $request->input('status'));
        }

        $currentAffairs = $query->ordered()->paginate(15)->withQueryString();

        // Admin metrics
        $metrics = [
            'total' => CurrentAffair::count(),
            'published' => CurrentAffair::where('is_published', true)->count(),
            'draft' => CurrentAffair::where('is_published', false)->count(),
            'total_downloads' => (int) CurrentAffair::sum('downloads_count'),
            'total_size_bytes' => (int) CurrentAffair::sum('file_size_bytes'),
        ];

        // Format total size
        $totalBytes = $metrics['total_size_bytes'];
        if ($totalBytes >= 1048576 * 1024) {
            $metrics['formatted_size'] = number_format($totalBytes / (1048576 * 1024), 2) . ' GB';
        } elseif ($totalBytes >= 1048576) {
            $metrics['formatted_size'] = number_format($totalBytes / 1048576, 2) . ' MB';
        } else {
            $metrics['formatted_size'] = number_format($totalBytes / 1024, 1) . ' KB';
        }

        $availableYears = CurrentAffair::distinct()->orderByDesc('year')->pluck('year')->all();
        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        return view('admin.current-affairs.index', [
            'currentAffairs' => $currentAffairs,
            'metrics' => $metrics,
            'availableYears' => $availableYears,
            'months' => CurrentAffair::getMonths(),
        ]);
    }

    /**
     * Show form for creating a new monthly issue.
     */
    public function create(): View
    {
        return view('admin.current-affairs.create', [
            'months' => CurrentAffair::getMonths(),
            'currentYear' => (int) date('Y'),
            'currentMonth' => (int) date('n'),
        ]);
    }

    /**
     * Store a newly created monthly issue with PDF and optional preview.
     */
    public function store(Request $request): RedirectResponse
    {
        $year = (int) $request->input('year');

        $validated = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2099'],
            'month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('current_affairs')->where(fn ($q) => $q->where('year', $year)),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'], // 50MB
            'preview_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5MB
            'preview_image_alt' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [
            'month.unique' => 'A Current Affairs record for this month and year already exists. Please choose a different month/year or update the existing record.',
            'pdf.required' => 'A monthly PDF document is required.',
            'pdf.mimes' => 'The uploaded file must be a valid PDF document.',
            'pdf.max' => 'The PDF document must not exceed 50 MB.',
        ]);

        // Upload PDF
        $pdfResult = $this->uploadPdfAsset($request->file('pdf'), $validated['title']);
        if (! $pdfResult['success']) {
            return back()->withInput()->withErrors(['pdf' => $pdfResult['error'] ?? 'PDF upload failed.']);
        }

        // Upload Preview Image if provided
        $previewPath = null;
        if ($request->hasFile('preview_image')) {
            $imgResult = $this->uploadPreviewAsset($request->file('preview_image'));
            if ($imgResult['success']) {
                $previewPath = $imgResult['url'];
            } else {
                if (app()->environment('production')) {
                    return back()->withInput()->withErrors(['preview_image' => $imgResult['error'] ?? 'Preview image upload failed.']);
                }
                Log::warning('Current Affairs preview upload warning: ' . ($imgResult['error'] ?? 'unknown'));
            }
        }

        $isPublished = (bool) ($validated['is_published'] ?? false);
        $publishedAt = $validated['published_at'] ?? ($isPublished ? now() : null);

        CurrentAffair::create([
            'title' => $validated['title'],
            'year' => (int) $validated['year'],
            'month' => (int) $validated['month'],
            'description' => HtmlSanitizer::clean($validated['description'] ?? null),
            'pdf_path' => $pdfResult['url'],
            'pdf_name' => $pdfResult['filename'],
            'preview_image' => $previewPath,
            'preview_image_alt' => $validated['preview_image_alt'] ?? null,
            'file_size_bytes' => $pdfResult['bytes'],
            'downloads_count' => 0,
            'is_published' => $isPublished,
            'published_at' => $publishedAt,
        ]);

        return redirect()->route('admin.current-affairs.index')
            ->with('success', "Current Affairs issue for {$validated['title']} was created successfully.");
    }

    /**
     * Show form for editing an existing monthly issue.
     */
    public function edit(CurrentAffair $currentAffair): View
    {
        return view('admin.current-affairs.edit', [
            'currentAffair' => $currentAffair,
            'months' => CurrentAffair::getMonths(),
        ]);
    }

    /**
     * Update an existing monthly issue.
     */
    public function update(Request $request, CurrentAffair $currentAffair): RedirectResponse
    {
        $year = (int) $request->input('year');

        $validated = $request->validate([
            'year' => ['required', 'integer', 'between:2000,2099'],
            'month' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('current_affairs')->where(fn ($q) => $q->where('year', $year))->ignore($currentAffair->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
            'preview_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            'preview_image_alt' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [
            'month.unique' => 'A Current Affairs record for this month and year already exists. Please choose a different month/year or update the existing record.',
            'pdf.mimes' => 'The uploaded file must be a valid PDF document.',
            'pdf.max' => 'The PDF document must not exceed 50 MB.',
        ]);

        $updateData = [
            'title' => $validated['title'],
            'year' => (int) $validated['year'],
            'month' => (int) $validated['month'],
            'description' => HtmlSanitizer::clean($validated['description'] ?? null),
            'preview_image_alt' => $validated['preview_image_alt'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'published_at' => $validated['published_at'] ?? ($currentAffair->published_at ?: (empty($validated['is_published']) ? null : now())),
        ];

        // Replace PDF if a new one is uploaded
        $oldPdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfResult = $this->uploadPdfAsset($request->file('pdf'), $validated['title']);
            if (! $pdfResult['success']) {
                return back()->withInput()->withErrors(['pdf' => $pdfResult['error'] ?? 'PDF upload failed.']);
            }

            $oldPdfPath = $currentAffair->pdf_path;
            $updateData['pdf_path'] = $pdfResult['url'];
            $updateData['pdf_name'] = $pdfResult['filename'];
            $updateData['file_size_bytes'] = $pdfResult['bytes'];
        }

        // Replace preview image if uploaded
        $oldPreviewPath = null;
        if ($request->hasFile('preview_image')) {
            $imgResult = $this->uploadPreviewAsset($request->file('preview_image'));
            if ($imgResult['success']) {
                $oldPreviewPath = $currentAffair->preview_image;
                $updateData['preview_image'] = $imgResult['url'];
            } else {
                if (app()->environment('production')) {
                    return back()->withInput()->withErrors(['preview_image' => $imgResult['error'] ?? 'Preview image upload failed.']);
                }
            }
        }

        $currentAffair->update($updateData);

        // Safe asset cleanup after successful database update
        if ($oldPdfPath && $oldPdfPath !== $currentAffair->pdf_path) {
            $this->deleteAssetIfUnreferenced($oldPdfPath, $currentAffair->id, 'raw');
        }

        if ($oldPreviewPath && $oldPreviewPath !== $currentAffair->preview_image) {
            $this->deleteAssetIfUnreferenced($oldPreviewPath, $currentAffair->id, 'image');
        }

        return redirect()->route('admin.current-affairs.index')
            ->with('success', "Current Affairs issue for {$currentAffair->period_label} updated successfully.");
    }

    /**
     * Toggle publication status.
     */
    public function toggleStatus(CurrentAffair $currentAffair): RedirectResponse
    {
        $newStatus = ! $currentAffair->is_published;
        $publishedAt = $currentAffair->published_at;

        if ($newStatus && ! $publishedAt) {
            $publishedAt = now();
        }

        $currentAffair->update([
            'is_published' => $newStatus,
            'published_at' => $publishedAt,
        ]);

        $statusLabel = $newStatus ? 'published' : 'moved to draft';

        return back()->with('success', "Current Affairs issue for {$currentAffair->period_label} {$statusLabel}.");
    }

    /**
     * Admin inline preview of the PDF.
     */
    public function preview(CurrentAffair $currentAffair): Response|RedirectResponse|BinaryFileResponse
    {
        $pdfPath = $currentAffair->pdf_path;

        if (empty($pdfPath)) {
            return back()->with('error', 'PDF document path is missing.');
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

        // Public directory
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

        // Remote Cloudinary URL
        if (str_starts_with($pdfPath, 'http://') || str_starts_with($pdfPath, 'https://')) {
            return redirect()->away($pdfPath);
        }

        return back()->with('error', 'The PDF file could not be located on the server.');
    }

    /**
     * Safely delete a monthly current affair issue and its assets.
     */
    public function destroy(CurrentAffair $currentAffair): RedirectResponse
    {
        $label = $currentAffair->period_label;
        $pdfPath = $currentAffair->pdf_path;
        $previewImage = $currentAffair->preview_image;
        $id = $currentAffair->id;

        $currentAffair->delete();

        // Cleanup assets if not used by another record
        $this->deleteAssetIfUnreferenced($pdfPath, $id, 'raw');
        $this->deleteAssetIfUnreferenced($previewImage, $id, 'image');

        return redirect()->route('admin.current-affairs.index')
            ->with('success', "Current Affairs issue for {$label} was deleted.");
    }

    /**
     * Upload PDF asset using Cloudinary (raw) or local disk fallback for dev/testing.
     */
    protected function uploadPdfAsset(UploadedFile $file, string $title): array
    {
        $bytes = $file->getSize() ?: 0;
        $originalFilename = $file->getClientOriginalName() ?: 'issue.pdf';
        $isProduction = app()->environment('production') || config('app.env') === 'production';

        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadRaw($file, CloudinaryService::FOLDER_CURRENT_AFFAIRS_PDFS);
            if (! empty($result['success']) && ! empty($result['secure_url'])) {
                return [
                    'success' => true,
                    'url' => $result['secure_url'],
                    'filename' => $originalFilename,
                    'bytes' => $result['bytes'] ?? $bytes,
                    'public_id' => $result['public_id'] ?? null,
                ];
            }

            $errorMsg = $result['error'] ?? 'Cloudinary PDF upload failed.';
            Log::error("Cloudinary PDF upload failed in Current Affairs: {$errorMsg}");

            // In production, prevent silent local-disk fallback on ephemeral containers
            if ($isProduction) {
                return [
                    'success' => false,
                    'error' => "Cloud storage upload failed: {$errorMsg}",
                ];
            }

            Log::warning('Development fallback: saving PDF to local storage.');
        } elseif ($isProduction) {
            return [
                'success' => false,
                'error' => 'Cloudinary is not configured. Cloud storage is required in production.',
            ];
        }

        // Local development / testing disk storage
        $safeName = Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . Str::random(8) . '.pdf';
        $path = $file->storeAs('current-affairs/pdfs', $safeName, 'public');

        return [
            'success' => true,
            'url' => $path,
            'filename' => $originalFilename,
            'bytes' => $bytes,
            'public_id' => $path,
        ];
    }

    /**
     * Upload preview image using Cloudinary or local disk fallback for dev/testing.
     */
    protected function uploadPreviewAsset(UploadedFile $file): array
    {
        $isProduction = app()->environment('production') || config('app.env') === 'production';

        $cloudinary = $this->getCloudinaryService();
        if ($cloudinary->isConfigured()) {
            $result = $cloudinary->uploadImage($file, CloudinaryService::FOLDER_CURRENT_AFFAIRS_PREVIEWS);
            if (! empty($result['success']) && ! empty($result['secure_url'])) {
                return [
                    'success' => true,
                    'url' => $result['secure_url'],
                    'public_id' => $result['public_id'] ?? null,
                ];
            }

            $errorMsg = $result['error'] ?? 'Cloudinary preview upload failed.';
            Log::error("Cloudinary preview upload failed in Current Affairs: {$errorMsg}");

            if ($isProduction) {
                return [
                    'success' => false,
                    'error' => "Cloud image upload failed: {$errorMsg}",
                ];
            }

            Log::warning('Development fallback: saving preview image to local storage.');
        } elseif ($isProduction) {
            return [
                'success' => false,
                'error' => 'Cloudinary is not configured. Cloud storage is required in production.',
            ];
        }

        $filename = Str::random(24) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('current-affairs/previews', $filename, 'public');

        return [
            'success' => true,
            'url' => $path,
            'public_id' => $path,
        ];
    }

    /**
     * Safely delete an asset if unreferenced by another record.
     */
    protected function deleteAssetIfUnreferenced(?string $assetPath, ?int $excludeAffairId = null, string $resourceType = 'raw'): void
    {
        if (empty($assetPath)) {
            return;
        }

        $column = ($resourceType === 'raw') ? 'pdf_path' : 'preview_image';
        $inOtherAffair = CurrentAffair::when($excludeAffairId, fn ($q) => $q->where('id', '!=', $excludeAffairId))
            ->where($column, $assetPath)
            ->exists();

        if ($inOtherAffair) {
            return;
        }

        $cloudinary = $this->getCloudinaryService();
        $publicId = $cloudinary->getPublicId($assetPath);

        if ($publicId && (str_starts_with($assetPath, 'http://') || str_starts_with($assetPath, 'https://') || str_starts_with($assetPath, 'aptimedha/'))) {
            if ($cloudinary->isConfigured()) {
                $cloudinary->deleteAsset($publicId, $resourceType);
            }
            return;
        }

        if (Storage::disk('public')->exists($assetPath)) {
            Storage::disk('public')->delete($assetPath);
        }
    }
}
