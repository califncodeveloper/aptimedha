<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CloudinaryTestController extends Controller
{
    public function __construct(
        protected CloudinaryService $cloudinaryService
    ) {}

    /**
     * Display the test upload management screen.
     */
    public function index(): View
    {
        $isConfigured = $this->cloudinaryService->isConfigured();
        $pingSuccess = $isConfigured ? $this->cloudinaryService->ping() : false;

        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $maskedKey = $apiKey ? Str::mask($apiKey, '*', 3, -4) : 'Not configured';

        return view('admin.cloudinary-test', [
            'isConfigured' => $isConfigured,
            'pingSuccess' => $pingSuccess,
            'cloudName' => $cloudName ?: 'Not configured',
            'maskedApiKey' => $maskedKey,
            'folders' => CloudinaryService::ALLOWED_FOLDERS,
        ]);
    }

    /**
     * Handle manual file upload test.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // 2MB limit
            'folder' => ['nullable', 'string'],
        ], [
            'image.required' => 'Please select an image file to upload.',
            'image.image' => 'The uploaded file must be a valid image.',
            'image.max' => 'The image size must not exceed 2MB (oversized file rejected).',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, webp.',
        ]);

        $folder = $request->input('folder', CloudinaryService::FOLDER_BASE);

        $result = $this->cloudinaryService->uploadImage($request->file('image'), $folder);

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'Upload failed.');
        }

        return back()->with('upload_result', $result)->with('success', 'Image uploaded successfully to Cloudinary!');
    }

    /**
     * Run full automated verification sequence.
     */
    public function runAutomatedCheck(): RedirectResponse
    {
        $report = [];

        // 1. Credentials load
        $isConfigured = $this->cloudinaryService->isConfigured();
        $report['credentials_loaded'] = $isConfigured;

        if (! $isConfigured) {
            return back()->with('automated_report', $report)->with('error', 'Cloudinary credentials are not configured.');
        }

        // 2. API Ping
        $report['api_ping'] = $this->cloudinaryService->ping();

        // 3. Upload test 1x1 PNG image
        $samplePngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $tempPath = tempnam(sys_get_temp_dir(), 'cld_test_');
        file_put_contents($tempPath, base64_decode($samplePngBase64));

        $uploadResult = $this->cloudinaryService->uploadImage($tempPath, CloudinaryService::FOLDER_BASE, [
            'public_id' => 'test_verification_' . Str::random(8),
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        $report['upload_success'] = $uploadResult['success'] ?? false;
        $report['secure_url_generated'] = ! empty($uploadResult['secure_url']) && str_starts_with($uploadResult['secure_url'], 'https://');
        $report['public_id_returned'] = ! empty($uploadResult['public_id']);
        $publicId = $uploadResult['public_id'] ?? null;
        $secureUrl = $uploadResult['secure_url'] ?? null;

        // 4. Accessible over HTTPS
        $httpsAccessible = false;
        if ($secureUrl) {
            try {
                $response = Http::timeout(10)->head($secureUrl);
                $httpsAccessible = $response->successful();
            } catch (\Throwable) {
                $httpsAccessible = false;
            }
        }
        $report['https_accessible'] = $httpsAccessible;

        // 5. Deletion test
        $deletionSuccess = false;
        if ($publicId) {
            $deleteResult = $this->cloudinaryService->deleteAsset($publicId);
            $deletionSuccess = $deleteResult['success'] ?? false;
            $report['deletion_result'] = $deleteResult['result'] ?? 'failed';
        }
        $report['deletion_success'] = $deletionSuccess;

        return back()->with('automated_report', $report)->with('success', 'Automated Cloudinary verification complete!');
    }

    /**
     * Delete an asset by public ID.
     */
    public function delete(Request $request): RedirectResponse
    {
        $request->validate([
            'public_id' => ['required', 'string', 'max:255'],
        ]);

        $result = $this->cloudinaryService->deleteAsset($request->input('public_id'));

        if (! ($result['success'] ?? false)) {
            return back()->with('error', $result['error'] ?? 'Asset deletion failed.');
        }

        return back()->with('success', 'Asset successfully deleted from Cloudinary (Result: ' . ($result['result'] ?? 'ok') . ').');
    }
}

