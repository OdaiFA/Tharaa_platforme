<?php

namespace Tests\Feature\Livewire\Admin\Quizzes;

use App\Livewire\Admin\Quizzes\QuizForm;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QuizFormTest extends TestCase
{
    use RefreshDatabase;

    private function makeLesson(): Lesson
    {
        $module = Module::factory()->for(Course::factory())->create();

        return Lesson::factory()->for($module)->create();
    }

    public function test_admin_can_render_the_quiz_page(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = $this->makeLesson();

        $this->actingAs($admin)
            ->get(route('admin.quizzes.index', $lesson))
            ->assertOk()
            ->assertSeeLivewire(QuizForm::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();
        $lesson = $this->makeLesson();

        $this->actingAs($user)->get(route('admin.quizzes.index', $lesson))->assertForbidden();
    }

    public function test_admin_can_create_a_quiz_for_a_lesson_with_none(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = $this->makeLesson();

        Livewire::actingAs($admin)
            ->test(QuizForm::class, ['lessonId' => $lesson->id])
            ->set('title', 'اختبار الدرس')
            ->set('passing_score', 70)
            ->set('max_attempts', 2)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('quizzes', ['lesson_id' => $lesson->id, 'title' => 'اختبار الدرس', 'passing_score' => 70]);
        $this->assertSame(1, Quiz::where('lesson_id', $lesson->id)->count());
    }

    public function test_saving_again_updates_the_same_quiz_instead_of_creating_a_second_one(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = $this->makeLesson();
        $quiz = Quiz::factory()->for($lesson)->create(['title' => 'قديم']);

        Livewire::actingAs($admin)
            ->test(QuizForm::class, ['lessonId' => $lesson->id])
            ->assertSet('title', 'قديم')
            ->set('title', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(1, Quiz::where('lesson_id', $lesson->id)->count());
        $this->assertSame('محدث', $quiz->fresh()->title);
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = $this->makeLesson();

        Livewire::actingAs($admin)
            ->test(QuizForm::class, ['lessonId' => $lesson->id])
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title']);
    }

    public function test_admin_can_delete_a_question_after_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $lesson = $this->makeLesson();
        $quiz = Quiz::factory()->for($lesson)->create();
        $question = QuizQuestion::factory()->for($quiz)->create();

        Livewire::actingAs($admin)
            ->test(QuizForm::class, ['lessonId' => $lesson->id])
            ->call('confirmDeleteQuestion', $question->id)
            ->call('deleteQuestion', $question->id);

        $this->assertModelMissing($question);
    }
}
