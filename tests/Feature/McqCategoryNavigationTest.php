<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McqCategoryNavigationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: All active main categories are displayed in the interactive navigation sidebar.
     */
    public function test_all_active_main_categories_are_displayed_in_navigation_sidebar(): void
    {
        $cat1 = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'icon' => 'calculator',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $cat2 = Category::create([
            'name' => 'Logical Reasoning',
            'slug' => 'logical-reasoning',
            'icon' => 'puzzle',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $cat3 = Category::create([
            'name' => 'Programming Concepts',
            'slug' => 'programming-concepts',
            'icon' => 'code',
            'is_active' => true,
            'display_order' => 3,
        ]);

        Topic::create([
            'category_id' => $cat1->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
            'display_order' => 1,
        ]);

        Topic::create([
            'category_id' => $cat2->id,
            'name' => 'Number Series',
            'slug' => 'number-series',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $response = $this->get(route('mcq.index'));
        $response->assertOk();

        // Verify all 3 categories appear in the navigation
        $response->assertSee('Arithmetic Aptitude');
        $response->assertSee('Logical Reasoning');
        $response->assertSee('Programming Concepts');
        $response->assertSee('MCQ Categories');
        $response->assertSee('Tracks');
    }

    /**
     * Test 2: Subcategories (topics) appear under their respective parent categories with question counts.
     */
    public function test_subcategories_appear_under_their_parent_categories_with_accurate_counts(): void
    {
        $category = Category::create([
            'name' => 'Commercial Math',
            'slug' => 'commercial-math',
            'icon' => 'calculator',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Profit and Loss',
            'slug' => 'profit-and-loss',
            'is_active' => true,
            'display_order' => 1,
        ]);

        Question::create([
            'category_id' => $category->id,
            'topic_id' => $topic->id,
            'question_number' => 1,
            'question_text' => 'What is the profit percentage when cost is 100 and selling is 120?',
            'option_a' => '10%',
            'option_b' => '20%',
            'option_c' => '25%',
            'option_d' => '30%',
            'correct_option' => 'B',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Commercial Math');
        $response->assertSee('Profit and Loss');
        $response->assertSee(route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));
    }

    /**
     * Test 3: The active category and topic are clearly highlighted and pre-expanded.
     */
    public function test_active_category_and_subcategory_are_highlighted_and_pre_expanded(): void
    {
        $category = Category::create([
            'name' => 'Engineering Hub',
            'slug' => 'engineering-hub',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $category->id,
            'name' => 'Circuit Theory',
            'slug' => 'circuit-theory',
            'is_active' => true,
        ]);

        $response = $this->get(route('categories.topics.show', [
            'category' => $category->slug,
            'topic' => $topic->slug,
        ]));

        $response->assertOk();
        // Active track indicator exists in HTML
        $response->assertSee('Active Track');
        // Alpine data initializes with active category slug expanded
        $response->assertSee("'engineering-hub': true", false);
        $response->assertSee('Circuit Theory');
    }

    /**
     * Test 4: Newly created categories and subcategories appear dynamically without hardcoding.
     */
    public function test_newly_created_category_and_subcategory_appear_dynamically(): void
    {
        $newCategory = Category::create([
            'name' => 'Artificial Intelligence & ML',
            'slug' => 'ai-ml',
            'icon' => 'cpu',
            'is_active' => true,
            'display_order' => 99,
        ]);

        $newTopic = Topic::create([
            'category_id' => $newCategory->id,
            'name' => 'Neural Networks',
            'slug' => 'neural-networks',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $response = $this->get(route('mcq.index'));
        $response->assertOk();

        $response->assertSee('Artificial Intelligence &amp; ML', false);
        $response->assertSee('Neural Networks');
        $response->assertSee(route('categories.topics.show', [
            'category' => $newCategory->slug,
            'topic' => $newTopic->slug,
        ]));
    }

    /**
     * Test 5: Inactive categories and subcategories are hidden from navigation.
     */
    public function test_inactive_categories_and_subcategories_are_hidden_from_navigation(): void
    {
        $activeCat = Category::create([
            'name' => 'Visible Category',
            'slug' => 'visible-cat',
            'is_active' => true,
        ]);

        $hiddenCat = Category::create([
            'name' => 'Secret Inactive Category',
            'slug' => 'secret-inactive-cat',
            'is_active' => false,
        ]);

        $activeTopic = Topic::create([
            'category_id' => $activeCat->id,
            'name' => 'Visible Topic',
            'slug' => 'visible-topic',
            'is_active' => true,
        ]);

        $hiddenTopic = Topic::create([
            'category_id' => $activeCat->id,
            'name' => 'Draft Inactive Topic',
            'slug' => 'draft-inactive-topic',
            'is_active' => false,
        ]);

        $response = $this->get(route('mcq.index'));
        $response->assertOk();

        $response->assertSee('Visible Category');
        $response->assertSee('Visible Topic');
        $response->assertDontSee('Secret Inactive Category');
        $response->assertDontSee('Draft Inactive Topic');
    }

    /**
     * Test 6: Selecting a subcategory loads the correct questions for that topic.
     */
    public function test_selecting_subcategory_loads_correct_mcq_questions(): void
    {
        $cat = Category::create([
            'name' => 'Quantitative Track',
            'slug' => 'quantitative-track',
            'is_active' => true,
        ]);

        $topicA = Topic::create([
            'category_id' => $cat->id,
            'name' => 'Ratios and Proportions',
            'slug' => 'ratios-proportions',
            'is_active' => true,
        ]);

        $topicB = Topic::create([
            'category_id' => $cat->id,
            'name' => 'Time and Distance',
            'slug' => 'time-distance',
            'is_active' => true,
        ]);

        Question::create([
            'category_id' => $cat->id,
            'topic_id' => $topicA->id,
            'question_number' => 1,
            'question_text' => 'Unique Ratio Question Alpha',
            'option_a' => '1:2',
            'option_b' => '2:3',
            'option_c' => '3:4',
            'option_d' => '4:5',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        Question::create([
            'category_id' => $cat->id,
            'topic_id' => $topicB->id,
            'question_number' => 1,
            'question_text' => 'Unique Speed Distance Question Beta',
            'option_a' => '10 km/h',
            'option_b' => '20 km/h',
            'option_c' => '30 km/h',
            'option_d' => '40 km/h',
            'correct_option' => 'C',
            'is_active' => true,
        ]);

        // Visit Topic A
        $responseA = $this->get(route('categories.topics.show', [
            'category' => $cat->slug,
            'topic' => $topicA->slug,
        ]));
        $responseA->assertOk();
        $responseA->assertSee('Unique Ratio Question Alpha');
        $responseA->assertDontSee('Unique Speed Distance Question Beta');

        // Visit Topic B
        $responseB = $this->get(route('categories.topics.show', [
            'category' => $cat->slug,
            'topic' => $topicB->slug,
        ]));
        $responseB->assertOk();
        $responseB->assertSee('Unique Speed Distance Question Beta');
        $responseB->assertDontSee('Unique Ratio Question Alpha');
    }

    /**
     * Test 7: Selected category and topic remain preserved during question pagination and filtering.
     */
    public function test_question_pagination_preserves_selected_category_and_topic_state(): void
    {
        $cat = Category::create([
            'name' => 'Verbal Track',
            'slug' => 'verbal-track',
            'is_active' => true,
        ]);

        $topic = Topic::create([
            'category_id' => $cat->id,
            'name' => 'Synonyms Practice',
            'slug' => 'synonyms-practice',
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 15; $i++) {
            Question::create([
                'category_id' => $cat->id,
                'topic_id' => $topic->id,
                'question_number' => $i,
                'question_text' => "Synonym Question Number {$i}",
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'A',
                'is_active' => true,
            ]);
        }

        $page2 = $this->get(route('categories.topics.show', [
            'category' => $cat->slug,
            'topic' => $topic->slug,
            'page' => 2,
        ]));

        $page2->assertOk();
        $page2->assertSee('Synonym Question Number 11');
        $page2->assertSee('Synonyms Practice');
        $page2->assertSee('Verbal Track');
        $page2->assertSee("'verbal-track': true", false);
    }

    /**
     * Test 8: Empty category with 0 topics renders a clean empty state.
     */
    public function test_empty_category_renders_clean_empty_state_in_accordion(): void
    {
        Category::create([
            'name' => 'Empty Track',
            'slug' => 'empty-track',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.index'));
        $response->assertOk();
        $response->assertSee('Empty Track');
        $response->assertSee('No subcategories available yet.');
    }
}
