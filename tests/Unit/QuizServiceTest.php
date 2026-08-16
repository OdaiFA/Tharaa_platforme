<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeQuiz(array $quizOverrides = [], array $questionOverrides = []): Quiz
    {
        $course = Course::factory()->create();
        $module = \App\Models\Module::factory()->create(['course_id' => $course->id]);
        $lesson = Lesson::factory()->create(['module_id' => $module->id]);

        $quiz = Quiz::factory()->create(array_merge([
            'lesson_id' => $lesson->id,
            'passing_score' => 60,
            'max_attempts' => 3,
        ], $quizOverrides));

        QuizQuestion::factory()->create(array_merge([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'options' => ['أ', 'ب', 'ج'],
            'correct_answer' => [1],
            'points' => 1,
        ], $questionOverrides));

        return $quiz;
    }

    public function test_user_can_attempt_within_max_attempts(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuiz(['max_attempts' => 2]);

        $this->assertTrue(app(QuizService::class)->canAttempt($user, $quiz));

        $enrollment = Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $quiz->lesson->module->course_id,
            'status' => 'in_progress',
            'progress_percentage' => 0,
        ]);

        $quiz->attempts()->create([
            'user_id' => $user->id,
            'enrollment_id' => $enrollment->id,
            'score' => 100,
            'total_points' => 1,
            'earned_points' => 1,
            'is_passed' => true,
            'attempt_number' => 1,
            'started_at' => now(),
            'completed_at' => now(),
        ]);
        $quiz->attempts()->create([
            'user_id' => $user->id,
            'enrollment_id' => $enrollment->id,
            'score' => 100,
            'total_points' => 1,
            'earned_points' => 1,
            'is_passed' => true,
            'attempt_number' => 2,
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->assertFalse(app(QuizService::class)->canAttempt($user, $quiz));
    }

    public function test_correct_answer_scores_fully_and_passes(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuiz(['passing_score' => 60]);
        $course = $quiz->lesson->module->course;

        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'in_progress',
            'progress_percentage' => 0,
        ]);

        $question = $quiz->questions()->first();

        $attempt = app(QuizService::class)->submitAttempt($user, $quiz, [$question->id => 1]);

        $this->assertSame(100, $attempt->score);
        $this->assertTrue($attempt->is_passed);
        $this->assertSame(1, $attempt->attempt_number);
    }

    public function test_wrong_answer_scores_zero(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuiz();
        $course = $quiz->lesson->module->course;

        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'in_progress',
            'progress_percentage' => 0,
        ]);

        $question = $quiz->questions()->first();

        $attempt = app(QuizService::class)->submitAttempt($user, $quiz, [$question->id => 0]);

        $this->assertSame(0, $attempt->score);
        $this->assertFalse($attempt->is_passed);
    }

    public function test_exceeding_max_attempts_throws(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuiz(['max_attempts' => 1]);
        $course = $quiz->lesson->module->course;

        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'in_progress',
            'progress_percentage' => 0,
        ]);

        $question = $quiz->questions()->first();
        $service = app(QuizService::class);

        $service->submitAttempt($user, $quiz, [$question->id => 1]);

        $this->expectException(\DomainException::class);

        $service->submitAttempt($user, $quiz, [$question->id => 1]);
    }
}
