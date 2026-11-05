<?php

namespace App\Services;

use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Cloudinary\Exception\ConfigurationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CloudinaryService
{
    /**
     * Standard AptiMedha Media Folders
     */
    public const FOLDER_BASE = 'aptimedha';
    public const FOLDER_MCQ_QUESTIONS = 'aptimedha/mcq/questions';
    public const FOLDER_MCQ_OPTIONS = 'aptimedha/mcq/options';
    public const FOLDER_MCQ_ANSWERS = 'aptimedha/mcq/answers';
    public const FOLDER_BLOGS_FEATURED = 'aptimedha/blogs/featured';
    public const FOLDER_BLOGS_CONTENT = 'aptimedha/blogs/content';
    public const FOLDER_PLACEMENT = 'aptimedha/placement';
    public const FOLDER_PLACEMENT_FEATURED = 'aptimedha/placement/featured';
    public const FOLDER_PLACEMENT_CONTENT = 'aptimedha/placement/content';
    public const FOLDER_PLACEMENT_DOWNLOADS = 'aptimedha/placement/downloads';
    public const FOLDER_CURRENT_AFFAIRS = 'aptimedha/current-affairs';
    public const FOLDER_CURRENT_AFFAIRS_PDFS = 'aptimedha/current-affairs/pdfs';
    public const FOLDER_CURRENT_AFFAIRS_PREVIEWS = 'aptimedha/current-affairs/previews';

    /**
     * Allowed Standard Media Folders
     */
    public const ALLOWED_FOLDERS = [
        self::FOLDER_BASE,
        self::FOLDER_MCQ_QUESTIONS,
        self::FOLDER_MCQ_OPTIONS,
        self::FOLDER_MCQ_ANSWERS,
        self::FOLDER_BLOGS_FEATURED,
        self::FOLDER_BLOGS_CONTENT,
        self::FOLDER_PLACEMENT,
        self::FOLDER_PLACEMENT_FEATURED,
        self::FOLDER_PLACEMENT_CONTENT,
        self::FOLDER_PLACEMENT_DOWNLOADS,
        self::FOLDER_CURRENT_AFFAIRS,
        self::FOLDER_CURRENT_AFFAIRS_PDFS,
        self::FOLDER_CURRENT_AFFAIRS_PREVIEWS,
    ];

    /**
     * The Cloudinary SDK instance.
     */
    protected ?Cloudinary $client = null;

    /**
     * Constructor.
     */
    public function __construct(?Cloudinary $client = null)
    {
        $this->client = $client;
    }

    /**
     * Set the Cloudinary client instance (useful for testing and dependency injection).
     */
    public function setClient(?Cloudinary $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Check if Cloudinary credentials are fully configured.
     */
    public function isConfigured(): bool
    {
        $url = (string) config('services.cloudinary.url', '');
        if (! empty($url)) {
            return true;
        }

        $cloudName = (string) config('services.cloudinary.cloud_name', '');
        $apiKey = (string) config('services.cloudinary.api_key', '');
        $apiSecret = (string) config('services.cloudinary.api_secret', '');

        return ! empty($cloudName) && ! empty($apiKey) && ! empty($apiSecret);
    }

    /**
     * Get the active Cloudinary SDK instance.
     */
    public function getClient(): ?Cloudinary
    {
        if ($this->client !== null) {
            return $this->client;
        }

        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $cloudinaryUrl = (string) config('services.cloudinary.url', '');
            if (! empty($cloudinaryUrl)) {
                $this->client = new Cloudinary($cloudinaryUrl);
            } else {
                $this->client = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => (string) config('services.cloudinary.cloud_name'),
                        'api_key' => (string) config('services.cloudinary.api_key'),
                        'api_secret' => (string) config('services.cloudinary.api_secret'),
                    ],
                    'url' => [
                        'secure' => (bool) config('services.cloudinary.secure', true),
                    ],
                    'http' => [
                        'timeout' => (int) config('services.cloudinary.timeout', 30),
                    ],
                ]);
            }

            return $this->client;
        } catch (Throwable $e) {
            Log::error('Failed to initialize Cloudinary client: ' . $this->sanitizeErrorMessage($e->getMessage()));
            return null;
        }
    }

    /**
     * Ping the Cloudinary API to verify connectivity and credentials.
     */
    public function ping(): bool
    {
        $client = $this->getClient();
        if (! $client) {
            return false;
        }

        try {
            $response = $client->adminApi()->ping();
            return isset($response['status']) && strtolower((string) $response['status']) === 'ok';
        } catch (Throwable $e) {
            Log::warning('Cloudinary ping failed: ' . $this->sanitizeErrorMessage($e->getMessage()));
            return false;
        }
    }

    /**
     * Upload an image asset to Cloudinary.
     *
     * @param  \Illuminate\Http\UploadedFile|string|resource  $file
     * @param  string|null  $folder
     * @param  array  $options
     * @return array
     */
    public function uploadImage($file, ?string $folder = self::FOLDER_BASE, array $options = []): array
    {
        $options['resource_type'] = 'image';
        return $this->uploadFile($file, $folder, 'image', $options);
    }

    /**
     * Upload a PDF or raw asset to Cloudinary.
     *
     * @param  \Illuminate\Http\UploadedFile|string|resource  $file
     * @param  string|null  $folder
     * @param  array  $options
     * @return array
     */
    public function uploadRaw($file, ?string $folder = self::FOLDER_BASE, array $options = []): array
    {
        $options['resource_type'] = 'raw';
        return $this->uploadFile($file, $folder, 'raw', $options);
    }

    /**
     * Upload any supported file to Cloudinary with structured response.
     *
     * @param  \Illuminate\Http\UploadedFile|string|resource  $file
     * @param  string|null  $folder
     * @param  string  $resourceType
     * @param  array  $options
     * @return array
     */
    public function uploadFile($file, ?string $folder = self::FOLDER_BASE, string $resourceType = 'auto', array $options = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Cloudinary is not configured. Please check your credentials.',
                'code' => 'NOT_CONFIGURED',
            ];
        }

        $client = $this->getClient();
        if (! $client) {
            return [
                'success' => false,
                'error' => 'Unable to instantiate Cloudinary client.',
                'code' => 'CLIENT_INIT_FAILED',
            ];
        }

        $sourcePath = null;
        $originalFilename = 'asset';

        if ($file instanceof UploadedFile) {
            if (! $file->isValid()) {
                return [
                    'success' => false,
                    'error' => 'The uploaded file is invalid: ' . $file->getErrorMessage(),
                    'code' => 'INVALID_UPLOADED_FILE',
                ];
            }
            $sourcePath = $file->getRealPath();
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        } elseif (is_string($file)) {
            $sourcePath = $file;
            $originalFilename = pathinfo($file, PATHINFO_FILENAME);
        } else {
            $sourcePath = $file;
        }

        $normalizedFolder = $this->normalizeFolder($folder);

        $uploadParams = array_merge([
            'resource_type' => $resourceType,
            'folder' => $normalizedFolder,
            'use_filename' => false,
            'unique_filename' => true,
            'overwrite' => false,
        ], $options);

        // Ensure unique public_id if not explicitly provided
        if (empty($uploadParams['public_id'])) {
            $prefix = Str::slug($originalFilename);
            $prefix = substr($prefix, 0, 30);
            $randomString = Str::random(12);
            $generatedId = $prefix ? "{$prefix}_{$randomString}" : $randomString;
            if ($resourceType === 'raw') {
                $ext = $file instanceof UploadedFile ? $file->getClientOriginalExtension() : pathinfo($sourcePath, PATHINFO_EXTENSION);
                if ($ext) {
                    $generatedId .= '.' . strtolower($ext);
                }
            }
            $uploadParams['public_id'] = $generatedId;
        }

        try {
            $response = $client->uploadApi()->upload($sourcePath, $uploadParams);

            return [
                'success' => true,
                'public_id' => $response['public_id'] ?? null,
                'secure_url' => $response['secure_url'] ?? null,
                'url' => $response['url'] ?? $response['secure_url'] ?? null,
                'format' => $response['format'] ?? null,
                'bytes' => $response['bytes'] ?? null,
                'width' => $response['width'] ?? null,
                'height' => $response['height'] ?? null,
                'resource_type' => $response['resource_type'] ?? $resourceType,
                'original_filename' => $originalFilename,
                'created_at' => $response['created_at'] ?? now()->toIso8601String(),
            ];
        } catch (ApiError $e) {
            $message = $this->sanitizeErrorMessage($e->getMessage());
            Log::error('Cloudinary API upload error: ' . $message, ['code' => $e->getCode()]);
            return [
                'success' => false,
                'error' => 'Cloudinary upload error: ' . $message,
                'code' => 'API_ERROR',
            ];
        } catch (ConfigurationException $e) {
            $message = $this->sanitizeErrorMessage($e->getMessage());
            Log::error('Cloudinary configuration error: ' . $message);
            return [
                'success' => false,
                'error' => 'Cloudinary configuration error: ' . $message,
                'code' => 'CONFIG_ERROR',
            ];
        } catch (Throwable $e) {
            $message = $this->sanitizeErrorMessage($e->getMessage());
            Log::error('Cloudinary upload failure: ' . $message);
            return [
                'success' => false,
                'error' => 'Failed to upload asset: ' . $message,
                'code' => 'UPLOAD_FAILED',
            ];
        }
    }

    /**
     * Replace an existing asset by public ID.
     *
     * @param  string  $publicId
     * @param  \Illuminate\Http\UploadedFile|string|resource  $file
     * @param  array  $options
     * @param  string  $resourceType
     * @return array
     */
    public function replaceAsset(string $publicId, $file, array $options = [], string $resourceType = 'image'): array
    {
        $cleanPublicId = $this->getPublicId($publicId) ?? $publicId;

        $options = array_merge([
            'public_id' => $cleanPublicId,
            'overwrite' => true,
            'invalidate' => true,
        ], $options);

        // Upload to the existing publicId
        return $this->uploadFile($file, null, $resourceType, $options);
    }

    /**
     * Delete a specific asset by public ID.
     *
     * @param  string  $publicId
     * @param  string  $resourceType
     * @param  array  $options
     * @return array
     */
    public function deleteAsset(string $publicId, string $resourceType = 'image', array $options = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'result' => 'not_configured',
                'error' => 'Cloudinary is not configured.',
            ];
        }

        $client = $this->getClient();
        if (! $client) {
            return [
                'success' => false,
                'result' => 'client_error',
                'error' => 'Unable to instantiate Cloudinary client.',
            ];
        }

        $cleanPublicId = $this->getPublicId($publicId) ?? $publicId;

        $destroyParams = array_merge([
            'resource_type' => $resourceType,
            'invalidate' => true,
        ], $options);

        try {
            $response = $client->uploadApi()->destroy($cleanPublicId, $destroyParams);
            $result = $response['result'] ?? 'unknown';

            return [
                'success' => in_array($result, ['ok', 'not found']),
                'result' => $result,
                'public_id' => $cleanPublicId,
                'error' => null,
            ];
        } catch (Throwable $e) {
            $message = $this->sanitizeErrorMessage($e->getMessage());
            Log::error('Cloudinary asset deletion failed: ' . $message);
            return [
                'success' => false,
                'result' => 'error',
                'public_id' => $cleanPublicId,
                'error' => 'Deletion failed: ' . $message,
            ];
        }
    }

    /**
     * Get a secure HTTPS delivery URL for a given public ID.
     *
     * @param  string  $publicId
     * @param  array  $options
     * @param  string  $resourceType
     * @return string|null
     */
    public function getSecureUrl(string $publicId, array $options = [], string $resourceType = 'image'): ?string
    {
        // If it's already an absolute HTTPS URL, return as-is
        if (str_starts_with($publicId, 'https://')) {
            return $publicId;
        }

        if (str_starts_with($publicId, 'http://')) {
            return 'https://' . substr($publicId, 7);
        }

        $cleanPublicId = $this->getPublicId($publicId) ?? $publicId;
        $cloudName = (string) config('services.cloudinary.cloud_name');

        if (empty($cloudName)) {
            return null;
        }

        $client = $this->getClient();
        if ($client) {
            try {
                if ($resourceType === 'raw') {
                    return (string) $client->raw($cleanPublicId)->toUrl();
                }
                if ($resourceType === 'video') {
                    return (string) $client->video($cleanPublicId)->toUrl();
                }
                return (string) $client->image($cleanPublicId)->toUrl();
            } catch (Throwable $e) {
                // Fallback to deterministic URL builder below
            }
        }

        return "https://res.cloudinary.com/{$cloudName}/{$resourceType}/upload/{$cleanPublicId}";
    }

    /**
     * Extract the public ID from a Cloudinary URL or return the public ID directly.
     *
     * @param  string  $urlOrPublicId
     * @return string|null
     */
    public function getPublicId(string $urlOrPublicId): ?string
    {
        $trimmed = trim($urlOrPublicId);
        if (empty($trimmed)) {
            return null;
        }

        // If not a URL, return as public ID
        if (! str_starts_with($trimmed, 'http://') && ! str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        $path = parse_url($trimmed, PHP_URL_PATH);
        if (! $path) {
            return null;
        }

        // Match Cloudinary asset path: /.../{resource_type}/upload/{version?}/{public_id}.{format?}
        if (preg_match('#/(?:image|raw|video)/upload/(?:v\d+/)?(.*?)(?:\.[a-zA-Z0-9]+)?$#', $path, $matches)) {
            return $matches[1] ?: null;
        }

        return null;
    }

    /**
     * Normalize and validate folder path.
     *
     * @param  string|null  $folder
     * @return string
     */
    public function normalizeFolder(?string $folder): string
    {
        if (empty($folder)) {
            return self::FOLDER_BASE;
        }

        $folder = trim($folder, '/');

        // Ensure path starts with aptimedha base prefix
        if (! str_starts_with($folder, self::FOLDER_BASE)) {
            $folder = self::FOLDER_BASE . '/' . $folder;
        }

        return $folder;
    }

    /**
     * Sanitize error message to prevent accidental credential leakage in logs or responses.
     */
    protected function sanitizeErrorMessage(string $message): string
    {
        $secret = (string) config('services.cloudinary.api_secret', '');
        $key = (string) config('services.cloudinary.api_key', '');

        $replacements = [];
        if (! empty($secret)) {
            $replacements[$secret] = '***REDACTED***';
        }
        if (! empty($key)) {
            $replacements[$key] = '***REDACTED***';
        }

        return empty($replacements) ? $message : strtr($message, $replacements);
    }
}

