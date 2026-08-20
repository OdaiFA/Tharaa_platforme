<?php

namespace Tests\Feature\Livewire\Admin\Questions;

use App\Livewire\Admin\Questions\QuestionsIndex;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuestionsIndexTest extends TestCase
{
    use RefreshDatabase;

    private function makeQuiz(): Quiz
    {
        $module = Module::factory()->for(Course::factory())->create();
        $lesson = Lesson::factory()->for($module)->create();

        return Quiz::factory()->for($lesson)->create();
    }

    public function test_admin_can_render_the_questions_page(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = $this->makeQuiz();

        $this->actingAs($admin)
            ->get(route('admin.questions.index', $quiz))
            ->assertOk()
            ->assertSeeLivewire(QuestionsIndex::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();
        $quiz = $this->makeQuiz();

        $this->actingAs($user)->get(route('admin.questions.index', $quiz))->assertForbidden();
    }

    public function test_admin_can_create_a_multiple_choice_question(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = $this->makeQuiz();

        Livewire::actingAs($admin)
            ->test(QuestionsIndex::class, ['quizId' => $quiz->id])
            ->set('question', 'ما هي عاصمة السعودية؟')
            ->set('type', 'multiple_choice')
            ->set('options', ['الرياض', 'جدة'])
            ->set('correct_answer', ['0'])
            ->set('points', 2)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $quiz->id,
            'question' => 'ما هي عاصمة السعودية؟',
            'type' => 'multiple_choice',
            'points' => 2,
        ]);
    }

    public function test_admin_can_create_a_true_false_question(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = $this->makeQuiz();

        Livewire::actingAs($admin)
            ->test(QuestionsIndex::class, ['quizId' => $quiz->id])
            ->set('question', 'الادخار مهم؟')
            ->set('type', 'true_false')
            ->set('true_false_answer', 'true')
            ->call('save')
            ->assertHasNoErrors();

        $question = QuizQuestion::where('quiz_id', $quiz->id)->first();
        $this->assertSame('true_false', $question->type);
        $this->assertSame(['true'], $question->correct_answer);
    }

    public function test_multiple_choice_requires_at_least_two_options(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = $this->makeQuiz();

        Livewire::actingAs($admin)
            ->test(QuestionsIndex::class, ['quizId' => $quiz->id])
            ->set('question', 'سؤال')
            ->set('type', 'multiple_choice')
            ->set('options', ['خيار واحد فقط'])
            ->call('save')
            ->assertHasErrors(['options']);
    }

    public function test_admin_can_edit_a_question(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = $this->makeQuiz();
        $question = QuizQuestion::factory()->for($quiz)->create(['question' => 'قديم', 'type' => 'multiple_choice', 'options' => ['أ', 'ب'], 'correct_answer' => ['0']]);

        Livewire::actingAs($admin)
            ->test(QuestionsIndex::class, ['quizId' => $quiz->id])
            ->call('edit', $question->id)
            ->assertSet('question', 'قديم')
            ->set('question', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $question->fresh()->question);
    }

    public function test_admin_can_delete_a_question_after_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $quiz = $this->makeQuiz();
        $question = QuizQuestion::factory()->for($quiz)->create();

        Livewire::actingAs($admin)
            ->test(QuestionsIndex::class, ['quizId' => $quiz->id])
            ->call('delete', $question->id);

        $this->assertModelExists($question);

        Livewire::actingAs($admin)
            ->test(QuestionsIndex::class, ['quizId' => $quiz->id])
            ->call('confirmDelete', $question->id)
            ->call('delete', $question->id);

        $this->assertModelMissing($question);
    }
}
