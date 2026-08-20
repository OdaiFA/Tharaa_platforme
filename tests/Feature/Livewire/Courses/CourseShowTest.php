<?php

namespace Tests\Feature\Livewire\Courses;

use App\Livewire\Courses\CourseShow;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_course_is_visible_to_guests(): void
    {
        $course = Course::factory()->create();

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSeeLivewire(CourseShow::class);
    }

    public function test_unpublished_course_is_not_visible_to_a_regular_user(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->draft()->create();

        $this->actingAs($user)->get(route('courses.show', $course))->assertNotFound();
    }

    public function test_unpublished_course_is_visible_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->draft()->create();

        $this->actingAs($admin)->get(route('courses.show', $course))->assertOk();
    }

    public function test_authenticated_user_can_enroll(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        Livewire::actingAs($user)
            ->test(CourseShow::class, ['course' => $course])
            ->call('enroll')
            ->assertRedirect(route('courses.learn', $course));

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_enrolling_twice_does_not_create_a_duplicate_row(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        Livewire::actingAs($user)->test(CourseShow::class, ['course' => $course])->call('enroll');
        Livewire::actingAs($user)->test(CourseShow::class, ['course' => $course])->call('enroll');

        $this->assertSame(1, Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->count());
    }

    public function test_creator_cannot_enroll_in_their_own_course(): void
    {
        $creator = User::factory()->create();
        $course = Course::factory()->create(['created_by' => $creator->id]);

        Livewire::actingAs($creator)
            ->test(CourseShow::class, ['course' => $course])
            ->call('enroll')
            ->assertForbidden();
    }

    public function test_already_enrolled_user_sees_continue_learning(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        Enrollment::factory()->for($user)->for($course)->create(['progress_percentage' => 40]);

        Livewire::actingAs($user)
            ->test(CourseShow::class, ['course' => $course])
            ->assertSee('40%');
    }
}
