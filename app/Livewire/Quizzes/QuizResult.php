<?php

namespace App\Livewire\Quizzes;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Livewire\Component;

class QuizResult extends Component
{
    public Quiz $quiz;

    public QuizAttempt $attempt;

    public bool $canRetry = false;

    public function mount(Quiz $quiz, QuizAttempt $attempt, QuizService $quizService): void
    {
        $this->authorize('take', $quiz);

        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $this->quiz = $quiz;
        $this->attempt = $attempt;

        // Fixes a pre-existing bug: the original Blade view called
        // QuizService::canAttempt() statically, but it is an instance
        // method — that call fatal-errors in PHP 8 every time this branch
        // is reached (i.e. every failed attempt). Resolved here via DI.
        $this->canRetry = ! $attempt->is_passed && $quizService->canAttempt(auth()->user(), $quiz);
    }

    public function render()
    {
        return view('livewire.quizzes.quiz-result');
    }
}
