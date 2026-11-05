<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CloudinaryService;
use Cloudinary\Api\ApiResponse;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Mockery;
use Tests\TestCase;

class CloudinaryCoreServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_service_detects_configuration_status(): void
    {
        $service = new CloudinaryService();

        Config::set('services.cloudinary.cloud_name', 'test_cloud');
        Config::set('services.cloudinary.api_key', 'test_key');
        Config::set('services.cloudinary.api_secret', 'test_secret');
        Config::set('services.cloudinary.url', null);

        $this->assertTrue($service->isConfigured());

        Config::set('services.cloudinary.cloud_name', '');
        $this->assertFalse($service->isConfigured());

        Config::set('services.cloudinary.url', 'cloudinary://key:secret@test_cloud');
        $this->assertTrue($service->isConfigured());
    }

    public function test_folder_normalization_enforces_aptimedha_prefix(): void
    {
        $service = new CloudinaryService();

        $this->assertEquals('aptimedha', $service->normalizeFolder(null));
        $this->assertEquals('aptimedha', $service->normalizeFolder(''));
        $this->assertEquals('aptimedha/mcq/questions', $service->normalizeFolder('aptimedha/mcq/questions'));
        $this->assertEquals('aptimedha/mcq/questions', $service->normalizeFolder('mcq/questions'));
        $this->assertEquals('aptimedha/blogs/featured', $service->normalizeFolder('/blogs/featured/'));
    }

    public function test_public_id_extraction_handles_urls_and_raw_ids(): void
    {
        $service = new CloudinaryService();

        // Raw public ID
        $this->assertEquals('aptimedha/mcq/questions/q1_123', $service->getPublicId('aptimedha/mcq/questions/q1_123'));

        // Standard HTTPS Cloudinary Image URL
        $url1 = 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1746433221/aptimedha/mcq/questions/sample_img.jpg';
        $this->assertEquals('aptimedha/mcq/questions/sample_img', $service->getPublicId($url1));

        // URL without version prefix
        $url2 = 'https://res.cloudinary.com/j7zcyoc5/image/upload/aptimedha/blogs/featured/hero_post.webp';
        $this->assertEquals('aptimedha/blogs/featured/hero_post', $service->getPublicId($url2));

        // URL with query parameters
        $url3 = 'https://res.cloudinary.com/j7zcyoc5/raw/upload/v1/aptimedha/placement/resume_guide.pdf?_a=ABC1234';
        $this->assertEquals('aptimedha/placement/resume_guide', $service->getPublicId($url3));

        // Null / empty
        $this->assertNull($service->getPublicId(''));
    }

    public function test_secure_url_always_ensures_https(): void
    {
        Config::set('services.cloudinary.cloud_name', 'j7zcyoc5');
        Config::set('services.cloudinary.api_key', '123456');
        Config::set('services.cloudinary.api_secret', 'secret');

        $service = new CloudinaryService();

        // Direct HTTPS URL
        $httpsUrl = 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/test.jpg';
        $this->assertEquals($httpsUrl, $service->getSecureUrl($httpsUrl));

        // Upgrades HTTP URL to HTTPS
        $httpUrl = 'http://res.cloudinary.com/j7zcyoc5/image/upload/v1/test.jpg';
        $this->assertEquals('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/test.jpg', $service->getSecureUrl($httpUrl));

        // Converts public ID into HTTPS URL
        $generated = $service->getSecureUrl('aptimedha/mcq/questions/test_q');
        $this->assertNotNull($generated);
        $this->assertStringStartsWith('https://', $generated);
        $this->assertStringContainsString('aptimedha/mcq/questions/test_q', $generated);
    }

    public function test_unconfigured_service_returns_safe_error_structure(): void
    {
        Config::set('services.cloudinary.cloud_name', '');
        Config::set('services.cloudinary.api_key', '');
        Config::set('services.cloudinary.api_secret', '');
        Config::set('services.cloudinary.url', '');

        $service = new CloudinaryService();

        $result = $service->uploadImage('some/fake/path.jpg');

        $this->assertFalse($result['success']);
        $this->assertEquals('NOT_CONFIGURED', $result['code']);
        $this->assertStringContainsString('not configured', $result['error']);

        $deleteResult = $service->deleteAsset('some/id');
        $this->assertFalse($deleteResult['success']);
        $this->assertEquals('not_configured', $deleteResult['result']);
    }

    public function test_guest_cannot_access_cloudinary_test_interface(): void
    {
        $response = $this->get(route('admin.cloudinary-test.index'));
        $response->assertRedirect(route('login'));

        $postResponse = $this->post(route('admin.cloudinary-test.upload'), []);
        $postResponse->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_cloudinary_test_interface(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.cloudinary-test.index'));
        $response->assertForbidden();

        $postResponse = $this->actingAs($user)->post(route('admin.cloudinary-test.upload'), []);
        $postResponse->assertForbidden();
    }

    public function test_admin_can_access_cloudinary_test_interface(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.cloudinary-test.index'));
        $response->assertOk();
        $response->assertSee('Cloudinary Core Service Verification');
        $response->assertSee('Configuration Status');
        $response->assertSee('One-Click Live Verification');
        $response->assertSee('Manual Test Upload');
    }

    public function test_upload_validation_rejects_non_image_files(): void
    {
        $admin = User::factory()->admin()->create();

        $fakePdf = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.cloudinary-test.upload'), [
            'image' => $fakePdf,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_upload_validation_rejects_oversized_files(): void
    {
        $admin = User::factory()->admin()->create();

        // 3MB image exceeds the 2MB (2048KB) validation limit
        $oversizedImage = UploadedFile::fake()->create('huge.jpg', 3000, 'image/jpeg');

        $response = $this->actingAs($admin)->post(route('admin.cloudinary-test.upload'), [
            'image' => $oversizedImage,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function test_mocked_cloudinary_upload_and_destroy_flow(): void
    {
        $mockClient = Mockery::mock(Cloudinary::class);
        $mockUploadApi = Mockery::mock(UploadApi::class);

        $mockClient->shouldReceive('uploadApi')->andReturn($mockUploadApi);

        // Mock upload
        $apiResponseData = [
            'public_id' => 'aptimedha/blogs/featured/test_mock_123',
            'secure_url' => 'https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/featured/test_mock_123.jpg',
            'format' => 'jpg',
            'bytes' => 12345,
            'width' => 800,
            'height' => 600,
            'resource_type' => 'image',
            'created_at' => '2026-11-05T08:00:00Z',
        ];
        $mockApiResponse = new ApiResponse($apiResponseData, []);

        $mockUploadApi->shouldReceive('upload')
            ->once()
            ->andReturn($mockApiResponse);

        // Mock destroy
        $mockUploadApi->shouldReceive('destroy')
            ->once()
            ->with('aptimedha/blogs/featured/test_mock_123', Mockery::subset(['resource_type' => 'image']))
            ->andReturn(new ApiResponse(['result' => 'ok'], []));

        $service = new CloudinaryService();
        Config::set('services.cloudinary.cloud_name', 'j7zcyoc5');
        Config::set('services.cloudinary.api_key', '123456');
        Config::set('services.cloudinary.api_secret', 'secret');
        $service->setClient($mockClient);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_mock_');
        file_put_contents($tempFile, 'image_data');

        $result = $service->uploadImage($tempFile, CloudinaryService::FOLDER_BLOGS_FEATURED);
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }

        $this->assertTrue($result['success']);
        $this->assertEquals('aptimedha/blogs/featured/test_mock_123', $result['public_id']);
        $this->assertEquals('https://res.cloudinary.com/j7zcyoc5/image/upload/v1/aptimedha/blogs/featured/test_mock_123.jpg', $result['secure_url']);
        $this->assertEquals(800, $result['width']);

        // Test delete
        $deleteResult = $service->deleteAsset($result['public_id']);
        $this->assertTrue($deleteResult['success']);
        $this->assertEquals('ok', $deleteResult['result']);
    }
}
