<?php

namespace App\Livewire\Courses;

use App\Models\AgeGroup;
use App\Repositories\CourseRepository;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CoursesIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $level = '';

    #[Url]
    public string $age_group_id = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedLevel(): void
    {
        $this->resetPage();
    }

    public function updatedAgeGroupId(): void
    {
        $this->resetPage();
    }

    public function render(CourseRepository $courses)
    {
        $list = $courses->published([
            'level' => $this->level ?: null,
            'age_group_id' => $this->age_group_id ?: null,
            'search' => $this->search ?: null,
        ]);

        return view('livewire.courses.courses-index', [
            'courses' => $list,
            'ageGroups' => AgeGroup::orderBy('min_age')->get(),
        ]);
    }
}
