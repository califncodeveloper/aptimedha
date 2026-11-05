<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\BlogSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeploymentSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Existing admin password is not overwritten by repeated seeder execution (SEC-01).
     */
    public function test_existing_admin_password_is_not_overwritten_by_database_seeder(): void
    {
        // Pre-create an admin with a custom password
        $customPassword = 'MySecretAdminPass987!';
        $admin = User::factory()->admin()->create([
            'name' => 'Original Admin Name',
            'username' => 'original_admin',
            'email' => 'admin@aptimedha.com',
            'password' => Hash::make($customPassword),
        ]);

        // Execute DatabaseSeeder
        $seeder = new DatabaseSeeder();
        $seeder->run();

        // Refresh from DB and verify password was NOT overwritten
        $admin->refresh();
        $this->assertTrue(Hash::check($customPassword, $admin->password));
        $this->assertFalse(Hash::check('Admin@12345', $admin->password));

        // Execute BlogSeeder independently
        $blogSeeder = new BlogSeeder();
        $blogSeeder->run();

        $admin->refresh();
        $this->assertTrue(Hash::check($customPassword, $admin->password));
        $this->assertFalse(Hash::check('Admin@12345', $admin->password));
    }

    /**
     * 2. Existing admin role and custom details are preserved on seeder execution (SEC-01).
     */
    public function test_existing_admin_role_and_details_are_preserved_on_seeder_execution(): void
    {
        $customAdmin = User::factory()->admin()->create([
            'name' => 'Chief Operations Officer',
            'username' => 'coo_admin',
            'email' => 'coo@aptimedha.com',
            'password' => Hash::make('CustomPass555!'),
        ]);

        $seeder = new DatabaseSeeder();
        $seeder->run();

        $customAdmin->refresh();
        $this->assertEquals(UserRole::Admin, $customAdmin->role);
        $this->assertTrue($customAdmin->isAdmin());
        $this->assertEquals('Chief Operations Officer', $customAdmin->name);
        $this->assertEquals('coo_admin', $customAdmin->username);
        $this->assertTrue(Hash::check('CustomPass555!', $customAdmin->password));
    }

    /**
     * 3. Admin provisioning remains available through the approved explicit command (SEC-01).
     */
    public function test_admin_provisioning_command_works_securely(): void
    {
        $this->artisan('app:create-admin', [
            '--name' => 'Explicit Super Admin',
            '--username' => 'explicit_admin',
            '--email' => 'explicit.admin@aptimedha.com',
        ])
        ->expectsQuestion('Administrator Password (min 8 characters)', 'ExplicitSecurePass123!')
        ->expectsQuestion('Confirm Administrator Password', 'ExplicitSecurePass123!')
        ->assertExitCode(0);

        $admin = User::where('email', 'explicit.admin@aptimedha.com')->firstOrFail();
        $this->assertEquals(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue(Hash::check('ExplicitSecurePass123!', $admin->password));
    }

    /**
     * 4. Normal container startup does not execute the full database seeder (SEC-01).
     */
    public function test_normal_container_startup_does_not_execute_database_seeder(): void
    {
        $entrypointPath = base_path('docker/entrypoint.sh');
        $this->assertFileExists($entrypointPath);

        $entrypointContent = file_get_contents($entrypointPath);

        // SEC-01 check: db:seed must NOT be in the startup script
        $this->assertStringNotContainsString('php artisan db:seed', $entrypointContent);

        // Legitimate migrations must remain intact
        $this->assertStringContainsString('php artisan migrate --force', $entrypointContent);
    }

    /**
     * 5. Production startup does not generate a new APP_KEY (SEC-03).
     * 6. Missing APP_KEY in production produces a clear startup failure (SEC-03).
     */
    public function test_entrypoint_protects_production_app_key(): void
    {
        $entrypointContent = file_get_contents(base_path('docker/entrypoint.sh'));

        // Must enforce check for production APP_KEY
        $this->assertStringContainsString('CRITICAL STARTUP ERROR: Production APP_KEY is not configured!', $entrypointContent);
        $this->assertStringContainsString('exit 1', $entrypointContent);

        // Must never print the APP_KEY value to stdout or stderr
        $this->assertStringNotContainsString('echo "APP_KEY: $APP_KEY"', $entrypointContent);
        $this->assertStringNotContainsString('echo $APP_KEY', $entrypointContent);
    }

    /**
     * 7. Local development key-generation behavior remains available (SEC-03).
     */
    public function test_entrypoint_allows_key_generation_only_in_non_production(): void
    {
        $entrypointContent = file_get_contents(base_path('docker/entrypoint.sh'));

        // key:generate must be scoped strictly to non-production
        $this->assertStringContainsString('CONTAINER_APP_ENV="${APP_ENV:-production}"', $entrypointContent);
        $this->assertStringContainsString('if [ "$CONTAINER_APP_ENV" = "production" ]; then', $entrypointContent);
        $this->assertStringContainsString('php artisan key:generate --force', $entrypointContent);
    }

    /**
     * 8. Database session driver is configured correctly in render.yaml and entrypoint (SEC-02).
     */
    public function test_database_session_driver_is_configured_for_production(): void
    {
        // 1. render.yaml verification
        $renderYamlContent = file_get_contents(base_path('render.yaml'));
        $this->assertStringContainsString('key: SESSION_DRIVER', $renderYamlContent);
        $this->assertStringContainsString('value: database', $renderYamlContent);
        $this->assertStringNotContainsString('value: cookie', $renderYamlContent);

        // 2. docker/entrypoint.sh verification
        $entrypointContent = file_get_contents(base_path('docker/entrypoint.sh'));
        $this->assertStringContainsString('SESSION_DRIVER_VAL="${SESSION_DRIVER:-database}"', $entrypointContent);
        $this->assertStringNotContainsString('SESSION_DRIVER=cookie', $entrypointContent);

        // 3. .env.example verification
        $envExampleContent = file_get_contents(base_path('.env.example'));
        $this->assertStringContainsString('SESSION_DRIVER=database', $envExampleContent);
    }

    /**
     * 8b. Session table schema matches Laravel specification (SEC-02).
     */
    public function test_session_table_schema_matches_laravel_spec(): void
    {
        $this->assertTrue(Schema::hasTable('sessions'), 'The sessions table must exist in the database.');

        $requiredColumns = [
            'id',
            'user_id',
            'ip_address',
            'user_agent',
            'payload',
            'last_activity',
        ];

        foreach ($requiredColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('sessions', $column),
                "The sessions table is missing expected column: {$column}"
            );
        }
    }

    /**
     * 9. Existing authentication flow works seamlessly with database sessions (SEC-02).
     */
    public function test_authentication_and_session_persistence_with_database_driver(): void
    {
        Config::set('session.driver', 'database');

        $user = User::factory()->create([
            'email' => 'session.tester@example.com',
            'username' => 'session_tester',
            'password' => Hash::make('SessionPass123!'),
            'role' => UserRole::User,
        ]);

        $response = $this->post('/login', [
            'email' => 'session.tester@example.com',
            'password' => 'SessionPass123!',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);

        // Verify a session row exists in the database
        $this->assertDatabaseHas('sessions', [
            'user_id' => $user->id,
        ]);

        // Logout
        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }

    /**
     * 10. Existing password reset flow continues working.
     */
    public function test_existing_password_reset_continues_working(): void
    {
        Config::set('session.driver', 'database');

        $user = User::factory()->create([
            'email' => 'pwreset.deploy@example.com',
            'password' => Hash::make('OldInitialPass123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $resetResponse = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'pwreset.deploy@example.com',
            'password' => 'NewHardenedPass888!',
            'password_confirmation' => 'NewHardenedPass888!',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $resetResponse->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(Hash::check('NewHardenedPass888!', $user->password));

        // Test login with newly reset password
        $loginResponse = $this->post('/login', [
            'email' => 'pwreset.deploy@example.com',
            'password' => 'NewHardenedPass888!',
        ]);

        $loginResponse->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}

