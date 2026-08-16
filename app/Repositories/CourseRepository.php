<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;

class CourseRepository
{
    public function published(?array $filters = [])
    {
        return Course::query()
            ->published()
            ->with(['modules', 'ageGroups'])
            ->when($filters['level'] ?? null, fn (Builder $q, $level) => $q->forLevel($level))
            ->when($filters['age_group_id'] ?? null, fn (Builder $q, $id) => $q->forAgeGroup($id))
            ->when($filters['search'] ?? null, fn (Builder $q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->latest()
            ->paginate(12);
    }

    public function recommendedFor(?int $ageGroupId)
    {
        if (! $ageGroupId) {
            return collect();
        }

        return Course::query()
            ->published()
            ->forAgeGroup($ageGroupId)
            ->with('ageGroups')
            ->latest()
            ->take(6)
            ->get();
    }

    public function findPublished(int $id): ?Course
    {
        return Course::query()->published()->with(['modules.lessons.quiz', 'ageGroups'])->find($id);
    }
}
