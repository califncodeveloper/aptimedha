<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'Aditi Sharma',
            'username' => 'aditi_s',
            'email' => 'aditi@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Aditi Sharma',
            'username' => 'aditi_s',
            'email' => 'aditi@example.com',
            'role' => UserRole::User->value,
        ]);
    }

    public function test_duplicate_email_registration_rejected(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'username' => 'first_user',
        ]);

        $response = $this->post('/register', [
            'name' => 'Second User',
            'username' => 'second_user',
            'email' => 'existing@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_duplicate_username_registration_rejected(): void
    {
        User::factory()->create([
            'username' => 'taken_handle',
            'email' => 'first@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Second User',
            'username' => 'taken_handle',
            'email' => 'second@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    public function test_invalid_registration_validation(): void
    {
        // Empty data
        $response = $this->post('/register', []);
        $response->assertSessionHasErrors(['name', 'username', 'email', 'password']);

        // Password too short and confirmation mismatch
        $response2 = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);
        $response2->assertSessionHasErrors(['password']);
    }

    public function test_successful_username_login(): void
    {
        $user = User::factory()->create([
            'username' => 'vikram_r',
            'email' => 'vikram@example.com',
            'password' => Hash::make('MyPassword123'),
            'role' => UserRole::User,
        ]);

        $response = $this->post('/login', [
            'email' => 'vikram_r',
            'password' => 'MyPassword123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_email_login(): void
    {
        $user = User::factory()->create([
            'username' => 'vikram_email',
            'email' => 'vikram.login@example.com',
            'password' => Hash::make('MyPassword123'),
            'role' => UserRole::User,
        ]);

        $response = $this->post('/login', [
            'email' => 'vikram.login@example.com',
            'password' => 'MyPassword123',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_password_rejection(): void
    {
        User::factory()->create([
            'email' => 'priya@example.com',
            'password' => Hash::make('CorrectPassword123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'priya@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_rate_limiting(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'target@example.com',
                'password' => 'wrong-pass',
            ]);
        }

        // 6th attempt must be throttled
        $response = $this->post('/login', [
            'email' => 'target@example.com',
            'password' => 'wrong-pass',
        ]);

        $response->assertSessionHasErrors(['email']);
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Too many login attempts', $errorMessage);
    }

    public function test_successful_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_guest_access_to_protected_pages_redirects_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/dashboard/profile')->assertRedirect(route('login'));
        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/mcqs')->assertRedirect(route('login'));
    }

    public function test_standard_user_access_to_user_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $this->actingAs($user)->get('/dashboard')->assertStatus(200);
        $this->actingAs($user)->get('/dashboard/profile')->assertStatus(200);
    }

    public function test_standard_user_denied_admin_access(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $this->actingAs($user)->get('/admin')->assertStatus(403);
        $this->actingAs($user)->get('/admin/mcqs')->assertStatus(403);
        $this->actingAs($user)->get('/admin/users')->assertStatus(403);
    }

    public function test_admin_access_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'username' => 'sys_admin',
            'email' => 'admin@aptimedha.com',
            'password' => Hash::make('AdminPass123!'),
        ]);

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/mcqs')->assertStatus(200);

        // Also test admin login redirect directs to admin dashboard
        Auth::logout();
        $response = $this->post('/login', [
            'email' => 'admin@aptimedha.com',
            'password' => 'AdminPass123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_public_registration_cannot_create_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sneaky Hacker',
            'username' => 'sneaky_hacker',
            'email' => 'hacker@example.com',
            'password' => 'HackerPass123!',
            'password_confirmation' => 'HackerPass123!',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('user.dashboard'));

        $user = User::where('email', 'hacker@example.com')->firstOrFail();
        $this->assertEquals(UserRole::User, $user->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_password_reset_token_validation(): void
    {
        $user = User::factory()->create(['email' => 'student@example.com']);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-or-expired-token',
            'email' => $user->email,
            'password' => 'NewStrongPass123!',
            'password_confirmation' => 'NewStrongPass123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_reset_success(): void
    {
        $user = User::factory()->create([
            'email' => 'reset_me@example.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'reset_me@example.com',
            'password' => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // Confirm new password works
        $loginResponse = $this->post('/login', [
            'email' => 'reset_me@example.com',
            'password' => 'BrandNewPassword123!',
        ]);
        $loginResponse->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Confirm token cannot be reused
        Auth::logout();
        $reuseResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'reset_me@example.com',
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ]);
        $reuseResponse->assertSessionHasErrors(['email']);
    }

    public function test_user_cannot_access_or_modify_another_user_profile(): void
    {
        $userA = User::factory()->create([
            'name' => 'User Alpha',
            'username' => 'user_alpha',
            'email' => 'alpha@example.com',
            'role' => UserRole::User,
        ]);

        $userB = User::factory()->create([
            'name' => 'User Beta',
            'username' => 'user_beta',
            'email' => 'beta@example.com',
            'role' => UserRole::User,
        ]);

        // User A tries to modify their profile with malicious parameter spoofing user B's ID and admin role
        $response = $this->actingAs($userA)->put('/dashboard/profile', [
            'id' => $userB->id,
            'user_id' => $userB->id,
            'name' => 'User Alpha Modified',
            'username' => 'alpha_new',
            'email' => 'alpha_new@example.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('user.profile'));

        // Verify User A was modified safely, but role remained User
        $userA->refresh();
        $this->assertEquals('User Alpha Modified', $userA->name);
        $this->assertEquals('alpha_new', $userA->username);
        $this->assertEquals(UserRole::User, $userA->role);

        // Verify User B remained untouched
        $userB->refresh();
        $this->assertEquals('User Beta', $userB->name);
        $this->assertEquals('user_beta', $userB->username);
        $this->assertEquals('beta@example.com', $userB->email);
    }

    public function test_admin_creation_artisan_command(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Super Administrator',
            '--username' => 'super_admin',
            '--email' => 'superadmin@aptimedha.com',
        ])
        ->expectsQuestion('Administrator Password (min 8 characters)', 'SuperSecret123!')
        ->expectsQuestion('Confirm Administrator Password', 'SuperSecret123!')
        ->assertExitCode(0);

        $admin = User::where('email', 'superadmin@aptimedha.com')->firstOrFail();
        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertTrue(Hash::check('SuperSecret123!', $admin->password));

        // Duplicate admin email should fail
        $this->artisan('app:create-admin', [
            '--name' => 'Duplicate Administrator',
            '--username' => 'super_admin2',
            '--email' => 'superadmin@aptimedha.com',
        ])
        ->expectsQuestion('Administrator Password (min 8 characters)', 'AnotherSecret123!')
        ->expectsQuestion('Confirm Administrator Password', 'AnotherSecret123!')
        ->assertExitCode(1);
    }

    public function test_google_oauth_pending_route(): void
    {
        $response = $this->get('/auth/google');
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }
}
