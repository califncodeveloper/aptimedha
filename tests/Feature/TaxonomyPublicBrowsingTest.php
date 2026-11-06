<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyPublicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_category_listing_shows_active_database_records(): void
    {
        Category::create([
            'name' => 'Active Public Category',
            'slug' => 'active-public-category',
            'description' => 'Test active description',
            'is_active' => true,
            'display_order' => 1,
        ]);

        Category::create([
            'name' => 'Hidden Inactive Category',
            'slug' => 'hidden-inactive-category',
            'description' => 'Should not appear',
            'is_active' => false,
            'display_order' => 2,
        ]);

        $response = $this->get(route('categories.index'));

        $response->assertOk();
        $response->assertSee('Active Public Category');
        $response->assertDontSee('Hidden Inactive Category');
    }

    public function test_category_show_page_displays_active_subjects_and_topics(): void
    {
        $category = Category::create([
            'name' => 'Reasoning Track',
            'slug' => 'reasoning-track',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Verbal Reasoning',
            'slug' => 'verbal-reasoning',
            'is_active' => true,
        ]);

        $activeTopic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Blood Relations',
            'slug' => 'blood-relations',
            'is_active' => true,
        ]);

        $inactiveTopic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Secret Inactive Topic',
            'slug' => 'secret-inactive-topic',
            'is_active' => false,
        ]);

        $response = $this->get(route('categories.show', $category->slug));

        $response->assertOk();
        $response->assertSee('Reasoning Track');
        $response->assertSee('Verbal Reasoning');
        $response->assertSee('Blood Relations');
        $response->assertDontSee('Secret Inactive Topic');
    }

    public function test_inactive_category_show_returns_404(): void
    {
        $category = Category::create([
            'name' => 'Draft Category',
            'slug' => 'draft-category',
            'is_active' => false,
        ]);

        $response = $this->get(route('categories.show', $category->slug));
        $response->assertNotFound();
    }

    public function test_invalid_category_slug_returns_404(): void
    {
        $response = $this->get(route('categories.show', 'non-existent-slug'));
        $response->assertNotFound();
    }

    public function test_inactive_subject_is_hidden_from_public_category_page(): void
    {
        $category = Category::create([
            'name' => 'Engineering Track',
            'slug' => 'engineering-track',
            'is_active' => true,
        ]);

        Subject::create([
            'category_id' => $category->id,
            'name' => 'Inactive Subject Branch',
            'slug' => 'inactive-subject-branch',
            'is_active' => false,
        ]);

        $response = $this->get(route('categories.show', $category->slug));
        $response->assertOk();
        $response->assertDontSee('Inactive Subject Branch');
    }

    public function test_direct_category_to_topic_navigation(): void
    {
        $category = Category::create([
            'name' => 'General Aptitude',
            'slug' => 'general-aptitude',
            'is_active' => true,
        ]);

        $directTopic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => null,
            'name' => 'Direct Math Basics',
            'slug' => 'direct-math-basics',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.show', $category->slug));
        $response->assertOk();
        $response->assertSee('Direct Topics');
        $response->assertSee('Direct Math Basics');
        $response->assertSee(route('categories.topics.show', ['category' => $category->slug, 'topic' => $directTopic->slug]));
    }

    public function test_category_show_links_to_topic_mcq_practice(): void
    {
        $category = Category::create([
            'name' => 'Logical Reasoning',
            'slug' => 'logical-reasoning',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'category_id' => $category->id,
            'name' => 'Deduction',
            'slug' => 'deduction',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'subject_id' => $subject->id,
            'name' => 'Syllogisms',
            'slug' => 'syllogisms',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.show', $category->slug));
        $response->assertOk();
        $response->assertSee(route('categories.topics.show', ['category' => $category->slug, 'topic' => $topic->slug]));
    }

    public function test_mcq_practice_route_resolves_database_category_and_topic(): void
    {
        $category = Category::create([
            'name' => 'Programming Track',
            'slug' => 'programming-track',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Python Basics',
            'slug' => 'python-basics',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.index', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Programming Track');
        $response->assertSee('Python Basics');
    }
}
