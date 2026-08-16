<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function __construct(
        private readonly EnrollmentService $enrollmentService,
    ) {}

    /**
     * Check whether a user is allowed to attempt the quiz (BR-EDU-011).
     */
    public function canAttempt(User $user, Quiz $quiz): bool
    {
        $attempts = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        return $attempts < $quiz->max_attempts;
    }

    /**
     * Submit a quiz attempt and calculate the score (BR-EDU-009, 010).
     */
    public function submitAttempt(User $user, Quiz $quiz, array $answers, ?Enrollment $enrollment = null): QuizAttempt
    {
        if (! $this->canAttempt($user, $quiz)) {
            throw new \DomainException('لقد استنفدت جميع المحاولات المسموحة لهذا الاختبار');
        }

        $enrollment = $enrollment ?? Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $quiz->lesson->module->course_id)
            ->firstOrFail();

        $questions = $quiz->questions()->get();
        $totalPoints = (int) $questions->sum('points');
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $earnedPoints += $this->scoreQuestion($question, $answers[$question->id] ?? null);
        }

        $score = $totalPoints > 0 ? (int) round(($earnedPoints / $totalPoints) * 100) : 0;
        $attemptNumber = (int) QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->max('attempt_number') + 1;

        $attempt = DB::transaction(function () use ($user, $quiz, $enrollment, $score, $totalPoints, $earnedPoints, $attemptNumber) {
            return QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'enrollment_id' => $enrollment->id,
                'score' => $score,
                'total_points' => $totalPoints,
                'earned_points' => $earnedPoints,
                'is_passed' => $score >= $quiz->passing_score,
                'attempt_number' => $attemptNumber,
                'started_at' => now(),
                'completed_at' => now(),
            ]);
        });

        if ($attempt->is_passed && $quiz->lesson) {
            $this->enrollmentService->completeLesson($user, $quiz->lesson);
        }

        return $attempt;
    }

    /**
     * Calculate the score of a single question.
     */
    public function calculateScore(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz;
        $score = $attempt->total_points > 0
            ? (int) round(($attempt->earned_points / $attempt->total_points) * 100)
            : 0;

        $attempt->update([
            'score' => $score,
            'is_passed' => $score >= $quiz->passing_score,
        ]);
    }

    private function scoreQuestion(QuizQuestion $question, mixed $answer): int
    {
        if ($answer === null || $answer === '') {
            return 0;
        }

        $correct = collect($question->correct_answer)->map(fn ($v) => (string) $v)->sort()->values();

        if ($question->type === 'true_false') {
            return (string) $answer === (string) $question->correct_answer[0] ? $question->points : 0;
        }

        $answerList = is_array($answer) ? $answer : [$answer];
        $normalized = collect($answerList)->map(fn ($v) => (string) $v)->sort()->values();

        return $normalized->diffAssoc($correct)->isEmpty() && $correct->diffAssoc($normalized)->isEmpty()
            ? $question->points
            : 0;
    }
}
