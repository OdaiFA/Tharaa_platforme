<?php

namespace Tests\Feature\Livewire\Admin\Courses;

use App\Livewire\Admin\Courses\CourseForm;
use App\Models\AgeGroup;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CourseFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_course(): void
    {
        $admin = User::factory()->admin()->create();
        $ageGroup = AgeGroup::factory()->create();

        Livewire::actingAs($admin)
            ->test(CourseForm::class)
            ->set('title', 'دورة جديدة')
            ->set('level', 'beginner')
            ->set('age_groups', [$ageGroup->id])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('courses', ['title' => 'دورة جديدة', 'created_by' => $admin->id]);
        $course = Course::where('title', 'دورة جديدة')->first();
        $this->assertTrue($course->ageGroups->contains($ageGroup->id));
    }

    public function test_thumbnail_upload_is_stored(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CourseForm::class)
            ->set('title', 'دورة بصورة')
            ->set('level', 'beginner')
            ->set('thumbnail', UploadedFile::fake()->image('cover.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $course = Course::where('title', 'دورة بصورة')->first();
        $this->assertNotNull($course->thumbnail);
        Storage::disk('public')->assertExists($course->thumbnail);
    }

    public function test_required_fields_are_validated(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CourseForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title']);
    }

    public function test_admin_can_edit_an_existing_course(): void
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['title' => 'قديم', 'is_published' => false]);

        Livewire::actingAs($admin)
            ->test(CourseForm::class, ['courseId' => $course->id])
            ->assertSet('title', 'قديم')
            ->set('title', 'محدث')
            ->set('is_published', true)
            ->call('save')
            ->assertHasNoErrors();

        $course->refresh();
        $this->assertSame('محدث', $course->title);
        $this->assertTrue($course->is_published);
    }

    public function test_regular_user_cannot_access_the_create_form(): void
    {
        $this->get(route('admin.courses.create'))->assertRedirect(route('login'));

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.courses.create'))->assertForbidden();
    }
}
