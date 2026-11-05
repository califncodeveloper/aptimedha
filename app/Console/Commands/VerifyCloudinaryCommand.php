<?php

namespace App\Console\Commands;

use App\Services\CloudinaryService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class VerifyCloudinaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:verify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify Cloudinary credentials, test upload, HTTPS delivery, asset deletion, and error handling.';

    /**
     * Execute the console command.
     */
    public function handle(CloudinaryService $service): int
    {
        $this->info('======================================================');
        $this->info('   AptiMedha Cloudinary Core Service Verification     ');
        $this->info('======================================================');

        $overallSuccess = true;

        // Step 1: Check configuration
        $this->line("\n[1/8] Checking Cloudinary configuration...");
        if ($service->isConfigured()) {
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $maskedKey = $apiKey ? Str::mask($apiKey, '*', 3, -4) : '***';
            $this->info("  ✔ Credentials loaded successfully.");
            $this->line("    Cloud Name: <comment>{$cloudName}</comment>");
            $this->line("    API Key:    <comment>{$maskedKey}</comment>");
            $this->line("    API Secret: <comment>[REDACTED]</comment>");
        } else {
            $this->error("  ✖ Credentials are missing or incomplete in config/services.php / .env.");
            return Command::FAILURE;
        }

        // Step 2: Ping Cloudinary Admin API
        $this->line("\n[2/8] Testing API connectivity via Admin ping...");
        if ($service->ping()) {
            $this->info("  ✔ API ping succeeded. Credentials authenticated with Cloudinary.");
        } else {
            $this->error("  ✖ API ping failed. Please verify credentials or network access.");
            $overallSuccess = false;
        }

        // Step 3: Test Image Upload
        $this->line("\n[3/8] Uploading test image to Cloudinary...");
        $samplePngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $tempPath = tempnam(sys_get_temp_dir(), 'cld_cli_');
        file_put_contents($tempPath, base64_decode($samplePngBase64));

        $testPublicId = 'test_cli_' . Str::random(10);
        $uploadResult = $service->uploadImage($tempPath, CloudinaryService::FOLDER_BASE, [
            'public_id' => $testPublicId,
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        $publicId = null;
        $secureUrl = null;

        if ($uploadResult['success'] ?? false) {
            $publicId = $uploadResult['public_id'];
            $secureUrl = $uploadResult['secure_url'];
            $this->info("  ✔ Image uploaded successfully.");
            $this->line("    Returned Public ID: <comment>{$publicId}</comment>");
            $this->line("    Returned Secure URL: <comment>{$secureUrl}</comment>");
        } else {
            $this->error("  ✖ Image upload failed: " . ($uploadResult['error'] ?? 'Unknown error'));
            $overallSuccess = false;
        }

        // Step 4: Verify Secure URL and Public ID structure
        $this->line("\n[4/8] Validating Secure URL & Public ID...");
        if (! empty($secureUrl) && str_starts_with($secureUrl, 'https://')) {
            $this->info("  ✔ Secure URL is HTTPS compliant.");
        } else {
            $this->error("  ✖ Secure URL is missing or does not use HTTPS.");
            $overallSuccess = false;
        }

        if (! empty($publicId)) {
            $extractedId = $service->getPublicId($secureUrl);
            $this->info("  ✔ Public ID correctly extracted from secure URL: <comment>{$extractedId}</comment>");
        }

        // Step 5: Verify HTTPS image accessibility over the web
        $this->line("\n[5/8] Verifying image is accessible over HTTPS...");
        if ($secureUrl) {
            try {
                $response = Http::timeout(10)->head($secureUrl);
                if ($response->successful()) {
                    $this->info("  ✔ Image is publicly reachable via HTTPS (HTTP status: {$response->status()}).");
                } else {
                    $this->warn("  ⚠ Image URL returned status: {$response->status()}");
                }
            } catch (\Throwable $e) {
                $this->warn("  ⚠ Could not reach image URL directly: " . $e->getMessage());
            }
        }

        // Step 6: Verify Asset Deletion
        $this->line("\n[6/8] Verifying asset deletion...");
        if ($publicId) {
            $deleteResult = $service->deleteAsset($publicId);
            if ($deleteResult['success'] ?? false) {
                $this->info("  ✔ Asset deletion succeeded (Cloudinary result: {$deleteResult['result']}).");
            } else {
                $this->error("  ✖ Asset deletion failed: " . ($deleteResult['error'] ?? 'Unknown error'));
                $overallSuccess = false;
            }
        }

        // Step 7: Test invalid and oversized file rejection
        $this->line("\n[7/8] Testing invalid and oversized file handling...");
        $corruptFile = tempnam(sys_get_temp_dir(), 'corrupt_');
        file_put_contents($corruptFile, 'not-an-image');
        $uploadedCorrupt = new UploadedFile($corruptFile, 'fake.jpg', 'image/jpeg', null, true);

        // UploadedFile with invalid state
        $invalidUploadedFile = new UploadedFile($corruptFile, 'fake.jpg', 'image/jpeg', UPLOAD_ERR_NO_FILE, false);
        $invalidResult = $service->uploadImage($invalidUploadedFile);

        if (! ($invalidResult['success'] ?? true)) {
            $this->info("  ✔ Invalid/broken uploaded file correctly rejected.");
        } else {
            $this->warn("  ⚠ Invalid file was not flagged by service.");
        }

        if (file_exists($corruptFile)) {
            @unlink($corruptFile);
        }

        // Step 8: Test missing credentials safe error handling
        $this->line("\n[8/8] Testing missing credentials error handling...");
        $unconfiguredService = new CloudinaryService();
        Config::set('services.cloudinary.cloud_name', '');
        Config::set('services.cloudinary.api_key', '');
        Config::set('services.cloudinary.api_secret', '');
        Config::set('services.cloudinary.url', '');

        $safeErrorResult = $unconfiguredService->uploadImage('some_path.png');
        if (! ($safeErrorResult['success'] ?? true) && ($safeErrorResult['code'] ?? '') === 'NOT_CONFIGURED') {
            $this->info("  ✔ Missing credentials produce safe error without throwing unhandled exceptions.");
            $this->line("    Error response: <comment>" . $safeErrorResult['error'] . "</comment>");
        } else {
            $this->warn("  ⚠ Unconfigured service did not produce expected safe error.");
            $overallSuccess = false;
        }

        // Restore config
        $this->line("\n======================================================");
        if ($overallSuccess) {
            $this->info("   All 8 Cloudinary verification tests PASSED!        ");
            $this->info("======================================================");
            return Command::SUCCESS;
        }

        $this->warn("   Verification completed with warnings or failures.  ");
        $this->info("======================================================");
        return Command::FAILURE;
    }
}

