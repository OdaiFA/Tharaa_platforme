<?php

namespace Tests\Feature\Livewire\Learning;

use App\Livewire\Learning\CourseLearn;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CourseLearnTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_user_can_view_the_learn_page(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Enrollment::factory()->for($user)->for($course)->create();

        $this->actingAs($user)
            ->get(route('courses.learn', $course))
            ->assertOk()
            ->assertSeeLivewire(CourseLearn::class);
    }

    public function test_non_enrolled_user_is_forbidden(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($user)->get(route('courses.learn', $course))->assertForbidden();
    }

    public function test_completing_a_lesson_marks_it_done_and_updates_progress(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['progress_percentage' => 0, 'status' => 'enrolled']);

        Livewire::actingAs($user)
            ->test(CourseLearn::class, ['course' => $course])
            ->call('completeLesson', $lesson->id)
            ->assertSet('completedLessons', [$lesson->id]);

        $this->assertSame(100, $enrollment->fresh()->progress_percentage);
        $this->assertSame('completed', $enrollment->fresh()->status);
    }

    public function test_progress_transitions_from_enrolled_to_in_progress_to_completed(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $lessonOne = Lesson::factory()->for($module)->create();
        $lessonTwo = Lesson::factory()->for($module)->create();
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['progress_percentage' => 0, 'status' => 'enrolled']);

        $component = Livewire::actingAs($user)->test(CourseLearn::class, ['course' => $course]);

        $component->call('completeLesson', $lessonOne->id);
        $this->assertSame('in_progress', $enrollment->fresh()->status);
        $this->assertSame(50, $enrollment->fresh()->progress_percentage);

        $component->call('completeLesson', $lessonTwo->id);
        $this->assertSame('completed', $enrollment->fresh()->status);
        $this->assertSame(100, $enrollment->fresh()->progress_percentage);
    }

    public function test_a_user_cannot_complete_a_lesson_for_a_course_they_are_not_enrolled_in(): void
    {
        $user = User::factory()->create();
        $enrolledCourse = Course::factory()->create();
        Enrollment::factory()->for($user)->for($enrolledCourse)->create();

        $otherCourse = Course::factory()->create();
        $otherModule = Module::factory()->for($otherCourse)->create();
        $otherLesson = Lesson::factory()->for($otherModule)->create();

        Livewire::actingAs($user)
            ->test(CourseLearn::class, ['course' => $enrolledCourse])
            ->call('completeLesson', $otherLesson->id)
            ->assertForbidden();
    }

    /**
     * LMS hardening (Issue 4): 100% lesson progress with an unpassed
     * required quiz must never show the certificate-earned banner — the
     * banner is now gated on enrollment.certificate_issued, not on
     * progress_percentage alone.
     */
    public function test_100_percent_progress_with_unpassed_quiz_shows_no_certificate_banner(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        Quiz::factory()->for($lesson)->create(['is_published' => true]);
        Enrollment::factory()->for($user)->for($course)->create(['progress_percentage' => 0, 'status' => 'enrolled']);

        Livewire::actingAs($user)
            ->test(CourseLearn::class, ['course' => $course])
            ->call('completeLesson', $lesson->id)
            ->assertDontSee('حصلت على شهادة إتمام')
            ->assertSee('لقد أكملت جميع دروس الدورة');
    }

    /**
     * LMS hardening (Issue 4): once the certificate is genuinely issued
     * (all lessons complete + all required quizzes passed), the banner
     * must show.
     */
    public function test_certificate_actually_issued_shows_the_banner(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => true]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        $quiz = Quiz::factory()->for($lesson)->create(['is_published' => true]);
        $enrollment = Enrollment::factory()->for($user)->for($course)->create(['progress_percentage' => 0, 'status' => 'enrolled']);
        QuizAttempt::factory()->create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'enrollment_id' => $enrollment->id,
            'is_passed' => true,
        ]);

        Livewire::actingAs($user)
            ->test(CourseLearn::class, ['course' => $course])
            ->call('completeLesson', $lesson->id)
            ->assertSee('حصلت على شهادة إتمام');
    }

    /**
     * LMS hardening (Issue 4): a certificate_enabled=false course must
     * never show the certificate-earned banner, even at 100% progress.
     */
    public function test_certificate_disabled_course_shows_no_banner(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate_enabled' => false]);
        $module = Module::factory()->for($course)->create();
        $lesson = Lesson::factory()->for($module)->create();
        Enrollment::factory()->for($user)->for($course)->create(['progress_percentage' => 0, 'status' => 'enrolled']);

        Livewire::actingAs($user)
            ->test(CourseLearn::class, ['course' => $course])
            ->call('completeLesson', $lesson->id)
            ->assertDontSee('حصلت على شهادة إتمام')
            ->assertSee('هذه الدورة لا تتضمن شهادة إتمام');
    }
}
