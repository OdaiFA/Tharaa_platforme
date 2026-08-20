<?php

namespace Tests\Feature\Livewire\Admin\Modules;

use App\Livewire\Admin\Modules\ModulesIndex;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModulesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_the_modules_page(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.modules.index', $course))
            ->assertOk()
            ->assertSeeLivewire(ModulesIndex::class);
    }

    public function test_regular_user_cannot_access_it(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->actingAs($user)->get(route('admin.modules.index', $course))->assertForbidden();
    }

    public function test_admin_can_create_a_module(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        Livewire::actingAs($admin)
            ->test(ModulesIndex::class, ['courseId' => $course->id])
            ->set('title', 'وحدة جديدة')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('modules', ['course_id' => $course->id, 'title' => 'وحدة جديدة']);
    }

    public function test_admin_can_edit_a_module(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create(['title' => 'قديم']);

        Livewire::actingAs($admin)
            ->test(ModulesIndex::class, ['courseId' => $course->id])
            ->call('edit', $module->id)
            ->assertSet('title', 'قديم')
            ->set('title', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $module->fresh()->title);
    }

    public function test_admin_can_delete_a_module_after_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $module = Module::factory()->for($course)->create();

        Livewire::actingAs($admin)
            ->test(ModulesIndex::class, ['courseId' => $course->id])
            ->call('delete', $module->id);

        $this->assertModelExists($module);

        Livewire::actingAs($admin)
            ->test(ModulesIndex::class, ['courseId' => $course->id])
            ->call('confirmDelete', $module->id)
            ->call('delete', $module->id);

        $this->assertModelMissing($module);
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        Livewire::actingAs($admin)
            ->test(ModulesIndex::class, ['courseId' => $course->id])
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title']);
    }
}
