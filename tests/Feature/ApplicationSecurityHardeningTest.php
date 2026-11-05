<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Discussion;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApplicationSecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Blade/JavaScript interpolation safely handles malicious strings.
     */
    public function test_blade_javascript_interpolation_safely_handles_malicious_strings(): void
    {
        $user = User::factory()->create([
            'name' => 'Discussion Author',
            'username' => 'disc_author',
        ]);

        $category = Category::create([
            'name' => 'Aptitude & "Quotes" <script>',
            'slug' => 'aptitude-test',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => "Arithmetic & O'Reilly",
            'slug' => 'arithmetic',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => "Percentages & O'Connor's \"Shortcut\" <script>alert(1)</script>",
            'slug' => 'percentages',
            'is_active' => true,
        ]);

        $question = Question::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'What is 10% of 100?',
            'option_a' => '5',
            'option_b' => '10',
            'option_c' => '15',
            'option_d' => '20',
            'correct_option' => 'B',
            'is_active' => true,
        ]);

        $maliciousComment = "Malicious discussion with 'single quotes', \"double quotes\", <script>alert('xss')</script> and \n newlines";

        $discussion = Discussion::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'comment' => $maliciousComment,
            'status' => 'approved',
        ]);

        // A. Verify MCQ show page renders safe @js encoding
        $mcqResponse = $this->actingAs($user)->get(route('mcq.show', $question->id));
        $mcqResponse->assertStatus(200);
        $mcqContent = $mcqResponse->getContent();

        // Must not contain unsafe addslashes
        $this->assertStringNotContainsString('addslashes(', $mcqContent);
        // Must contain @js hex-escaped single quotes and script tags
        $this->assertStringContainsString('\u0027single quotes\u0027', $mcqContent);
        $this->assertStringContainsString('\u003Cscript\u003E', $mcqContent);

        // B. Verify Category show page renders safe topic filtering in Alpine
        $catResponse = $this->get(route('categories.show', $category->slug));
        $catResponse->assertStatus(200);
        $catContent = $catResponse->getContent();
        $this->assertStringNotContainsString("addslashes(", $catContent);
        $this->assertStringContainsString('\u0027connor\u0027s', $catContent);

        // C. Verify User Discussions page renders safe @js openEdit call
        $userDiscResponse = $this->actingAs($user)->get(route('user.discussions'));
        $userDiscResponse->assertStatus(200);
        $userDiscContent = $userDiscResponse->getContent();
        $this->assertStringNotContainsString("addslashes(", $userDiscContent);
        $this->assertStringContainsString('\u0027single quotes\u0027', $userDiscContent);
    }

    /**
     * 2. Security headers are present on normal application responses.
     */
    public function test_security_headers_are_present_on_normal_responses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $this->assertTrue($response->headers->has('Content-Security-Policy-Report-Only'));

        $csp = $response->headers->get('Content-Security-Policy-Report-Only');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
    }

    /**
     * 3. HSTS is applied only under the correct HTTPS conditions.
     */
    public function test_hsts_applied_only_under_https_conditions(): void
    {
        // Plain HTTP: HSTS header must NOT be sent
        $httpResponse = $this->get('http://localhost/');
        $this->assertFalse($httpResponse->headers->has('Strict-Transport-Security'));

        // HTTPS (simulated directly via secure request): HSTS must be present
        $httpsResponse = $this->get('https://localhost/');
        $httpsResponse->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        // Reverse proxy (Render HTTPS forwarded request): HSTS must be present
        $proxyResponse = $this->withHeaders(['X-Forwarded-Proto' => 'https'])->get('/');
        $proxyResponse->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    /**
     * 4. User registration cannot create an Admin account through mass assignment.
     */
    public function test_user_registration_cannot_create_admin_through_mass_assignment(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'register',
            ]),
        ]);

        $response = $this->post('/register', [
            'name' => 'Attacker User',
            'username' => 'attacker_u',
            'email' => 'attacker@example.com',
            'password' => 'AttackerPass123!',
            'password_confirmation' => 'AttackerPass123!',
            'role' => 'admin',
            'cf-turnstile-response' => 'mock-turnstile-token',
        ]);

        $response->assertRedirect(route('user.dashboard'));

        $user = User::where('email', 'attacker@example.com')->firstOrFail();
        $this->assertEquals(UserRole::User, $user->role);
        $this->assertFalse($user->isAdmin());

        // Also test direct Eloquent mass assignment
        $massAssigned = User::create([
            'name' => 'Mass Assigned Target',
            'username' => 'mass_assigned',
            'email' => 'mass@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => UserRole::Admin, // Should be ignored because role is not in fillable
        ]);

        $this->assertNull($massAssigned->role);
        $this->assertFalse($massAssigned->isAdmin());
    }

    /**
     * 5. A normal user cannot modify their own role to Admin.
     */
    public function test_normal_user_cannot_modify_own_role_to_admin(): void
    {
        $user = User::factory()->create([
            'name' => 'Legit User',
            'username' => 'legit_user',
            'email' => 'legit@example.com',
        ]);

        $response = $this->actingAs($user)->put('/dashboard/profile', [
            'name' => 'Legit User Renamed',
            'username' => 'legit_user_new',
            'email' => 'legit_new@example.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('user.profile'));

        $user->refresh();
        $this->assertEquals(UserRole::User, $user->role);
        $this->assertFalse($user->isAdmin());
        $this->assertEquals('Legit User Renamed', $user->name);
    }

    /**
     * 6. Legitimate admin role-management functionality continues working.
     */
    public function test_admin_role_management_continues_working(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'super_admin',
            'email' => 'admin.manage@aptimedha.com',
        ]);

        $targetUser = User::factory()->create([
            'username' => 'student_promotee',
            'email' => 'promotee@example.com',
        ]);

        // Promote student to admin
        $promoteResponse = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => $targetUser->name,
            'username' => $targetUser->username,
            'email' => $targetUser->email,
            'role' => UserRole::Admin->value,
        ]);

        $promoteResponse->assertRedirect(route('admin.users.index'));
        $targetUser->refresh();
        $this->assertEquals(UserRole::Admin, $targetUser->role);
        $this->assertTrue($targetUser->isAdmin());

        // Demote admin back to user
        $demoteResponse = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => $targetUser->name,
            'username' => $targetUser->username,
            'email' => $targetUser->email,
            'role' => UserRole::User->value,
        ]);

        $demoteResponse->assertRedirect(route('admin.users.index'));
        $targetUser->refresh();
        $this->assertEquals(UserRole::User, $targetUser->role);
        $this->assertFalse($targetUser->isAdmin());
    }

    /**
     * 7. SVG uploads are rejected by the Cloudinary test controller.
     */
    public function test_svg_uploads_are_rejected_by_cloudinary_test_controller(): void
    {
        $admin = User::factory()->admin()->create();

        $svgFile = UploadedFile::fake()->createWithContent(
            'malicious.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("xss")</script></svg>'
        );

        $response = $this->actingAs($admin)->post(route('admin.cloudinary-test.upload'), [
            'image' => $svgFile,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    /**
     * 8. Supported image formats continue to work in Cloudinary test controller.
     */
    public function test_supported_image_formats_work_in_cloudinary_test_controller(): void
    {
        $admin = User::factory()->admin()->create();

        $mockCloudinary = $this->createMock(CloudinaryService::class);
        $mockCloudinary->expects($this->exactly(3))
            ->method('uploadImage')
            ->willReturn([
                'success' => true,
                'public_id' => 'test_123',
                'secure_url' => 'https://res.cloudinary.com/test/image/upload/v1/test_123.jpg',
            ]);

        $this->app->instance(CloudinaryService::class, $mockCloudinary);

        // A. PNG (1x1 valid png)
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $png = UploadedFile::fake()->createWithContent('test.png', $pngBytes);
        $resPng = $this->actingAs($admin)->post(route('admin.cloudinary-test.upload'), ['image' => $png]);
        $resPng->assertSessionHasNoErrors();
        $resPng->assertSessionHas('success');

        // B. JPG (1x1 valid jpeg)
        $jpgBytes = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        $jpg = UploadedFile::fake()->createWithContent('test.jpg', $jpgBytes);
        $resJpg = $this->actingAs($admin)->post(route('admin.cloudinary-test.upload'), ['image' => $jpg]);
        $resJpg->assertSessionHasNoErrors();
        $resJpg->assertSessionHas('success');

        // C. WebP (1x1 valid webp)
        $webpBytes = base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==');
        $webp = UploadedFile::fake()->createWithContent('test.webp', $webpBytes);
        $resWebp = $this->actingAs($admin)->post(route('admin.cloudinary-test.upload'), ['image' => $webp]);
        $resWebp->assertSessionHasNoErrors();
        $resWebp->assertSessionHas('success');
    }

    /**
     * 9. Unauthenticated users cannot access Cloudinary test controller.
     */
    public function test_unauthenticated_and_regular_users_cannot_access_cloudinary_test(): void
    {
        // Guest redirected to login
        $this->get(route('admin.cloudinary-test.index'))->assertRedirect(route('login'));

        // Standard user forbidden (403)
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.cloudinary-test.index'))->assertStatus(403);
    }
}
