<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Discussion;
use App\Models\Question;
use App\Models\QuestionReport;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserEngagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected User $otherUser;
    protected Category $category;
    protected Subject $subject;
    protected Topic $topic;
    protected Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'username' => 'admin_user',
        ]);

        $this->user = User::factory()->create([
            'username' => 'learner_one',
        ]);

        $this->otherUser = User::factory()->create([
            'username' => 'learner_two',
        ]);

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
            'total_questions_count' => 1,
        ]);

        $this->question = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 1,
            'section_number' => 1,
            'question_text' => 'What is 25% of 240?',
            'option_a' => '40',
            'option_b' => '50',
            'option_c' => '60',
            'option_d' => '80',
            'correct_option' => 'C',
            'explanation' => '25% of 240 is (1/4) * 240 = 60.',
            'is_active' => true,
        ]);
    }

    /**
     * Bookmark Test 1: Guest cannot bookmark questions.
     */
    public function test_guest_cannot_bookmark_questions(): void
    {
        $response = $this->post(route('user.bookmarks.toggle', $this->question->id));
        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('bookmarks', [
            'question_id' => $this->question->id,
        ]);
    }

    /**
     * Bookmark Test 2: Authenticated user can toggle bookmark on and off.
     */
    public function test_authenticated_user_can_bookmark_and_toggle_remove_bookmark(): void
    {
        // 1. Toggle ON
        $response = $this->actingAs($this->user)->postJson(route('user.bookmarks.toggle'), [
            'question_id' => $this->question->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'bookmarked' => true,
            ]);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
        ]);

        // 2. Toggle OFF
        $responseOff = $this->actingAs($this->user)->postJson(route('user.bookmarks.toggle'), [
            'question_id' => $this->question->id,
        ]);

        $responseOff->assertOk()
            ->assertJson([
                'success' => true,
                'bookmarked' => false,
            ]);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
        ]);
    }

    /**
     * Bookmark Test 3: User can delete bookmark from bookmarks listing.
     */
    public function test_authenticated_user_can_delete_bookmark_from_bookmarks_page(): void
    {
        $bookmark = Bookmark::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->delete(route('user.bookmarks.destroy', $bookmark->id));
        $response->assertRedirect(route('user.bookmarks'));

        $this->assertDatabaseMissing('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }

    /**
     * Bookmark Test 4: User cannot delete another user's bookmark.
     */
    public function test_user_cannot_delete_another_users_bookmark(): void
    {
        $bookmark = Bookmark::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->otherUser)->delete(route('user.bookmarks.destroy', $bookmark->id));
        $response->assertForbidden();

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
        ]);
    }

    /**
     * Discussion Test 1: Guest cannot post discussion.
     */
    public function test_guest_cannot_post_discussion(): void
    {
        $response = $this->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'comment' => 'This is a test comment.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('discussions', 0);
    }

    /**
     * Discussion Test 2: Authenticated user can create discussion and view it.
     */
    public function test_authenticated_user_can_create_and_view_discussions(): void
    {
        $response = $this->actingAs($this->user)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'comment' => 'Remember 25% is just dividing by 4.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('discussions', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Remember 25% is just dividing by 4.',
            'status' => 'approved',
        ]);

        // User can view their discussions list
        $viewResponse = $this->actingAs($this->user)->get(route('user.discussions'));
        $viewResponse->assertOk()
            ->assertSee('Remember 25% is just dividing by 4.');

        // Public MCQ page displays discussion
        $publicMcqResponse = $this->get(route('mcq.show', $this->question->id));
        $publicMcqResponse->assertOk()
            ->assertSee('Remember 25% is just dividing by 4.');
    }

    /**
     * Discussion Test 3: User can edit and delete only their own discussion.
     */
    public function test_user_can_edit_own_discussion_but_not_another_users(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Initial comment by user one.',
            'status' => 'approved',
        ]);

        // Other user attempts to update -> 403 Forbidden
        $unauthUpdate = $this->actingAs($this->otherUser)->put(route('user.discussions.update', $discussion->id), [
            'comment' => 'Tampered text.',
        ]);
        $unauthUpdate->assertForbidden();

        // Other user attempts to delete -> 403 Forbidden
        $unauthDelete = $this->actingAs($this->otherUser)->delete(route('user.discussions.destroy', $discussion->id));
        $unauthDelete->assertForbidden();

        // Owner updates successfully
        $ownerUpdate = $this->actingAs($this->user)->put(route('user.discussions.update', $discussion->id), [
            'comment' => 'Updated comment by user one.',
        ]);
        $ownerUpdate->assertSessionHas('success');
        $this->assertDatabaseHas('discussions', [
            'id' => $discussion->id,
            'comment' => 'Updated comment by user one.',
        ]);

        // Owner deletes successfully
        $ownerDelete = $this->actingAs($this->user)->delete(route('user.discussions.destroy', $discussion->id));
        $ownerDelete->assertRedirect(route('user.discussions'));
        $this->assertDatabaseMissing('discussions', [
            'id' => $discussion->id,
        ]);
    }

    /**
     * Discussion Test 4: Administrator can moderate discussions.
     */
    public function test_admin_can_moderate_discussions(): void
    {
        $discussion = Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Spam comment needing moderation.',
            'status' => 'pending',
        ]);

        // Non-admin cannot access admin moderation
        $userResponse = $this->actingAs($this->user)->get(route('admin.discussions.index'));
        $userResponse->assertForbidden();

        // Admin can view discussions queue
        $adminView = $this->actingAs($this->admin)->get(route('admin.discussions.index'));
        $adminView->assertOk()
            ->assertSee('Spam comment needing moderation.');

        // Admin updates status to approved
        $statusUpdate = $this->actingAs($this->admin)->patch(route('admin.discussions.status', $discussion->id), [
            'status' => 'approved',
        ]);
        $statusUpdate->assertSessionHas('success');
        $this->assertDatabaseHas('discussions', [
            'id' => $discussion->id,
            'status' => 'approved',
        ]);

        // Admin deletes inappropriate post
        $adminDelete = $this->actingAs($this->admin)->delete(route('admin.discussions.destroy', $discussion->id));
        $adminDelete->assertRedirect(route('admin.discussions.index'));
        $this->assertDatabaseMissing('discussions', [
            'id' => $discussion->id,
        ]);
    }

    /**
     * Report Test 1: Guest cannot submit report.
     */
    public function test_guest_cannot_submit_report(): void
    {
        $response = $this->post(route('user.reports.store'), [
            'question_id' => $this->question->id,
            'issue_type' => 'incorrect_answer',
            'description' => 'Option marked is wrong.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('question_reports', 0);
    }

    /**
     * Report Test 2: Authenticated user can submit MCQ report and duplicate is prevented.
     */
    public function test_authenticated_user_can_submit_report_and_duplicate_is_prevented(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('user.reports.store'), [
            'question_id' => $this->question->id,
            'issue_type' => 'incorrect_explanation',
            'description' => 'Denominator calculation needs clarification.',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('question_reports', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'issue_type' => 'incorrect_explanation',
            'status' => 'new',
        ]);

        // Submitting duplicate report while pending returns 422 warning
        $duplicateResponse = $this->actingAs($this->user)->postJson(route('user.reports.store'), [
            'question_id' => $this->question->id,
            'issue_type' => 'incorrect_explanation',
            'description' => 'Spamming second report.',
        ]);

        $duplicateResponse->assertStatus(422);
        $this->assertDatabaseCount('question_reports', 1);
    }

    /**
     * Report Test 3: User cannot access another user's private report records.
     */
    public function test_user_cannot_access_another_users_report(): void
    {
        QuestionReport::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'issue_type' => 'incorrect_answer',
            'description' => 'Confidential feedback from user one.',
            'status' => 'new',
        ]);

        $otherUserReportsView = $this->actingAs($this->otherUser)->get(route('user.reports'));
        $otherUserReportsView->assertOk()
            ->assertDontSee('Confidential feedback from user one.');
    }

    /**
     * Report Test 4: Admin can view and resolve reports with admin resolution notes.
     */
    public function test_admin_can_update_report_status_and_add_resolution_notes(): void
    {
        $report = QuestionReport::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'issue_type' => 'typo_formatting',
            'description' => 'Subscript formula missing in step 2.',
            'status' => 'new',
        ]);

        $adminShow = $this->actingAs($this->admin)->get(route('admin.reports.show', $report->id));
        $adminShow->assertOk()
            ->assertSee('Subscript formula missing in step 2.');

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.reports.update', $report->id), [
            'status' => 'resolved',
            'admin_notes' => 'Fixed formatting and verified explanation.',
        ]);

        $updateResponse->assertRedirect(route('admin.reports.show', $report->id));

        $this->assertDatabaseHas('question_reports', [
            'id' => $report->id,
            'status' => 'resolved',
            'admin_notes' => 'Fixed formatting and verified explanation.',
            'resolved_by' => $this->admin->id,
        ]);

        // User can see the resolution note in their dashboard reports page
        $userView = $this->actingAs($this->user)->get(route('user.reports'));
        $userView->assertOk()
            ->assertSee('Fixed formatting and verified explanation.');
    }

    /**
     * Dashboard Test: User dashboard shows real database metrics.
     */
    public function test_user_dashboard_shows_real_database_values(): void
    {
        Bookmark::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'created_at' => now(),
        ]);

        Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Dashboard discussion query.',
            'status' => 'approved',
        ]);

        QuestionReport::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'issue_type' => 'incorrect_answer',
            'description' => 'Dashboard question report.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)->get(route('user.dashboard'));
        $response->assertOk()
            ->assertSee('Saved Bookmarks')
            ->assertSee('What is 25% of 240?')
            ->assertSee('Dashboard discussion query.');
    }

    /**
     * Profile Test: Profile details update validation and security.
     */
    public function test_profile_update_validation_and_cannot_modify_role(): void
    {
        $response = $this->actingAs($this->user)->put(route('user.profile.update'), [
            'name' => 'Updated Learner Name',
            'username' => 'updated_learner',
            'email' => 'updated@aptimedha.com',
            'role' => 'admin', // Malicious attempt to elevate privileges
        ]);

        $response->assertRedirect(route('user.profile'));

        $this->user->refresh();
        $this->assertEquals('Updated Learner Name', $this->user->name);
        $this->assertEquals('updated_learner', $this->user->username);
        $this->assertEquals('updated@aptimedha.com', $this->user->email);
        $this->assertEquals(UserRole::User, $this->user->role); // Role remains User!
    }

    /**
     * Contact Form Test: Validation and secure database storage.
     */
    public function test_contact_form_validation_and_storage(): void
    {
        // 1. Validation error on empty submission (including required turnstile verification)
        $invalidResponse = $this->post(route('contact.submit'), []);
        $invalidResponse->assertSessionHasErrors(['name', 'email', 'subject', 'message', 'cf-turnstile-response']);

        // 2. Successful submission persists to database
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'action' => 'contact',
            ]),
        ]);

        $validResponse = $this->post(route('contact.submit'), [
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'enquiry_type' => 'suggestion',
            'subject' => 'Please add more Python OOP questions',
            'message' => 'Would love to see more practice sets on inheritance and decorators.',
            'cf-turnstile-response' => 'mock-valid-turnstile-token',
        ]);

        $validResponse->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'is_read' => false,
        ]);

        // 3. Admin can view contact messages
        $adminView = $this->actingAs($this->admin)->get(route('admin.contacts.index'));
        $adminView->assertOk()
            ->assertSee('Priya Sharma')
            ->assertSee('Please add more Python OOP questions');

        $message = ContactMessage::where('email', 'priya@example.com')->first();
        $adminDetail = $this->actingAs($this->admin)->get(route('admin.contacts.show', $message->id));
        $adminDetail->assertOk()
            ->assertSee('Would love to see more practice sets');

        // Opening message marks it as read
        $message->refresh();
        $this->assertTrue($message->is_read);
    }

    /**
     * Fix 1 Test A: Valid reply under a discussion belonging to the same question.
     */
    public function test_reply_to_discussion_on_same_question_is_valid(): void
    {
        $parentDiscussion = Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Parent discussion topic question #1.',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->otherUser)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'parent_id' => $parentDiscussion->id,
            'comment' => 'Great point! Here is a helpful follow-up shortcut.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('discussions', [
            'user_id' => $this->otherUser->id,
            'question_id' => $this->question->id,
            'parent_id' => $parentDiscussion->id,
            'comment' => 'Great point! Here is a helpful follow-up shortcut.',
        ]);
    }

    /**
     * Fix 1 Test B: Attempted reply under a discussion belonging to another question is rejected.
     */
    public function test_reply_to_discussion_on_different_question_is_rejected(): void
    {
        $question2 = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 2,
            'section_number' => 1,
            'question_text' => 'What is 50% of 120?',
            'option_a' => '30',
            'option_b' => '60',
            'option_c' => '90',
            'option_d' => '120',
            'correct_option' => 'B',
            'explanation' => 'Half of 120 is 60.',
            'is_active' => true,
        ]);

        // Discussion belongs to question 2
        $discussionOnQ2 = Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $question2->id,
            'comment' => 'Comment on question 2.',
            'status' => 'approved',
        ]);

        // Malicious attempt: reply posted to question 1 targeting parent on question 2
        $response = $this->actingAs($this->otherUser)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'parent_id' => $discussionOnQ2->id,
            'comment' => 'Cross-boundary forged reply attempt.',
        ]);

        $response->assertSessionHasErrors(['parent_id']);

        $this->assertDatabaseMissing('discussions', [
            'comment' => 'Cross-boundary forged reply attempt.',
        ]);
    }

    /**
     * Fix 1 Test C: Invalid parent ID is rejected.
     */
    public function test_reply_with_invalid_parent_id_is_rejected(): void
    {
        $response = $this->actingAs($this->user)->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'parent_id' => 999999,
            'comment' => 'Reply targeting nonexistent parent.',
        ]);

        $response->assertSessionHasErrors(['parent_id']);
        $this->assertDatabaseMissing('discussions', [
            'comment' => 'Reply targeting nonexistent parent.',
        ]);
    }

    /**
     * Fix 1 Test D: Guest cannot reply to discussion.
     */
    public function test_guest_cannot_reply_to_discussion(): void
    {
        $parentDiscussion = Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Top level comment.',
            'status' => 'approved',
        ]);

        $response = $this->post(route('user.discussions.store'), [
            'question_id' => $this->question->id,
            'parent_id' => $parentDiscussion->id,
            'comment' => 'Guest reply attempt.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('discussions', [
            'comment' => 'Guest reply attempt.',
        ]);
    }

    /**
     * Fix 2 Test: Public MCQ displays approved replies but excludes pending and hidden replies.
     */
    public function test_public_mcq_displays_approved_replies_but_excludes_pending_and_hidden_replies(): void
    {
        $topLevel = Discussion::create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'comment' => 'Top-level discussion question #1.',
            'status' => 'approved',
        ]);

        Discussion::create([
            'user_id' => $this->otherUser->id,
            'question_id' => $this->question->id,
            'parent_id' => $topLevel->id,
            'comment' => 'Visible approved reply content.',
            'status' => 'approved',
        ]);

        Discussion::create([
            'user_id' => $this->otherUser->id,
            'question_id' => $this->question->id,
            'parent_id' => $topLevel->id,
            'comment' => 'Draft pending reply text.',
            'status' => 'pending',
        ]);

        Discussion::create([
            'user_id' => $this->otherUser->id,
            'question_id' => $this->question->id,
            'parent_id' => $topLevel->id,
            'comment' => 'Moderated hidden reply text.',
            'status' => 'hidden',
        ]);

        $response = $this->get(route('mcq.show', $this->question->id));
        $response->assertOk();
        $response->assertSee('Visible approved reply content.');
        $response->assertDontSee('Draft pending reply text.');
        $response->assertDontSee('Moderated hidden reply text.');
    }

    /**
     * Fix 3 Test: Cannot bookmark inactive questions.
     */
    public function test_cannot_bookmark_inactive_question(): void
    {
        $inactiveQuestion = Question::create([
            'category_id' => $this->category->id,
            'subject_id' => $this->subject->id,
            'topic_id' => $this->topic->id,
            'question_number' => 99,
            'section_number' => 1,
            'question_text' => 'Draft unapproved question.',
            'option_a' => '1',
            'option_b' => '2',
            'option_c' => '3',
            'option_d' => '4',
            'correct_option' => 'A',
            'is_active' => false,
        ]);

        // Attempting to bookmark inactive question returns 404
        $response = $this->actingAs($this->user)->post(route('user.bookmarks.toggle', $inactiveQuestion->id));
        $response->assertNotFound();

        // Also test JSON payload toggle
        $responseJson = $this->actingAs($this->user)->postJson(route('user.bookmarks.toggle'), [
            'question_id' => $inactiveQuestion->id,
        ]);
        $responseJson->assertNotFound();

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->user->id,
            'question_id' => $inactiveQuestion->id,
        ]);
    }
}

