<?php

namespace App\Livewire\Admin\Quizzes;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Livewire\Component;

class QuizForm extends Component
{
    public int $lessonId;

    public string $title = '';

    public ?string $description = null;

    public ?int $passing_score = 60;

    public ?int $max_attempts = 3;

    public bool $is_published = false;

    public ?int $confirmingDeleteId = null;

    public function mount(int $lessonId): void
    {
        $this->lessonId = $lessonId;

        $quiz = Lesson::findOrFail($lessonId)->quiz;

        if ($quiz) {
            $this->title = $quiz->title;
            $this->description = $quiz->description;
            $this->passing_score = $quiz->passing_score;
            $this->max_attempts = $quiz->max_attempts;
            $this->is_published = $quiz->is_published;
        }
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'عنوان الاختبار مطلوب',
            'passing_score.between' => 'درجة النجاح يجب أن تكون بين 0 و 100',
            'max_attempts.between' => 'عدد المحاولات يجب أن يكون بين 1 و 10',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        Quiz::updateOrCreate(
            ['lesson_id' => $this->lessonId],
            array_merge($validated, ['is_published' => $this->is_published]),
        );

        session()->flash('success', 'تم حفظ الاختبار بنجاح');
    }

    public function confirmDeleteQuestion(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDeleteQuestion(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteQuestion(int $id): void
    {
        if ($this->confirmingDeleteId !== $id) {
            return;
        }

        QuizQuestion::findOrFail($id)->delete();
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف السؤال بنجاح');
    }

    public function render()
    {
        $lesson = Lesson::with('quiz.questions')->findOrFail($this->lessonId);

        return view('livewire.admin.quizzes.quiz-form', [
            'lesson' => $lesson,
        ]);
    }
}
