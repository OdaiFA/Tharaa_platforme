<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'level' => $this->level,
            'thumbnail' => $this->thumbnail ? url('storage/' . $this->thumbnail) : null,
            'duration_hours' => $this->duration_hours,
            'is_published' => (bool) $this->is_published,
            'certificate_enabled' => (bool) $this->certificate_enabled,
            'passing_score' => $this->passing_score,
            'age_groups' => AgeGroupResource::collection($this->whenLoaded('ageGroups')),
            'modules_count' => $this->whenCounted('modules'),
            'enrollments_count' => $this->whenCounted('enrollments'),
            'created_at' => $this->created_at,
        ];
    }
}
