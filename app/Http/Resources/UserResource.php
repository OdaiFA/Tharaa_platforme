<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'age_group' => new AgeGroupResource($this->whenLoaded('ageGroup')),
            'currency' => $this->currency,
            'financial_level' => $this->financial_level,
            'role' => $this->role,
            'avatar' => $this->avatar_url,
            'created_at' => $this->created_at,
        ];
    }
}
