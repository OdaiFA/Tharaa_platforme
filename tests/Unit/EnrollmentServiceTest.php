<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\EnrollmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnrollmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolling_twice_does_not_create_a_duplicate_row(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $service = app(EnrollmentService::class);
        $first = $service->enroll($user, $course);
        $second = $service->enroll($user, $course);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->count());
    }

    public function test_completing_all_lessons_marks_the_enrollment_completed(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        app(EnrollmentService::class)->completeLesson($user, $lesson);

        $this->assertSame('completed', $enrollment->fresh()->status);
        $this->assertSame(100, $enrollment->fresh()->progress_percentage);
    }

    public function test_certificate_is_issued_when_all_lessons_completed_and_quizzes_passed(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);
        QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'is_passed' => true,
        ]);

        $service = app(EnrollmentService::class);
        $service->completeLesson($user, $lesson);

        $enrollment->refresh();
        $this->assertTrue((bool) $enrollment->certificate_issued);
        $this->assertNotNull($enrollment->certificate_url);
        Storage::disk('public')->assertExists($enrollment->certificate_url);
    }

    public function test_certificate_is_not_issued_when_course_has_certificates_disabled(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => false]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        $result = app(EnrollmentService::class)->issueCertificate($enrollment->fresh(['user', 'course']));

        $this->assertFalse($result);
        $this->assertFalse((bool) $enrollment->fresh()->certificate_issued);
    }

    public function test_certificate_is_not_issued_when_lessons_are_incomplete(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        Lesson::factory()->for($module)->count(2)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        $result = app(EnrollmentService::class)->issueCertificate($enrollment->fresh(['user', 'course']));

        $this->assertFalse($result);
        $this->assertFalse((bool) $enrollment->fresh()->certificate_issued);
    }

    public function test_certificate_is_not_issued_when_a_required_quiz_was_not_passed(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);
        QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'is_passed' => false,
        ]);

        // All lessons completed, but the lesson's quiz was never passed.
        app(EnrollmentService::class)->completeLesson($user, $lesson);

        $this->assertFalse((bool) $enrollment->fresh()->certificate_issued);
    }

    /**
     * LMS hardening: completing an already-completed lesson a second time
     * (e.g. a student who manually completes a lesson and then also passes
     * its quiz, which independently calls completeLesson() again to
     * re-check certificate eligibility) must not create a duplicate
     * progress row and must not send a duplicate "course completed"
     * notification — LessonCompleted is intentionally re-dispatched (so
     * eligibility changes are picked up), but every downstream side effect
     * is idempotent.
     */
    public function test_completing_an_already_completed_lesson_is_idempotent(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        $service = app(EnrollmentService::class);
        $service->completeLesson($user, $lesson);
        $notificationsAfterFirst = UserNotification::where('notifiable_id', $user->id)->where('type', 'course_completed')->count();

        $service->completeLesson($user, $lesson);

        $this->assertSame(1, LessonProgress::where('user_id', $user->id)->where('lesson_id', $lesson->id)->count());
        $this->assertSame(
            $notificationsAfterFirst,
            UserNotification::where('notifiable_id', $user->id)->where('type', 'course_completed')->count()
        );
    }

    /**
     * LMS hardening: re-checking completion after certificate-eligibility
     * changes (a quiz newly passing for a lesson that was already
     * completed) must still issue the certificate — this is the scenario
     * that a naive "skip if already completed" idempotency guard would
     * incorrectly block.
     */
    public function test_completing_an_already_completed_lesson_still_reissues_eligibility_check(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(['is_published' => true]);
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        $service = app(EnrollmentService::class);
        // Lesson completed manually first — quiz not yet passed, so no certificate.
        $service->completeLesson($user, $lesson);
        $this->assertFalse((bool) $enrollment->fresh()->certificate_issued);

        // Quiz passes afterward and re-triggers completeLesson() for the
        // same (already-completed) lesson.
        QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'is_passed' => true,
        ]);
        $service->completeLesson($user, $lesson);

        $this->assertTrue((bool) $enrollment->fresh()->certificate_issued);
        Storage::disk('public')->assertExists($enrollment->fresh()->certificate_url);
    }

    /**
     * LMS hardening: calling issueCertificate() a second time for an
     * already-issued enrollment must reuse the existing certificate state
     * rather than generating a new PDF/code and orphaning the old one.
     */
    public function test_second_certificate_issuance_does_not_duplicate(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        $service = app(EnrollmentService::class);
        $service->completeLesson($user, $lesson);
        $enrollment->refresh();
        $firstCode = $enrollment->certificate_url;

        $result = $service->issueCertificate($enrollment->fresh(['user', 'course']));

        $this->assertTrue($result);
        $this->assertSame($firstCode, $enrollment->fresh()->certificate_url);
        $this->assertCount(1, Storage::disk('public')->files('certificates'));
    }

    /**
     * LMS hardening: a single LessonCompleted dispatch (from completing the
     * course's final lesson) must produce exactly one CourseCompleted
     * side-effect cascade — one certificate file, one congratulation
     * notification — not two. This exercises the real event/listener chain
     * (no Event::fake) to verify the listener chain reaches the expected
     * final state, per the LMS hardening event-integration requirement.
     */
    public function test_course_completion_cascade_runs_exactly_once(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        Enrollment::factory()->for($user)->for($course)->create(['status' => 'enrolled', 'progress_percentage' => 0]);

        app(EnrollmentService::class)->completeLesson($user, $lesson);

        $this->assertCount(1, Storage::disk('public')->files('certificates'));
        $this->assertSame(
            1,
            UserNotification::where('notifiable_id', $user->id)->where('type', 'course_completed')->count()
        );
    }
}
