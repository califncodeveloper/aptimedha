<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class McqManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Category $category;
    protected Subject $subject;
    protected Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();

        $this->category = Category::create([
            'name' => 'Arithmetic Aptitude',
            'slug' => 'arithmetic-aptitude',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->subject = Subject::create([
            'category_id' => $this->category->id,
            'name' => 'Commercial Arithmetic',
            'slug' => 'commercial-arithmetic',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $this->topic = Topic::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'name' => 'Percentages',
            'slug' => 'percentages',
            'is_active' => true,
            'display_order' => 1,
            'total_questions_count' => 0,
        ]);
    }

    /**
     * Test 1: Admin can create an MCQ.
     */
    public function test_admin_can_create_an_mcq(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'What is 30% of 150?',
            'option_a' => '35',
            'option_b' => '40',
            'option_c' => '45',
            'option_d' => '50',
            'correct_option' => 'C',
            'explanation' => '30/100 * 150 = 45.',
            'difficulty' => 'easy',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.mcqs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'What is 30% of 150?',
            'correct_option' => 'C',
            'is_active' => 1,
        ]);

        $this->assertEquals(1, $this->topic->fresh()->total_questions_count);
    }

    /**
     * Test 2: Admin can view MCQ list.
     */
    public function test_admin_can_view_mcq_list(): void
    {
        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'What is 10% of 200?',
            'option_a' => '10',
            'option_b' => '20',
            'option_c' => '30',
            'option_d' => '40',
            'correct_option' => 'B',
            'explanation' => '10% of 200 = 20.',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.mcqs.index'));

        $response->assertOk();
        $response->assertSee('What is 10% of 200?');
        $response->assertSee('Percentages');
        $response->assertSee('Q1');
    }

    /**
     * Test 3: Admin can edit an MCQ.
     */
    public function test_admin_can_edit_an_mcq(): void
    {
        $question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Initial Question Statement',
            'option_a' => '1',
            'option_b' => '2',
            'option_c' => '3',
            'option_d' => '4',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.mcqs.update', $question->id), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'Updated Question Statement',
            'option_a' => '10',
            'option_b' => '20',
            'option_c' => '30',
            'option_d' => '40',
            'correct_option' => 'D',
            'explanation' => 'Updated explanation working.',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.mcqs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question_text' => 'Updated Question Statement',
            'correct_option' => 'D',
            'option_d' => '40',
        ]);
    }

    /**
     * Test 4: Admin can deactivate an MCQ.
     */
    public function test_admin_can_deactivate_an_mcq(): void
    {
        $question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Active Question',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.mcqs.toggle-status', $question->id));

        $response->assertRedirect();
        $this->assertFalse($question->fresh()->is_active);
    }

    /**
     * Test 5: Admin can delete an MCQ safely.
     */
    public function test_admin_can_delete_an_mcq_safely(): void
    {
        // Case A: Safe deletion when no dependent records exist
        $questionA = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Standalone Question',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.mcqs.destroy', $questionA->id));
        $response->assertRedirect(route('admin.mcqs.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('questions', ['id' => $questionA->id]);

        // Case B: Soft/safe deactivation when dependent bookmark exists
        $questionB = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'Bookmarked Question',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        DB::table('bookmarks')->insert([
            'user_id' => $this->user->id,
            'question_id' => $questionB->id,
            'created_at' => now(),
        ]);

        $responseB = $this->actingAs($this->admin)->delete(route('admin.mcqs.destroy', $questionB->id));
        $responseB->assertRedirect(route('admin.mcqs.index'));
        $responseB->assertSessionHas('warning');

        // Question must still exist in DB but marked inactive
        $this->assertDatabaseHas('questions', [
            'id' => $questionB->id,
            'is_active' => 0,
        ]);
    }

    /**
     * Test 6: Guest cannot access admin MCQ management.
     */
    public function test_guest_cannot_access_admin_mcq_management(): void
    {
        $this->get(route('admin.mcqs.index'))->assertRedirect(route('login'));
        $this->get(route('admin.mcqs.create'))->assertRedirect(route('login'));
        $this->post(route('admin.mcqs.store'), [])->assertRedirect(route('login'));
    }

    /**
     * Test 7: Standard user cannot access admin MCQ management.
     */
    public function test_standard_user_cannot_access_admin_mcq_management(): void
    {
        $this->actingAs($this->user)->get(route('admin.mcqs.index'))->assertForbidden();
        $this->actingAs($this->user)->get(route('admin.mcqs.create'))->assertForbidden();
        $this->actingAs($this->user)->post(route('admin.mcqs.store'), [])->assertForbidden();
    }

    /**
     * Test 8: Invalid category is rejected.
     */
    public function test_invalid_category_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => 99999,
            'topic_id' => $this->topic->id,
            'question_text' => 'Invalid Category MCQ',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    /**
     * Test 9: Subject/category mismatch is rejected.
     */
    public function test_subject_category_mismatch_is_rejected(): void
    {
        $otherCategory = Category::create([
            'name' => 'Verbal Ability',
            'slug' => 'verbal-ability',
            'is_active' => true,
        ]);

        $otherSubject = Subject::create([
            'category_id' => $otherCategory->id,
            'name' => 'Grammar',
            'slug' => 'grammar',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $otherSubject->id, // Belongs to otherCategory, not this->category
            'topic_id' => $this->topic->id,
            'question_text' => 'Subject Mismatch MCQ',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        $response->assertSessionHasErrors('subject_id');
    }

    /**
     * Test 10: Topic/category/subject mismatch is rejected.
     */
    public function test_topic_category_subject_mismatch_is_rejected(): void
    {
        $subject2 = Subject::create([
            'category_id' => $this->category->id,
            'name' => 'Modern Math',
            'slug' => 'modern-math',
            'is_active' => true,
        ]);

        // $this->topic belongs to $this->subject, but we pass $subject2
        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $subject2->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'Topic Mismatch MCQ',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        $response->assertSessionHasErrors('topic_id');
    }

    /**
     * Test 11: Invalid correct option is rejected.
     */
    public function test_invalid_correct_option_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'Invalid Correct Option MCQ',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'E', // Only A, B, C, D allowed
        ]);

        $response->assertSessionHasErrors('correct_option');
    }

    /**
     * Test 12: Exactly four options are required.
     */
    public function test_exactly_four_options_are_required(): void
    {
        // Missing option_d
        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'Missing Option D MCQ',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'correct_option' => 'A',
        ]);

        $response->assertSessionHasErrors('option_d');
    }

    /**
     * Test 13: Optional question image uploads successfully.
     */
    public function test_optional_question_image_uploads_successfully(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('diagram.png', 400, 'image/png');

        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Question with Image',
            'question_image' => $file,
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'B',
        ]);

        $response->assertRedirect(route('admin.mcqs.index'));

        $question = Question::where('question_text', 'Question with Image')->first();
        $this->assertNotNull($question);
        $this->assertNotNull($question->question_image);

        Storage::disk('public')->assertExists($question->question_image);
    }

    /**
     * Test 14: Invalid image upload is rejected.
     */
    public function test_invalid_image_upload_is_rejected(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('admin.mcqs.store'), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_text' => 'Invalid Image MCQ',
            'question_image' => $file,
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        $response->assertSessionHasErrors('question_image');
    }

    /**
     * Test 15: Public MCQ listing uses database records.
     */
    public function test_public_mcq_listing_uses_database_records(): void
    {
        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Real Database Question 101',
            'option_a' => 'Alpha',
            'option_b' => 'Beta',
            'option_c' => 'Gamma',
            'option_d' => 'Delta',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.index', [
            'category' => $this->category->slug,
            'topic' => $this->topic->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Real Database Question 101');
        $response->assertSee('Alpha');
        $response->assertSee('Gamma');
    }

    /**
     * Test 16: Inactive MCQs are hidden publicly.
     */
    public function test_inactive_mcqs_are_hidden_publicly(): void
    {
        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Visible Active Question',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'Draft Inactive Question',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => false,
        ]);

        $response = $this->get(route('mcq.index', [
            'category' => $this->category->slug,
            'topic' => $this->topic->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Visible Active Question');
        $response->assertDontSee('Draft Inactive Question');
    }

    /**
     * Test 17: Single MCQ page resolves correctly.
     */
    public function test_single_mcq_page_resolves_correctly(): void
    {
        $question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Standalone Practice Question',
            'option_a' => 'Opt 1',
            'option_b' => 'Opt 2',
            'option_c' => 'Opt 3',
            'option_d' => 'Opt 4',
            'correct_option' => 'B',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.show', $question->id));
        $response->assertOk();
        $response->assertSee('Standalone Practice Question');
        $response->assertSee('Opt 2');

        // Missing ID returns 404
        $this->get(route('mcq.show', 99999))->assertNotFound();

        // Inactive question returns 404
        $question->update(['is_active' => false]);
        $this->get(route('mcq.show', $question->id))->assertNotFound();
    }

    /**
     * Test 18: Pagination works with 10 questions per page.
     */
    public function test_pagination_works_with_10_questions_per_page(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            Question::create([
                'category_id' => $this->category->id,
                'subject_id' => $this->subject->id,
                'topic_id' => $this->topic->id,
                'question_number' => $i,
                'question_text' => sprintf("SampleUniqueQuestionText_%02d", $i),
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_option' => 'A',
                'is_active' => true,
            ]);
        }

        // Page 1
        $page1 = $this->get(route('mcq.index', ['category' => $this->category->slug, 'topic' => $this->topic->slug]));
        $page1->assertOk();
        $page1->assertSee('SampleUniqueQuestionText_01');
        $page1->assertSee('SampleUniqueQuestionText_10');
        $page1->assertDontSee('SampleUniqueQuestionText_11');

        // Page 2
        $page2 = $this->get(route('mcq.index', ['category' => $this->category->slug, 'topic' => $this->topic->slug, 'page' => 2]));
        $page2->assertOk();
        $page2->assertSee('SampleUniqueQuestionText_11');
        $page2->assertSee('SampleUniqueQuestionText_15');
        $page2->assertDontSee('SampleUniqueQuestionText_01');
    }

    /**
     * Test 19: Correct answer and explanation reveal correctly.
     */
    public function test_correct_answer_and_explanation_reveal_correctly(): void
    {
        $question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'What is the sum of angles in a triangle?',
            'option_a' => '90 degrees',
            'option_b' => '180 degrees',
            'option_c' => '270 degrees',
            'option_d' => '360 degrees',
            'correct_option' => 'B',
            'explanation' => 'The sum of all interior angles of any planar triangle is always 180 degrees.',
            'is_active' => true,
        ]);

        $response = $this->get(route('mcq.show', $question->id));
        $response->assertOk();
        $response->assertSee('180 degrees');
        $response->assertSee('The sum of all interior angles of any planar triangle is always 180 degrees.');
        $response->assertSee('isCorrect(\'B\')', false);
    }

    /**
     * Test 20: Empty topic displays a proper empty state.
     */
    public function test_empty_topic_displays_a_proper_empty_state(): void
    {
        $emptyTopic = Topic::create([
            'category_id' => $this->category->id,
            'name' => 'Empty Algebra Topic',
            'slug' => 'empty-algebra-topic',
            'is_active' => true,
            'total_questions_count' => 0,
        ]);

        $response = $this->get(route('mcq.index', [
            'category' => $this->category->slug,
            'topic' => $emptyTopic->slug,
        ]));

        $response->assertOk();
        $response->assertSee('No Questions in This Topic Yet');
        $response->assertSee('Empty Algebra Topic');
    }

    /**
     * Test 21: Shared image is preserved on disk if another question still references it.
     */
    public function test_shared_image_is_preserved_when_one_referencing_question_is_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('questions/shared_figure.png', 'fake image content');

        $questionA = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Question referencing shared figure 1',
            'question_image' => 'questions/shared_figure.png',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $questionB = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'Question referencing shared figure 2',
            'question_image' => 'questions/shared_figure.png',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'B',
            'is_active' => true,
        ]);

        // Delete questionA
        $this->actingAs($this->admin)->delete(route('admin.mcqs.destroy', $questionA->id));

        // QuestionA is missing from DB
        $this->assertDatabaseMissing('questions', ['id' => $questionA->id]);

        // Image file MUST still exist in storage because questionB still references it
        Storage::disk('public')->assertExists('questions/shared_figure.png');

        // Delete questionB
        $this->actingAs($this->admin)->delete(route('admin.mcqs.destroy', $questionB->id));

        // Now that no questions reference it, file should be safely deleted
        Storage::disk('public')->assertMissing('questions/shared_figure.png');
    }

    /**
     * Test 22: Shared image is preserved when one question replaces or removes its image.
     */
    public function test_shared_image_is_preserved_when_one_question_removes_it(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('questions/shared_diagram.png', 'content');

        $questionA = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Question A',
            'question_image' => 'questions/shared_diagram.png',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
            'is_active' => true,
        ]);

        $questionB = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'question_text' => 'Question B',
            'question_image' => 'questions/shared_diagram.png',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'B',
            'is_active' => true,
        ]);

        // Question A removes its image
        $this->actingAs($this->admin)->put(route('admin.mcqs.update', $questionA->id), [
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'question_text' => 'Question A updated',
            'remove_image' => '1',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_option' => 'A',
        ]);

        $this->assertNull($questionA->fresh()->question_image);

        // File must still exist for Question B
        Storage::disk('public')->assertExists('questions/shared_diagram.png');
    }
}
