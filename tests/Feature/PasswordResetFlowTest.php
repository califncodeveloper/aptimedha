<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee('Registered Email Address');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'student@aptimedha.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'student@aptimedha.com',
        ]);

        $response->assertSessionHas('status');
        $response->assertRedirect();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_reset_password_screen_can_be_rendered_with_token_and_email(): void
    {
        $user = User::factory()->create([
            'email' => 'student@aptimedha.com',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->get("/reset-password/{$token}?email=" . urlencode($user->email));

        $response->assertStatus(200);
        $response->assertSee('Set New Password');
        $response->assertSee($user->email);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'student@aptimedha.com',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student@aptimedha.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->fresh()->password));
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'student@aptimedha.com',
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token-value',
            'email' => 'student@aptimedha.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_reset_token_cannot_be_reused(): void
    {
        $user = User::factory()->create([
            'email' => 'student@aptimedha.com',
        ]);

        $token = Password::broker()->createToken($user);

        // First reset
        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student@aptimedha.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ])->assertRedirect(route('login'));

        // Attempt reuse
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student@aptimedha.com',
            'password' => 'AnotherPassword123!',
            'password_confirmation' => 'AnotherPassword123!',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_password_reset_requires_matching_confirmation_and_minimum_length(): void
    {
        $user = User::factory()->create([
            'email' => 'student@aptimedha.com',
        ]);

        $token = Password::broker()->createToken($user);

        // Mismatched confirmation
        $response1 = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student@aptimedha.com',
            'password' => 'ValidPass123!',
            'password_confirmation' => 'DifferentPass123!',
        ]);
        $response1->assertSessionHasErrors(['password']);

        // Too short (< 8 chars)
        $response2 = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'student@aptimedha.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        $response2->assertSessionHasErrors(['password']);
    }
}

