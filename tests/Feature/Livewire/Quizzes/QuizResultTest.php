<?php

namespace Tests\Feature\Livewire\Quizzes;

use App\Livewire\Quizzes\QuizResult;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuizResultTest extends TestCase
{
    use RefreshDatabase;

    private function makeAttempt(User $user, array $attemptAttributes = []): array
    {
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(['max_attempts' => 3]);
        $enrollment = Enrollment::factory()->for($user)->for($course)->create();
        $attempt = QuizAttempt::factory()->create(array_merge([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'attempt_number' => 1,
        ], $attemptAttributes));

        return compact('quiz', 'attempt');
    }

    public function test_owner_can_view_their_result(): void
    {
        $user = User::factory()->create();
        ['quiz' => $quiz, 'attempt' => $attempt] = $this->makeAttempt($user, ['is_passed' => true, 'score' => 100]);

        $this->actingAs($user)
            ->get(route('quizzes.result', [$quiz, $attempt]))
            ->assertOk()
            ->assertSeeLivewire(QuizResult::class);
    }

    public function test_user_cannot_view_another_users_attempt_result(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        ['quiz' => $quiz, 'attempt' => $attempt] = $this->makeAttempt($owner);

        $this->actingAs($intruder)
            ->get(route('quizzes.result', [$quiz, $attempt]))
            ->assertForbidden();
    }

    public function test_failed_attempt_within_max_attempts_allows_retry(): void
    {
        $user = User::factory()->create();
        ['quiz' => $quiz, 'attempt' => $attempt] = $this->makeAttempt($user, ['is_passed' => false, 'score' => 20]);

        Livewire::actingAs($user)
            ->test(QuizResult::class, ['quiz' => $quiz, 'attempt' => $attempt])
            ->assertSet('canRetry', true)
            ->assertSee('إعادة المحاولة');
    }

    public function test_failed_attempt_at_max_attempts_does_not_offer_retry(): void
    {
        $user = User::factory()->create();
        ['quiz' => $quiz, 'attempt' => $attempt] = $this->makeAttempt($user, ['is_passed' => false, 'score' => 20]);
        $quiz->update(['max_attempts' => 1]);

        Livewire::actingAs($user)
            ->test(QuizResult::class, ['quiz' => $quiz, 'attempt' => $attempt])
            ->assertSet('canRetry', false)
            ->assertDontSee('إعادة المحاولة');
    }

    public function test_passed_attempt_never_offers_retry(): void
    {
        $user = User::factory()->create();
        ['quiz' => $quiz, 'attempt' => $attempt] = $this->makeAttempt($user, ['is_passed' => true, 'score' => 100]);

        Livewire::actingAs($user)
            ->test(QuizResult::class, ['quiz' => $quiz, 'attempt' => $attempt])
            ->assertSet('canRetry', false);
    }
}
