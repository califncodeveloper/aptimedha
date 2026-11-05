<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CriticalBackdoorRemediationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Requirement 1: GET /setup-admin must return 404 (Not Found).
     */
    public function test_get_setup_admin_returns_404(): void
    {
        $response = $this->get('/setup-admin');

        $response->assertNotFound();
    }

    /**
     * Requirement 2: POST /setup-admin must return 404 (Not Found).
     */
    public function test_post_setup_admin_returns_404(): void
    {
        $response = $this->post('/setup-admin');

        $response->assertNotFound();
    }

    /**
     * Requirement 3: An unauthenticated visitor cannot access /admin and is redirected to login.
     */
    public function test_unauthenticated_visitor_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Requirement 4: A normal user cannot access admin routes (receives 403 Forbidden).
     */
    public function test_normal_user_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::User,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    /**
     * Requirement 5: An existing administrator can still access the admin dashboard.
     */
    public function test_existing_administrator_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@aptimedha.com',
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    /**
     * Requirement 6a: Normal login continues working for administrator and standard user.
     */
    public function test_normal_login_works_for_administrator_and_user(): void
    {
        $admin = User::factory()->create([
            'email' => 'master_admin@aptimedha.com',
            'password' => Hash::make('SecretPass123!'),
            'role' => UserRole::Admin,
        ]);

        $loginResponse = $this->post('/login', [
            'email' => 'master_admin@aptimedha.com',
            'password' => 'SecretPass123!',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Requirement 6b: Normal logout continues working.
     */
    public function test_normal_logout_works(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}

