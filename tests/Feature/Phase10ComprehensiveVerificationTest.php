<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Discussion;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class Phase10ComprehensiveVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->admin()->create([
            'name' => 'System Admin',
            'email' => 'admin@aptimedha.com',
            'username' => 'sys_admin',
        ]);
    }

    public function test_admin_dashboard_shows_actual_database_metrics_and_zero_dummy_data(): void
    {
        $admin = $this->createAdmin();

        $category = Category::create([
            'name' => 'Quantitative Aptitude',
            'slug' => 'quant-apt',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Arithmetic',
            'slug' => 'arithmetic',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $question = Question::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'What is 50% of 200?',
            'option_a' => '50',
            'option_b' => '100',
            'option_c' => '150',
            'option_d' => '200',
            'correct_option' => 'B',
            'explanation' => 'Half of 200 is 100.',
            'is_active' => true,
            'views_count' => 15,
        ]);

        $blogCategory = BlogCategory::create([
            'name' => 'Placement Guidance',
            'slug' => 'placement-guidance',
        ]);

        Blog::create([
            'title' => 'Crack the Screening Round',
            'slug' => 'crack-screening',
            'body' => '<p>Practice daily with timed sections.</p>',
            'blog_category_id' => $blogCategory->id,
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 42,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Platform Overview');
        $response->assertSee('Accurate Analytics');
        $response->assertSee('Quantitative Aptitude');
        $response->assertSee('Percentages');
        $response->assertSee('What is 50% of 200?');
        // Check that hardcoded dummy texts from previous phase are NOT present
        $response->assertDontSee('10,450');
        $response->assertDontSee('What is 25% of 240?');
        $response->assertDontSee('10450');
    }

    public function test_admin_user_management_crud(): void
    {
        $admin = $this->createAdmin();

        $student = User::factory()->create([
            'name' => 'Arun Kumar',
            'username' => 'arun_k',
            'email' => 'arun@example.com',
            'role' => UserRole::User,
        ]);

        // Index
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Arun Kumar');
        $response->assertSee('arun@example.com');
        $response->assertDontSee('Pooja Nair'); // Ensure old JS dummy array is gone

        // Search
        $searchResponse = $this->actingAs($admin)->get('/admin/users?search=Arun');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Arun Kumar');

        // Show
        $showResponse = $this->actingAs($admin)->get("/admin/users/{$student->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Arun Kumar');
        $showResponse->assertSee('arun@example.com');

        // Edit
        $editResponse = $this->actingAs($admin)->get("/admin/users/{$student->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Arun Kumar');

        // Update
        $updateResponse = $this->actingAs($admin)->put("/admin/users/{$student->id}", [
            'name' => 'Arun Kumar Updated',
            'username' => 'arun_updated',
            'email' => 'arun_new@example.com',
            'role' => 'user',
        ]);
        $updateResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Arun Kumar Updated',
            'username' => 'arun_updated',
            'email' => 'arun_new@example.com',
        ]);

        // Destroy
        $deleteResponse = $this->actingAs($admin)->delete("/admin/users/{$student->id}");
        $deleteResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $student->id]);

        // Self-deletion prevented
        $selfDeleteResponse = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        $selfDeleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_mcq_view_tracking_and_deduplication(): void
    {
        $category = Category::create([
            'name' => 'Logical Reasoning',
            'slug' => 'logical-reasoning',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Deductive',
            'slug' => 'deductive',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Syllogism',
            'slug' => 'syllogism',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $question = Question::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'topic_id' => $topic->id,
            'question_number' => 101,
            'section_number' => 1,
            'question_text' => 'All cats are animals. All animals have hearts.',
            'option_a' => 'True',
            'option_b' => 'False',
            'option_c' => 'Cannot be determined',
            'option_d' => 'None',
            'correct_option' => 'A',
            'explanation' => 'Standard transitive syllogism.',
            'is_active' => true,
            'views_count' => 0,
        ]);

        // First view increments count
        $response1 = $this->get("/mcq/{$question->id}");
        $response1->assertStatus(200);
        $this->assertEquals(1, $question->fresh()->views_count);

        // Immediate refresh in the same session does NOT inflate count
        $response2 = $this->get("/mcq/{$question->id}");
        $response2->assertStatus(200);
        $this->assertEquals(1, $question->fresh()->views_count);
    }

    public function test_blog_view_tracking_and_deduplication(): void
    {
        $admin = $this->createAdmin();

        $blog = Blog::create([
            'title' => 'Placement Roadmap 2026',
            'slug' => 'placement-roadmap-2026',
            'body' => '<p>Mastering reasoning and aptitude in 60 days.</p>',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'views_count' => 0,
        ]);

        // First view increments count
        $response1 = $this->get("/blog/{$blog->slug}");
        $response1->assertStatus(200);
        $this->assertEquals(1, $blog->fresh()->views_count);

        // Refresh in the same session does not inflate count
        $response2 = $this->get("/blog/{$blog->slug}");
        $response2->assertStatus(200);
        $this->assertEquals(1, $blog->fresh()->views_count);
    }

    public function test_homepage_renders_dynamic_database_categories_and_blogs(): void
    {
        $admin = $this->createAdmin();

        $category = Category::create([
            'name' => 'Data Structures',
            'slug' => 'data-structures',
            'description' => 'Trees, graphs, linked lists and heaps.',
            'icon' => 'code-2',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $blog = Blog::create([
            'title' => 'Top 20 Campus Interview Insights',
            'slug' => 'top-20-campus-interview-insights',
            'body' => '<p>Tips for technical interviews.</p>',
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
            'views_count' => 10,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Data Structures');
        $response->assertSee('Top 20 Campus Interview Insights');
        $response->assertSee(asset('images/aptimedha-logo.png'));
    }
}
