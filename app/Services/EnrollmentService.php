<?php

namespace App\Services;

use App\Events\CourseCompleted;
use App\Events\LessonCompleted;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EnrollmentService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Enroll a user in a course (BR-EDU-007).
     */
    public function enroll(User $user, Course $course): Enrollment
    {
        return Enrollment::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ], [
            'status' => 'enrolled',
            'enrolled_at' => now(),
        ]);
    }

    /**
     * Recalculate enrollment progress from lesson progress (BR-EDU-008).
     */
    public function updateProgress(Enrollment $enrollment): void
    {
        $totalLessons = $enrollment->course->modules()
            ->published()
            ->withCount(['lessons' => fn ($q) => $q->published()])
            ->get()
            ->sum('lessons_count');

        $completedLessons = $enrollment->lessonProgress()
            ->where('status', 'completed')
            ->count();

        if ($totalLessons > 0) {
            $percentage = (int) round(($completedLessons / $totalLessons) * 100);
            $enrollment->update([
                'progress_percentage' => $percentage,
                'status' => $percentage >= 100 ? 'completed' : ($percentage > 0 ? 'in_progress' : 'enrolled'),
            ]);
        }

        $this->checkCourseCompletion($enrollment);
    }

    /**
     * Mark a lesson as completed for a user (BR-EDU-008).
     */
    public function completeLesson(User $user, Lesson $lesson): LessonProgress
    {
        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $lesson->module->course_id)
            ->firstOrFail();

        $progress = LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'enrollment_id' => $enrollment->id,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        LessonCompleted::dispatch($user, $lesson, $enrollment);

        $this->updateProgress($enrollment->fresh());

        return $progress;
    }

    /**
     * Issue a certificate when course requirements are met (BR-EDU-012).
     */
    public function issueCertificate(Enrollment $enrollment): bool
    {
        $course = $enrollment->course;

        if (! $course->certificate_enabled) {
            return false;
        }

        if (! $this->allLessonsCompleted($enrollment) || ! $this->allQuizzesPassed($enrollment)) {
            return false;
        }

        $payload = [
            'user_name' => $enrollment->user->name,
            'course_title' => $course->title,
            'completion_date' => now()->format('d/m/Y'),
            'course_duration_hours' => $course->duration_hours,
            'certificate_code' => 'THR-' . strtoupper(Str::random(8)),
        ];

        $pdf = Pdf::loadView('certificates.template', $payload);
        $filename = 'certificates/' . $payload['certificate_code'] . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $enrollment->update([
            'certificate_issued' => true,
            'certificate_url' => $filename,
            'completed_at' => now(),
            'status' => 'completed',
        ]);

        return true;
    }

    /**
     * Check whether all requirements are met and fire the completion event.
     */
    public function checkCourseCompletion(Enrollment $enrollment): void
    {
        if ($enrollment->status === 'completed') {
            CourseCompleted::dispatch($enrollment);
        }
    }

    private function allLessonsCompleted(Enrollment $enrollment): bool
    {
        $totalLessons = $enrollment->course->modules()
            ->published()
            ->withCount(['lessons' => fn ($q) => $q->published()])
            ->get()
            ->sum('lessons_count');

        if ($totalLessons === 0) {
            return false;
        }

        $completed = $enrollment->lessonProgress()
            ->where('status', 'completed')
            ->count();

        return $completed >= $totalLessons;
    }

    private function allQuizzesPassed(Enrollment $enrollment): bool
    {
        $quizzes = $enrollment->course->modules()
            ->published()
            ->get()
            ->flatMap(fn ($module) => $module->lessons()->published()->get())
            ->flatMap(fn ($lesson) => $lesson->quiz()->where('is_published', true)->get());

        if ($quizzes->isEmpty()) {
            return true;
        }

        $quizIds = $quizzes->pluck('id');

        $passed = $enrollment->quizAttempts()
            ->whereIn('quiz_id', $quizIds)
            ->where('is_passed', true)
            ->pluck('quiz_id')
            ->unique();

        return $passed->count() === $quizIds->unique()->count();
    }
}
