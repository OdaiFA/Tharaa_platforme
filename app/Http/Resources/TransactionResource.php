<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->transaction_date?->format('Y-m-d'),
            'is_recurring' => (bool) $this->is_recurring,
            'recurrence_type' => $this->recurrence_type,
            'recurrence_end_date' => $this->recurrence_end_date?->format('Y-m-d'),
            'account' => new AccountResource($this->whenLoaded('account')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'transfer_to_account' => new AccountResource($this->whenLoaded('transferToAccount')),
            'created_at' => $this->created_at,
        ];
    }
}
