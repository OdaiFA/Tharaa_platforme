<?php

namespace Tests\Feature\Livewire\Courses;

use App\Livewire\Courses\RecommendedCourses;
use App\Models\AgeGroup;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RecommendedCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('courses.recommended'))
            ->assertOk()
            ->assertSeeLivewire(RecommendedCourses::class);
    }

    public function test_user_with_no_age_group_sees_no_recommendations(): void
    {
        $user = User::factory()->create(['age_group_id' => null]);
        Course::factory()->create();

        Livewire::actingAs($user)
            ->test(RecommendedCourses::class)
            ->assertSee('لا توجد دورات موصى بها');
    }

    public function test_user_sees_only_courses_matching_their_age_group(): void
    {
        $group = AgeGroup::factory()->create();
        $otherGroup = AgeGroup::factory()->create();
        $user = User::factory()->create(['age_group_id' => $group->id]);

        $matching = Course::factory()->create(['title' => 'دورة مطابقة']);
        $matching->ageGroups()->attach($group);

        $notMatching = Course::factory()->create(['title' => 'دورة غير مطابقة']);
        $notMatching->ageGroups()->attach($otherGroup);

        Livewire::actingAs($user)
            ->test(RecommendedCourses::class)
            ->assertSee('دورة مطابقة')
            ->assertDontSee('دورة غير مطابقة');
    }
}
