<?php

namespace App\Livewire\Quizzes;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Livewire\Component;

class QuizShow extends Component
{
    public Quiz $quiz;

    public bool $exhausted = false;

    public int $attemptCount = 0;

    public array $answers = [];

    public function mount(Quiz $quiz, QuizService $quizService): void
    {
        $this->authorize('take', $quiz);

        $this->quiz = $quiz;
        $this->exhausted = ! $quizService->canAttempt(auth()->user(), $quiz);

        if (! $this->exhausted) {
            $this->attemptCount = QuizAttempt::query()
                ->where('user_id', auth()->id())
                ->where('quiz_id', $quiz->id)
                ->count();
        }
    }

    public function submit(QuizService $quizService)
    {
        $this->authorize('take', $this->quiz);

        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $this->quiz->lesson->module->course_id)
            ->first();

        try {
            $attempt = $quizService->submitAttempt(auth()->user(), $this->quiz, $this->answers, $enrollment);
        } catch (\DomainException $e) {
            $this->addError('quiz', $e->getMessage());

            return;
        }

        return redirect()->route('quizzes.result', [$this->quiz, $attempt]);
    }

    public function render()
    {
        return view('livewire.quizzes.quiz-show');
    }
}
