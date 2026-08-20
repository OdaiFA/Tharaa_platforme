<?php

namespace Tests\Feature\Livewire\Quizzes;

use App\Livewire\Quizzes\QuizShow;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuizShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{quiz: Quiz, question: QuizQuestion, user: User}
     */
    private function makeQuizWithTrueFalseQuestion(array $quizAttributes = []): array
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(array_merge(['passing_score' => 60], $quizAttributes));
        $question = QuizQuestion::factory()->for($quiz)->create([
            'type' => 'true_false',
            'options' => [],
            'correct_answer' => ['true'],
            'points' => 1,
        ]);
        $enrollment = Enrollment::factory()->for($user)->for($course)->create();

        return compact('quiz', 'question', 'user', 'enrollment');
    }

    public function test_enrolled_user_can_view_the_quiz(): void
    {
        ['quiz' => $quiz, 'user' => $user] = $this->makeQuizWithTrueFalseQuestion();

        $this->actingAs($user)
            ->get(route('quizzes.show', $quiz))
            ->assertOk()
            ->assertSeeLivewire(QuizShow::class);
    }

    public function test_non_enrolled_user_cannot_take_the_quiz(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create();

        $this->actingAs($user)->get(route('quizzes.show', $quiz))->assertForbidden();
    }

    public function test_correct_answer_passes_and_redirects_to_result(): void
    {
        ['quiz' => $quiz, 'question' => $question, 'user' => $user] = $this->makeQuizWithTrueFalseQuestion();

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", 'true')
            ->call('submit')
            ->assertRedirect();

        $attempt = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        $this->assertNotNull($attempt);
        $this->assertTrue($attempt->is_passed);
        $this->assertSame(100, $attempt->score);
    }

    public function test_wrong_answer_fails(): void
    {
        ['quiz' => $quiz, 'question' => $question, 'user' => $user] = $this->makeQuizWithTrueFalseQuestion();

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", 'false')
            ->call('submit');

        $attempt = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        $this->assertFalse($attempt->is_passed);
        $this->assertSame(0, $attempt->score);
    }

    public function test_exhausted_attempts_show_the_exhausted_state_instead_of_the_form(): void
    {
        ['quiz' => $quiz, 'user' => $user, 'enrollment' => $enrollment] = $this->makeQuizWithTrueFalseQuestion(['max_attempts' => 1]);

        QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'attempt_number' => 1,
        ]);

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->assertSet('exhausted', true)
            ->assertSee('انتهت محاولاتك');
    }

    public function test_submitting_after_max_attempts_is_rejected_at_the_service_layer(): void
    {
        ['quiz' => $quiz, 'question' => $question, 'user' => $user, 'enrollment' => $enrollment] = $this->makeQuizWithTrueFalseQuestion(['max_attempts' => 1]);

        QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'attempt_number' => 1,
        ]);

        // Force the component into the form state to prove the service
        // layer itself blocks the submission (defense in depth), not just
        // the mount-time UI gate.
        $component = Livewire::actingAs($user)->test(QuizShow::class, ['quiz' => $quiz]);
        $component->set('exhausted', false);
        $component->set("answers.{$question->id}", 'true');
        $component->call('submit');

        $component->assertHasErrors('quiz');
        $this->assertSame(1, QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->count());
    }

    public function test_attempt_numbering_increments_across_submissions(): void
    {
        ['quiz' => $quiz, 'question' => $question, 'user' => $user] = $this->makeQuizWithTrueFalseQuestion(['max_attempts' => 3]);

        Livewire::actingAs($user)->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", 'false')->call('submit');

        Livewire::actingAs($user)->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", 'false')->call('submit');

        $attempts = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->orderBy('attempt_number')->pluck('attempt_number');
        $this->assertSame([1, 2], $attempts->all());
    }

    /**
     * FIXED (LMS hardening batch). The canonical multiple_choice answer
     * format is the option's array INDEX — this is what the admin
     * question-builder has always stored in `correct_answer`, and what
     * QuizService::scoreQuestion() has always compared against (confirmed by
     * the pre-existing tests\Unit\QuizServiceTest, which submits index
     * answers directly to the service and asserts correct scoring). The bug
     * was isolated to the student quiz form submitting the option's TEXT
     * instead of its index; the form now submits
     * `wire:model="answers.{id}"` bound to the option's loop index. See
     * docs/lms-hardening/LMS_FINAL_HARDENING.md.
     */
    public function test_multiple_choice_correct_first_option_scores_full(): void
    {
        [$quiz, $question, $user] = $this->makeMultipleChoiceQuiz(['فرس', 'أسد', 'نمر', 'ذئب'], correctIndex: 0);

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", '0')
            ->call('submit');

        $attempt = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        $this->assertSame(1, $attempt->earned_points);
        $this->assertTrue($attempt->is_passed);
    }

    public function test_multiple_choice_correct_middle_option_scores_full(): void
    {
        [$quiz, $question, $user] = $this->makeMultipleChoiceQuiz(['فرس', 'أسد', 'نمر', 'ذئب'], correctIndex: 2);

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", '2')
            ->call('submit');

        $attempt = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        $this->assertSame(1, $attempt->earned_points);
        $this->assertTrue($attempt->is_passed);
    }

    public function test_multiple_choice_correct_last_option_scores_full(): void
    {
        [$quiz, $question, $user] = $this->makeMultipleChoiceQuiz(['فرس', 'أسد', 'نمر', 'ذئب'], correctIndex: 3);

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", '3')
            ->call('submit');

        $attempt = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        $this->assertSame(1, $attempt->earned_points);
        $this->assertTrue($attempt->is_passed);
    }

    public function test_multiple_choice_wrong_option_scores_zero(): void
    {
        [$quiz, $question, $user] = $this->makeMultipleChoiceQuiz(['فرس', 'أسد', 'نمر', 'ذئب'], correctIndex: 1);

        Livewire::actingAs($user)
            ->test(QuizShow::class, ['quiz' => $quiz])
            ->set("answers.{$question->id}", '3')
            ->call('submit');

        $attempt = QuizAttempt::where('user_id', $user->id)->where('quiz_id', $quiz->id)->first();
        $this->assertSame(0, $attempt->earned_points);
        $this->assertFalse($attempt->is_passed);
    }

    /**
     * @return array{0: Quiz, 1: QuizQuestion, 2: User}
     */
    private function makeMultipleChoiceQuiz(array $options, int $correctIndex): array
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(['passing_score' => 60]);
        $question = QuizQuestion::factory()->for($quiz)->create([
            'type' => 'multiple_choice',
            'options' => $options,
            'correct_answer' => [$correctIndex],
            'points' => 1,
        ]);
        Enrollment::factory()->for($user)->for($course)->create();

        return [$quiz, $question, $user];
    }
}
