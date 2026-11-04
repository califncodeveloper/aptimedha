<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageHeroRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_redesigned_hero_section_elements(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();

        // 1. Trust badge
        $response->assertSee('Placement Preparation Hub');
        $response->assertSee('Verified Solutions');

        // 2. Headlines
        $response->assertSee('Practise Smart.');
        $response->assertSee('Get Placement Ready.');
        $response->assertSee('Master quantitative aptitude, logical reasoning, verbal ability, and programming with verified step-by-step solutions');

        // 3. Search Bar
        $response->assertSee('Search Percentages, Python, Number Series...');
        $response->assertSee('Practice');

        // 4. Trending Topics
        $response->assertSee('Trending:');
        $response->assertSee('Percentages');
        $response->assertSee('Profit & Loss');
        $response->assertSee('Number Series');
        $response->assertSee('Python');

        // 5. CTA Buttons
        $response->assertSee('Start Practicing');
        $response->assertSee('Explore Topics');

        // 6. Interactive Preview Card & Question
        $response->assertSee('Quantitative Aptitude');
        $response->assertSee('A train 150 metres long passes an electric pole in 9 seconds. What is the speed of the train in km/hr?');
        $response->assertSee('50 km/hr');
        $response->assertSee('60 km/hr');
        $response->assertSee('65 km/hr');
        $response->assertSee('72 km/hr');
        $response->assertSee('Correct Answer');
        $response->assertSee('Verified Step-by-Step Solution');
        $response->assertSee('Time and Distance Chapter');
        $response->assertSee('AptiMedha Verified');

        // 7. Floating Badges
        $response->assertSee('Campus Assessment Ready');
        $response->assertSee('TCS, Infosys & Wipro Patterns');
        $response->assertSee('Instant Verification');
        $response->assertSee('Formulas & Speed Math');

        // 8. Trust Indicators
        $response->assertSee('Curated MCQs');
        $response->assertSee('Step-by-Step Solutions');
        $response->assertSee('100% Free Public Access');
    }

    public function test_smart_search_and_trending_topics_navigate_to_correct_topic(): void
    {
        $categoryApt = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $subjectApt = Subject::create([
            'category_id' => $categoryApt->id,
            'name' => 'Commercial Math',
            'slug' => 'commercial-math',
            'is_active' => true,
        ]);

        $topicPercentages = Topic::create([
            'category_id' => $categoryApt->id,
            'subject_id' => $subjectApt->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
        ]);

        $categoryProg = Category::create([
            'name' => 'Programming',
            'slug' => 'programming',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $subjectProg = Subject::create([
            'category_id' => $categoryProg->id,
            'name' => 'Languages',
            'slug' => 'languages',
            'is_active' => true,
        ]);

        $topicPython = Topic::create([
            'category_id' => $categoryProg->id,
            'subject_id' => $subjectProg->id,
            'name' => 'Python',
            'slug' => 'python',
            'is_active' => true,
        ]);

        // Querying topic 'percentages' should resolve to Percentages
        $resApt = $this->get(route('mcq.index', ['topic' => 'percentages']));
        $resApt->assertOk();
        $resApt->assertSee('Percentages');

        // Querying topic 'python' should automatically resolve category to Programming
        $resProg = $this->get(route('mcq.index', ['topic' => 'python']));
        $resProg->assertOk();
        $resProg->assertSee('Python');
    }
}
