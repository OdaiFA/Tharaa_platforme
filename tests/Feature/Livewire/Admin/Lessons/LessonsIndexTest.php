<?php

namespace Tests\Feature\Livewire\Admin\Lessons;

use App\Livewire\Admin\Lessons\LessonsIndex;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LessonsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_the_lessons_page(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::factory()->for(Course::factory())->create();

        $this->actingAs($admin)
            ->get(route('admin.lessons.index', $module))
            ->assertOk()
            ->assertSeeLivewire(LessonsIndex::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();
        $module = Module::factory()->for(Course::factory())->create();

        $this->actingAs($user)->get(route('admin.lessons.index', $module))->assertForbidden();
    }

    public function test_admin_can_create_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::factory()->for(Course::factory())->create();

        Livewire::actingAs($admin)
            ->test(LessonsIndex::class, ['moduleId' => $module->id])
            ->set('title', 'درس جديد')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('lessons', ['module_id' => $module->id, 'title' => 'درس جديد']);
    }

    public function test_admin_can_edit_a_lesson(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::factory()->for(Course::factory())->create();
        $lesson = Lesson::factory()->for($module)->create(['title' => 'قديم']);

        Livewire::actingAs($admin)
            ->test(LessonsIndex::class, ['moduleId' => $module->id])
            ->call('edit', $lesson->id)
            ->set('title', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $lesson->fresh()->title);
    }

    public function test_admin_can_delete_a_lesson_after_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::factory()->for(Course::factory())->create();
        $lesson = Lesson::factory()->for($module)->create();

        Livewire::actingAs($admin)
            ->test(LessonsIndex::class, ['moduleId' => $module->id])
            ->call('confirmDelete', $lesson->id)
            ->call('delete', $lesson->id);

        $this->assertSoftDeleted($lesson);
    }

    public function test_invalid_video_url_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $module = Module::factory()->for(Course::factory())->create();

        Livewire::actingAs($admin)
            ->test(LessonsIndex::class, ['moduleId' => $module->id])
            ->set('title', 'درس')
            ->set('video_url', 'not-a-url')
            ->call('save')
            ->assertHasErrors(['video_url']);
    }
}
