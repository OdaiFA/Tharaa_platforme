<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiQuizController extends Controller
{
    public function __construct(private readonly QuizService $quizService) {}

    public function submit(Request $request, Quiz $quiz): JsonResponse
    {
        $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable'],
        ]);

        $enrollment = $request->user()->enrollments()
            ->where('course_id', $quiz->lesson->module->course_id)
            ->first();

        if (! $enrollment) {
            return response()->json(['message' => 'يجب التسجيل في الدورة أولاً'], 403);
        }

        try {
            $attempt = $this->quizService->submitAttempt(
                $request->user(),
                $quiz,
                $request->input('answers'),
                $enrollment,
            );
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'score' => $attempt->score,
            'earned_points' => $attempt->earned_points,
            'total_points' => $attempt->total_points,
            'is_passed' => $attempt->is_passed,
            'attempt_number' => $attempt->attempt_number,
        ], 201);
    }
}
