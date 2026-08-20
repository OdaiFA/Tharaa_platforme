<?php

namespace App\Livewire\Admin\Questions;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Validation\Rule;
use Livewire\Component;

class QuestionsIndex extends Component
{
    public int $quizId;

    public string $question = '';

    public string $type = 'multiple_choice';

    /** @var array<int, string> */
    public array $options = ['', ''];

    /** @var array<int, string> */
    public array $correct_answer = [];

    public string $true_false_answer = 'true';

    public int $points = 1;

    public int $order_index = 0;

    public ?int $editingId = null;

    public ?int $confirmingDeleteId = null;

    public function mount(int $quizId): void
    {
        $this->quizId = $quizId;
    }

    public function addOption(): void
    {
        if (count($this->options) < 6) {
            $this->options[] = '';
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
        }
    }

    protected function rules(): array
    {
        $rules = [
            'question' => ['required', 'string'],
            'type' => ['required', Rule::in(['multiple_choice', 'true_false'])],
            'points' => ['nullable', 'integer', 'min:1'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ];

        if ($this->type === 'multiple_choice') {
            $rules['options'] = ['required', 'array', 'min:2'];
            $rules['options.*'] = ['required', 'string'];
            $rules['correct_answer'] = ['required', 'array', 'min:1'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'question.required' => 'نص السؤال مطلوب',
            'options.required' => 'الخيارات مطلوبة',
            'options.min' => 'يجب توفير خيارين على الأقل',
            'correct_answer.required' => 'الإجابة الصحيحة مطلوبة',
            'points.integer' => 'النقاط يجب أن تكون رقماً صحيحاً',
        ];
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->type === 'true_false') {
            $validated['options'] = [];
            $validated['correct_answer'] = [$this->true_false_answer];
        }

        if ($this->editingId) {
            QuizQuestion::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'تم تحديث السؤال بنجاح');
        } else {
            QuizQuestion::create(array_merge($validated, ['quiz_id' => $this->quizId]));
            session()->flash('success', 'تمت إضافة السؤال بنجاح');
        }

        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->reset(['question', 'points', 'order_index', 'editingId', 'correct_answer']);
        $this->type = 'multiple_choice';
        $this->options = ['', ''];
        $this->true_false_answer = 'true';
        $this->points = 1;
    }

    public function edit(int $id): void
    {
        $question = QuizQuestion::findOrFail($id);

        $this->editingId = $question->id;
        $this->question = $question->question;
        $this->type = $question->type;
        $this->points = $question->points;
        $this->order_index = $question->order_index;

        if ($question->type === 'true_false') {
            $this->true_false_answer = (string) ($question->correct_answer[0] ?? 'true');
            $this->options = ['', ''];
        } else {
            $this->options = $question->options ?: ['', ''];
            $this->correct_answer = $question->correct_answer ?? [];
        }

        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id): void
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
        $quiz = Quiz::with('questions')->findOrFail($this->quizId);

        return view('livewire.admin.questions.questions-index', [
            'quiz' => $quiz,
        ]);
    }
}
