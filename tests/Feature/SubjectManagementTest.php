<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_standard_user_cannot_access_subjects(): void
    {
        $response = $this->get(route('admin.subjects.index'));
        $response->assertRedirect(route('login'));

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.subjects.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_create_subject_under_valid_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Computer Science',
            'slug' => 'computer-science',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.subjects.store'), [
            'category_id' => $category->id,
            'name' => 'Algorithms & Data Structures',
            'description' => 'Algorithm complexity and structure modules',
            'display_order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.subjects.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subjects', [
            'category_id' => $category->id,
            'name' => 'Algorithms & Data Structures',
            'slug' => 'algorithms-data-structures',
            'is_active' => 1,
        ]);
    }

    public function test_invalid_parent_category_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.subjects.store'), [
            'category_id' => 999999, // non-existent category
            'name' => 'Invalid Subject',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_admin_can_update_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Engineering',
            'slug' => 'engineering',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Mechanical Engg',
            'slug' => 'mechanical-engg',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.subjects.update', $subject->id), [
            'category_id' => $category->id,
            'name' => 'Mechanical Engineering (Core)',
            'slug' => 'mechanical-engineering-core',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.subjects.index'));
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Mechanical Engineering (Core)',
            'slug' => 'mechanical-engineering-core',
        ]);
    }

    public function test_subject_deletion_blocked_when_child_topics_exist(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Verbal Ability',
            'slug' => 'verbal-ability',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Vocabulary',
            'slug' => 'vocabulary',
            'is_active' => true,
        ]);

        Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Antonyms',
            'slug' => 'antonyms',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.subjects.destroy', $subject->id));
        $response->assertRedirect(route('admin.subjects.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('subjects', ['id' => $subject->id]);
    }
}

