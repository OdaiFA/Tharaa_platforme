<?php

namespace App\Events;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetThresholdReached
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Budget $budget,
        public readonly Transaction $transaction,
    ) {}

    public function broadcastOn(): array
    {
        return [];
    }
}
