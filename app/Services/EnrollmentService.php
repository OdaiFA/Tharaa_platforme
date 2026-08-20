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
     *
     * This is the single authoritative path to course-completion detection
     * (see completeLesson()) — checkCourseCompletion() is always called
     * here so that re-evaluating progress after a NEW fact (e.g. a quiz
     * being passed after the lesson was already marked complete) can still
     * discover a fresh certificate-eligibility state. Re-dispatching
     * CourseCompleted for an enrollment that was already 'completed' is
     * intentionally allowed — every CourseCompleted listener is itself
     * idempotent (see IssueCertificate / SendCongratulationNotification),
     * so repeated dispatch is safe and is how eligibility changes (like a
     * quiz newly passing) get picked up without a second completion event
     * type.
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
     *
     * Always dispatches LessonCompleted, even for an already-completed
     * lesson — this is intentional: a lesson-completion call can also be
     * the signal that certificate eligibility should be re-checked (e.g.
     * QuizService::submitAttempt() calls this after a passing attempt,
     * which may happen after the lesson was already manually completed).
     * Duplicate progress ROWS are prevented structurally by
     * updateOrCreate(); duplicate SIDE EFFECTS (certificates,
     * notifications) are prevented by making each downstream listener
     * idempotent rather than by blocking the event here.
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

        // updateProgress() runs synchronously inside the UpdateEnrollmentProgress
        // listener triggered by this dispatch — it is the single authoritative
        // path to course-completion detection, so it is intentionally not
        // called again here.
        LessonCompleted::dispatch($user, $lesson, $enrollment);

        return $progress;
    }

    /**
     * Issue a certificate when course requirements are met (BR-EDU-012).
     *
     * Idempotent: if a certificate was already issued for this enrollment,
     * the existing state is returned as-is rather than regenerating a new
     * PDF/code — this is required because CourseCompleted can legitimately
     * be observed more than once by a caller (e.g. manual re-checks), and
     * this is the authoritative guard against duplicate certificates.
     */
    public function issueCertificate(Enrollment $enrollment): bool
    {
        if ($enrollment->certificate_issued) {
            return true;
        }

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

        $written = Storage::disk('public')->put($filename, $pdf->output());

        if (! $written) {
            return false;
        }

        try {
            $enrollment->update([
                'certificate_issued' => true,
                'certificate_url' => $filename,
                'completed_at' => now(),
                'status' => 'completed',
            ]);
        } catch (\Throwable $e) {
            // The PDF was written but the DB state could not be persisted —
            // clean up the orphaned file rather than leaving a certificate
            // on disk with no corresponding enrollment record.
            Storage::disk('public')->delete($filename);

            throw $e;
        }

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
