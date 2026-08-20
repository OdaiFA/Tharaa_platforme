<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * The LMS REST API was not touched by this Livewire migration (Livewire
 * applies only to the web UI). These tests prove the existing API contract
 * still behaves exactly as before.
 */
class LmsApiRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_listing_returns_only_published_courses(): void
    {
        Course::factory()->create(['title' => 'منشورة']);
        Course::factory()->draft()->create(['title' => 'مسودة']);

        $response = $this->getJson('/api/courses');

        $response->assertOk();
        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('منشورة'));
        $this->assertFalse($titles->contains('مسودة'));
    }

    public function test_course_show_returns_course_and_enrollment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $course = Course::factory()->create();
        Enrollment::factory()->for($user)->for($course)->create();

        $response = $this->getJson("/api/courses/{$course->id}");

        $response->assertOk();
        $response->assertJsonPath('course.id', $course->id);
        $this->assertNotNull($response->json('enrollment'));
    }

    public function test_enrollment_via_api_creates_an_enrollment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $course = Course::factory()->create();

        $response = $this->postJson("/api/courses/{$course->id}/enroll");

        $response->assertCreated();
        $this->assertDatabaseHas('enrollments', ['user_id' => $user->id, 'course_id' => $course->id]);
    }

    public function test_quiz_submission_via_api_scores_and_returns_result(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(['passing_score' => 50]);
        $question = QuizQuestion::factory()->for($quiz)->create([
            'type' => 'true_false',
            'options' => [],
            'correct_answer' => ['true'],
            'points' => 1,
        ]);
        Enrollment::factory()->for($user)->for($course)->create();

        $response = $this->postJson("/api/quizzes/{$quiz->id}/submit", [
            'answers' => [$question->id => 'true'],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('is_passed', true);
        $response->assertJsonPath('score', 100);
    }

    public function test_quiz_submission_without_enrollment_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create();
        $question = QuizQuestion::factory()->for($quiz)->create([
            'type' => 'true_false',
            'options' => [],
            'correct_answer' => ['true'],
        ]);

        $response = $this->postJson("/api/quizzes/{$quiz->id}/submit", [
            'answers' => [$question->id => 'true'],
        ]);

        $response->assertStatus(403);
    }

    /**
     * LMS hardening (Issue 1): the canonical multiple_choice answer format
     * is the option's index — this must hold consistently through the API
     * submission path too, not just the web/Livewire form.
     */
    public function test_multiple_choice_quiz_submission_via_api_scores_correctly(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(['passing_score' => 50]);
        $question = QuizQuestion::factory()->for($quiz)->create([
            'type' => 'multiple_choice',
            'options' => ['أ', 'ب', 'ج'],
            'correct_answer' => [2],
            'points' => 1,
        ]);
        Enrollment::factory()->for($user)->for($course)->create();

        $response = $this->postJson("/api/quizzes/{$quiz->id}/submit", [
            'answers' => [$question->id => 2],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('is_passed', true);
        $response->assertJsonPath('score', 100);
    }
}
