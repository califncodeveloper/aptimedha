<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Discussion;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AbuseProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Subject $subject;
    protected Topic $topic;
    protected Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Quantitative Aptitude',
            'slug' => 'quantitative-aptitude',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->subject = Subject::create([
            'category_id' => $this->category->id,
            'name' => 'Arithmetic',
            'slug' => 'arithmetic',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->topic = Topic::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
            'display_order' => 1,
            'total_questions_count' => 1,
        ]);

        $this->question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'What is 20% of 150?',
            'option_a' => '20',
            'option_b' => '30',
            'option_c' => '40',
            'option_d' => '50',
            'correct_option' => 'B',
            'explanation' => '20% of 150 is 30.',
            'is_active' => true,
        ]);
    }

    /**
     * 1. Registration rate limit: max 5 attempts per minute per IP.
     */
    public function test_registration_rate_limit(): void
    {
        $ip = '198.51.100.11';

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'register',
            ]),
        ]);

        // First 5 attempts should not be rate-limited (HTTP 429)
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/register', [
                'name' => "User {$i}",
                'username' => "user_ratelimit_{$i}",
                'email' => "user{$i}@example.com",
                'password' => 'SecurePass123!',
                'password_confirmation' => 'SecurePass123!',
                'terms' => 'on',
                'cf-turnstile-response' => "mock-valid-token-{$i}",
            ]);

            $this->assertNotEquals(429, $response->getStatusCode(), "Attempt {$i} was unexpectedly rate limited.");
        }

        // 6th attempt from the same IP must be rejected with HTTP 429
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/register', [
            'name' => 'User 6',
            'username' => 'user_ratelimit_6',
            'email' => 'user6@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => 'on',
            'cf-turnstile-response' => 'mock-valid-token-6',
        ]);

        $response->assertStatus(429);
    }

    /**
     * 2. Registration rejected when CAPTCHA is missing.
     */
    public function test_registration_rejected_when_captcha_is_missing(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Captcha User',
            'username' => 'no_captcha_user',
            'email' => 'nocaptcha@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => 'on',
            // cf-turnstile-response missing
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseMissing('users', ['email' => 'nocaptcha@example.com']);
        $this->assertGuest();
    }

    /**
     * 3. Registration rejected when CAPTCHA verification fails.
     */
    public function test_registration_rejected_when_captcha_verification_fails(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);

        $response = $this->post('/register', [
            'name' => 'Bad Captcha User',
            'username' => 'bad_captcha_user',
            'email' => 'badcaptcha@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => 'on',
            'cf-turnstile-response' => 'invalid-token-from-client',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseMissing('users', ['email' => 'badcaptcha@example.com']);
        $this->assertGuest();
    }

    /**
     * 4. Registration succeeds with mocked valid CAPTCHA.
     */
    public function test_registration_succeeds_with_mocked_valid_captcha(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'register',
            ]),
        ]);

        $response = $this->post('/register', [
            'name' => 'Valid Captcha User',
            'username' => 'valid_captcha_user',
            'email' => 'validcaptcha@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => 'on',
            'cf-turnstile-response' => 'valid-token-from-turnstile',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'validcaptcha@example.com',
            'username' => 'valid_captcha_user',
        ]);
        $this->assertAuthenticated();
    }

    /**
     * 5. Contact form rate limit: max 5 submissions per minute per IP.
     */
    public function test_contact_form_rate_limit(): void
    {
        $ip = '198.51.100.22';

        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'contact',
            ]),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post(route('contact.submit'), [
                'name' => "Contact Tester {$i}",
                'email' => "contact{$i}@example.com",
                'subject' => "Subject {$i}",
                'message' => "This is test message number {$i}.",
                'cf-turnstile-response' => "contact-token-{$i}",
            ]);

            $this->assertNotEquals(429, $response->getStatusCode(), "Contact attempt {$i} was unexpectedly rate limited.");
        }

        // 6th submission from the same IP must be rate limited (429)
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post(route('contact.submit'), [
            'name' => 'Contact Tester 6',
            'email' => 'contact6@example.com',
            'subject' => 'Subject 6',
            'message' => 'This is test message number 6.',
            'cf-turnstile-response' => 'contact-token-6',
        ]);

        $response->assertStatus(429);
    }

    /**
     * 6. Contact submission rejected on invalid CAPTCHA.
     */
    public function test_contact_submission_rejected_on_invalid_captcha(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ]),
        ]);

        $response = $this->post(route('contact.submit'), [
            'name' => 'Hacker Bot',
            'email' => 'bot@example.com',
            'subject' => 'Spam link',
            'message' => 'Spam content advertising irrelevant service.',
            'cf-turnstile-response' => 'fake-captcha-token',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
        $this->assertDatabaseMissing('contact_messages', ['email' => 'bot@example.com']);
    }

    /**
     * 7. Contact submission succeeds with mocked valid CAPTCHA.
     */
    public function test_contact_submission_succeeds_with_mocked_valid_captcha(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'contact',
            ]),
        ]);

        $response = $this->post(route('contact.submit'), [
            'name' => 'Genuine Inquirer',
            'email' => 'genuine@example.com',
            'subject' => 'Question about syllogism practice sets',
            'message' => 'Can you please add more sets on reverse syllogisms?',
            'cf-turnstile-response' => 'valid-contact-turnstile-token',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'genuine@example.com',
            'subject' => 'Question about syllogism practice sets',
        ]);
    }

    /**
     * 8. Discussion rate limit per user: max 10 submissions per minute.
     */
    public function test_discussion_rate_limit_per_user(): void
    {
        $user = User::factory()->create(['username' => 'discussion_prolific']);

        // Submit 10 discussions
        for ($i = 1; $i <= 10; $i++) {
            $response = $this->actingAs($user)->post(route('user.discussions.store'), [
                'question_id' => $this->question->id,
                'comment' => "Discussion comment number {$i} regarding percentages.",
            ]);

            $this->assertNotEquals(429, $response->getStatusCode(), "Discussion submission {$i} was unexpectedly rate limited.");
        }

        // 11th submission by the same user must return 429
        $response = $this->actingAs($user)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'comment' => 'Excessive 11th comment from the same user.',
        ]);

        $response->assertStatus(429);
    }

    /**
     * 9. Different users have independent discussion rate limits.
     */
    public function test_different_users_have_independent_rate_limits(): void
    {
        $userA = User::factory()->create(['username' => 'user_alpha']);
        $userB = User::factory()->create(['username' => 'user_beta']);

        // User A uses up their 10 discussion quota
        for ($i = 1; $i <= 10; $i++) {
            $this->actingAs($userA)->post(route('user.discussions.store'), [
                'question_id' => $this->question->id,
                'comment' => "User A discussion comment {$i}.",
            ]);
        }

        // User A is blocked on attempt 11
        $this->actingAs($userA)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'comment' => 'User A attempt 11.',
        ])->assertStatus(429);

        // User B has not posted yet and must NOT be blocked
        $responseB = $this->actingAs($userB)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'comment' => 'User B first legitimate discussion post.',
        ]);

        $this->assertNotEquals(429, $responseB->getStatusCode());
        $responseB->assertSessionHas('success');
        $this->assertDatabaseHas('discussions', [
            'user_id' => $userB->id,
            'comment' => 'User B first legitimate discussion post.',
        ]);
    }

    /**
     * 10. Password reset request rate limit: max 5 requests per minute per IP.
     */
    public function test_password_reset_request_rate_limit(): void
    {
        $ip = '198.51.100.33';
        $user = User::factory()->create(['email' => 'resetuser@example.com']);

        // 5 reset requests from the same IP
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/forgot-password', [
                'email' => $user->email,
            ]);

            $this->assertNotEquals(429, $response->getStatusCode(), "Password reset request {$i} was unexpectedly rate limited.");
        }

        // 6th reset request from the same IP must return 429
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(429);
    }

    /**
     * 11. Password reset execution rate limit: max 10 requests per minute per IP.
     */
    public function test_password_reset_execution_rate_limit(): void
    {
        $ip = '198.51.100.44';

        // 10 reset attempts from the same IP
        for ($i = 1; $i <= 10; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/reset-password', [
                'token' => 'dummy-token-' . $i,
                'email' => 'user@example.com',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

            $this->assertNotEquals(429, $response->getStatusCode(), "Password reset execution {$i} was unexpectedly rate limited.");
        }

        // 11th reset execution attempt from the same IP must return 429
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])->post('/reset-password', [
            'token' => 'dummy-token-11',
            'email' => 'user@example.com',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertStatus(429);
    }

    /**
     * 12. Report submission rate limit: max 10 submissions per minute per user.
     */
    public function test_report_submission_rate_limit(): void
    {
        $user = User::factory()->create(['username' => 'report_prolific']);

        // Create 12 questions so duplicate pending check is not triggered
        $questions = [];
        for ($i = 1; $i <= 12; $i++) {
            $questions[] = Question::create([
                'category_id' => $this->category->id,
                'subject_id' => $this->subject->id,
                'topic_id' => $this->topic->id,
                'question_number' => 100 + $i,
                'section_number' => 1,
                'question_text' => "Question text for question {$i}?",
                'option_a' => '1',
                'option_b' => '2',
                'option_c' => '3',
                'option_d' => '4',
                'correct_option' => 'A',
                'is_active' => true,
            ]);
        }

        // Submit 10 reports
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($user)->post(route('user.reports.store'), [
                'question_id' => $questions[$i]->id,
                'issue_type' => 'incorrect_answer',
                'description' => "Report description for question {$i}",
            ]);

            $this->assertNotEquals(429, $response->getStatusCode(), "Report submission {$i} was unexpectedly rate limited.");
        }

        // 11th report submission from the same user must be rate limited with 429
        $response = $this->actingAs($user)->post(route('user.reports.store'), [
            'question_id' => $questions[10]->id,
            'issue_type' => 'incorrect_answer',
            'description' => '11th report description',
        ]);

        $response->assertStatus(429);
    }

    /**
     * 13. Bookmark rate limit: max 30 requests per minute per user.
     */
    public function test_bookmark_rate_limit(): void
    {
        $user = User::factory()->create(['username' => 'bookmark_heavy']);

        // Toggle 30 times
        for ($i = 1; $i <= 30; $i++) {
            $response = $this->actingAs($user)->postJson(route('user.bookmarks.toggle', $this->question->id));
            $this->assertNotEquals(429, $response->getStatusCode(), "Bookmark toggle {$i} was unexpectedly rate limited.");
        }

        // 31st toggle attempt must return 429
        $response = $this->actingAs($user)->postJson(route('user.bookmarks.toggle', $this->question->id));
        $response->assertStatus(429);
    }

    /**
     * 14. Existing Google login and password reset regression tests.
     */
    public function test_google_login_and_password_reset_regression(): void
    {
        // 1. Google OAuth redirect test with configured client credentials
        Config::set('services.google.client_id', 'test-google-client-id');
        Config::set('services.google.redirect', 'http://localhost:8000/auth/google/callback');

        $googleResponse = $this->get(route('auth.google'));
        $googleResponse->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $googleResponse->headers->get('Location'));

        // 2. Full Password Reset flow regression
        $user = User::factory()->create([
            'email' => 'regression_reset@example.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $resetResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewBrandNewPass123!',
            'password_confirmation' => 'NewBrandNewPass123!',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $user->refresh();
        $this->assertTrue(Hash::check('NewBrandNewPass123!', $user->password));
    }
}

