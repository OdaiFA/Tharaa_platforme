<?php

namespace App\Events;

use App\Models\Goal;
use App\Models\GoalContribution;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoalContributionAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Goal $goal,
        public readonly GoalContribution $contribution,
    ) {}

    public function broadcastOn(): array
    {
        return [];
    }
}
