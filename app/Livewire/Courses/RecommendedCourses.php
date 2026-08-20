<?php

namespace App\Livewire\Courses;

use App\Repositories\CourseRepository;
use Livewire\Component;

class RecommendedCourses extends Component
{
    public function render(CourseRepository $courses)
    {
        $ageGroupId = auth()->user()?->age_group_id;

        return view('livewire.courses.recommended-courses', [
            'courses' => $courses->recommendedFor($ageGroupId),
        ]);
    }
}
