<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class GoogleOAuthHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.google.client_id', 'mock-google-client-id');
        Config::set('services.google.client_secret', 'mock-google-client-secret');
        Config::set('services.google.redirect', 'https://aptimedha.onrender.com/auth/google/callback');
    }

    /**
     * Helper to mock successful Google OAuth token exchange.
     */
    protected function fakeGoogleOAuth(array $userinfoData, int $tokenStatus = 200, int $userinfoStatus = 200): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => $tokenStatus === 200
                ? Http::response([
                    'access_token' => 'mock-google-access-token-xyz',
                    'expires_in' => 3600,
                ], 200)
                : Http::response(['error' => 'invalid_grant'], $tokenStatus),
            'https://www.googleapis.com/oauth2/v3/userinfo' => $userinfoStatus === 200
                ? Http::response($userinfoData, 200)
                : Http::response(['error' => 'server_error'], $userinfoStatus),
        ]);
    }

    /**
     * 1. Verified Google email allows login.
     */
    public function test_verified_google_email_allows_login(): void
    {
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-1001',
            'name' => 'Aarav Sharma',
            'email' => 'aarav.sharma@gmail.com',
            'email_verified' => true,
            'picture' => 'https://lh3.googleusercontent.com/avatar/aarav.png',
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-1001'])
            ->get('/auth/google/callback?state=state-1001&code=auth-code-1001');

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'aarav.sharma@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Aarav Sharma', $user->name);
        $this->assertEquals(UserRole::User, $user->role);
        $this->assertNotNull($user->email_verified_at);

        $this->assertDatabaseHas('user_providers', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-sub-1001',
            'avatar' => 'https://lh3.googleusercontent.com/avatar/aarav.png',
        ]);
    }

    /**
     * 2. Unverified Google email is rejected.
     */
    public function test_unverified_google_email_is_rejected(): void
    {
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-1002',
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'email_verified' => false,
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-1002'])
            ->get('/auth/google/callback?state=state-1002&code=auth-code-1002');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your Google account email is not verified. Please verify your email with Google or sign in using your password.');
        $this->assertGuest();

        $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
        $this->assertDatabaseMissing('user_providers', ['provider_id' => 'google-sub-1002']);
    }

    /**
     * 2b. Omitted/null email_verified is also rejected.
     */
    public function test_omitted_email_verified_claim_is_rejected(): void
    {
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-1002b',
            'name' => 'No Verification Claim User',
            'email' => 'noclaim@example.com',
            // email_verified omitted entirely
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-1002b'])
            ->get('/auth/google/callback?state=state-1002b&code=auth-code-1002b');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'noclaim@example.com']);
    }

    /**
     * 3. Missing Google email is rejected.
     */
    public function test_missing_google_email_is_rejected(): void
    {
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-1003',
            'name' => 'Missing Email User',
            'email' => null,
            'email_verified' => true,
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-1003'])
            ->get('/auth/google/callback?state=state-1003&code=auth-code-1003');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Unable to retrieve email or identity from Google.');
        $this->assertGuest();
        $this->assertDatabaseMissing('user_providers', ['provider_id' => 'google-sub-1003']);
    }

    /**
     * 4. Existing Google provider ID authenticates the correct user.
     */
    public function test_existing_google_provider_id_authenticates_correct_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Existing User',
            'username' => 'existing_user_4',
            'email' => 'existing4@example.com',
        ]);

        UserProvider::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-sub-1004',
            'avatar' => 'https://example.com/old-avatar.png',
        ]);

        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-1004',
            'name' => 'Existing User',
            'email' => 'existing4@example.com',
            'email_verified' => true,
            'picture' => 'https://example.com/new-avatar.png',
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-1004'])
            ->get('/auth/google/callback?state=state-1004&code=auth-code-1004');

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Verify avatar was updated
        $this->assertDatabaseHas('user_providers', [
            'user_id' => $user->id,
            'provider_id' => 'google-sub-1004',
            'avatar' => 'https://example.com/new-avatar.png',
        ]);
    }

    /**
     * 5. Verified matching email follows the approved account-linking flow.
     */
    public function test_verified_matching_email_follows_approved_account_linking_flow(): void
    {
        $existingUser = User::factory()->create([
            'name' => 'Pooja Verma',
            'username' => 'pooja_v',
            'email' => 'pooja.verma@example.com',
            'role' => UserRole::User,
        ]);

        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-1005',
            'name' => 'Pooja Verma',
            'email' => 'pooja.verma@example.com',
            'email_verified' => true,
            'picture' => 'https://lh3.googleusercontent.com/avatar/pooja.png',
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-1005'])
            ->get('/auth/google/callback?state=state-1005&code=auth-code-1005');

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($existingUser);

        $this->assertDatabaseHas('user_providers', [
            'user_id' => $existingUser->id,
            'provider' => 'google',
            'provider_id' => 'google-sub-1005',
            'avatar' => 'https://lh3.googleusercontent.com/avatar/pooja.png',
        ]);
    }

    /**
     * 6. Provider ID cannot be linked to another user.
     * Part A: Provider ID already linked to User A cannot authenticate User B with conflicting email.
     */
    public function test_provider_id_cannot_be_linked_to_another_user(): void
    {
        $userA = User::factory()->create([
            'email' => 'usera@example.com',
            'username' => 'user_a',
        ]);

        $userB = User::factory()->create([
            'email' => 'userb@example.com',
            'username' => 'user_b',
        ]);

        // User A already has google-sub-conflict linked
        UserProvider::create([
            'user_id' => $userA->id,
            'provider' => 'google',
            'provider_id' => 'google-sub-conflict',
        ]);

        // Google returns google-sub-conflict with User B's email
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-conflict',
            'name' => 'User B Conflicting',
            'email' => 'userb@example.com',
            'email_verified' => true,
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-conflict'])
            ->get('/auth/google/callback?state=state-conflict&code=auth-code-conflict');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'This Google account is already linked to another AptiMedha account.');
        $this->assertGuest();

        // Ensure User B was NOT linked to google-sub-conflict
        $this->assertDatabaseMissing('user_providers', [
            'user_id' => $userB->id,
            'provider_id' => 'google-sub-conflict',
        ]);
    }

    /**
     * 6b. User already linked to Google ID A cannot have their account overwritten by Google ID B.
     */
    public function test_user_cannot_have_existing_google_link_overwritten(): void
    {
        $user = User::factory()->create([
            'email' => 'linked@example.com',
            'username' => 'linked_user',
        ]);

        UserProvider::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-sub-original',
        ]);

        // Google returns new ID google-sub-second for the same email
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-second',
            'name' => 'Linked User Second',
            'email' => 'linked@example.com',
            'email_verified' => true,
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-overwrite'])
            ->get('/auth/google/callback?state=state-overwrite&code=auth-code-overwrite');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'This AptiMedha account is already linked to a different Google account.');
        $this->assertGuest();

        // Ensure original provider record remains intact
        $this->assertDatabaseHas('user_providers', [
            'user_id' => $user->id,
            'provider_id' => 'google-sub-original',
        ]);
        $this->assertDatabaseMissing('user_providers', [
            'provider_id' => 'google-sub-second',
        ]);
    }

    /**
     * 7. Existing admin role cannot be overwritten by OAuth.
     */
    public function test_existing_admin_role_cannot_be_overwritten_by_oauth(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Chief Admin',
            'username' => 'chief_admin',
            'email' => 'admin@aptimedha.com',
        ]);

        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-admin',
            'name' => 'Chief Admin Google',
            'email' => 'admin@aptimedha.com',
            'email_verified' => true,
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-admin'])
            ->get('/auth/google/callback?state=state-admin&code=auth-code-admin');

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $admin->refresh();
        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->isAdmin());
    }

    /**
     * 8. New Google user receives standard User role.
     */
    public function test_new_google_user_receives_standard_user_role(): void
    {
        // Even if Google payload includes arbitrary role claims
        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-newuser',
            'name' => 'Regular Student',
            'email' => 'student.fresh@gmail.com',
            'email_verified' => true,
            'role' => 'admin',
            'isAdmin' => true,
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-newuser'])
            ->get('/auth/google/callback?state=state-newuser&code=auth-code-newuser');

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'student.fresh@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::User, $user->role);
        $this->assertFalse($user->isAdmin());
    }

    /**
     * 9. Google OAuth state mismatch is rejected.
     */
    public function test_google_oauth_state_mismatch_is_rejected(): void
    {
        $response = $this->withSession(['google_oauth_state' => 'expected-secret-state'])
            ->get('/auth/google/callback?state=forged-state&code=any-code');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Invalid or expired OAuth state. Please try signing in again.');
        $this->assertGuest();
    }

    /**
     * 10. Google OAuth cancellation is handled safely.
     */
    public function test_google_oauth_cancellation_is_handled_safely(): void
    {
        $response = $this->withSession(['google_oauth_state' => 'some-state'])
            ->get('/auth/google/callback?error=access_denied&error_description=User+cancelled');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Google Sign-In was cancelled or failed.');
        $this->assertGuest();
    }

    /**
     * 11. Token exchange failure is handled safely.
     */
    public function test_token_exchange_failure_is_handled_safely(): void
    {
        $this->fakeGoogleOAuth([], tokenStatus: 400);

        $response = $this->withSession(['google_oauth_state' => 'state-tokenerr'])
            ->get('/auth/google/callback?state=state-tokenerr&code=bad-code');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Failed to authenticate with Google. Please try again.');
        $this->assertGuest();
    }

    /**
     * 11b. Google API unavailable / network exception handled safely.
     */
    public function test_google_api_network_exception_is_handled_safely(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
            },
        ]);

        $response = $this->withSession(['google_oauth_state' => 'state-timeout'])
            ->get('/auth/google/callback?state=state-timeout&code=timeout-code');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Failed to connect to Google authentication service. Please try again.');
        $this->assertGuest();
    }

    /**
     * 12. Duplicate account creation is handled safely.
     */
    public function test_duplicate_account_creation_handled_safely(): void
    {
        // Pre-create the user in database
        $user = User::factory()->create([
            'email' => 'duplicate.race@example.com',
            'username' => 'duplicate_race',
        ]);

        // Pre-create provider
        UserProvider::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-sub-race',
        ]);

        $this->fakeGoogleOAuth([
            'sub' => 'google-sub-race',
            'name' => 'Duplicate Race User',
            'email' => 'duplicate.race@example.com',
            'email_verified' => true,
        ]);

        // Second request arrives with same credentials
        $response = $this->withSession(['google_oauth_state' => 'state-race'])
            ->get('/auth/google/callback?state=state-race&code=code-race');

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 13. Existing email/password login continues working.
     */
    public function test_existing_email_password_login_continues_working(): void
    {
        $user = User::factory()->create([
            'email' => 'password.user@example.com',
            'username' => 'password_user',
            'password' => Hash::make('ValidPass123!'),
            'role' => UserRole::User,
        ]);

        // Test email login
        $emailResponse = $this->post('/login', [
            'email' => 'password.user@example.com',
            'password' => 'ValidPass123!',
        ]);

        $emailResponse->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);

        Auth::logout();

        // Test username login
        $usernameResponse = $this->post('/login', [
            'email' => 'password_user',
            'password' => 'ValidPass123!',
        ]);

        $usernameResponse->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 14. Existing password reset continues working.
     */
    public function test_existing_password_reset_continues_working(): void
    {
        $user = User::factory()->create([
            'email' => 'reset.flow@example.com',
            'password' => Hash::make('InitialPassword123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'reset.flow@example.com',
            'password' => 'NewHardenedPass123!',
            'password_confirmation' => 'NewHardenedPass123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('NewHardenedPass123!', $user->password));

        // Authenticate with new password
        $loginResponse = $this->post('/login', [
            'email' => 'reset.flow@example.com',
            'password' => 'NewHardenedPass123!',
        ]);

        $loginResponse->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}

