<?php

namespace Tests\Feature\Livewire\Courses;

use App\Livewire\Courses\CoursesIndex;
use App\Models\AgeGroup;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CoursesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders_published_courses(): void
    {
        $response = $this->get(route('courses.index'));

        $response->assertOk();
        $response->assertSeeLivewire(CoursesIndex::class);
    }

    public function test_only_published_courses_are_visible(): void
    {
        Course::factory()->create(['title' => 'دورة منشورة']);
        Course::factory()->draft()->create(['title' => 'دورة مسودة']);

        Livewire::test(CoursesIndex::class)
            ->assertSee('دورة منشورة')
            ->assertDontSee('دورة مسودة');
    }

    public function test_search_filters_by_title(): void
    {
        Course::factory()->create(['title' => 'دورة الميزانية']);
        Course::factory()->create(['title' => 'دورة الاستثمار']);

        Livewire::test(CoursesIndex::class)
            ->set('search', 'الميزانية')
            ->assertSee('دورة الميزانية')
            ->assertDontSee('دورة الاستثمار');
    }

    public function test_level_filter_scopes_the_list(): void
    {
        Course::factory()->create(['title' => 'دورة مبتدئ', 'level' => 'beginner']);
        Course::factory()->create(['title' => 'دورة متقدم', 'level' => 'advanced']);

        Livewire::test(CoursesIndex::class)
            ->set('level', 'beginner')
            ->assertSee('دورة مبتدئ')
            ->assertDontSee('دورة متقدم');
    }

    public function test_age_group_filter_scopes_the_list(): void
    {
        $group = AgeGroup::factory()->create();
        $matching = Course::factory()->create(['title' => 'دورة الفئة']);
        $matching->ageGroups()->attach($group);
        Course::factory()->create(['title' => 'دورة أخرى']);

        Livewire::test(CoursesIndex::class)
            ->set('age_group_id', (string) $group->id)
            ->assertSee('دورة الفئة')
            ->assertDontSee('دورة أخرى');
    }
}
