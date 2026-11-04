<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TopicManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_standard_user_cannot_access_topics(): void
    {
        $response = $this->get(route('admin.topics.index'));
        $response->assertRedirect(route('login'));

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.topics.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_create_topic_under_valid_category_and_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Arithmetic',
            'slug' => 'arithmetic',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Commercial Math',
            'slug' => 'commercial-math',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.topics.store'), [
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Simple Interest',
            'display_order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.topics.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('topics', [
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Simple Interest',
            'slug' => 'simple-interest',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_create_direct_category_topic_without_subject(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create([
            'name' => 'Direct Track',
            'slug' => 'direct-track',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.topics.store'), [
            'category_id' => $category->id,
            'subject_id' => null,
            'name' => 'Direct Chapter One',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.topics.index'));
        $this->assertDatabaseHas('topics', [
            'category_id' => $category->id,
            'subject_id' => null,
            'name' => 'Direct Chapter One',
        ]);
    }

    public function test_invalid_category_subject_mismatch_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $catA = Category::create(['name' => 'Category A', 'slug' => 'category-a', 'is_active' => true]);
        $catB = Category::create(['name' => 'Category B', 'slug' => 'category-b', 'is_active' => true]);

        $subjectUnderA = Subject::create([
            'category_id' => $catA->id,
            'name' => 'Subject Under A',
            'slug' => 'subject-under-a',
            'is_active' => true,
        ]);

        // Attempt to create topic under Cat B with Subject belonging to Cat A
        $response = $this->actingAs($admin)->post(route('admin.topics.store'), [
            'category_id' => $catB->id,
            'subject_id' => $subjectUnderA->id,
            'name' => 'Mismatched Topic',
        ]);

        $response->assertSessionHasErrors('subject_id');
    }

    public function test_topic_deletion_blocked_when_associated_mcqs_exist(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::create(['name' => 'Math', 'slug' => 'math', 'is_active' => true]);
        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
        ]);

        Question::create([
            'topic_id' => $topic->id,
            'category_id' => $category->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'What is 10% of 100?',
            'option_a' => '5',
            'option_b' => '10',
            'option_c' => '15',
            'option_d' => '20',
            'correct_option' => 'B',
            'explanation' => '10% of 100 is 10.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.topics.destroy', $topic->id));
        $response->assertRedirect(route('admin.topics.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('topics', ['id' => $topic->id]);
    }
}

