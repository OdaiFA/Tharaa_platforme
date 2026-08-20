<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function __construct(private readonly QuizService $quizService) {}

    public function show(Quiz $quiz): View
    {
        $this->authorize('take', $quiz);

        return view('quizzes.show', compact('quiz'));
    }

    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $this->authorize('take', $quiz);

        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable'],
        ]);

        $enrollment = auth()->user()->enrollments()
            ->where('course_id', $quiz->lesson->module->course_id)
            ->first();

        try {
            $attempt = $this->quizService->submitAttempt(
                auth()->user(),
                $quiz,
                $request->input('answers'),
                $enrollment,
            );
        } catch (\DomainException $e) {
            return back()->withErrors(['quiz' => $e->getMessage()]);
        }

        return redirect()->route('quizzes.result', [$quiz, $attempt]);
    }

    public function result(Quiz $quiz, QuizAttempt $attempt): View
    {
        $this->authorize('take', $quiz);

        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        return view('quizzes.result', compact('quiz', 'attempt'));
    }
}
