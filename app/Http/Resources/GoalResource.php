<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'target_amount' => (float) $this->target_amount,
            'current_amount' => (float) $this->current_amount,
            'currency_code' => $this->currency_code,
            'progress_percentage' => $this->progress_percentage,
            'deadline' => $this->deadline?->format('Y-m-d'),
            'priority' => $this->priority,
            'status' => $this->status,
            'icon' => $this->icon,
            'contributions_count' => $this->whenCounted('contributions'),
            'created_at' => $this->created_at,
        ];
    }
}
