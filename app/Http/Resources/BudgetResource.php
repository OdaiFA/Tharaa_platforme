<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_amount' => (float) $this->total_amount,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'alert_percentage' => $this->alert_percentage,
            'currency' => $this->currency,
            'is_active' => (bool) $this->is_active,
            'categories' => $this->whenLoaded('budgetCategories', fn () => $this->budgetCategories->map(
                fn ($bc) => [
                    'category' => new CategoryResource($bc->category),
                    'limit_amount' => (float) $bc->limit_amount,
                    'alert_percentage' => $bc->alert_percentage,
                ]
            )),
            'created_at' => $this->created_at,
        ];
    }
}
