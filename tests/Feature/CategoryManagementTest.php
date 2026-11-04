<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_category_management(): void
    {
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirect(route('login'));

        $response = $this->post(route('admin.categories.store'), ['name' => 'Test']);
        $response->assertRedirect(route('login'));
    }

    public function test_standard_user_cannot_access_category_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.categories.index'));
        $response->assertForbidden();

        $response = $this->actingAs($user)->post(route('admin.categories.store'), ['name' => 'Unauthorized Category']);
        $response->assertForbidden();
    }

    public function test_admin_can_view_category_listing(): void
    {
        $admin = User::factory()->admin()->create();
        $cat = Category::create([
            'name' => 'Quantitative Ability',
            'slug' => 'quantitative-ability',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));
        $response->assertOk();
        $response->assertSee('Quantitative Ability');
        $response->assertSee('quantitative-ability');
    }

    public function test_admin_can_create_category_with_auto_generated_slug(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Data Science Essentials',
            'description' => 'Comprehensive data science topics',
            'icon' => 'database',
            'display_order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'Data Science Essentials',
            'slug' => 'data-science-essentials',
            'icon' => 'database',
            'display_order' => 5,
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Old Title',
            'slug' => 'old-title',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Updated Title',
            'slug' => 'updated-title',
            'description' => 'New description text',
            'icon' => 'cpu',
            'display_order' => 2,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Title',
            'slug' => 'updated-title',
            'description' => 'New description text',
        ]);
    }

    public function test_admin_can_deactivate_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Active Category',
            'slug' => 'active-category',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Active Category',
            'slug' => 'active-category',
            // No is_active checkbox sent means false
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => 0,
        ]);
    }

    public function test_duplicate_category_slug_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Category::create([
            'name' => 'Category One',
            'slug' => 'unique-slug',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Category Two',
            'slug' => 'unique-slug',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_category_deletion_blocked_when_dependent_records_exist(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Aptitude Track',
            'slug' => 'aptitude-track',
            'is_active' => true,
        ]);

        Subject::create([
            'category_id' => $category->id,
            'name' => 'Arithmetic',
            'slug' => 'arithmetic',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id));
        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_delete_category_when_no_dependent_records_exist(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Empty Track',
            'slug' => 'empty-track',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id));
        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

