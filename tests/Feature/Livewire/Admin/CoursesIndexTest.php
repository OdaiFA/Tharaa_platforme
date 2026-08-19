<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Courses\CoursesIndex;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CoursesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_courses_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.courses.index'))
            ->assertOk()
            ->assertSeeLivewire(CoursesIndex::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.courses.index'))
            ->assertForbidden();
    }

    public function test_course_list_renders(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create(['title' => 'أساسيات الميزانية الشخصية']);

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->assertSee('أساسيات الميزانية الشخصية');
    }

    public function test_search_filters_the_list_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create(['title' => 'الادخار الذكي للمستقبل']);
        Course::factory()->create(['title' => 'مقدمة في الاستثمار']);

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->set('search', 'الادخار')
            ->assertSee('الادخار الذكي للمستقبل')
            ->assertDontSee('مقدمة في الاستثمار');
    }

    public function test_published_filter_shows_only_published_courses(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->create(['title' => 'دورة منشورة', 'is_published' => true]);
        Course::factory()->draft()->create(['title' => 'دورة مسودة']);

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->set('published', true)
            ->assertSee('دورة منشورة')
            ->assertDontSee('دورة مسودة');
    }

    public function test_pagination_limits_to_ten_per_page(): void
    {
        $admin = User::factory()->admin()->create();
        Course::factory()->count(15)->create();

        $component = Livewire::actingAs($admin)->test(CoursesIndex::class);

        $this->assertCount(10, $component->viewData('courses'));
    }

    public function test_delete_requires_confirmation_then_soft_deletes_the_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->call('delete', $course->id);

        $this->assertNotSoftDeleted($course);

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->call('confirmDelete', $course->id)
            ->call('delete', $course->id);

        $this->assertSoftDeleted($course);
    }

    public function test_soft_deleted_course_still_appears_in_the_admin_list_with_deleted_badge(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['title' => 'دورة محذوفة للاختبار']);
        $course->delete();

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->assertSee('دورة محذوفة للاختبار')
            ->assertSee('محذوفة');
    }

    public function test_restore_requires_confirmation_then_restores_the_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $course->delete();

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->call('restore', $course->id);

        $this->assertSoftDeleted($course);

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->call('confirmRestore', $course->id)
            ->call('restore', $course->id);

        $this->assertNotSoftDeleted($course);
    }

    public function test_restored_course_keeps_its_original_publish_state(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['is_published' => true]);
        $course->delete();

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->call('confirmRestore', $course->id)
            ->call('restore', $course->id);

        $this->assertTrue($course->fresh()->is_published);
    }

    public function test_related_modules_and_enrollments_survive_soft_deletion_of_the_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();
        $enrollment = Enrollment::factory()->for($course)->create();

        Livewire::actingAs($admin)
            ->test(CoursesIndex::class)
            ->call('confirmDelete', $course->id)
            ->call('delete', $course->id);

        $this->assertSoftDeleted($course);
        $this->assertDatabaseHas('modules', ['id' => $module->id]);
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }
}
