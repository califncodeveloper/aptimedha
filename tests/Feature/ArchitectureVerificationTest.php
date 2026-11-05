<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchitectureVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_successfully(): void
    {
        Category::create([
            'name' => 'Sample Category',
            'slug' => 'sample-category',
            'is_active' => true,
        ]);

        $this->get('/')->assertStatus(200);
        $this->get('/categories')->assertStatus(200);
        $this->get('/categories/sample-category')->assertStatus(200);
        $this->get('/mcq')->assertStatus(200);
        $this->get('/mcq/sample-mcq')->assertStatus(200);
        $this->get('/placement-preparation')->assertStatus(200);
        $this->get('/placement')->assertRedirect(route('placement.index'));
        $this->get('/blog')->assertStatus(200);
        $this->get('/blog/sample-post')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/disclaimer')->assertStatus(200);
        $this->get('/terms-and-conditions')->assertStatus(200);
        $this->get('/terms')->assertRedirect(route('terms'));
        $this->get('/privacy-policy')->assertStatus(200);
        $this->get('/privacy')->assertRedirect(route('privacy'));
        $this->get('/copyright')->assertStatus(200);
    }

    public function test_guest_auth_pages_render_successfully(): void
    {
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
        $this->get('/forgot-password')->assertStatus(200);
        $this->get('/reset-password/sample-token')->assertStatus(200);
    }

    public function test_unauthenticated_users_are_redirected_from_protected_routes(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/dashboard/profile')->assertRedirect(route('login'));
        $this->get('/dashboard/bookmarks')->assertRedirect(route('login'));
        $this->get('/dashboard/discussions')->assertRedirect(route('login'));
        $this->get('/dashboard/reported-questions')->assertRedirect(route('login'));

        $this->get('/admin')->assertRedirect(route('login'));
        $this->get('/admin/mcqs')->assertRedirect(route('login'));
        $this->get('/admin/categories')->assertRedirect(route('login'));
        $this->get('/admin/subjects')->assertRedirect(route('login'));
        $this->get('/admin/topics')->assertRedirect(route('login'));
        $this->get('/admin/blogs')->assertRedirect(route('login'));
        $this->get('/admin/users')->assertRedirect(route('login'));
        $this->get('/admin/discussions')->assertRedirect(route('login'));
        $this->get('/admin/reports')->assertRedirect(route('login'));
        $this->get('/admin/pages')->assertRedirect(route('login'));
        $this->get('/admin/settings')->assertRedirect(route('login'));
    }

    public function test_authenticated_regular_users_cannot_access_admin_routes(): void
    {
        $user = new User([
            'name' => 'Regular User',
            'email' => 'user@example.com',
        ]);
        $user->role = UserRole::User;

        $this->actingAs($user)->get('/dashboard')->assertStatus(200);
        $this->actingAs($user)->get('/dashboard/profile')->assertStatus(200);
        $this->actingAs($user)->get('/dashboard/bookmarks')->assertStatus(200);
        $this->actingAs($user)->get('/dashboard/discussions')->assertStatus(200);
        $this->actingAs($user)->get('/dashboard/reported-questions')->assertStatus(200);

        $this->actingAs($user)->get('/admin')->assertStatus(403);
        $this->actingAs($user)->get('/admin/mcqs')->assertStatus(403);
        $this->actingAs($user)->get('/admin/categories')->assertStatus(403);
        $this->actingAs($user)->get('/admin/subjects')->assertStatus(403);
        $this->actingAs($user)->get('/admin/topics')->assertStatus(403);
        $this->actingAs($user)->get('/admin/blogs')->assertStatus(403);
        $this->actingAs($user)->get('/admin/users')->assertStatus(403);
        $this->actingAs($user)->get('/admin/discussions')->assertStatus(403);
        $this->actingAs($user)->get('/admin/reports')->assertStatus(403);
        $this->actingAs($user)->get('/admin/pages')->assertStatus(403);
        $this->actingAs($user)->get('/admin/settings')->assertStatus(403);
    }

    public function test_authenticated_admin_users_can_access_admin_routes(): void
    {
        $admin = new User([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->role = UserRole::Admin;

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/mcqs')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/categories')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/subjects')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/topics')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/blogs')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/discussions')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/reports')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/pages')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/settings')->assertStatus(200);
    }
}

